<?php
require_once "../config.php";

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $stmt = $link->prepare("INSERT INTO subitamelisting (mesuer, iteam_id, qty, menu_id) VALUES (?, ?, ?, ?)");
    foreach ($data as $row) {
        $stmt->bind_param("ssss", $row['mesuer'], $row['iteamId'], $row['qty'], $row['id']);
        $stmt->execute();
    }
    $stmt->close();
    // No echo needed as success message is handled in main file
}

$link->close();
?>