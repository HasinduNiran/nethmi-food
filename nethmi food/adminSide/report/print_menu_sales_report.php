<?php
require_once "../config.php";

// Get and sanitize parameters
$search = isset($_GET['search']) ? $link->real_escape_string($_GET['search']) : '';
$startDate = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $link->real_escape_string($_GET['start_date']) : null;
$endDate = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $link->real_escape_string($_GET['end_date']) : null;

// Check if price_type column exists in bill_items table
$checkColumnQuery = "SHOW COLUMNS FROM bill_items LIKE 'price_type'";
$columnResult = $link->query($checkColumnQuery);
$hasPriceTypeColumn = $columnResult && $columnResult->num_rows > 0;

// Build query conditions
$conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $conditions[] = "(m.item_name LIKE ? OR m.item_id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if ($startDate) {
    $conditions[] = "DATE(b.bill_time) >= ?";
    $params[] = $startDate;
    $types .= 's';
}

if ($endDate) {
    $conditions[] = "DATE(b.bill_time) <= ?";
    $params[] = $endDate;
    $types .= 's';
}

// Combine conditions
$whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// SQL query with direct hotel_type values from bills table
$sql = "
    SELECT 
        m.item_id,
        m.item_name,
        m.item_category,
        m.item_type,
        bi.portion_size,
        " . ($hasPriceTypeColumn ? "bi.price_type," : "'regular' as price_type,") . "
        CONCAT(
            UPPER(SUBSTRING(bi.portion_size, 1, 1)), 
            SUBSTRING(bi.portion_size, 2),
            ' (',
            CASE 
                WHEN b.hotel_type = 1 THEN 'Dine'
                WHEN b.hotel_type = 4 THEN 'Uber'
                WHEN b.hotel_type = 6 THEN 'PickMe'
                WHEN b.hotel_type = 7 THEN 'Takeaway'
                WHEN b.hotel_type = 11 THEN 'Delivery'
                ELSE 'Regular'
            END,
            ')'
        ) as display_size,
        bi.price as actual_price,
        SUM(bi.quantity) as total_quantity,
        SUM(bi.price * bi.quantity) as total_sales,
        b.hotel_type
    FROM 
        bill_items bi
    JOIN 
        menu m ON bi.item_id = CONVERT(m.item_id USING utf8mb4) COLLATE utf8mb4_general_ci
    JOIN 
        bills b ON bi.bill_id = b.bill_id
    $whereClause
    GROUP BY 
        m.item_id, m.item_name, m.item_category, m.item_type, bi.portion_size, " . 
        ($hasPriceTypeColumn ? "bi.price_type, " : "") . "bi.price, b.hotel_type
    ORDER BY 
        total_sales DESC
";

$stmt = $link->prepare($sql);

if ($stmt) {
    // Bind parameters if any
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    $totalQuantity = 0;
    $totalSales = 0;
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
        $totalQuantity += $row['total_quantity'];
        $totalSales += $row['total_sales'];
    }
} else {
    die("Query preparation failed: " . $link->error);
}

// The rest of the HTML code remains the same
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-meta {
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .filters {
            margin-bottom: 15px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <h1>Menu Sales Report</h1>
        <p><?php echo date('F j, Y g:i A'); ?></p>
    </div>

    <div class="report-meta">
        <div class="filters">
            <strong>Date Range:</strong> 
            <?php echo !empty($startDate) ? date('Y-m-d', strtotime($startDate)) : 'All time'; ?> 
            to 
            <?php echo !empty($endDate) ? date('Y-m-d', strtotime($endDate)) : date('Y-m-d'); ?>
            
            <?php if (!empty($search)): ?>
                <br><strong>Search Term:</strong> <?php echo htmlspecialchars($search); ?>
            <?php endif; ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Type</th>
                <th>Size/Service</th>
                <th>Unit Price</th>
                <th>Quantity Sold</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No menu sales data found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_id'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($item['item_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($item['item_category'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($item['item_type'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($item['display_size'] ?? 'N/A'); ?></td>
                        <td>Rs. <?php echo number_format((float)$item['actual_price'], 2); ?></td>
                        <td><?php echo number_format($item['total_quantity']); ?></td>
                        <td>Rs. <?php echo number_format((float)$item['total_sales'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="6" style="text-align: right;"><strong>TOTALS:</strong></td>
                    <td><?php echo number_format($totalQuantity); ?></td>
                    <td>Rs. <?php echo number_format($totalSales, 2); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Report generated from restaurant management system</p>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
<?php $link->close(); ?>
