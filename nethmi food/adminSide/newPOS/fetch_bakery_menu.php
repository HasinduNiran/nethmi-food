<?php
require_once 'db_config.php';

$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

if (!empty($category)) {
    $query = $conn->prepare("SELECT * FROM bakery_menu_stocks WHERE item_type = ?");
    $query->bind_param('s', $category);
} else {
    $query = $conn->prepare("SELECT * FROM bakery_menu_stocks");
}

$query->execute();
$result = $query->get_result();

$menu_items = [];
while ($row = $result->fetch_assoc()) {
    $menu_items[] = $row;
}

echo json_encode($menu_items);

$query->close();
$conn->close();
?>
