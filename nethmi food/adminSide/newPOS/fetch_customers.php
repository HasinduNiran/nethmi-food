<?php
require_once 'db_config.php';

if (isset($_GET['query'])) {
    $query = $conn->real_escape_string($_GET['query']);
    
    $sql = "SELECT customer_id, name, phone_number FROM customers WHERE phone_number LIKE ?";
    $stmt = $conn->prepare($sql);
    
    $likeQuery = $query . "%";
    $stmt->bind_param('s', $likeQuery);
    
    $stmt->execute();
    $result = $stmt->get_result();

    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }

    echo json_encode($customers);
}

$conn->close();
?>
