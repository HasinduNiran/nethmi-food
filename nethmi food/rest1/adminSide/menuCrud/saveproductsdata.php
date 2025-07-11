<?php

require_once "../config.php";

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $stmt = $link->prepare("INSERT INTO product_listing ( mesuer, iteam_id, qty, menu_id) VALUES (?, ?, ?, ?)");
    foreach ($data as $row) {
        $stmt->bind_param("siss", $row['mesuer'], $row['iteamId'], $row['qty'], $row['id']);
        $stmt->execute();
    }
    $stmt->close();
    echo "Data saved successfully";
} else {
    echo "No data to save";
}

$link->close();
?>
