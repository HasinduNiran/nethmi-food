<?php
require_once 'db_config.php';

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Start transaction
$conn->begin_transaction();

try {
    // Get current Sri Lanka date and time
    $current_datetime = date('Y-m-d H:i:s');
    
    // Insert or update bill with Sri Lanka timestamp
    $sql = "INSERT INTO bills (bill_id, table_id, bill_time, status, hotel_type, reference_number) 
            VALUES (?, ?, ?, 'active', ?, ?) 
            ON DUPLICATE KEY UPDATE 
            table_id = VALUES(table_id), 
            status = 'active',
            bill_time = VALUES(bill_time)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisis", 
        $data['bill_id'], 
        $data['table_id'],
        $current_datetime,  // Using Sri Lanka datetime instead of NOW()
        $data['hotel_type'],
        $data['reference_number']
    );
    $stmt->execute();
    
    // Update table status to 'occupied'
    $sql = "UPDATE restaurant_tables SET status = 'occupied' WHERE table_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $data['table_id']);
    $stmt->execute();
    
    // Delete existing held items
    $sql = "DELETE FROM held_items WHERE bill_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $data['bill_id']);
    $stmt->execute();
    
    // Insert held items
    foreach ($data['items'] as $item) {
        $sql = "INSERT INTO held_items (bill_id, item_id, quantity, portion_size, product_name, price, free_count) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $portion_size = isset($item['portion_size']) ? $item['portion_size'] : '';
        $stmt->bind_param("isissdi", $data['bill_id'], $item['item_id'], $item['quantity'], $portion_size, $item['name'],  $item['price'],  $item['fc']);
        $stmt->execute();
    }
    
    // Delete existing held payments
    $sql = "DELETE FROM held_bill_payments WHERE bill_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $data['bill_id']);
    $stmt->execute();
    
    // Insert held payments
    foreach ($data['payments'] as $payment) {
        $sql = "INSERT INTO held_bill_payments (bill_id, payment_method, amount, card_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isds", $data['bill_id'], $payment['method'], $payment['amount'], $payment['cardId']);
        $stmt->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        "success" => true,
        "bill_time" => $current_datetime,
        "message" => "Order held successfully"
    ]);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>

<?php
// require_once 'db_config.php';

// // Get JSON data
// $data = json_decode(file_get_contents('php://input'), true);

// // Start transaction
// $conn->begin_transaction();

// try {
//     // Insert or update bill
//     $sql = "INSERT INTO bills (bill_id, table_id, bill_time, status, hotel_type) 
//             VALUES (?, ?, NOW(), 'active', ?) 
//             ON DUPLICATE KEY UPDATE 
//             table_id = VALUES(table_id), 
//             status = 'active'";
    
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("iii", 
//         $data['bill_id'], 
//         $data['table_id'],
//         $data['hotel_type']
//     );
//     $stmt->execute();
    
//     // Update table status to 'occupied'
//     $sql = "UPDATE restaurant_tables SET status = 'occupied' WHERE table_id = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $data['table_id']);
//     $stmt->execute();
    
//     // Delete existing held items
//     $sql = "DELETE FROM held_items WHERE bill_id = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $data['bill_id']);
//     $stmt->execute();
    
//     // Insert held items
//     foreach ($data['items'] as $item) {
//         $sql = "INSERT INTO held_items (bill_id, item_id, quantity) VALUES (?, ?, ?)";
//         $stmt = $conn->prepare($sql);
//         $stmt->bind_param("isi", $data['bill_id'], $item['item_id'], $item['quantity']);
//         $stmt->execute();
//     }
    
//     // Delete existing held payments
//     $sql = "DELETE FROM held_bill_payments WHERE bill_id = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $data['bill_id']);
//     $stmt->execute();
    
//     // Insert held payments
//     foreach ($data['payments'] as $payment) {
//         $sql = "INSERT INTO held_bill_payments (bill_id, payment_method, amount, card_id) VALUES (?, ?, ?, ?)";
//         $stmt = $conn->prepare($sql);
//         $stmt->bind_param("isds", $data['bill_id'], $payment['method'], $payment['amount'], $payment['cardId']);
//         $stmt->execute();
//     }
    
//     // Commit transaction
//     $conn->commit();
    
//     echo json_encode(["success" => true]);
// } catch (Exception $e) {
//     // Rollback transaction on error
//     $conn->rollback();
//     echo json_encode(["success" => false, "message" => $e->getMessage()]);
// }

// $conn->close();
?>
