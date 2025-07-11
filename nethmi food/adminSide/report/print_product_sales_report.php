<?php
require_once "../config.php";

// Get and sanitize parameters
$category = isset($_GET['category']) ? $link->real_escape_string($_GET['category']) : '';
$search = isset($_GET['search']) ? $link->real_escape_string($_GET['search']) : '';
$startDate = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $link->real_escape_string($_GET['start_date']) : '1970-01-01';
$endDate = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $link->real_escape_string($_GET['end_date']) : date('Y-m-d');

// Build the SQL query with explicit collation for JOINs to fix the collation mismatch
$sql = "SELECT 
            bi.item_id, 
            bi.product_name,
            bi.portion_size,
            bi.price,
            SUM(bi.quantity) as total_quantity,
            SUM(bi.price * bi.quantity) as total_sales,
            m.item_category
        FROM 
            bill_items bi
        LEFT JOIN 
            menu m ON bi.item_id = CONVERT(m.item_id USING utf8mb4) COLLATE utf8mb4_general_ci
        LEFT JOIN 
            bills b ON bi.bill_id = b.bill_id
        WHERE 
            b.bill_time BETWEEN '$startDate' AND '$endDate 23:59:59'";

// Add category filter if specified
if (!empty($category)) {
    $sql .= " AND m.item_category = '$category'";
}

// Add search filter if specified
if (!empty($search)) {
    $sql .= " AND (bi.product_name LIKE '%$search%' OR bi.item_id LIKE '%$search%')";
}

// Group by product and add sorting
$sql .= " GROUP BY bi.item_id, bi.product_name, bi.portion_size, bi.price
         ORDER BY total_sales DESC";

$result = $link->query($sql);

if (!$result) {
    die("Error executing query: " . $link->error);
}

// Calculate totals
$totalQuantity = 0;
$totalSales = 0;
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
    $totalQuantity += $row['total_quantity'];
    $totalSales += $row['total_sales'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Sales Report</title>
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
        <h1>Product Sales Report</h1>
        <p><?php echo date('F j, Y g:i A'); ?></p>
    </div>

    <div class="report-meta">
        <div class="filters">
            <strong>Date Range:</strong> 
            <?php echo !empty($startDate) ? date('Y-m-d', strtotime($startDate)) : 'All time'; ?> 
            to 
            <?php echo !empty($endDate) ? date('Y-m-d', strtotime($endDate)) : date('Y-m-d'); ?>
            
            <?php if (!empty($category)): ?>
                <br><strong>Category:</strong> <?php echo htmlspecialchars($category); ?>
            <?php endif; ?>
            
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
                <th>Portion Size</th>
                <th>Unit Price</th>
                <th>Quantity Sold</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No product sales data found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_id'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($item['product_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($item['item_category'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($item['portion_size'] ?? 'N/A'); ?></td>
                        <td>Rs. <?php echo number_format((float)$item['price'], 2); ?></td>
                        <td><?php echo number_format($item['total_quantity']); ?></td>
                        <td>Rs. <?php echo number_format((float)$item['total_sales'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="5" style="text-align: right;"><strong>TOTALS:</strong></td>
                    <td><?php echo number_format($totalQuantity); ?></td>
                    <td>Rs. <?php echo number_format($totalSales, 2); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Report generated from restaurant management system</p>
    </div>
</body>
</html>
<?php $link->close(); ?>
