<?php
session_start();
require_once '../config.php';

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

// Start output buffering to capture any unintended output
ob_start();
// Set header for JSON response
header('Content-Type: application/json');
// Disable error display to prevent output interference
ini_set('display_errors', 0);
error_reporting(E_ALL);
$response = []; // Initialize response array

try {
    // Get POST data
    $customer_id = $_POST['customer_id'] ?? null;
    $amount = $_POST['amount'] ?? null;
    $description = $_POST['description'] ?? null;
    
    if (!$customer_id || !$amount) {
        throw new Exception("Customer ID and Amount are required.");
    }
    
    $amount = floatval($amount);
    if ($amount <= 0) {
        throw new Exception("Amount must be greater than zero.");
    }
    
    // Get current Sri Lanka date and time
    $current_datetime = date('Y-m-d H:i:s');
    
    // Start transaction
    $link->begin_transaction();
    
    // Insert into customer_credit_payments with Sri Lanka timestamp
    $sql = "INSERT INTO customer_credit_payments (customer_id, amount, payment_date, description) VALUES (?, ?, ?, ?)";
    $stmt = $link->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $link->error);
    }
    $stmt->bind_param("idss", $customer_id, $amount, $current_datetime, $description);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    // Update credit_balance in customers table (subtract the payment amount)
    $update_sql = "UPDATE customers SET credit_balance = credit_balance - ? WHERE customer_id = ?";
    $update_stmt = $link->prepare($update_sql);
    if (!$update_stmt) {
        throw new Exception("Prepare failed: " . $link->error);
    }
    $update_stmt->bind_param("di", $amount, $customer_id);
    if (!$update_stmt->execute()) {
        throw new Exception("Execute failed: " . $update_stmt->error);
    }
    
    // Check if credit_balance would go negative (optional)
    $check_sql = "SELECT credit_balance FROM customers WHERE customer_id = ?";
    $check_stmt = $link->prepare($check_sql);
    $check_stmt->bind_param("i", $customer_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['credit_balance'] < 0) {
        throw new Exception("Payment exceeds credit balance.");
    }
    
    // Commit transaction
    $link->commit();
    
    $response = [
        "success" => true, 
        "message" => "Credit payment recorded successfully",
        "payment_date" => $current_datetime,
        "remaining_balance" => $row['credit_balance']
    ];
    
} catch (Exception $e) {
    $link->rollback();
    $response = ["success" => false, "message" => $e->getMessage()];
}

// Clear buffer and output JSON
ob_end_clean();
echo json_encode($response);
$link->close();
exit; // Ensure no further output
?>