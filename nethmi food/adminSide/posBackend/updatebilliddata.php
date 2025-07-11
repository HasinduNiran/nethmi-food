<?php
require_once "../config.php";

if (isset($_GET['id'])) {
    $quantity = 1;
    $bill_id = (int) $_GET['id'];  // Ensure it's an integer to prevent SQL injection
    $cart_query = "SELECT * FROM bill_items               
                   WHERE bill_id = ?";

    if ($stmt = $link->prepare($cart_query)) {
        $stmt->bind_param("i", $bill_id);  // Binding parameter to prevent SQL injection
        $stmt->execute();
        $cart_result = $stmt->get_result();

        if ($cart_result && $cart_result->num_rows > 0) {
            while ($cart_row = $cart_result->fetch_assoc()) {
                $item_id = $cart_row['item_id'];

                $valuedata = 0;
                $query = "SELECT * FROM `product_listing` WHERE `menu_id` = ?";
                if ($stmt2 = $link->prepare($query)) {
                    $stmt2->bind_param("s", $item_id);
                    $stmt2->execute();
                    $result = $stmt2->get_result();
                    $quantity = (int) $cart_row['quantity'];
                    if ($result && $result->num_rows > 0) {
                        for ($i = 0; $i < $result->num_rows; $i++) {
                            $rowin = $result->fetch_assoc();
                            $mid = (int) $rowin['iteam_id'];  
                            $qty = (float) $rowin['qty'];
                            $valuedata = $quantity * $qty;  
                            $formattedQty = number_format($valuedata, 2, '.', '');

                            $querym = "UPDATE `inventory` SET `qty` = `qty` - ? WHERE `id` = ?";
                            if ($stmt3 = $link->prepare($querym)) {
                                $stmt3->bind_param("si", $formattedQty, $mid);
                                if (!$stmt3->execute()) {
                                    echo "Error updating inventory: " . $stmt3->error;
                                }
                                
                            } else {
                                echo "Error preparing inventory update query: " . $link->error;
                            }
                            
                        }
                    } else {
                        echo "Error: No matching product listing found.";
                    }
                } else {
                    echo "Error preparing product listing query: " . $link->error;
                }
            }
        } else {
            echo "No items found in the cart.";
        }
    } else {
        echo "Error preparing bill items query: " . $link->error;
    }
} else {
    echo "No bill ID provided.";
}
