<?php
// print_sales_report.php
require_once "../config.php"; 

try {
    if (!$link) {
        throw new Exception("Database connection failed.");
    }

    $staffId = $_GET['staff_id'] ?? '';
    $startDate = $_GET['start_date'] ?? '';
    $endDate = $_GET['end_date'] ?? '';

    $query = "SELECT 
                b.bill_id, 
                b.staff_id, 
                b.payment_method, 
                b.bill_time, 
                b.payment_amount, 
                b.paid_amount, 
                b.balance_amount, 
                b.total_before_discount, 
                b.discount_amount
              FROM bills b
              WHERE 1=1";

    $params = [];
    $types = '';

    if (!empty($staffId)) {
        $query .= " AND b.staff_id = ?";
        $params[] = $staffId;
        $types .= 'i';
    }
    if (!empty($startDate) && !empty($endDate)) {
        $query .= " AND b.bill_time BETWEEN ? AND ?";
        $params[] = $startDate . ' 00:00:00';
        $params[] = $endDate . ' 23:59:59';
        $types .= 'ss';
    }

    $stmt = $link->prepare($query);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $link->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $link->close();
} catch (Exception $e) {
    $link->close();
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report (Invoice Wise)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .no-data {
            text-align: center;
            padding: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <h1>Sales Report (Invoice Wise)</h1>
    
    <?php if (!empty($staffId) || (!empty($startDate) && !empty($endDate))): ?>
        <p>
            <?php
            $filters = [];
            if (!empty($staffId)) $filters[] = "Staff ID: $staffId";
            if (!empty($startDate) && !empty($endDate)) $filters[] = "Date Range: $startDate to $endDate";
            echo "Filters: " . implode(", ", $filters);
            ?>
        </p>
    <?php endif; ?>

    <?php if (empty($data)): ?>
        <p class="no-data">No sales invoices found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Bill ID</th>
       
                    <th>Bill Time</th>
                    <th>Payment Amount</th>
                    <th>Paid Amount</th>
                    <th>Balance Amount</th>
                    <th>Total Before Discount</th>
                    <th>Discount Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['bill_id']); ?></td>
        
                        <td><?php echo htmlspecialchars($item['bill_time'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($item['payment_amount'] ?? '0.00'); ?></td>
                        <td><?php echo htmlspecialchars($item['paid_amount'] ?? '0.00'); ?></td>
                        <td><?php echo htmlspecialchars($item['balance_amount'] ?? '0.00'); ?></td>
                        <td><?php echo htmlspecialchars($item['total_before_discount'] ?? '0.00'); ?></td>
                        <td><?php echo htmlspecialchars($item['discount_amount'] ?? '0.00'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <button class="no-print" onclick="window.close()">Close</button>
</body>
</html>