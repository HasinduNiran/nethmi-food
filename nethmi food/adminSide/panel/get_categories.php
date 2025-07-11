<?php
require_once "../config.php";

$sql = "SELECT DISTINCT category FROM inventory";
$result = $link->query($sql);
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row['category'];
}
echo json_encode($categories);
?>