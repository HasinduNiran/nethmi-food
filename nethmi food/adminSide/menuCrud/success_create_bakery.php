<?php
require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = $link;
    
    $item_id = $_POST["item_id"];
    $item_name = $_POST["item_name"];
    
    if (isset($_POST["is_quick"]) && $_POST["is_quick"] === "true") {
        // Quick registration remains unchanged
        $check_query = "SELECT item_id FROM bakery_items WHERE item_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $item_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        // Check if item_id exists in menu table
        $check_menu_query = "SELECT item_id FROM menu WHERE item_id = ?";
        $check_menu_stmt = $conn->prepare($check_menu_query);
        $check_menu_stmt->bind_param("s", $item_id);
        $check_menu_stmt->execute();
        $check_menu_result = $check_menu_stmt->get_result();

        if ($check_menu_result->num_rows > 0 || $check_result->num_rows > 0) {
            http_response_code(400);
            echo "The item_id is already in use.";
        } else {
            $insert_query = "INSERT INTO bakery_items (item_id, item_name) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ss", $item_id, $item_name);

            if ($stmt->execute()) {
                echo "Bakery item registered successfully";
            } else {
                http_response_code(500);
                echo "Error: " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
        $check_menu_stmt->close();
    } else {
        $quantity = $_POST["quantity"];
        $cost_price = $_POST["cost_price"];
        $item_type = $_POST["item_type"];
        $item_price = $_POST["item_price"];
        $item_description = $_POST["item_description"];
        $uber_pickme_price = $_POST["uber_pickme_price"];
        $dining_price = $_POST["dining_price"];
        $takeaway_price = $_POST["takeaway_price"];
        $delivery_service_item_price = $_POST["delivery_service_item_price"];
        $supplier_id = !empty($_POST["supplier_id"]) ? $_POST["supplier_id"] : null;
        $bakery_category = $_POST["bakery_category"] ?? null;

        $conn->begin_transaction();

        try {
            $insert_menu_query = "INSERT INTO bakery_menu 
                (item_id, item_name, quantity, cost_price, item_type, bakery_category, item_price, item_description, uber_pickme_price, dining_price, takeaway_price, delivery_service_item_price, supplier_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_menu = $conn->prepare($insert_menu_query);
            $stmt_menu->bind_param("ssddssddddddi", 
                $item_id, 
                $item_name, 
                $quantity, 
                $cost_price, 
                $item_type, 
                $bakery_category, 
                $item_price, 
                $item_description, 
                $uber_pickme_price, 
                $dining_price, 
                $takeaway_price, 
                $delivery_service_item_price,
                $supplier_id
            );
            $stmt_menu->execute();

            $check_stocks_query = "SELECT item_id FROM bakery_menu_stocks WHERE item_id = ? AND item_price = ? AND dining_price = ?";
            $check_stocks_stmt = $conn->prepare($check_stocks_query);
            $check_stocks_stmt->bind_param("sdd", $item_id, $item_price, $dining_price);
            $check_stocks_stmt->execute();
            $check_stocks_result = $check_stocks_stmt->get_result();

            if ($check_stocks_result->num_rows > 0) {
                $update_stocks_query = "UPDATE bakery_menu_stocks SET quantity = quantity + ?, supplier_id = ? WHERE item_id = ? AND item_price = ? AND dining_price = ?";
                $stmt_stocks = $conn->prepare($update_stocks_query);
                $stmt_stocks->bind_param("disdd", $quantity, $supplier_id, $item_id, $item_price, $dining_price);
                $stmt_stocks->execute();
                $stmt_stocks->close();
            } else {
                $insert_stocks_query = "INSERT INTO bakery_menu_stocks 
                    (item_id, item_name, quantity, cost_price, item_type, bakery_category, item_price, item_description, uber_pickme_price, dining_price, takeaway_price, delivery_service_item_price, supplier_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_stocks = $conn->prepare($insert_stocks_query);
                $stmt_stocks->bind_param("ssddssddddddi", 
                    $item_id, 
                    $item_name, 
                    $quantity, 
                    $cost_price, 
                    $item_type, 
                    $bakery_category, 
                    $item_price, 
                    $item_description, 
                    $uber_pickme_price, 
                    $dining_price, 
                    $takeaway_price, 
                    $delivery_service_item_price,
                    $supplier_id
                );
                $stmt_stocks->execute();
                $stmt_stocks->close();
            }

            $conn->commit();
            echo "Bakery item created successfully";
            
            $stmt_menu->close();
            $check_stocks_stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            echo "Error: " . $e->getMessage();
        }
    }

    $conn->close();
}
?>