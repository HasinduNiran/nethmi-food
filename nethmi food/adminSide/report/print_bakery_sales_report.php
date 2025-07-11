<?php
// Initialize the session
session_start();

// Include database connection
require_once "../config.php"; 

// Get search parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$startDate = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;

// Base SQL query - modified to include cost_price for profit calculation
$sql = "
    SELECT 
        bm.item_id,
        bm.item_name,
        bm.item_price,
        bm.cost_price,
        bm.bakery_category,
        SUM(bi.quantity) as total_quantity,
        SUM(bi.quantity * bm.item_price) as total_sales,
        SUM(bi.quantity * bm.cost_price) as total_cost
    FROM 
        bill_items bi
    JOIN 
        bakery_menu_stocks bm ON bi.item_id = bm.item_id
    JOIN 
        bills b ON bi.bill_id = b.bill_id
    WHERE 
        1=1
";

// Add search condition if search parameter is provided
if (!empty($search)) {
    $sql .= " AND (bm.item_name LIKE ? OR bm.item_id LIKE ?)";
}

// Add date range if dates are provided
if ($startDate) {
    $sql .= " AND DATE(b.bill_time) >= ?";
}

if ($endDate) {
    $sql .= " AND DATE(b.bill_time) <= ?";
}

// Group by and order by
$sql .= " GROUP BY bm.item_id ORDER BY total_sales DESC";

// Prepare the statement
$stmt = $link->prepare($sql);

// Bind parameters based on conditions
$types = '';
$params = [];

if (!empty($search)) {
    $searchParam = "%$search%";
    $types .= 'ss';
    $params[] = $searchParam;
    $params[] = $searchParam;
}

if ($startDate) {
    $types .= 's';
    $params[] = $startDate;
}

if ($endDate) {
    $types .= 's';
    $params[] = $endDate;
}

// Bind parameters if any
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// Execute the query
$stmt->execute();

// Get result
$result = $stmt->get_result();

// Fetch all rows as associative array
$reports = $result->fetch_all(MYSQLI_ASSOC);

// Calculate total sales amount, cost, and profit
$totalSalesAmount = 0;
$totalCostAmount = 0;
$totalItemsSold = 0;

foreach ($reports as &$report) {
    $totalSalesAmount += $report['total_sales'];
    $totalCostAmount += $report['total_cost'];
    $totalItemsSold += $report['total_quantity'];
    
    // Calculate profit for each item
    $report['profit'] = $report['total_sales'] - $report['total_cost'];
}

// Calculate total profit
$totalProfit = $totalSalesAmount - $totalCostAmount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bakery Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .restaurant-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .date-range {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .total-row {
            font-weight: bold;
        }
        .timestamp {
            margin-top: 30px;
            text-align: right;
            font-style: italic;
            font-size: 10px;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
        .totals-section {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .totals-section th {
            background-color: #e9ecef;
            text-align: left;
            padding: 8px;
        }
        .totals-section td {
            padding: 8px;
            text-align: right;
            font-weight: bold;
        }
        .profit-positive {
            color: green;
        }
        .profit-negative {
            color: red;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="restaurant-name">MVintage Resturant & Cafe</div>
        <div class="report-title">Bakery Sales Report (Product Wise)</div>
        <div class="date-range">
            <?php
            $dateInfo = "";
            if ($startDate && $endDate) {
                $dateInfo = "From: " . date('d/m/Y', strtotime($startDate)) . " To: " . date('d/m/Y', strtotime($endDate));
            } elseif ($startDate) {
                $dateInfo = "From: " . date('d/m/Y', strtotime($startDate)) . " To: Present";
            } elseif ($endDate) {
                $dateInfo = "Up To: " . date('d/m/Y', strtotime($endDate));
            } else {
                $dateInfo = "All Time";
            }
            echo $dateInfo;
            ?>
        </div>
        <?php if (!empty($search)): ?>
        <div>Search Query: <?php echo htmlspecialchars($search); ?></div>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Unit Price</th>
                <th>Cost Price</th>
                <th>Quantity Sold</th>
                <th>Total Sales</th>
                <th>Total Cost</th>
                <th>Profit</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reports)): ?>
                <tr>
                    <td colspan="9" style="text-align: center;">No bakery product sales found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reports as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_id'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($item['item_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($item['bakery_category'] ?? 'N/A'); ?></td>
                        <td>Rs. <?php echo number_format($item['item_price'], 2); ?></td>
                        <td>Rs. <?php echo number_format($item['cost_price'], 2); ?></td>
                        <td><?php echo $item['total_quantity']; ?></td>
                        <td>Rs. <?php echo number_format($item['total_sales'], 2); ?></td>
                        <td>Rs. <?php echo number_format($item['total_cost'], 2); ?></td>
                        <td class="<?php echo $item['profit'] >= 0 ? 'profit-positive' : 'profit-negative'; ?>">
                            Rs. <?php echo number_format($item['profit'], 2); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="5" style="text-align: right;"><strong>Grand Totals:</strong></td>
                    <td><strong><?php echo $totalItemsSold; ?></strong></td>
                    <td><strong>Rs. <?php echo number_format($totalSalesAmount, 2); ?></strong></td>
                    <td><strong>Rs. <?php echo number_format($totalCostAmount, 2); ?></strong></td>
                    <td class="<?php echo $totalProfit >= 0 ? 'profit-positive' : 'profit-negative'; ?>">
                        <strong>Rs. <?php echo number_format($totalProfit, 2); ?></strong>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Summary section at the bottom -->
    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <th>Summary</th>
                <td></td>
            </tr>
            <tr>
                <th>Total Items Sold:</th>
                <td><?php echo $totalItemsSold; ?></td>
            </tr>
            <tr>
                <th>Total Sales Amount:</th>
                <td>Rs. <?php echo number_format($totalSalesAmount, 2); ?></td>
            </tr>
            <tr>
                <th>Total Cost Amount:</th>
                <td>Rs. <?php echo number_format($totalCostAmount, 2); ?></td>
            </tr>
            <tr>
                <th>Total Profit:</th>
                <td class="<?php echo $totalProfit >= 0 ? 'profit-positive' : 'profit-negative'; ?>">
                    Rs. <?php echo number_format($totalProfit, 2); ?>
                </td>
            </tr>
            <tr>
                <th>Profit Margin:</th>
                <td class="<?php echo $totalProfit >= 0 ? 'profit-positive' : 'profit-negative'; ?>">
                    <?php echo $totalSalesAmount > 0 ? number_format(($totalProfit / $totalSalesAmount) * 100, 2) : '0.00'; ?>%
                </td>
            </tr>
        </table>
    </div>

    <div class="timestamp">
        Generated on: <?php echo date('d/m/Y H:i:s'); ?>
    </div>
</body>
</html>
