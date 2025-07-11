<?php
require_once "../config.php";

$query = $_GET['query'] ?? '';

if ($query) {
    $sql = "SELECT id, asset_name, enter_date FROM assets WHERE asset_name LIKE ?";
    $stmt = $link->prepare($sql);
    $searchQuery = "%$query%";
    $stmt->bind_param('s', $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    $assets = [];
    while ($row = $result->fetch_assoc()) {
        $assets[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($assets);
} else {
    header('Content-Type: application/json');
    echo json_encode([]);
}