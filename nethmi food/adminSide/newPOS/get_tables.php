<?php
require_once 'db_config.php';

// Set the response content type
header('Content-Type: application/json');

// Query to get all tables
$sql = "SELECT table_id, capacity, status FROM restaurant_tables ORDER BY table_id";
$result = $conn->query($sql);

$tables = [];
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $tables[] = $row;
    }
    echo json_encode($tables);
} else {
    echo json_encode([]);
}

$conn->close();
?>