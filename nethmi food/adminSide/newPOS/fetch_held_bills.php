<?php
require_once 'db_config.php';
$sql = "SELECT 
            b.bill_id, 
            b.table_id,
            b.hotel_type,
            b.bill_time,
            (
                SELECT SUM(h.price * h.quantity) 
                FROM held_items h 
                WHERE h.bill_id = b.bill_id
            ) AS payment_amount
        FROM bills b 
        WHERE b.status = 'active'
        ORDER BY bill_id DESC";

$result = $conn->query($sql);
$bills = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bills[] = $row;
    }
}
echo json_encode($bills);
$conn->close();
?>