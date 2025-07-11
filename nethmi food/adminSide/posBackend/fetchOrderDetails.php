<?php
require_once '../config.php'; // Include your database configuration

if (isset($_POST['table_id'])) {
    $table_id = mysqli_real_escape_string($link, $_POST['table_id']);

    // Query to fetch order details for done items
    $query = "
        SELECT item_id, quantity, time_submitted, date_confirm, date_processing, time_ended 
        FROM kitchen 
        WHERE table_id = $table_id AND time_ended IS NOT NULL
    ";

    $result = mysqli_query($link, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        echo '<ul class="list-group">';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<li class="list-group-item">Item ID: ' . htmlspecialchars($row['item_id']) . ', Quantity: ' . htmlspecialchars($row['quantity']) . ', Time Submitted: ' . htmlspecialchars($row['time_submitted']) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No completed items found for this table.</p>';
    }
} else {
    echo '<p>Invalid table ID.</p>';
}
?>
