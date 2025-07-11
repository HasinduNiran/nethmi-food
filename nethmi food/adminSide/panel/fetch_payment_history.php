<?php
require_once '../config.php';

if (isset($_GET['customer_id'])) {
    $customerId = mysqli_real_escape_string($link, $_GET['customer_id']);

    // Fetch payment history for the customer
    $paymentQuery = "SELECT * FROM customer_payments WHERE customer_id = '$customerId' AND payment_type = 'advance'";
    $paymentResult = mysqli_query($link, $paymentQuery);

    $paymentHistory = [];
    while ($row = mysqli_fetch_assoc($paymentResult)) {
        $paymentHistory[] = $row;
    }

    echo json_encode($paymentHistory);
}
?>