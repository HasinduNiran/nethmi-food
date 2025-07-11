<?php
require_once 'db_config.php';

$orders = [];

$order_query = "SELECT order_id, table_id, item_count, date, state 
                FROM dine_in_orders 
                WHERE state = 'pending'";
$order_result = $conn->query($order_query);

if ($order_result->num_rows > 0) {
    while ($order = $order_result->fetch_assoc()) {
        $order_id = $order['order_id'];
        $items_query = "SELECT item_id, item_name, portion_size, quantity 
                        FROM dine_in_order_items 
                        WHERE order_id = $order_id";
        $items_result = $conn->query($items_query);
        
        $order_items = [];
        while ($item = $items_result->fetch_assoc()) {
            $order_items[] = $item;
        }
        
        $order['order_items'] = $order_items;
        $orders[] = $order;
    }
}

header('Content-Type: application/json');
echo json_encode($orders);

$conn->close();
?>