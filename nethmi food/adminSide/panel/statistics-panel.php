<?php
session_start(); // Ensure session is started
require_once '../posBackend/checkIfLoggedIn.php';
ob_start(); // Start output buffering

include '../inc/dashHeader.php'; 
require_once '../config.php';

// Initialize default date and time range to today
$startDateTime = isset($_GET['start_datetime']) ? $_GET['start_datetime'] : date('Y-m-d\TH:i');
$endDateTime = isset($_GET['end_datetime']) ? $_GET['end_datetime'] : date('Y-m-d\TH:i', strtotime('+1 day'));

// Handle Excel export
if (isset($_GET['export_excel'])) {
    // Clear the buffer
    if (ob_get_length()) {
        ob_end_clean();
    }

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=statistics_{$_GET['start_datetime']}_to_{$_GET['end_datetime']}.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // SQL queries for Excel export
    $startDateTime = mysqli_real_escape_string($link, $_GET['start_datetime']);
    $endDateTime = mysqli_real_escape_string($link, $_GET['end_datetime']);

    // Fetch all details
    $totalRevenueQuery = "SELECT SUM(payment_amount) AS total_revenue 
                          FROM bills 
                          WHERE bill_time BETWEEN '$startDateTime' AND '$endDateTime'";
    $totalRevenueResult = mysqli_query($link, $totalRevenueQuery);
    $totalRevenue = mysqli_fetch_assoc($totalRevenueResult)['total_revenue'] ?? 0;

    $cashInQuery = "SELECT SUM(cash_amount) AS total_cash_in 
                    FROM cash_balance 
                    WHERE status = 'Cash In' AND entry_date BETWEEN '$startDateTime' AND '$endDateTime'";
    $cashOutQuery = "SELECT SUM(cash_amount) AS total_cash_out 
                     FROM cash_balance 
                     WHERE status = 'Cash Out' AND entry_date BETWEEN '$startDateTime' AND '$endDateTime'";

    $cashInResult = mysqli_query($link, $cashInQuery);
    $cashOutResult = mysqli_query($link, $cashOutQuery);

    $totalCashIn = mysqli_fetch_assoc($cashInResult)['total_cash_in'] ?? 0;
    $totalCashOut = mysqli_fetch_assoc($cashOutResult)['total_cash_out'] ?? 0;

    $netProfit = $totalRevenue + $totalCashIn - $totalCashOut;

    $cashFlowDetailsQuery = "SELECT * FROM cash_balance 
                             WHERE entry_date BETWEEN '$startDateTime' AND '$endDateTime' 
                             ORDER BY entry_date DESC";
    $cashFlowDetailsResult = mysqli_query($link, $cashFlowDetailsQuery);

    $billsDetailsQuery = "SELECT bill_id, table_id, payment_method, payment_amount, bill_time 
                          FROM bills 
                          WHERE bill_time BETWEEN '$startDateTime' AND '$endDateTime' 
                          ORDER BY bill_time DESC";
    $billsDetailsResult = mysqli_query($link, $billsDetailsQuery);

    // Generate Excel content
    echo "<table border='1'>";
    echo "<tr><th>Metric</th><th>Amount (Rs.)</th></tr>";
    echo "<tr><td>Total Revenue</td><td>" . number_format($totalRevenue, 2) . "</td></tr>";
    echo "<tr><td>Total Cash In</td><td>" . number_format($totalCashIn, 2) . "</td></tr>";
    echo "<tr><td>Total Cash Out</td><td>" . number_format($totalCashOut, 2) . "</td></tr>";
    echo "<tr><td>Net Profit</td><td>" . number_format($netProfit, 2) . "</td></tr>";
    echo "</table>";

    // Cash Flow Details
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Status</th><th>Description</th><th>Amount</th><th>Entry Date</th></tr>";
    while ($row = mysqli_fetch_assoc($cashFlowDetailsResult)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['status']}</td><td>{$row['description']}</td><td>" . number_format($row['cash_amount'], 2) . "</td><td>{$row['entry_date']}</td></tr>";
    }
    echo "</table>";

    // Bills Details
    echo "<table border='1'>";
    echo "<tr><th>Bill ID</th><th>Table ID</th><th>Payment Method</th><th>Payment Amount</th><th>Bill Time</th></tr>";
    while ($row = mysqli_fetch_assoc($billsDetailsResult)) {
        echo "<tr><td>{$row['bill_id']}</td><td>{$row['table_id']}</td><td>{$row['payment_method']}</td><td>" . number_format($row['payment_amount'], 2) . "</td><td>{$row['bill_time']}</td></tr>";
    }
    echo "</table>";

    exit;
}

// Fetch total revenue between dates
$totalRevenueQuery = "SELECT SUM(payment_amount) AS total_revenue 
                      FROM bills 
                      WHERE bill_time BETWEEN '$startDateTime' AND '$endDateTime'";
