<?php
session_start();
$user_name = $_SESSION['username'] ?? 'Unknown User';

// Get payment data from URL parameters
$payment_id = $_GET['payment_id'] ?? 'ADV/001'; // Default payment ID
$customer_id = $_GET['customer_id'] ?? '';
$phone_number = $_GET['phone_number'] ?? '';
$payment_amount = $_GET['payment_amount'] ?? 0;
$payment_date = $_GET['payment_date'] ?? date('Y-m-d H:i:s');
$payment_type = $_GET['payment_type'] ?? 'advance';
$notes = $_GET['notes'] ?? '';

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

// Format the payment date
$formatted_date = date('Y-m-d', strtotime($payment_date));
$formatted_time = date('h:iA', strtotime($payment_date));

// Get customer name from URL parameter or database if needed
$customer_name = $_GET['customer_name'] ?? 'Customer';

// If name wasn't passed and we have a customer ID, try to get it from database
if (empty($customer_name) && !empty($customer_id) && file_exists("./db_config.php")) {
    include "./db_config.php";
    
    $stmt = $conn->prepare("SELECT name FROM customers WHERE customer_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $customer_name = $row['name'];
        }
        $stmt->close();
    }
}

function formatAmount($amount) {
    return number_format($amount, 2, '.', '');
}

header('Content-Type: text/html');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Advance Payment Receipt</title>
    <style>
        * {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        body {
            width: 80mm;
            margin: 0 auto;
            padding: 10px 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #000; /* Full line for header separation */
        }
        .header img {
            max-width: 100%;
            height: auto;
        }
        .highlight {
            background-color: #000;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            padding: 4px 0;
            text-transform: uppercase;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        th, td {
            padding: 3px;
            font-size: 11px;
        }
        th {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            background-color: #f0f0f0;
            border-bottom: 1px solid #000; /* Full line for table header */
        }
        .item-row td {
            border-bottom: 1px dashed #ccc; /* Dashed line for item separation */
        }
        .customer-info {
            margin: 10px 0;
            text-align: left;
            border: 1px solid #ccc;
            padding: 5px;
            background-color: #f9f9f9;
        }
        .customer-info p {
            margin: 3px 0;
            text-align: left;
        }
        .payment-info {
            margin: 10px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
        }
        .payment-row td {
            padding: 5px;
            text-align: left;
        }
        .payment-row td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .total-box {
            text-align: center;
            margin: 10px auto;
            border: 2px solid #000; /* Full line for total box */
            padding: 8px 15px;
            font-size: 18px;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
            border-top: 1px solid #000; /* Full line for footer separation */
            padding-top: 5px;
        }
        .footer p {
            margin: 3px 0;
        }
        .print-btn {
            display: block;
            margin: 10px auto;
            padding: 5px 15px;
            background-color: #1a5f7a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        @media print{
            .print-btn {
                display: none !important;
            }
        }
    </style>
    <script>
        // Automatically trigger print dialog when the page loads
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        };
    </script>
</head>
<body>
    <div class="header">
        <img src="../images/logo-massimo.png" alt="Restaurant Logo">
        <div class="highlight">Advance Payment Receipt</div>
    </div>

    <table style="margin-bottom: 8px;">
        <tr>
            <td style="text-align: left; width: 50%;">Date: <?php echo $formatted_date; ?></td>
            <td style="text-align: right; width: 50%;">Time: <?php echo $formatted_time; ?></td>
        </tr>
        <tr>
            <td style="text-align: left;">Receipt No: <?php echo htmlspecialchars($payment_id); ?></td>
            <td style="text-align: right;">User: <?php echo htmlspecialchars($user_name); ?></td>
        </tr>
    </table>

    <div class="customer-info">
        <p><strong>Customer ID:</strong> <?php echo htmlspecialchars($customer_id); ?></p>
        <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($phone_number); ?></p>
    </div>

    <table class="payment-info">
        <tr class="payment-row">
            <td><strong>Payment Type:</strong></td>
            <td><?php echo ucfirst(htmlspecialchars($payment_type)); ?> Payment</td>
        </tr>
        <tr class="payment-row">
            <td><strong>Payment Date:</strong></td>
            <td><?php echo $formatted_date . ' ' . $formatted_time; ?></td>
        </tr>
        <?php if (!empty($notes)): ?>
        <tr class="payment-row">
            <td><strong>Notes:</strong></td>
            <td><?php echo htmlspecialchars($notes); ?></td>
        </tr>
        <?php endif; ?>
        <tr class="payment-row">
            <td><strong>Amount:</strong></td>
            <td><?php echo formatAmount($payment_amount); ?></td>
        </tr>
    </table>

    <div class="total-box">
        <span>Total Amount<br><b><?php echo formatAmount($payment_amount); ?></b></span>
    </div>

    <div class="footer">
        <p>Thank You for Your Payment!</p>
        <p><strong>Vintage Resturant & Cafe</strong></p>
    </div>

    <button class="print-btn" onclick="printBill()">Print Receipt</button>

    <script>
        function printBill() {
            window.print();
        }
    </script>
</body>
</html>