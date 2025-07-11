<?php
require_once "../config.php"; 

if (isset($_GET['id'])) {
    $item_id = $_GET['id']; 
    $quantity = 1; 

    $query = "SELECT iteam_id, qty FROM `product_listing` WHERE `menu_id` = ?";
    if ($stmt2 = $link->prepare($query)) {
        $stmt2->bind_param("s", $item_id);
        $stmt2->execute();
        $result = $stmt2->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($rowin = $result->fetch_assoc()) {
                $mid = (int)$rowin['iteam_id'];  
                $qty = (float)$rowin['qty'];
                $valuedata = $quantity * $qty;  

                $querym = "UPDATE `inventory` SET `qty` = `qty` - ? WHERE `id` = ?";
                if ($stmt3 = $link->prepare($querym)) {
                    $stmt3->bind_param("si", $valuedata, $mid); 
                    if ($stmt3->execute()) {
                        echo "Inventory updated successfully for item ID: $mid\n";
                    } else {
                        echo "Error updating inventory for item ID $mid: " . $stmt3->error;
                    }
                    $stmt3->close();
                } else {
                    echo "Error preparing inventory update query: " . $link->error;
                }
            }
        } else {
            echo "No matching product listing found for menu ID: $item_id.";
        }
        $stmt2->close();
    } else {
        echo "Error preparing product listing query: " . $link->error;
    }
} else {
    echo "No menu ID provided.";
}
?>
