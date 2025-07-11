<?php

require_once "../config.php";

if (isset($_GET['id'])) {
    $bill_id = mysqli_real_escape_string($link, $_GET['id']);
    $cart_query = "SELECT bi.*, m.item_name, m.item_price FROM bill_items bi
                   JOIN menu m ON bi.item_id = m.item_id
                   WHERE bi.bill_id = '$bill_id'";
    $cart_result = mysqli_query($link, $cart_query);

    if ($cart_result && mysqli_num_rows($cart_result) > 0) {
        while ($cart_row = mysqli_fetch_assoc($cart_result)) {
            $item_id = (int) $cart_row['item_id'];
            $quantity = (int) $cart_row['quantity'];

            $query = "SELECT * FROM `product_listing` WHERE `menu_id` = $item_id";
            $result = $link->query($query);

            if ($result && $result->num_rows > 0) {
                while ($rowin = $result->fetch_assoc()) {
                    $mid = (int) $rowin['iteam_id'];
                    $qty = (int) $rowin['qty'];
                    $mersuer = $quantity * $qty;

                    // Update inventory quantity
                    $querym = "UPDATE `inventory` SET `qty` = `qty` - $mersuer WHERE `id` = $mid";
                    if (!$link->query($querym)) {
                        echo "Error updating inventory: " . $link->error;
                    }
                }
            } else {
                echo "Error: " . $link->error;
            }
        }
    } else {
        echo "No items found in the cart.";
    }
} else {
    echo "No bill ID provided.";
}
