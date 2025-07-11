<?php
include '../../config.php';

$sql = "SELECT COUNT(*) AS pending_count FROM dine_in_orders WHERE state = 'pending'";
$result = $link->query($sql);

$response = ["pending_count" => 0];

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response["pending_count"] = $row["pending_count"];
}

$link->close();

header("Content-Type: application/json");
echo json_encode($response);
?>
