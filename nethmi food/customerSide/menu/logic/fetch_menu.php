<?php 
include '../../config.php';

$sql = "SELECT * FROM menu";
$result = $link->query($sql);
$menu = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menu[] = $row;
    }
}
$link->close();


header("Content-Type: application/json");
echo json_encode($menu);
?>