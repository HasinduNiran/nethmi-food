<?php
require_once '../../config.php';

// Ensure the database connection is valid
if (!$link) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Select the latest record from the Kitchen table where time_ended is not NULL
$selectQuery = "SELECT kitchen_id FROM kitchen WHERE time_ended IS NOT NULL ORDER BY time_ended DESC LIMIT 1";
$result = $link->query($selectQuery);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $kitchen_id = $row['kitchen_id'];

    // Update the record to set time_ended as NULL
    $updateQuery = "UPDATE kitchen SET time_ended = NULL WHERE kitchen_id = $kitchen_id";
    if ($link->query($updateQuery) === TRUE) {
        // Redirect back to kitchen panel with a success message
        echo '<script>
                alert("Last action undone successfully.");
                window.location.href = "../../panel/kitchen-panel.php";
              </script>';
        exit();
    } else {
        // Error undoing time_ended
        error_log("Error undoing time_ended: " . $link->error);
        echo '<script>
                alert("Failed to undo the last action. Please try again.");
                window.location.href = "../../panel/kitchen-panel.php";
              </script>';
        exit();
    }
} else {
    // No records with time_ended set
    echo '<script>
            alert("No records available to undo.");
            window.location.href = "../../panel/kitchen-panel.php";
          </script>';
    exit();
}
?>
