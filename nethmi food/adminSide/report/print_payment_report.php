<?php
// Initialize the session
session_start();

// Include database connection
require_once "../config.php"; 

// Get search parameters
$paymentMethod = isset($_GET['payment_method']) ? trim($_GET['payment_method']) : '';
$startDate = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;

// Base SQL query
$sql = "
    SELECT 
        bp.bill_id,
        bp.payment_method,
        bp.amount,
        bp.card_id,
        bp.created_at,
        b.bill_time,
        b.customer_name
    FROM 
        bill_payments bp
    JOIN 
        bills b ON bp.bill_id = b.bill_id
    WHERE 
        1=1
";

// Add payment method filter if provided
if (!empty($paymentMethod)) {
    $sql .= " AND bp.payment_method = ?";
}

// Add date range if dates are provided
if ($startDate) {
    $sql .= " AND DATE(bp.created_at) >= ?";
}

if ($endDate) {
    $sql .= " AND DATE(bp.created_at) <= ?";
}

// Order by
$sql .= " ORDER BY bp.created_at DESC";

// Prepare the statement
$stmt = $link->prepare($sql);

// Bind parameters based on conditions
$types = '';
$params = [];

if (!empty($paymentMethod)) {
    $types .= 's';
    $params[] = $paymentMethod;
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

// Calculate totals
$totalAmount = 0;
$countByMethod = [];

foreach($reports as $item) {
    $totalAmount += $item['amount'];
    
    $method = $item['payment_method'];
    if (!isset($countByMethod[$method])) {
        $countByMethod[$method] = [
            'count' => 0,
            'total' => 0
        ];
    }
    $countByMethod[$method]['count']++;
    $countByMethod[$method]['total'] += $item['amount'];
}

// Payment method mapping for display
$paymentMethodLabels = [
    'cash' => 'Cash',
    'card' => 'Card',
    'cre' => 'Credit',
    'credit' => 'Credit Card',
    'debit' => 'Debit Card',
    'bank' => 'Bank Transfer'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Method Sales Report</title>
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
            margin-bottom: 20px;
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
        .summary-table {
            width: 60%;
            margin-top: 30px;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
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
    </style>
</head>
<body>
    <div class="report-header">
        <div class="restaurant-name">Vintage Resturant & Cafe</div>
        <div class="report-title">Payment Method Sales Report</div>
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
        <?php if (!empty($paymentMethod)): ?>
        <div>Payment Method: <?php echo htmlspecialchars($paymentMethodLabels[$paymentMethod] ?? $paymentMethod); ?></div>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Bill ID</th>
                <th>Customer</th>
                <th>Payment Method</th>
                <th>Amount</th>
                <th>Card ID</th>
                <th>Bill Time</th>
                <th>Payment Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reports)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No payment records found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reports as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['bill_id'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($item['customer_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($paymentMethodLabels[$item['payment_method']] ?? $item['payment_method']); ?></td>
                        <td>Rs. <?php echo number_format($item['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['card_id'] != 'N/A' ? $item['card_id'] : 'N/A'); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($item['bill_time'])); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;"><strong>Grand Total:</strong></td>
                    <td colspan="4"><strong>Rs. <?php echo number_format($totalAmount, 2); ?></strong></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Payment Method Summary -->
    <?php if (!empty($countByMethod)): ?>
    <h3>Payment Method Summary</h3>
    <table class="summary-table">
        <thead>
            <tr>
                <th>Payment Method</th>
                <th>Transaction Count</th>
                <th>Total Amount</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($countByMethod as $method => $data): ?>
            <tr>
                <td><?php echo htmlspecialchars($paymentMethodLabels[$method] ?? $method); ?></td>
                <td><?php echo $data['count']; ?></td>
                <td>Rs. <?php echo number_format($data['total'], 2); ?></td>
                <td><?php echo number_format(($data['total'] / $totalAmount) * 100, 2); ?>%</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="timestamp">
        Generated on: <?php echo date('d/m/Y H:i:s'); ?>
    </div>
</body>
</html>
