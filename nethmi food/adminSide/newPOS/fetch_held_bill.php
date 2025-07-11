<?php
require_once 'db_config.php';
$billId = $_GET['bill_id'];

// Get bill details
$sql = "SELECT * FROM bills WHERE bill_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $billId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $bill = $result->fetch_assoc();
    
    $sql = "SELECT 
                id,
                bill_id,
                item_id,
                product_name,
                price,
                quantity,
                status,
                portion_size,
                created_at,
                free_count
            FROM held_items
            WHERE bill_id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $billId);
    $stmt->execute();
    $itemsResult = $stmt->get_result();
    
    $items = [];
    while ($row = $itemsResult->fetch_assoc()) {
        $items[] = $row;
    }
    
    // Get held payments
    $sql = "SELECT * FROM held_bill_payments WHERE bill_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $billId);
    $stmt->execute();
    $paymentsResult = $stmt->get_result();
    
    $payments = [];
    while ($row = $paymentsResult->fetch_assoc()) {
        $payments[] = $row;
    }
    
    $response = [
        'bill_id' => $bill['bill_id'],
        'hotel_type' => $bill['hotel_type'],
        'table_id' => $bill['table_id'],
        'reference_number' => $bill['reference_number'],
        'items' => $items,
        'payments' => $payments
    ];
    
    echo json_encode($response);
} else {
    echo json_encode(["error" => "Bill not found"]);
}

$conn->close();
?>