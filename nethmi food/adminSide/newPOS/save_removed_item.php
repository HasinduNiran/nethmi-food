<?php
session_start();
require_once 'db_config.php';

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Get logged_account_id from session
$logged_account_id = isset($_SESSION['logged_account_id']) ? $_SESSION['logged_account_id'] : null;

// Validate required data
if (!$data['bill_id'] || !$data['item_id'] || !$data['reason']) {
    echo json_encode(["success" => false, "message" => "Missing required data"]);
    exit;
}

try {
    // Get current Sri Lanka date and time
    $current_datetime = date('Y-m-d H:i:s');
    $current_date = date('Y-m-d');
    
    // Insert into removed_bill_items table with Sri Lanka timestamp
    $sql = "INSERT INTO removed_bill_items (
            bill_id, 
            item_name, 
            item_id, 
            unit_price, 
            quantity, 
            total_price, 
            removed_date,
            removed_by, 
            reason
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssdidsss", 
        $data['bill_id'],
        $data['item_name'],
        $data['item_id'],
        $data['unit_price'],
        $data['quantity'],
        $data['total_price'],
        $current_datetime,  // Using Sri Lanka datetime instead of CURRENT_DATE()
        $logged_account_id,
        $data['reason']
    );
    
    $stmt->execute();
    
    // Check if the item was inserted successfully
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "removed_date" => $current_datetime,
            "business_date" => $current_date
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to save removed item"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>