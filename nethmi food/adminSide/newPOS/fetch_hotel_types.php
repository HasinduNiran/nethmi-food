<?php
require_once 'db_config.php';

// Fetch hotel types
$hotel_types_query = "SELECT * FROM holetype";
$hotel_types_result = $conn->query($hotel_types_query);

$hotel_types = [];
if ($hotel_types_result->num_rows > 0) {
    while ($row = $hotel_types_result->fetch_assoc()) {
        $hotel_types[] = $row;
    }
}
echo json_encode($hotel_types);
$conn->close();
?>