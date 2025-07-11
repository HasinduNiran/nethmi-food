<?php
require_once "../config.php";

$q = isset($_GET['q']) ? $_GET['q'] : '';
$sql = "SELECT ii.itemid, ii.itemname, ii.category FROM inventory_items ii WHERE ii.itemname LIKE ? OR ii.itemid LIKE ? LIMIT 10";
$stmt = $link->prepare($sql);
$searchTerm = "%$q%";
$stmt->bind_param('ss', $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

header('Content-Type: application/json');
echo json_encode($items);
?>