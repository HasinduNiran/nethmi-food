<?php
require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the values from the form
    $item_id = $_POST["item_id"];
    $item_name = $_POST["item_name"];
    $item_type = $_POST["item_type"];
    $item_category = $_POST["item_category"];
    $item_price = $_POST["item_price"];
    $item_description = $_POST["item_description"];
    $conn = $link;

    // Prepare the SQL query to check if the item_id already exists
    $check_query = "SELECT item_id FROM menu WHERE item_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $item_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    // Check if the item_id already exists
    if ($check_result->num_rows > 0) {
        $message = "The item_id is already in use.<br>Please try again to choose a different item_id.";
        $iconClass = "fa-times-circle";
        $cardClass = "alert-danger";
        $bgColor = "#FFA7A7"; // Custom background color for error
    } else {
        // Prepare the SQL query for insertion
        $insert_query = "INSERT INTO menu (item_id, item_name, item_type, item_category, item_price, item_description) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);

        // Bind the parameters
        $stmt->bind_param("ssssds", $item_id, $item_name, $item_type, $item_category, $item_price, $item_description);

        // Execute the query
        if ($stmt->execute()) {
            $lastInsertedId = $conn->insert_id;
            echo $lastInsertedId;
        } else {
            $message = "Error: " . $insert_query . "<br>" . $conn->error;
        }

        $stmt->close();
    }

    // Close the check statement and the connection
    $check_stmt->close();
    $conn->close();
}
