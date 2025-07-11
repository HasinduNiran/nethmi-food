
<?php
include '../config.php';

$bill_id = $_GET['bill_id'] ?? null;
$item_id = $_GET['item_id'] ?? null;
$quantity = $_GET['quantity'] ?? null;
$type = $_GET['type'] ?? null;

if (!$bill_id || !$item_id || !$quantity || !$type) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$query = "INSERT INTO held_items (bill_id, item_id, quantity, type) VALUES ('$bill_id', '$item_id', '$quantity', '$type')";
$result = mysqli_query($link, $query);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($link)]);
}
?>
