<?php
session_start();
require_once '../config.php';

if ($link->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $link->connect_error]));
}

if (isset($_SESSION['logged_account_id'])) {
    $account_id = intval($_SESSION['logged_account_id']); 
    $sql = "SELECT role FROM staffs WHERE account_id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(["role" => $row['role']]);
    } else {
        echo json_encode(["error" => "No staff record found for this account"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "User not logged in"]);
}

$link->close();
?>