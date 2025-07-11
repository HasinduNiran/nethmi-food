<?php
require_once "../config.php";

header('Content-Type: application/json');

if (isset($_GET['item_id'])) {
    $item_id = $_GET['item_id'];
    $conn = $link;

    $query = "SELECT item_name FROM bakery_items WHERE item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['item_name' => $row['item_name']]);
    } else {
        echo json_encode(['item_name' => null]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['item_name' => null]);
}
?>