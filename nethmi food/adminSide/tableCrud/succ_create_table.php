<?php
require_once "../config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table_id = $_POST['table_id'];
    $capacity = $_POST['capacity'];

    // Validate inputs
    if (!empty($table_id) && !empty($capacity)) {
        $sql = "INSERT INTO restaurant_tables (table_id, capacity, is_available) VALUES (?, ?, 1)";
        if ($stmt = $link->prepare($sql)) {
            $stmt->bind_param("ii", $table_id, $capacity);
            if ($stmt->execute()) {
                echo '<script>alert("Table created successfully!"); window.location.href="createTable.php";</script>';
            } else {
                echo '<script>alert("Error creating table: ' . $link->error . '");</script>';
            }
            $stmt->close();
        } else {
            echo '<script>alert("Database error: ' . $link->error . '");</script>';
        }
    } else {
        echo '<script>alert("All fields are required.");</script>';
    }

    $link->close();
}
?>

