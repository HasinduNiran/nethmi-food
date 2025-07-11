<?php
require_once 'db_config.php';

if (isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $update_query = "UPDATE dine_in_orders SET state = 'solved' WHERE order_id = ?";
    
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $order_id);

    $response = [];
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Order $order_id finalized successfully";
    } else {
        $response['success'] = false;
        $response['message'] = "Failed to finalize order: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'No order ID provided'
    ]);
}
?>