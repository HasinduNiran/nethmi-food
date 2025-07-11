<?php
require_once 'db_config.php';

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

if (isset($_GET['customer_id'])) {
    $customer_id = intval($_GET['customer_id']); 
    $sql = "SELECT customer_id, payment_amount FROM customer_payments WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "No record found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "customer_id parameter is missing"]);
}

$conn->close();
?>
