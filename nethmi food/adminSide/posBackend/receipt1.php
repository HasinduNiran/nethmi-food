<?php
// Get data passed via GET request
$totalRevenue = isset($_GET['total_revenue']) ? $_GET['total_revenue'] : 0;
$totalCashIn = isset($_GET['total_cash_in']) ? $_GET['total_cash_in'] : 0;
$totalCashOut = isset($_GET['total_cash_out']) ? $_GET['total_cash_out'] : 0;
$netProfit = isset($_GET['net_profit']) ? $_GET['net_profit'] : 0;
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Fetch details for Cash Flow and Bills
require_once '../config.php';

// Fetch Cash Flow Details
$cashFlowQuery = "SELECT * FROM cash_balance 
                  WHERE DATE(entry_date) BETWEEN '$startDate' AND '$endDate'";
$cashFlowResult = mysqli_query($link, $cashFlowQuery);

// Fetch Bills Details
$billsQuery = "SELECT * FROM bills 
               WHERE DATE(bill_time) BETWEEN '$startDate' AND '$endDate'";
$billsResult = mysqli_query($link, $billsQuery);

// Initialize totals
$cashInTotal = 0;
$cashOutTotal = 0;
$billTotal = 0;

// Calculate Cash Flow Totals
if ($cashFlowResult) {
    while ($row = mysqli_fetch_assoc($cashFlowResult)) {
        if ($row['status'] == 'Cash In') {
            $cashInTotal += $row['cash_amount'];
        } elseif ($row['status'] == 'Cash Out') {
            $cashOutTotal += $row['cash_amount'];
        }
    }
}

// Calculate Bills Total
if ($billsResult) {
    while ($row = mysqli_fetch_assoc($billsResult)) {
        $billTotal += $row['payment_amount'];
    }
}

// Calculate Final Totals
$subtotal = $billTotal + $cashInTotal - $cashOutTotal;
$grandTotal = $subtotal; // No tax calculation
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            width: 58mm;
            text-align: center;
        }
        .header, .footer {
            text-align: center;
            border-bottom: 1px dashed #000;
            margin-bottom: 10px;
        }
        h3 {
            margin: 0;
            font-size: 14px;
        }
        p {
            margin: 2px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            font-size: 10px;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border-bottom: 1px dashed #000;
            padding: 4px;
            text-align: left;
        }
        th {
            text-align: left;
            font-weight: bold;
        }
        .totals, .cash-flow, .bills {
            margin-top: 10px;
            font-size: 12px;
        }
        .footer {
            border-top: 1px dashed #000;
            margin-top: 10px;
            font-size: 10px;
        }
    </style>
</head>
<body onload="window.print();">
    <div class="header">
        <h3>Financial Report</h3>
        <p>Date Range: <?php echo $startDate; ?> to <?php echo $endDate; ?></p>
    </div>

    <!-- Summary Totals -->
    <div class="totals">
        <p><strong>Total Revenue:</strong> Rs. <?php echo number_format($billTotal, 2); ?></p>
        <p><strong>Total Cash In:</strong> Rs. <?php echo number_format($cashInTotal, 2); ?></p>
        <p><strong>Total Cash Out:</strong> Rs. <?php echo number_format($cashOutTotal, 2); ?></p>
        <p><strong>Subtotal:</strong> Rs. <?php echo number_format($subtotal, 2); ?></p>
        <p><strong>Grand Total:</strong> Rs. <?php echo number_format($grandTotal, 2); ?></p>
    </div>

    <!-- Cash Flow Details -->
    <div class="cash-flow">
        <h4>Cash Flow Details</h4>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                mysqli_data_seek($cashFlowResult, 0); // Reset pointer
                if ($cashFlowResult && mysqli_num_rows($cashFlowResult) > 0) {
                    while ($row = mysqli_fetch_assoc($cashFlowResult)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>Rs. <?php echo number_format($row['cash_amount'], 2); ?></td>
                        </tr>
                    <?php endwhile;
                } else {
                    echo "<tr><td colspan='3'>No cash flow data.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bills Details -->
    <div class="bills">
        <h4>Bills Details</h4>
        <table>
            <thead>
                <tr>
                    <th>Bill ID</th>
                    <th>Table ID</th>
                    <th>Staff ID</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                mysqli_data_seek($billsResult, 0); // Reset pointer
                if ($billsResult && mysqli_num_rows($billsResult) > 0) {
                    while ($row = mysqli_fetch_assoc($billsResult)): ?>
                        <tr>
                            <td><?php echo $row['bill_id']; ?></td>
                            <td><?php echo $row['table_id']; ?></td>
                            <td><?php echo $row['staff_id']; ?></td>
                            <td>Rs. <?php echo number_format($row['payment_amount'], 2); ?></td>
                        </tr>
                    <?php endwhile;
                } else {
                    echo "<tr><td colspan='4'>No bill data.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for your efforts!</p>
    </div>
</body>
</html>
