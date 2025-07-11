<?php
session_start();
require_once '../config.php';

// Ensure the user is logged in
if (!isset($_SESSION['roll'])) {
    echo '<script>
            alert("Access Denied: You must log in to proceed.");
            window.location.href = "../login.php";
          </script>';
    exit;
}

// Check if the user has the required role
$allowed_roles = [1, 2, 3, 5]; // Waiter, Manager, Admin, Cashier
if (!in_array($_SESSION['roll'], $allowed_roles)) {
    echo '<script>
            alert("Access Denied: You do not have permission to hold payments.");
            window.location.href = "../dashboard.php";
          </script>';
    exit;
}

// Validate and retrieve parameters
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['bill_id']) && isset($_GET['table_id'])) {
    $bill_id = intval($_GET['bill_id']);
    $table_id = intval($_GET['table_id']);

    // Validate parameters
    if (!$bill_id || !$table_id) {
        echo '<script>
                alert("Invalid parameters. Please check and try again.");
                window.history.back();
              </script>';
        exit;
    }

    // Calculate the total amount for the bill
    $cart_total_query = "SELECT SUM(bi.quantity * m.item_price) AS total_amount
                         FROM bill_items bi
                         JOIN menu m ON bi.item_id = m.item_id
                         WHERE bi.bill_id = $bill_id";

    $cart_total_result = $link->query($cart_total_query);
    $cart_total = 0;

    if ($cart_total_result && $row = $cart_total_result->fetch_assoc()) {
        $cart_total = floatval($row['total_amount']);
    }

    // Set grand total as cart total (no tax calculation)
    $grand_total = $cart_total;

    // Update the `order_status` to "hold" and save the grand total in the `payment_amount` column
    $update_bill_query = "UPDATE bills 
                          SET order_status = 'hold', payment_amount = $grand_total 
                          WHERE bill_id = $bill_id";

    if ($link->query($update_bill_query)) {
        // Optional: Move cart items to held payments for tracking
        $insert_held_query = "INSERT INTO held_payments (bill_id, item_id, quantity, price, total)
                              SELECT bi.bill_id, bi.item_id, bi.quantity, m.item_price, (bi.quantity * m.item_price)
                              FROM bill_items bi
                              JOIN menu m ON bi.item_id = m.item_id
                              WHERE bi.bill_id = $bill_id";

        if ($link->query($insert_held_query)) {
            // Clear the current cart
            $clear_cart_query = "DELETE FROM bill_items WHERE bill_id = $bill_id";
            if ($link->query($clear_cart_query)) {
                // Redirect to POS Table or any desired location
                echo '<script>
                        alert("Payment has been held successfully.");
                        window.location.href = "../pos-panel.php";
                      </script>';
            } else {
                error_log("MySQL Error (Clear Cart): " . $link->error);
                echo '<script>
                        alert("Failed to clear the cart. Please try again.");
                        window.history.back();
                      </script>';
            }
        } else {
            error_log("MySQL Error (Insert Held Payments): " . $link->error);
            echo '<script>
                    alert("Failed to store held payment details. Please try again.");
                    window.history.back();
                  </script>';
        }
    } else {
        error_log("MySQL Error (Update Order Status): " . $link->error);
        echo '<script>
                alert("Failed to update order status. Please try again.");
                window.history.back();
              </script>';
    }
} else {
    echo '<script>
            alert("Invalid request.");
            window.history.back();
          </script>';
}
?>
