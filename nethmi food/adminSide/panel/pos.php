<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['roll'])) {
    die('Access Denied');
}

// Redirect to kitchen panel if the role is 4 (Chef)
if ($_SESSION['roll'] == 4) {
    echo "<script>window.location.href = '../panel/kitchen-panel.php';</script>";
    exit;
}

// Check for opening cash balance popup logic
$show_popup = false;
if ($_SESSION['roll'] == 2 || $_SESSION['roll'] == 3 || $_SESSION['roll'] == 5) { // Manager, Admin, Cashier
    $today = date('Y-m-d');
    $check_balance_query = "SELECT * FROM cash_balance WHERE DATE(entry_date) = '$today'";
    $result = mysqli_query($link, $check_balance_query);
    if (mysqli_num_rows($result) === 0) {
        $show_popup = true; // Show popup if no opening cash is set for today
    }
}

// Handle form submission for opening cash balance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_cash_balance'])) {
    $cash_amount = floatval($_POST['cash_amount']);
    $status = $_POST['status'];
    $description = mysqli_real_escape_string($link, $_POST['description']);
    $entry_date = date('Y-m-d H:i:s');

    $insert_query = "INSERT INTO cash_balance (status, description, cash_amount, entry_date) 
                     VALUES ('$status', '$description', '$cash_amount', '$entry_date')";

    if (mysqli_query($link, $insert_query)) {
        $success_message = "Opening cash balance added successfully!";
        $show_popup = false; // Hide popup after successful submission
    } else {
        $error_message = "Error: " . mysqli_error($link);
    }
}

include '../inc/dashHeader.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }

        .container {
            text-align: center;
            margin-top: 6rem;
            transform: translateX(35px); /* Adjust this value as needed to align the container */
        }

        /* Logo Styling */
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }

        /* H2 Heading Styling */
        h2 {
            font-size: 2.5rem;
            font-family: 'Georgia', serif;
            font-weight: bold;
            text-transform: uppercase;
            color: #4A90E2; /* Blue shade */
            margin-bottom: 30px;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Category Button Styling */
        .category-button {
            color: white;
            border: none;
            width: 100%;
            height: 100px;
            margin-bottom: 15px;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: bold;
            text-transform: uppercase;
            background: linear-gradient(90deg, red, orange, yellow, green, blue, indigo, violet);
            background-size: 400% 400%;
            animation: gradientAnimation 5s infinite ease-in-out;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .category-button:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        /* Gradient Animation */
        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }
            100% {
                background-position: 20% 5%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        /* Popup styling */
        .modal-dialog {
            max-width: 400px;
            margin: 1.75rem auto;
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

<!-- Opening Cash Balance Modal -->
<?php if ($show_popup): ?>
<div class="modal fade" id="cashBalanceModal" tabindex="-1" role="dialog" aria-labelledby="cashBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cashBalanceModalLabel">Opening Cash Balance</h5>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cash_amount">Cash Amount</label>
                        <input type="number" step="0.01" name="cash_amount" id="cash_amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="Cash In">Cash In</option>
                            <option value="Cash Out">Cash Out</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="submit_cash_balance" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="container">
    <!-- Logo Section -->
    <img src="jcat.png" alt="Logo" class="logo">
    
    <!-- Heading Section -->
    <h2>Select Your Stations</h2>
    
    <div class="row">
        <?php
        $queryCategory = "SELECT * FROM `holetype`";
        $resultCategory = $link->query($queryCategory);
        if ($resultCategory) {
            while ($row = $resultCategory->fetch_assoc()) {
        ?>
                <div class="col-md-6 col-sm-12 mb-3">
                    <a href="../posBackend/posTable.php?id=<?php echo $row['id']; ?>" class="btn category-button">
                    Vintage Resturant & Cafe<br><?php echo htmlspecialchars($row['name']); ?>
                    </a>
                </div>
        <?php
            }
        }
        ?>
    </div>
</div>

<!-- Floating Alerts -->
<?php if (isset($success_message)): ?>
    <div class="alert alert-success floating-alert"><?php echo $success_message; ?></div>
<?php elseif (isset($error_message)): ?>
    <div class="alert alert-danger floating-alert"><?php echo $error_message; ?></div>
<?php endif; ?>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        <?php if ($show_popup): ?>
            $('#cashBalanceModal').modal('show');
        <?php endif; ?>
    });
</script>

</body>
</html>
<?php include '../inc/dashFooter.php'; ?>
