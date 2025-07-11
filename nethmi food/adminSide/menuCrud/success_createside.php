<?php
require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the values from the form
    $item_id = $_POST["item_id"];
    $item_name = $_POST["item_name"];
    $item_category = $_POST["item_category"];
    $qty = $_POST["qty"];
    $mesurer = $_POST["mesurer"];

    // Get the current date in Y-M-d format
    date_default_timezone_set("Asia/Colombo");
    $manufacturedate = date("Y-m-d");

    // Use the database connection
    $conn = $link;

    // Prepare the SQL query to check if the item_id already exists
    $check_query = "SELECT side_item_id FROM side_menu WHERE side_item_id = ?";
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
        $insert_query = "INSERT INTO side_menu (side_item_id, item_name, item_category, qty, mesuer, manufacturedate) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);

        // Bind the parameters
        $stmt->bind_param("ssssss", $item_id, $item_name, $item_category, $qty, $mesurer, $manufacturedate);

        // Execute the query
        if ($stmt->execute()) {
            $lastInsertedId = $conn->insert_id;
            echo "Successfully inserted. Last inserted ID: " . $lastInsertedId;
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    // Close the check statement and the connection
    $check_stmt->close();
    $conn->close();
}
?>
