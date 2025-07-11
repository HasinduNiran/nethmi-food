<?php
require_once '../config.php';

session_start();
$user_name = $_SESSION['username'];

if (!$user_name) {
    header("Location: ../unauthorized/unauthorized_access.php");
    exit();
}

$sql = "SELECT supplier_id, supplier_name, telephone, company, credit_balance FROM suppliers";
$stmt = $link->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$suppliers = [];
while ($row = $result->fetch_assoc()) {
    $suppliers[] = $row;
}

echo json_encode($suppliers);
?>