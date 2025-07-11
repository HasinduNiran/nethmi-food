<?php
// add_advance_payment.php
require_once '../config.php';

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $customer_id = mysqli_real_escape_string($link, $_POST['customer_id']);
    $phone_number = mysqli_real_escape_string($link, $_POST['phone_number']);
    $payment_amount = mysqli_real_escape_string($link, $_POST['payment_amount']);
    $payment_type = mysqli_real_escape_string($link, $_POST['payment_type']);
    $notes = mysqli_real_escape_string($link, $_POST['notes']);
    
    // Get current Sri Lanka date and time
    $payment_timestamp = date('Y-m-d H:i:s');
    
    // Get customer name for receipt
    $customer_name = "";
    $customerQuery = "SELECT name FROM customers WHERE customer_id = '$customer_id'";
    $customerResult = mysqli_query($link, $customerQuery);
    if ($customerResult && mysqli_num_rows($customerResult) > 0) {
        $customerRow = mysqli_fetch_assoc($customerResult);
        $customer_name = $customerRow['name'];
    }
    
    // Start a transaction to ensure atomicity
    mysqli_begin_transaction($link);
    try {
        // Insert payment into the database with Sri Lanka timestamp
        $insertQuery = "INSERT INTO customer_payments (customer_id, payment_amount, payment_type, notes, payment_date) 
                        VALUES ('$customer_id', '$payment_amount', '$payment_type', '$notes', '$payment_timestamp')";
        if (!mysqli_query($link, $insertQuery)) {
            throw new Exception('Failed to record payment.');
        }
        
        // Get the payment ID
        $payment_id = mysqli_insert_id($link);
        
        // Format payment ID with prefix
        $formatted_payment_id = 'ADV/' . str_pad($payment_id, 6, '0', STR_PAD_LEFT);
        
        // If the payment type is 'advance', update the advance_payment in the customers table
        if ($payment_type === 'advance') {
            // Retrieve the current advance_payment
            $selectQuery = "SELECT advance_payment FROM customers WHERE customer_id = '$customer_id'";
            $result = mysqli_query($link, $selectQuery);
            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $current_advance_payment = $row['advance_payment'] ?? 0;
                // Calculate the new advance_payment
                $new_advance_payment = $current_advance_payment + $payment_amount;
                // Update the advance_payment in the customers table
                $updateQuery = "UPDATE customers SET advance_payment = '$new_advance_payment' WHERE customer_id = '$customer_id'";
                if (!mysqli_query($link, $updateQuery)) {
                    throw new Exception('Failed to update advance payment.');
                }
            } else {
                throw new Exception('Customer not found.');
            }
        }
        
        // Commit the transaction
        mysqli_commit($link);
        
        // Return success response with payment details for receipt
        echo json_encode([
            'status' => 'success', 
            'message' => 'Payment recorded and advance payment updated successfully!',
            'payment_id' => $formatted_payment_id,
            'customer_id' => $customer_id,
            'customer_name' => $customer_name,
            'phone_number' => $phone_number,
            'payment_amount' => $payment_amount,
            'payment_date' => $payment_timestamp,
            'payment_type' => $payment_type,
            'notes' => $notes
        ]);
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        mysqli_rollback($link);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
?>

<?php
// // add_advance_payment.php

// require_once '../config.php';

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Retrieve and sanitize form data
//     $customer_id = mysqli_real_escape_string($link, $_POST['customer_id']);
//     $payment_amount = mysqli_real_escape_string($link, $_POST['payment_amount']);
//     $payment_type = mysqli_real_escape_string($link, $_POST['payment_type']);
//     $notes = mysqli_real_escape_string($link, $_POST['notes']);

//     // Start a transaction to ensure atomicity
//     mysqli_begin_transaction($link);

//     try {
//         // Insert payment into the database
//         $insertQuery = "INSERT INTO customer_payments (customer_id, payment_amount, payment_type, notes, payment_date) 
//                         VALUES ('$customer_id', '$payment_amount', '$payment_type', '$notes', NOW())";

//         if (!mysqli_query($link, $insertQuery)) {
//             throw new Exception('Failed to record payment.');
//         }

//         // If the payment type is 'advance', update the advance_payment in the customers table
//         if ($payment_type === 'advance') {
//             // Retrieve the current advance_payment
//             $selectQuery = "SELECT advance_payment FROM customers WHERE customer_id = '$customer_id'";
//             $result = mysqli_query($link, $selectQuery);

//             if ($result && mysqli_num_rows($result) > 0) {
//                 $row = mysqli_fetch_assoc($result);
//                 $current_advance_payment = $row['advance_payment'] ?? 0;

//                 // Calculate the new advance_payment
//                 $new_advance_payment = $current_advance_payment + $payment_amount;

//                 // Update the advance_payment in the customers table
//                 $updateQuery = "UPDATE customers SET advance_payment = '$new_advance_payment' WHERE customer_id = '$customer_id'";
//                 if (!mysqli_query($link, $updateQuery)) {
//                     throw new Exception('Failed to update advance payment.');
//                 }
//             } else {
//                 throw new Exception('Customer not found.');
//             }
//         }

//         // Commit the transaction
//         mysqli_commit($link);
//         echo json_encode(['status' => 'success', 'message' => 'Payment recorded and advance payment updated successfully!']);
//     } catch (Exception $e) {
//         // Rollback the transaction in case of error
//         mysqli_rollback($link);
//         echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
//     }

//     exit;
// }
?>