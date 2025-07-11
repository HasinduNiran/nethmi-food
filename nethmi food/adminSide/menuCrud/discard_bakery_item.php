<?php
session_start();
require_once "../config.php";

// Set content type to JSON
header('Content-Type: application/json');

// Default response
$response = [
    'success' => false,
    'message' => 'Unknown error occurred'
];

// Log incoming data for debugging
$logFile = fopen("discard_log.txt", "a");
fwrite($logFile, date('Y-m-d H:i:s') . " - Received: " . print_r($_POST, true) . "\n");
fwrite($logFile, date('Y-m-d H:i:s') . " - Session: " . print_r($_SESSION, true) . "\n");

// Validate input
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if required fields exist
    if (empty($_POST['item_id'])) {
        $response['message'] = 'Item ID is missing';
        fwrite($logFile, "Error: Item ID is missing\n");
    } 
    elseif (empty($_POST['quantity'])) {
        $response['message'] = 'Quantity is missing';
        fwrite($logFile, "Error: Quantity is missing\n");
    }
    elseif (!is_numeric($_POST['quantity']) || floatval($_POST['quantity']) <= 0) {
        $response['message'] = 'Invalid quantity value';
        fwrite($logFile, "Error: Invalid quantity value: " . $_POST['quantity'] . "\n");
    }
    elseif (empty($_POST['reason'])) {
        $response['message'] = 'Reason is missing';
        fwrite($logFile, "Error: Reason is missing\n");
    }
    else {
        $item_id = $_POST['item_id'];
        $quantity = floatval($_POST['quantity']);
        $reason = $_POST['reason'];
        
        // Get current user ID from session
        $user_id = isset($_SESSION['logged_account_id']) ? $_SESSION['logged_account_id'] : 0;
        fwrite($logFile, "Using user ID: " . $user_id . "\n");
        
        // Start transaction
        $conn = $link;
        $conn->begin_transaction();
        
        try {
            // Check current quantity
            $check_query = "SELECT quantity, item_name FROM bakery_menu_stocks WHERE item_id = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("s", $item_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("Item not found");
            }
            
            $row = $result->fetch_assoc();
            $current_quantity = floatval($row['quantity']);
            $item_name = $row['item_name'];
            
            fwrite($logFile, "Current quantity: $current_quantity, Requested discard: $quantity\n");
            
            if ($quantity > $current_quantity) {
                throw new Exception("Discard quantity cannot exceed available quantity");
            }
            
            // Update quantity in bakery_menu_stocks
            $new_quantity = $current_quantity - $quantity;
            $update_query = "UPDATE bakery_menu_stocks SET quantity = ? WHERE item_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ds", $new_quantity, $item_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update item quantity: " . $conn->error);
            }
            
            // Insert record into discarded_items
            $insert_query = "INSERT INTO discarded_items (item_id, item_name, quantity_discarded, discard_reason, discard_date, user_id) 
                           VALUES (?, ?, ?, ?, NOW(), ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ssdss", $item_id, $item_name, $quantity, $reason, $user_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to record discard: " . $conn->error);
            }
            
            // Commit transaction
            $conn->commit();
            
            $response['success'] = true;
            $response['message'] = 'Item discarded successfully';
            fwrite($logFile, "Success: Item discarded successfully\n");
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $response['message'] = $e->getMessage();
            fwrite($logFile, "Exception: " . $e->getMessage() . "\n");
        }
        
        $conn->close();
    }
}
else {
    $response['message'] = 'Invalid request method';
    fwrite($logFile, "Error: Invalid request method\n");
}

fwrite($logFile, "Response: " . json_encode($response) . "\n\n");
fclose($logFile);

// Return JSON response - do NOT echo anything before this
echo json_encode($response);
?>
