<?php
include '../../config.php';

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['table_id']) || !isset($data['cart_data'])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$table_id = $data['table_id'];
$item_count = count($data['cart_data']); 

// Get current Sri Lanka date and time
$current_datetime = date('Y-m-d H:i:s');

// Insert with Sri Lanka timestamp
$sql = "INSERT INTO dine_in_orders (table_id, item_count, date) VALUES (?, ?, ?)";
$stmt = $link->prepare($sql);
$stmt->bind_param("sis", $table_id, $item_count, $current_datetime);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    echo json_encode([
        "success" => true, 
        "order_id" => $order_id,
        "order_date" => $current_datetime
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create order"]);
}

$stmt->close();
$link->close();
?>