<?php
require_once '../config.php';
// Check connection
if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}

$query = "SELECT DISTINCT category FROM products";
$result = $link->query($query);
$categories = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

header('Content-Type: application/json');
echo json_encode($categories);

$link->close();
?>