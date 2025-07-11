<?php
include '../../config.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['order_id']) || !isset($data['cart_data'])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$order_id = intval($data['order_id']);
$cart_data = $data['cart_data'];

$sql = "INSERT INTO dine_in_order_items (order_id, item_id, item_name, portion_size, quantity) VALUES (?, ?, ?, ?, ?)";
$stmt = $link->prepare($sql);

foreach ($cart_data as $item) {
    $item_id = $item['itemId'];
    $item_name = $item['name'];
    $portion_size = $item['portion'];
    $quantity = intval($item['quantity']);

    $stmt->bind_param("isssi", $order_id, $item_id, $item_name, $portion_size, $quantity);
    $stmt->execute();
}

echo json_encode(["success" => true, "message" => "Order items inserted successfully"]);

$stmt->close();
$link->close();
?>
