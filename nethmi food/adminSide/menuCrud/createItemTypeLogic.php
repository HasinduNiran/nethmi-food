<?php
session_start();
include '../inc/dashHeader.php';
require_once "../config.php";

$item_type_name = $item_type_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['item_type_name'])) {
        $item_type_err = 'Item Type Name is required';
    } else {
        $item_type_name = filter_input(INPUT_POST, 'item_type_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    if (empty($item_type_err)) {
        $sql = "INSERT INTO menu_item_type (item_type_name) VALUES (?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $item_type_name);
            if (mysqli_stmt_execute($stmt)) {
                echo "New item type added successfully!";
            } else {
                echo "Error inserting data.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

