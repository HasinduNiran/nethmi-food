<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not redirect to login page
require_once '../posBackend/checkIfLoggedIn.php';

// Include config file
require_once "../config.php";

// Process delete operation after confirmation
if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["item_id"])) {
    
    // Get item ID from POST data
    $item_id = trim($_POST["item_id"]);
    
    // Prepare a delete statement
    $sql = "DELETE FROM menu WHERE item_id = ?";
    
    if($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $item_id);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)) {
            // Record deleted successfully. Redirect to menu panel
            header("location: ../panel/menu-panel.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else {
    // If no valid ID was provided, redirect to error page
    header("location: ../panel/menu-panel.php");
    exit();
}
?>