<?php
require_once 'db_config.php';

if (isset($_POST['customer_id'])) {
    $customer_id = intval($_POST['customer_id']); // Ensure it's an integer

    $sql = "DELETE FROM customer_payments WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => "Record deleted successfully"]);
    } else {
        echo json_encode(["error" => "No matching record found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "customer_id parameter is missing"]);
}

$conn->close();
?>
