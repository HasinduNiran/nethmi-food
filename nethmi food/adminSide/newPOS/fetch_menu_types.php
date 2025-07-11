<?php
require_once 'db_config.php';

// Fetch food categories from the database
$sql = "SELECT item_type_name FROM menu_item_type ORDER BY item_type_name";
$result = $conn->query($sql);

$categories = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row["item_type_name"];
    }
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($categories);

$conn->close();
?>