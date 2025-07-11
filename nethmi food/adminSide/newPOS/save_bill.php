<?php
session_start();
require_once 'db_config.php';

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

// Start output buffering
ob_start();

// Set error handling
ini_set('display_errors', 0);
error_reporting(0);

// Set headers
header('Content-Type: application/json');

try {
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    // Check for valid JSON input
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON input");
    }

    // Get logged_account_id from session
    $logged_account_id = isset($_SESSION['logged_account_id']) ? $_SESSION['logged_account_id'] : null;

    // Get current Sri Lanka date and time
    $current_datetime = date('Y-m-d H:i:s');

    // Start transaction
    $conn->begin_transaction();

    // Check if an active bill with the same bill_id already exists
    $check_sql = "SELECT bill_id FROM bills WHERE bill_id = ? AND status = 'active'";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $check_stmt->bind_param("i", $data['bill_id']);
    if (!$check_stmt->execute()) {
        throw new Exception("Execute failed: " . $check_stmt->error);
    }
    $result = $check_stmt->get_result();

    // Check if this is Uber or Pick Me and has a reference number
    $hotelType = $data['hotel_type'];
    $referenceNumber = isset($data['reference_number']) ? $data['reference_number'] : null;

    // Setup reference field value - will be NULL by default
    $reference_field = null;
    if (($hotelType == '4' || $hotelType == '6') && !empty($referenceNumber)) {
        $reference_field = $referenceNumber;
    }

    if ($result->num_rows > 0) {
        // Update existing bill with Sri Lanka time
        $update_sql = "UPDATE bills SET 
                       table_id = ?, 
                       staff_id = ?, 
                       payment_time = ?, 
                       status = ?, 
                       total_before_discount = ?, 
                       discount_amount = ?, 
                       payment_amount = ?, 
                       paid_amount = ?, 
                       balance_amount = ?, 
                       customer_name = ?,
                       hotel_type = ?,
                       reference_number = ?,
                       service_charge = ? 
                       WHERE bill_id = ?";

        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $update_stmt->bind_param(
            "iissdddddsisdi",
            $data['table_id'],
            $logged_account_id,
            $current_datetime,  // Using Sri Lanka datetime instead of NOW()
            $data['status'],
            $data['total_before_discount'],
            $data['discount_amount'],
            $data['payment_amount'],
            $data['paid_amount'],
            $data['balance_amount'],
            $data['customer_name'],
            $data['hotel_type'],
            $reference_field,
            $data['service_charge'],
            $data['bill_id']
        );
        if (!$update_stmt->execute()) {
            throw new Exception("Execute failed: " . $update_stmt->error);
        }

        // Delete existing bill items and payments
        $delete_items_sql = "DELETE FROM bill_items WHERE bill_id = ?";
        $delete_stmt = $conn->prepare($delete_items_sql);
        if (!$delete_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $delete_stmt->bind_param("i", $data['bill_id']);
        if (!$delete_stmt->execute()) {
            throw new Exception("Execute failed: " . $delete_stmt->error);
        }

        // Corrected payment deletion
        $delete_payments_sql = "DELETE FROM bill_payments WHERE bill_id = ?";
        $delete_stmt = $conn->prepare($delete_payments_sql);
        if (!$delete_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $delete_stmt->bind_param("i", $data['bill_id']);
        if (!$delete_stmt->execute()) {
            throw new Exception("Execute failed: " . $delete_stmt->error);
        }
    } else {
        // Insert new bill with Sri Lanka time
        $sql = "INSERT INTO bills (
                bill_id, 
                table_id, 
                staff_id, 
                bill_time, 
                payment_time, 
                status, 
                total_before_discount, 
                discount_amount, 
                payment_amount, 
                paid_amount, 
                balance_amount, 
                customer_name,
                customer_id, 
                hotel_type,
                reference_number,
                service_charge
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param(
            "iiisssdddddsiisd",
            $data['bill_id'],
            $data['table_id'],
            $logged_account_id,
            $current_datetime,    // Using Sri Lanka datetime for bill_time
            $current_datetime,    // Using Sri Lanka datetime for payment_time
            $data['status'],
            $data['total_before_discount'],
            $data['discount_amount'],
            $data['payment_amount'],
            $data['paid_amount'],
            $data['balance_amount'],
            $data['customer_name'],
            $data['customer_id'],
            $data['hotel_type'],
            $reference_field,
            $data['service_charge']
        );
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
    }

    // Insert bill items
    foreach ($data['items'] as $item) {
        $sql = "INSERT INTO bill_items (bill_id, item_id, quantity, portion_size, product_name, price, free_count) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("isissdi", $data['bill_id'], $item['item_id'], $item['quantity'],  $item['portion_size'], $item['name'],  $item['price'],  $item['fc']);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Fetch ingredients for the menu item
        $ingredients_sql = "SELECT ingredient_id, quantity, measurement FROM menu_ingredients WHERE menu_item_id = ?";
        $ingredients_stmt = $conn->prepare($ingredients_sql);
        if (!$ingredients_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $ingredients_stmt->bind_param("i", $item['item_id']);
        if (!$ingredients_stmt->execute()) {
            throw new Exception("Execute failed: " . $ingredients_stmt->error);
        }
        $ingredients_result = $ingredients_stmt->get_result();

        while ($ingredient = $ingredients_result->fetch_assoc()) {
            // Calculate total quantity used
            $total_quantity_used = $ingredient['quantity'] * $item['quantity'];

            // Corrected measurement conversion
            $measurement_sql = "SELECT id FROM mesuer WHERE id = ?";
            $measurement_stmt = $conn->prepare($measurement_sql);
            if (!$measurement_stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $measurement_stmt->bind_param("i", $ingredient['measurement']);
            if (!$measurement_stmt->execute()) {
                throw new Exception("Execute failed: " . $measurement_stmt->error);
            }
            $measurement_result = $measurement_stmt->get_result();
            $measurement_row = $measurement_result->fetch_assoc();
            $measurement_id = $measurement_row['id'];

            $converted_quantity = $total_quantity_used;
            if ($measurement_id == 1 || $measurement_id == 2) {
                $converted_quantity = $total_quantity_used / 1000; // Convert to base units (kg/l)
            }

            // Update inventory
            $update_inventory_sql = "UPDATE inventory SET qty = qty - ? WHERE id = ?";
            $update_inventory_stmt = $conn->prepare($update_inventory_sql);
            if (!$update_inventory_stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $update_inventory_stmt->bind_param("di", $converted_quantity, $ingredient['ingredient_id']);
            if (!$update_inventory_stmt->execute()) {
                throw new Exception("Execute failed: " . $update_inventory_stmt->error);
            }
        }

        // Handle bakery items
        $check_bakery_sql = "SELECT quantity FROM bakery_menu_stocks WHERE item_id = ?";
        $check_bakery_stmt = $conn->prepare($check_bakery_sql);
        if (!$check_bakery_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $check_bakery_stmt->bind_param("i", $item['item_id']);
        if (!$check_bakery_stmt->execute()) {
            throw new Exception("Execute failed: " . $check_bakery_stmt->error);
        }
        $bakery_result = $check_bakery_stmt->get_result();

        if ($bakery_result->num_rows > 0) {
            $update_stock_sql = "UPDATE bakery_menu_stocks SET quantity = quantity - ? WHERE item_id = ?";
            $update_stock_stmt = $conn->prepare($update_stock_sql);
            if (!$update_stock_stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $update_stock_stmt->bind_param("ii", $item['quantity'], $item['item_id']);
            if (!$update_stock_stmt->execute()) {
                throw new Exception("Execute failed: " . $update_stock_stmt->error);
            }
        }
    }

    // Insert bill payments and handle credit
    foreach ($data['payments'] as $payment) {
        $sql = "INSERT INTO bill_payments (bill_id, payment_method, amount, card_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("isds", $data['bill_id'], $payment['payment_method'], $payment['amount'], $payment['card_id']);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // If payment method is credit, add to customer_credits
        if (strtolower($payment['payment_method']) === 'cre') {
            // if (!isset($data['customer_id'])) {
            //     throw new Exception("Customer ID is required for credit payments");
            // }

            $credit_sql = "INSERT INTO customer_credits (customer_id, amount, date, bill_id) VALUES (?, ?, ?, ?)";
            $credit_stmt = $conn->prepare($credit_sql);
            if (!$credit_stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $credit_stmt->bind_param(
                "idsi",
                $data['customer_id'],
                $payment['amount'],
                $current_datetime,  // Using Sri Lanka datetime instead of NOW()
                $data['bill_id']
            );
            if (!$credit_stmt->execute()) {
                throw new Exception("Execute failed: " . $credit_stmt->error);
            }
            // Update credit_balance in customers table
            $update_credit_balance_sql = "UPDATE customers SET credit_balance = credit_balance + ? WHERE customer_id = ?";
            $update_stmt = $conn->prepare($update_credit_balance_sql);
            if (!$update_stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $update_stmt->bind_param(
                "di",
                $payment['amount'],  // New credit amount to add
                $data['customer_id'] // Customer ID to update
            );
            if (!$update_stmt->execute()) {
                throw new Exception("Execute failed: " . $update_stmt->error);
            }
        }
    }

    // Change table status to "Dirty" after bill is completed
    $update_table_sql = "UPDATE restaurant_tables SET status = 'available' WHERE table_id = ?";
    $update_table_stmt = $conn->prepare($update_table_sql);
    $update_table_stmt->bind_param("i", $data['table_id']);
    $update_table_stmt->execute();
    
    // Commit transaction
    $conn->commit();

    // Clear output buffer
    ob_end_clean();

    // Return success response
    echo json_encode(["success" => true, "action" => $result->num_rows > 0 ? "updated" : "inserted"]);
} catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();

    // Clear output buffer
    ob_end_clean();

    // Return error response
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>

<?php
// session_start(); // Add session start to access the logged_account_id
// require_once 'db_config.php';

// // Get JSON data
// $data = json_decode(file_get_contents('php://input'), true);

// // Get logged_account_id from session
// $logged_account_id = isset($_SESSION['logged_account_id']) ? $_SESSION['logged_account_id'] : null;

// // Start transaction
// $conn->begin_transaction();

// try {
//     // Check if an active bill with the same bill_id already exists
//     $check_sql = "SELECT bill_id FROM bills WHERE bill_id = ? AND status = 'active'";
//     $check_stmt = $conn->prepare($check_sql);
//     $check_stmt->bind_param("i", $data['bill_id']);
//     $check_stmt->execute();
//     $result = $check_stmt->get_result();

//     if ($result->num_rows > 0) {
//         // Update existing bill
//         $update_sql = "UPDATE bills SET 
//                        table_id = ?, 
//                        staff_id = ?, 
//                        payment_time = NOW(), 
//                        status = ?, 
//                        total_before_discount = ?, 
//                        discount_amount = ?, 
//                        payment_amount = ?, 
//                        paid_amount = ?, 
//                        balance_amount = ?, 
//                        customer_name = ?,
//                        hotel_type = ? 
//                        WHERE bill_id = ?";

//         $update_stmt = $conn->prepare($update_sql);
//         $update_stmt->bind_param("iisdddddsii", 
//             $data['table_id'],
//             $logged_account_id, 
//             $data['status'], 
//             $data['total_before_discount'], 
//             $data['discount_amount'], 
//             $data['payment_amount'], 
//             $data['paid_amount'], 
//             $data['balance_amount'],
//             $data['customer_name'],
//             $data['hotel_type'],
//             $data['bill_id']
//         );
//         $update_stmt->execute();

//         // Delete existing bill items and payments
//         $delete_items_sql = "DELETE FROM bill_items WHERE bill_id = ?";
//         $delete_stmt = $conn->prepare($delete_items_sql);
//         $delete_stmt->bind_param("i", $data['bill_id']);
//         $delete_stmt->execute();

//         $delete_payments_sql = "DELETE FROM bill_payments WHERE bill_id = ?";
//         $delete_stmt = $conn->prepare($delete_payments_sql);
//         $delete_stmt->bind_param("i", $data['bill_id']);
//         $delete_stmt->execute();
//     } else {
//         // Insert new bill
//         $sql = "INSERT INTO bills (bill_id, table_id, staff_id, bill_time, payment_time, status, total_before_discount, discount_amount, payment_amount, paid_amount, balance_amount, customer_name, hotel_type) 
//                 VALUES (?, ?, ?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";

//         $stmt = $conn->prepare($sql);
//         $stmt->bind_param("iiisdddddsi", 
//             $data['bill_id'], 
//             $data['table_id'],
//             $logged_account_id,
//             $data['status'], 
//             $data['total_before_discount'], 
//             $data['discount_amount'], 
//             $data['payment_amount'], 
//             $data['paid_amount'], 
//             $data['balance_amount'],
//             $data['customer_name'],
//             $data['hotel_type']
//         );
//         $stmt->execute();
//     }

//     // Insert bill items
//     foreach ($data['items'] as $item) {
//         $sql = "INSERT INTO bill_items (bill_id, item_id, quantity) VALUES (?, ?, ?)";
//         $stmt = $conn->prepare($sql);
//         $stmt->bind_param("isi", $data['bill_id'], $item['item_id'], $item['quantity']);
//         $stmt->execute();

//         // Check if the item is from the bakery_menu_stocks table
//         $check_bakery_sql = "SELECT quantity FROM bakery_menu_stocks WHERE item_id = ?";
//         $check_bakery_stmt = $conn->prepare($check_bakery_sql);
//         $check_bakery_stmt->bind_param("i", $item['item_id']);
//         $check_bakery_stmt->execute();
//         $bakery_result = $check_bakery_stmt->get_result();

//         if ($bakery_result->num_rows > 0) {
//             $update_stock_sql = "UPDATE bakery_menu_stocks SET quantity = quantity - ? WHERE item_id = ?";
//             $update_stock_stmt = $conn->prepare($update_stock_sql);
//             $update_stock_stmt->bind_param("ii", $item['quantity'], $item['item_id']);
//             $update_stock_stmt->execute();
//         }
//     }

//     // Insert bill payments
//     foreach ($data['payments'] as $payment) {
//         $sql = "INSERT INTO bill_payments (bill_id, payment_method, amount, card_id) VALUES (?, ?, ?, ?)";
//         $stmt = $conn->prepare($sql);
//         $stmt->bind_param("isds", $data['bill_id'], $payment['payment_method'], $payment['amount'], $payment['card_id']);
//         $stmt->execute();
//     }

//     // Commit transaction
//     $conn->commit();

//     echo json_encode(["success" => true, "action" => $result->num_rows > 0 ? "updated" : "inserted"]);
// } catch (Exception $e) {
//     $conn->rollback();
//     echo json_encode(["success" => false, "message" => $e->getMessage()]);
// }

// $conn->close();
?>
