<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include DB Connection
include 'config.php'; // Adjust as needed

// Get today's date
$current_date = date('Y-m-d');

// Fetch total opening balance
$opening = $link->query("SELECT SUM(total_balance) AS opening_total FROM opening_balance WHERE date = '$current_date'")->fetch_assoc();
$opening_total = $opening['opening_total'] ?? 0;

// Fetch total day end balance
$day_end = $link->query("SELECT SUM(total_balance) AS day_end_total FROM day_end_balance WHERE date = '$current_date'")->fetch_assoc();
$day_end_total = $day_end['day_end_total'] ?? 0;

// Fetch payment breakdown for today
$payment_sql = "
    SELECT 
        bp.payment_method,
        COALESCE(SUM(b.payment_amount), 0) AS total_by_method
    FROM bill_payments bp
    INNER JOIN bills b ON bp.bill_id = b.bill_id
    WHERE DATE(b.bill_time) = '$current_date'
    GROUP BY bp.payment_method
";
$payment_result = $link->query($payment_sql);
$payment_methods = [];
while ($row = $payment_result->fetch_assoc()) {
    $payment_methods[strtolower($row['payment_method'])] = $row['total_by_method'];
}

// Individual sales
$total_cash_sale   = $payment_methods['cash'] ?? 0;
$total_credit_sale = $payment_methods['credit'] ?? 0;
$total_debit_sale  = $payment_methods['debit'] ?? 0;
$total_bank_sale   = $payment_methods['bank'] ?? 0;

$total_non_cash_sales = $total_credit_sale + $total_debit_sale + $total_bank_sale;

// Grand total sales (all payment types)
$grand_total_sales = $total_cash_sale + $total_credit_sale + $total_debit_sale + $total_bank_sale;

// Fetch total issued (cash disbursements)
$issued_sql = "SELECT COALESCE(SUM(issued_amount), 0) AS total_issued FROM cash_disbursements WHERE DATE(issued_date) = '$current_date'";
$issued_result = $link->query($issued_sql);
$total_issued = $issued_result->fetch_assoc()['total_issued'] ?? 0;

// Fetch total received (cash receipts)
$receipts_sql = "SELECT COALESCE(SUM(received_amount), 0) AS total_received FROM cash_receipts WHERE DATE(received_date) = '$current_date'";
$receipts_result = $link->query($receipts_sql);
$total_received = $receipts_result->fetch_assoc()['total_received'] ?? 0;

// Calculate expected cash (Opening + Cash Sales + Cash Receipts - Issued)
$expected_cash = $opening_total + $total_cash_sale + $total_received - $total_issued;

// Compare expected cash with actual day end balance
$difference = $day_end_total - $expected_cash;
$difference_status = $difference == 0 ? 'Balanced' : ($difference > 0 ? 'Excess' : 'Shortage');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Day End Shift Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .summary-card {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .difference-card {
            background: linear-gradient(135deg, #ff512f, #dd2476);
            color: #fff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            font-size: 1.4rem;
            font-weight: bold;
        }
        .difference-card.shortage {
            background: linear-gradient(135deg, #c31432, #240b36);
        }
        .difference-card.excess {
            background: linear-gradient(135deg, #00b09b, #96c93d);
        }
    </style>
</head>
<body>
<div class="container py-4">
    <h2 class="text-center mb-4">📋 Day End Shift Report – <?= date('Y-m-d') ?></h2>

    <!-- Opening and Day End Balances -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="summary-card">
                <h5>🧾 Opening Balance</h5>
                <h3>Rs. <?= number_format($opening_total, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="summary-card" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                <h5>✅ Day End Balance</h5>
                <h3>Rs. <?= number_format($day_end_total, 2) ?></h3>
            </div>
        </div>
    </div>

    <!-- Payment Breakdown -->
    <h4 class="mt-4">💳 Sales Breakdown</h4>
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <h5>💵 Cash Sale</h5>
                <h3>Rs. <?= number_format($total_cash_sale, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <h5>💳 Credit Sale</h5>
                <h3>Rs. <?= number_format($total_credit_sale, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <h5>🏧 Debit Sale</h5>
                <h3>Rs. <?= number_format($total_debit_sale, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <h5>🏦 Bank Sale</h5>
                <h3>Rs. <?= number_format($total_bank_sale, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="summary-card" style="background: linear-gradient(135deg, #00c6ff, #0072ff);">
    <h5>💳 Total Non-Cash Sales (Bank + Debit + Credit)</h5>
    <h3>Rs. <?= number_format($total_non_cash_sales, 2) ?></h3>
</div>


    <!-- Cash Receipts -->
    <div class="summary-card" style="background: linear-gradient(135deg, #7f00ff, #e100ff);">
        <h5>📥 Cash Receipts (Other Incoming Cash)</h5>
        <h3>Rs. <?= number_format($total_received, 2) ?></h3>
    </div>

    <!-- Grand Total Sales -->
    <div class="summary-card" style="background: linear-gradient(135deg, #f7971e, #ffd200);">
        <h5>💰 Total Sales (All Payment Types)</h5>
        <h3>Rs. <?= number_format($grand_total_sales, 2) ?></h3>
    </div>

    <!-- Cash Summary -->
    <h4 class="mt-4">💵 Cash Drawer Summary</h4>
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="summary-card" style="background: linear-gradient(135deg, #ff6a00, #ee0979);">
                <h5>Opening + Cash Sales + Cash Receipts</h5>
                <h3>Rs. <?= number_format($opening_total + $total_cash_sale + $total_received, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="summary-card" style="background: linear-gradient(135deg, #8360c3, #2ebf91);">
                <h5>Issued Cash (Cash Disbursements)</h5>
                <h3>Rs. <?= number_format($total_issued, 2) ?></h3>
            </div>
        </div>
    </div>

    <div class="summary-card" style="background: linear-gradient(135deg, #0f2027, #2c5364);">
        <h5>Expected Cash (Opening + Cash Sales + Receipts - Issued)</h5>
        <h3>Rs. <?= number_format($expected_cash, 2) ?></h3>
    </div>

    <!-- Difference -->
    <div class="difference-card <?= $difference < 0 ? 'shortage' : ($difference > 0 ? 'excess' : '') ?>">
        <?= $difference_status ?>: Rs. <?= number_format(abs($difference), 2) ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
