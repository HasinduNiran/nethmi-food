<?php
require_once 'db_config.php';

// Get hotel type from GET request
$hotel_type = isset($_GET['hotel_type']) ? $conn->real_escape_string($_GET['hotel_type']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

$menu_query = $conn->prepare("SELECT * FROM menu WHERE item_type = ?");
$menu_query->bind_param('s', $category);
$menu_query->execute();
$menu_result = $menu_query->get_result();

$menu_items = [];
if ($menu_result->num_rows > 0) {
    while ($row = $menu_result->fetch_assoc()) {
        $menu_items[] = $row;
    }
}
echo json_encode($menu_items);
$conn->close();
?>