$totalRevenueResult = mysqli_query($link, $totalRevenueQuery);
$totalRevenueRow = mysqli_fetch_assoc($totalRevenueResult);
$totalRevenue = $totalRevenueRow['total_revenue'] ?? 0;

// Fetch cash in and cash out details between dates
$cashInQuery = "SELECT SUM(cash_amount) AS total_cash_in 
                FROM cash_balance 
                WHERE status = 'Cash In' AND entry_date BETWEEN '$startDateTime' AND '$endDateTime'";
$cashOutQuery = "SELECT SUM(cash_amount) AS total_cash_out 
                 FROM cash_balance 
                 WHERE status = 'Cash Out' AND entry_date BETWEEN '$startDateTime' AND '$endDateTime'";

$cashInResult = mysqli_query($link, $cashInQuery);
$cashOutResult = mysqli_query($link, $cashOutQuery);

$totalCashIn = mysqli_fetch_assoc($cashInResult)['total_cash_in'] ?? 0;
$totalCashOut = mysqli_fetch_assoc($cashOutResult)['total_cash_out'] ?? 0;

// Calculate net profit
$netProfit = $totalRevenue + $totalCashIn - $totalCashOut;

// Fetch cash flow details
$cashFlowDetailsQuery = "SELECT * FROM cash_balance 
                         WHERE entry_date BETWEEN '$startDateTime' AND '$endDateTime' 
                         ORDER BY entry_date DESC";
$cashFlowDetailsResult = mysqli_query($link, $cashFlowDetailsQuery);

// Fetch bills details
$billsDetailsQuery = "SELECT bill_id, table_id, payment_method, payment_amount, bill_time 
                      FROM bills 
                      WHERE bill_time BETWEEN '$startDateTime' AND '$endDateTime' 
                      ORDER BY bill_time DESC";
$billsDetailsResult = mysqli_query($link, $billsDetailsQuery);
?>

<div class="container mt-5" style="max-width: 900px;">
    <h2 class="text-center">Statistics Panel</h2>

    <!-- Date and Time Range Form -->
    <form method="GET" action="" class="form-inline justify-content-center mb-4">
        <label for="start_datetime" class="mr-2">Start Date and Time:</label>
        <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control mr-3" value="<?php echo $startDateTime; ?>" required>

        <label for="end_datetime" class="mr-2">End Date and Time:</label>
        <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control mr-3" value="<?php echo $endDateTime; ?>" required>

        <button type="submit" class="btn btn-primary">Fetch Data</button>
        <a href="?export_excel=1&start_datetime=<?php echo $startDateTime; ?>&end_datetime=<?php echo $endDateTime; ?>" class="btn btn-success ml-3">Export to Excel</a>
    </form>

    <!-- Aggregated Summary Table -->
    <table class="table table-bordered text-center table-sm">
        <thead class="thead-dark">
            <tr>
                <th>Metric</th>
                <th>Amount (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Revenue</td>
                <td><?php echo number_format($totalRevenue, 2); ?></td>
            </tr>
            <tr>
                <td>Total Cash In</td>
                <td><?php echo number_format($totalCashIn, 2); ?></td>
            </tr>
            <tr>
                <td>Total Cash Out</td>
                <td><?php echo number_format($totalCashOut, 2); ?></td>
            </tr>
            <tr>
                <td>Net Profit</td>
                <td><?php echo number_format($netProfit, 2); ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Cash Flow Details Table -->
    <h4 class="text-center">Cash Flow Details</h4>
    <table class="table table-bordered table-sm text-center">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Status</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Entry Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($cashFlowDetailsResult && mysqli_num_rows($cashFlowDetailsResult) > 0) {
                while ($row = mysqli_fetch_assoc($cashFlowDetailsResult)) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['description']}</td>
                            <td>" . number_format($row['cash_amount'], 2) . "</td>
                            <td>{$row['entry_date']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No Cash Flow Details Found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Bills Details Table -->
    <h4 class="text-center">Bills Details</h4>
    <table class="table table-bordered table-sm text-center">
        <thead class="thead-dark">
            <tr>
                <th>Bill ID</th>
                <th>Table ID</th>
                <th>Payment Method</th>
                <th>Payment Amount</th>
                <th>Bill Time</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($billsDetailsResult && mysqli_num_rows($billsDetailsResult) > 0) {
                while ($row = mysqli_fetch_assoc($billsDetailsResult)) {
                    echo "<tr>
                            <td>{$row['bill_id']}</td>
                            <td>{$row['table_id']}</td>
                            <td>{$row['payment_method']}</td>
                            <td>" . number_format($row['payment_amount'], 2) . "</td>
                            <td>{$row['bill_time']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No Bills Details Found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../inc/dashFooter.php'; ?>
