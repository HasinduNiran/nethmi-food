<?php
require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item_type_id'])) {
    $item_type_id = intval($_POST['item_type_id']);

    $sql = "DELETE FROM menu_item_type WHERE item_type_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $item_type_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "success";
        } else {
            echo "Error deleting item.";
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($link);
?>
