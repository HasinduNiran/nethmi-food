<?php
// Include the database connection
include '../config.php';

if (isset($_GET['bill_id'])) {
    $bill_id = $_GET['bill_id'];

    $update_query = "UPDATE bill_items SET status = 'old' WHERE bill_id = '$bill_id' AND status = 'new'";
    
    if (mysqli_query($link, $update_query)) {
        echo "Cart items successfully held.";
    } else {
        echo "Error updating cart: " . mysqli_error($link);
    }
} else {
    echo "Invalid request: bill_id is missing.";
}