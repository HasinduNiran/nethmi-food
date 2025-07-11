<?php
require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input data
    $item_id = trim($_POST["item_id"]);
    $item_name = trim($_POST["item_name"]);
    $item_type = trim($_POST["item_type"]);
    $sub_item_type = trim($_POST["sub_item_type"]); // Added this line
    $item_category = trim($_POST["item_category"]);
    $item_description = trim($_POST["item_description"]);
    
    // Convert empty strings to NULL for optional fields
    $uber_pickme_regular = !empty($_POST["uber_pickme_regular"]) ? $_POST["uber_pickme_regular"] : null;
    $uber_pickme_medium = !empty($_POST["uber_pickme_medium"]) ? $_POST["uber_pickme_medium"] : null;
    $uber_pickme_large = !empty($_POST["uber_pickme_large"]) ? $_POST["uber_pickme_large"] : null;
    $takeaway_regular = !empty($_POST["takeaway_regular"]) ? $_POST["takeaway_regular"] : null;
    $takeaway_medium = !empty($_POST["takeaway_medium"]) ? $_POST["takeaway_medium"] : null;
    $takeaway_large = !empty($_POST["takeaway_large"]) ? $_POST["takeaway_large"] : null;
    $delivery_service_regular = !empty($_POST["delivery_service_regular"]) ? $_POST["delivery_service_regular"] : null;
    $delivery_service_medium = !empty($_POST["delivery_service_medium"]) ? $_POST["delivery_service_medium"] : null;
    $delivery_service_large = !empty($_POST["delivery_service_large"]) ? $_POST["delivery_service_large"] : null;
    $regular_price = !empty($_POST["regular_price"]) ? $_POST["regular_price"] : null;
    $medium_price = !empty($_POST["medium_price"]) ? $_POST["medium_price"] : null;
    $large_price = !empty($_POST["large_price"]) ? $_POST["large_price"] : null;

    $conn = $link;

    // Check if item_id exists in menu or bakery_items
    $check_menu_query = "SELECT item_id FROM menu WHERE item_id = ?";
    $check_menu_stmt = $conn->prepare($check_menu_query);
    $check_menu_stmt->bind_param("s", $item_id);
    $check_menu_stmt->execute();
    $check_menu_result = $check_menu_stmt->get_result();

    $check_bakery_query = "SELECT item_id FROM bakery_items WHERE item_id = ?";
    $check_bakery_stmt = $conn->prepare($check_bakery_query);
    $check_bakery_stmt->bind_param("s", $item_id);
    $check_bakery_stmt->execute();
    $check_bakery_result = $check_bakery_stmt->get_result();

    if ($check_menu_result->num_rows > 0 || $check_bakery_result->num_rows > 0) {
        http_response_code(400);
        echo "The item_id is already in use.";
        exit;
    }

    // Insert into menu table - updated SQL query to include sub_item_type
    $insert_query = "INSERT INTO menu (
        item_id, item_name, item_type, sub_item_type, item_category, item_description, 
        uber_pickme_regular, uber_pickme_medium, uber_pickme_large,
        takeaway_regular, takeaway_medium, takeaway_large,
        delivery_service_regular, delivery_service_medium, delivery_service_large,
        regular_price, medium_price, large_price
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insert_query);
    if (!$stmt) {
        http_response_code(500);
        echo "Error preparing statement: " . $conn->error;
        exit;
    }

    // Properly bind parameters with their types (added sub_item_type)
    $stmt->bind_param(
        "ssssssssssssssssss",
        $item_id, $item_name, $item_type, $sub_item_type, $item_category, $item_description,
        $uber_pickme_regular, $uber_pickme_medium, $uber_pickme_large,
        $takeaway_regular, $takeaway_medium, $takeaway_large,
        $delivery_service_regular, $delivery_service_medium, $delivery_service_large,
        $regular_price, $medium_price, $large_price
    );

    if ($stmt->execute()) {
        echo "Item created successfully";
    } else {
        http_response_code(500);
        echo "Error: " . $stmt->error;
    }
    $stmt->close();

    $check_menu_stmt->close();
    $check_bakery_stmt->close();
    $conn->close();
}
?>