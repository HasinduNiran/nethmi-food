<?php
session_start();
require_once '../config.php';

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

// Access control: Allow only Admin, Manager, or Cashier
if (!in_array($_SESSION['roll'], [2, 3, 5])) { // Roll: Admin=2, Manager=3, Cashier=5
    die("Access Denied.");
}

// Default date range (current date in Sri Lanka timezone)
$current_date = date('Y-m-d');
$start_date = $end_date = $current_date;

// Handle form submission for Cash In/Out, Handover, or Takeover
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_cash_flow'])) {
    $status = $_POST['status'];
    $description = mysqli_real_escape_string($link, $_POST['description']);
    $cash_amount = floatval($_POST['cash_amount']);
    $cashier_name = mysqli_real_escape_string($link, $_POST['cashier_name']);
    $entry_date = date('Y-m-d H:i:s'); // Sri Lanka datetime

    $insert_query = "INSERT INTO cash_balance (status, description, cash_amount, cashier_name, entry_date) 
                     VALUES ('$status', '$description', '$cash_amount', '$cashier_name', '$entry_date')";

    if (mysqli_query($link, $insert_query)) {
        $success_message = "Cash flow entry added successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($link);
    }
}

// Handle date range filter
if (isset($_GET['filter_dates'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

// Fetch records within the selected date range
$cash_query = "SELECT * FROM cash_balance WHERE DATE(entry_date) BETWEEN '$start_date' AND '$end_date' ORDER BY entry_date DESC";
$cash_result = mysqli_query($link, $cash_query);

include '../inc/dashHeader.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Flow Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
        }

        .container {
            margin-top: 20px;
            width:70%;
        }

        .form-control, .btn {
            border-radius: 5px;
        }

        .table th, .table td {
            text-align: center;
        }

        .floating-alert {
            position: fixed;
            bottom: 10px;
            right: 10px;
            z-index: 9999;
            width: 300px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Cash Flow Management</h2>

    <!-- Success/Error Message -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success floating-alert"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="alert alert-danger floating-alert"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Cash Flow Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Add Cash In / Cash Out / Handover / Takeover</div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="Cash In">Cash In</option>
                            <option value="Cash Out">Cash Out</option>
                            <option value="Handover">Handover</option>
                            <option value="Takeover">Takeover</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="cash_amount">Cash Amount</label>
                        <input type="number" step="0.01" name="cash_amount" id="cash_amount" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="cashier_name">Cashier Name</label>
                        <input type="text" name="cashier_name" id="cashier_name" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="description">Description</label>
                        <input type="text" name="description" id="description" class="form-control" required>
                    </div>
                </div>
                <button type="submit" name="submit_cash_flow" class="btn btn-success btn-block">Add Entry</button>
            </form>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">Filter Cash Flow by Date</div>
        <div class="card-body">
            <form method="GET" action="">
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date">Start Date</label>
                        <input type="text" name="start_date" id="start_date" class="form-control" value="<?php echo $start_date; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date">End Date</label>
                        <input type="text" name="end_date" id="end_date" class="form-control" value="<?php echo $end_date; ?>" required>
                    </div>
                </div>
                <button type="submit" name="filter_dates" class="btn btn-primary btn-block">Filter</button>
            </form>
        </div>
    </div>

    <!-- Cash Flow Table -->
    <div class="card">
        <div class="card-header bg-dark text-white">Cash Flow Records</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th>ID</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Cash Amount (Rs.)</th>
                        <th>Cashier Name</th>
                        <th>Entry Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($cash_result) > 0) {
                        while ($row = mysqli_fetch_assoc($cash_result)) {
                            // Format the entry date to show Sri Lanka time properly
                            $formatted_date = date('Y-m-d H:i:s', strtotime($row['entry_date']));
                            
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['status']}</td>
                                    <td>{$row['description']}</td>
                                    <td>" . number_format($row['cash_amount'], 2) . "</td>
                                    <td>{$row['cashier_name']}</td>
                                    <td>{$formatted_date}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No cash flow records found for the selected date range.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Initialize datepickers with Sri Lanka timezone consideration
        $('#start_date, #end_date').datetimepicker({
            format: 'Y-m-d',
            timepicker: false,
            defaultDate: '<?php echo $current_date; ?>', // Set default to current Sri Lanka date
            maxDate: '<?php echo date("Y-m-d", strtotime("+1 year")); ?>', // Allow up to 1 year ahead
            scrollMonth: false,
            scrollTime: false,
            scrollInput: false
        });
    });
</script>
</body>
</html>

<?php include '../inc/dashFooter.php'; ?>