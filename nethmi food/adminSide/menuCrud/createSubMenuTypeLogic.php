<?php
session_start();
include '../inc/dashHeader.php';
require_once "../config.php";

$parent_type_id = $sub_type_name = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate parent type ID
    if (empty($_POST['parent_type_id'])) {
        $error = 'Parent Menu Type is required';
    } else {
        $parent_type_id = filter_input(INPUT_POST, 'parent_type_id', FILTER_SANITIZE_NUMBER_INT);
    }
    
    // Validate sub type name
    if (empty($_POST['sub_type_name'])) {
        $error = 'Sub Menu Type Name is required';
    } else {
        $sub_type_name = filter_input(INPUT_POST, 'sub_type_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    if (empty($error)) {
        // First, verify that the parent type exists
        $check_sql = "SELECT item_type_id FROM menu_item_type WHERE item_type_id = ?";
        if ($check_stmt = mysqli_prepare($link, $check_sql)) {
            mysqli_stmt_bind_param($check_stmt, "i", $parent_type_id);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) == 0) {
                echo "Parent menu type does not exist.";
                mysqli_stmt_close($check_stmt);
                exit;
            }
            mysqli_stmt_close($check_stmt);
        }
        
        // Insert the sub menu type
        $sql = "INSERT INTO sub_menu_type (parent_type_id, sub_type_name) VALUES (?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "is", $parent_type_id, $sub_type_name);
            if (mysqli_stmt_execute($stmt)) {
                echo "New sub menu type added successfully!";
            } else {
                echo "Error inserting data: " . mysqli_error($link);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($link);
        }
    } else {
        echo $error;
    }
}
?>