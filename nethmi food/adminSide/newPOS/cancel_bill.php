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
if (!$data['bill_id'] || !$data['reason'] || !$logged_account_id) {
    echo json_encode(["success" => false, "message" => "Missing required data"]);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Get current date and time for Sri Lanka
    $current_date = date('Y-m-d');
    $current_datetime = date('Y-m-d H:i:s');
    
    // Check if a bill with this ID already exists in the bills table
    $check_sql = "SELECT bill_id, status FROM bills WHERE bill_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $data['bill_id']);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    // Variable to track if we need to insert or update bills
    $bill_exists = false;
    $bill_status = null;
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $bill_exists = true;
        $bill_status = $row['status'];
    }
    
    // Get customer name from customer-suggestions
    $customer_suggestions = isset($data['customer_name']) ? $data['customer_name'] : 'Cancelled Bill';
    
    // Insert into cancelled_bills table with Sri Lanka time
    $cancelled_sql = "INSERT INTO cancelled_bills (bill_id, cancelled_date, reason, cancelled_by, bill_amount) 
                     VALUES (?, ?, ?, ?, ?)";
    $cancelled_stmt = $conn->prepare($cancelled_sql);
    $cancelled_stmt->bind_param("issid", 
        $data['bill_id'], 
        $current_date, 
        $data['reason'], 
        $logged_account_id,
        $data['payment_amount']
    );
    $cancelled_stmt->execute();
    
    if ($bill_exists) {
        // Update existing bill record to cancelled status
        $update_sql = "UPDATE bills SET 
                      status = 'cancelled',
                      payment_amount = ?
                      WHERE bill_id = ?";
        
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("di", 
            $data['payment_amount'],
            $data['bill_id']
        );
        $update_stmt->execute();
        
        // If bill was active, we need to delete existing items and re-insert new ones
        if ($bill_status == 'active') {
            $delete_items_sql = "DELETE FROM bill_items WHERE bill_id = ?";
            $delete_stmt = $conn->prepare($delete_items_sql);
            $delete_stmt->bind_param("i", $data['bill_id']);
            $delete_stmt->execute();
        }
    } else {
        // Insert new bill record with cancelled status using Sri Lanka time
        $insert_sql = "INSERT INTO bills (
                      bill_id, 
                      staff_id, 
                      table_id, 
                      bill_time, 
                      status, 
                      total_before_discount, 
                      discount_amount, 
                      payment_amount,
                      customer_name,
                      hotel_type
                    ) VALUES (?, ?, ?, ?, 'cancelled', ?, ?, ?, ?, ?)";
        
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iiisdddsi", 
            $data['bill_id'], 
            $logged_account_id,
            $data['table_id'],
            $current_datetime,  // Using Sri Lanka datetime instead of NOW()
            $data['total_before_discount'],
            $data['discount_amount'],
            $data['payment_amount'],
            $customer_suggestions,
            $data['hotel_type']
        );
        $insert_stmt->execute();
    }
    
    // Insert bill items if it was a new bill or an active bill
    if (!$bill_exists || $bill_status == 'active') {
        foreach ($data['items'] as $item) {
            $items_sql = "INSERT INTO bill_items (bill_id, item_id, quantity, product_name, price) VALUES (?, ?, ?, ?, ?)";
            $items_stmt = $conn->prepare($items_sql);
            $items_stmt->bind_param("isisd", 
                $data['bill_id'], 
                $item['item_id'], 
                $item['quantity'],
                $item['name'],
                $item['price']
            );
            $items_stmt->execute();
        }
    }
    
    // Update table status to 'available' when bill is cancelled
    if (isset($data['table_id']) && $data['table_id']) {
        $update_table_sql = "UPDATE restaurant_tables SET status = 'available' WHERE table_id = ?";
        $update_table_stmt = $conn->prepare($update_table_sql);
        $update_table_stmt->bind_param("i", $data['table_id']);
        $update_table_stmt->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>