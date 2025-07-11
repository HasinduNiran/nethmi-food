<?php
require_once '../../config.php';
date_default_timezone_set('Asia/Colombo');

if (isset($_GET['action']) && isset($_GET['kitchen_id'])) {
    $action = $_GET['action'];
    $kitchen_id = intval($_GET['kitchen_id']); // Sanitize input

    // Handle actions
    if ($action === 'set_time_confimed') {
        $currentTime = date('Y-m-d H:i:s');

        // Update both date_confirm and date_processing
        $updateQuery = "UPDATE kitchen SET date_confirm = '$currentTime', date_processing = '$currentTime' WHERE kitchen_id = $kitchen_id";
        if ($link->query($updateQuery) === TRUE) {
            header("Location: ../../panel/kitchen-panel.php"); // Redirect back to kitchen panel
        } else {
            echo "Error updating date_confirm and date_processing: " . $link->error;
        }
    } elseif ($action === 'set_time_processing') {
        $currentTime = date('Y-m-d H:i:s');
        $updateQuery = "UPDATE kitchen SET date_processing = '$currentTime' WHERE kitchen_id = $kitchen_id";
        if ($link->query($updateQuery) === TRUE) {
            header("Location: ../../panel/kitchen-panel.php"); // Redirect back to kitchen panel
        } else {
            echo "Error updating date_processing: " . $link->error;
        }
    } elseif ($action === 'set_time_ended') {
        $currentTime = date('Y-m-d H:i:s');
        $updateQuery = "UPDATE kitchen SET time_ended = '$currentTime' WHERE kitchen_id = $kitchen_id";
        if ($link->query($updateQuery) === TRUE) {
            header("Location: ../../panel/kitchen-panel.php"); // Redirect back to kitchen panel
        } else {
            echo "Error updating time_ended: " . $link->error;
        }
    }
}
?>
