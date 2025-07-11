<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table_id = intval($_POST['table_id']);

    $query = "SELECT bill_id, total_amount FROM bills WHERE table_id = $table_id AND order_status = 'held'";
    $result = $link->query($query);

    if ($result && $result->num_rows > 0) {
        echo '<ul class="list-group">';
        while ($row = $result->fetch_assoc()) {
            echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                Order ID: ' . $row['bill_id'] . ' - Rs. ' . $row['total_amount'] . '
                <button class="btn btn-success btn-sm" onclick="proceedToPayment(' . $row['bill_id'] . ')">Proceed to Payment</button>
            </li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No held orders for this table.</p>';
    }
}
?>
