<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include DB Connection
include 'config.php'; // Update path if needed

// Get selected filters from dropdown and date range
$selected_status = $_GET['status'] ?? '';
$selected_order_status = $_GET['order_status'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

// Build dynamic WHERE clause for bills
$where_clauses = [];
if ($selected_status !== '') {
    $where_clauses[] = "LOWER(b.status) = '" . strtolower($link->real_escape_string($selected_status)) . "'";
}
if ($selected_order_status !== '') {
    $where_clauses[] = "LOWER(b.order_status) = '" . strtolower($link->real_escape_string($selected_order_status)) . "'";
}
if ($from_date !== '' && $to_date !== '') {
    $where_clauses[] = "DATE(b.bill_time) BETWEEN '" . $link->real_escape_string($from_date) . "' AND '" . $link->real_escape_string($to_date) . "'";
} elseif ($from_date !== '') {
    $where_clauses[] = "DATE(b.bill_time) >= '" . $link->real_escape_string($from_date) . "'";
} elseif ($to_date !== '') {
    $where_clauses[] = "DATE(b.bill_time) <= '" . $link->real_escape_string($to_date) . "'";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Fetch summary data from bills
$sql = "SELECT 
            COUNT(*) AS total_bills,
            COALESCE(SUM(b.payment_amount), 0) AS total_payment,
            COALESCE(SUM(b.discount_amount), 0) AS total_discount,
            COALESCE(SUM(b.service_charge), 0) AS total_service_charge,
            COALESCE(SUM(b.paid_amount), 0) AS total_paid,
            COALESCE(SUM(b.balance_amount), 0) AS total_balance
        FROM bills b
        $where_sql";
$result = $link->query($sql);
$summary = $result->fetch_assoc();

// Fetch payment method breakdown from bill_payments
$payment_sql = "
    SELECT 
        bp.payment_method,
        COALESCE(SUM(b.payment_amount), 0) AS total_by_method
    FROM bill_payments bp
    INNER JOIN bills b ON bp.bill_id = b.bill_id
    $where_sql
    GROUP BY bp.payment_method
";
$payment_result = $link->query($payment_sql);
$payment_methods = [];
while ($row = $payment_result->fetch_assoc()) {
    $payment_methods[strtolower($row['payment_method'])] = $row['total_by_method'];
}

// Calculate grand total of all payment types
$total_all_methods = 
    ($payment_methods['cash'] ?? 0) +
    ($payment_methods['bank'] ?? 0) +
    ($payment_methods['credit'] ?? 0) +
    ($payment_methods['debit'] ?? 0);

// Fetch all filtered bills
$bills_sql = "SELECT * FROM bills b $where_sql ORDER BY b.bill_time DESC";
$bills = $link->query($bills_sql);

// Fetch distinct statuses and order_status for dropdowns
$status_res = $link->query("SELECT DISTINCT status FROM bills");
$order_status_res = $link->query("SELECT DISTINCT order_status FROM bills");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bill Summary Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .summary-card {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            padding: 20px;
            margin-bottom: 20px;
        }
        .grand-total-card {
            background: linear-gradient(135deg, #ff6a00, #ee0979);
            color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            padding: 20px;
            margin-bottom: 20px;
        }
        .summary-card h5, .grand-total-card h5 {
            margin-bottom: 10px;
            font-weight: 600;
        }
        .table-container {
            overflow-x: auto;
        }
        th, td {
            white-space: nowrap;
        }
        .filter-form {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="container py-4">
    <h2 class="text-center mb-4">📊 Bill Summary Report</h2>

    <!-- Filter Form -->
    <form method="GET" class="filter-form row g-3">
        <div class="col-md-3">
        <!--    <label class="form-label">Status</label>-->
        <!--    <select name="status" class="form-select">-->
        <!--        <option value="">All</option>-->
        <!--        <?php while($row = $status_res->fetch_assoc()): ?>-->
        <!--            <option value="<?= htmlspecialchars($row['status']) ?>" <?= $selected_status == $row['status'] ? 'selected' : '' ?>>-->
        <!--                <?= htmlspecialchars($row['status']) ?>-->
        <!--            </option>-->
        <!--        <?php endwhile; ?>-->
        <!--    </select>-->
        <!--</div>-->
        <!--<div class="col-md-3">-->
        <!--    <label class="form-label">Order Status</label>-->
        <!--    <select name="order_status" class="form-select">-->
        <!--        <option value="">All</option>-->
        <!--        <?php while($row = $order_status_res->fetch_assoc()): ?>-->
        <!--            <option value="<?= htmlspecialchars($row['order_status']) ?>" <?= $selected_order_status == $row['order_status'] ? 'selected' : '' ?>>-->
        <!--                <?= htmlspecialchars($row['order_status']) ?>-->
        <!--            </option>-->
        <!--        <?php endwhile; ?>-->
        <!--    </select>-->
        </div>
        <div class="col-md-3">
            <label class="form-label">From Date</label>
            <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($from_date) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">To Date</label>
            <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($to_date) ?>">
        </div>
        <div class="col-md-12 text-end">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="full_summary_report.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <!-- Payment Type Summary -->
    <h4 class="mt-4">💳 Payment Type Breakdown</h4>
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <h5>Cash Sale</h5>
                <h3>Rs. <?= number_format($payment_methods['cash'] ?? 0, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <h5>Bank Sale</h5>
                <h3>Rs. <?= number_format($payment_methods['bank'] ?? 0, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <h5>Credit Sale</h5>
                <h3>Rs. <?= number_format($payment_methods['credit'] ?? 0, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <h5>Debit Sale</h5>
                <h3>Rs. <?= number_format($payment_methods['debit'] ?? 0, 2) ?></h3>
            </div>
        </div>
    </div>

    <!-- Grand Total -->
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="grand-total-card">
                <h5>Grand Total of All Payment Types</h5>
                <h3>Rs. <?= number_format($total_all_methods, 2) ?></h3>
            </div>
        </div>
    </div>

    <!-- All Filtered Bills -->
    <h4 class="mt-4">📃 Filtered Bills</h4>
    <div class="table-container">
        <table class="table table-bordered table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Bill ID</th>
                    <th>Customer Name</th>
                    <th>Payment Amount</th>
                    <th>Paid Amount</th>
                    <th>Balance</th>
                    <th>Discount</th>
                    <th>Service Charge</th>
                    <th>Bill Time</th>
                    <th>Payment Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $bills->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['bill_id']) ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td>Rs. <?= number_format($row['payment_amount'] ?? 0, 2) ?></td>
                    <td>Rs. <?= number_format($row['paid_amount'] ?? 0, 2) ?></td>
                    <td>Rs. <?= number_format($row['balance_amount'] ?? 0, 2) ?></td>
                    <td>Rs. <?= number_format($row['discount_amount'] ?? 0, 2) ?></td>
                    <td>Rs. <?= number_format($row['service_charge'] ?? 0, 2) ?></td>
                    <td><?= htmlspecialchars($row['bill_time']) ?></td>
                    <td><?= htmlspecialchars($row['payment_time'] ?? '') ?></td>

                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
