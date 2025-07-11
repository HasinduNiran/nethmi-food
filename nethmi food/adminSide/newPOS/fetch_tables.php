<?php
require_once 'db_config.php';

// Fetch tables for the selected hotel type
$tables_query = "SELECT table_id, capacity, status FROM restaurant_tables";
$stmt = $conn->prepare($tables_query);
$stmt->execute();
$tables_result = $stmt->get_result();

$tables = [];
if ($tables_result->num_rows > 0) {
    while ($row = $tables_result->fetch_assoc()) {
        $tables[] = $row;
    }
}
echo json_encode($tables);
$stmt->close();
$conn->close();
?>