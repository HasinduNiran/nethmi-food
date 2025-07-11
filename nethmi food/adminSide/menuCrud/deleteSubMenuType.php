<?php
require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sub_type_id'])) {
    $sub_type_id = intval($_POST['sub_type_id']);

    $sql = "DELETE FROM sub_menu_type WHERE sub_type_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $sub_type_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "success";
        } else {
            echo "Error deleting sub menu type: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($link);
    }
} else {
    echo "Invalid request.";
}

mysqli_close($link);
?>