<?php
// Assuming you'll save this as a separate file get_bill_details.php
require_once 'db_config.php'; // Use your existing connection

// Get the bill_id from the request
$billId = isset($_GET['bill_id']) ? intval($_GET['bill_id']) : 0;
if ($billId <= 0) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid bill ID']);
    exit;
}

try {
    // Fetch bill details
    $billQuery = "
        SELECT 
            b.bill_id, 
            b.table_id, 
            b.bill_time, 
            b.payment_time, 
            b.status, 
            b.payment_amount, 
            b.paid_amount, 
            b.balance_amount, 
            b.discount_amount,
            b.hotel_type,
            c.name as customer_name,
            c.phone_number as customer_phone
        FROM 
            bills b
        LEFT JOIN 
            customers c ON b.customer_id = c.customer_id
        WHERE 
            b.bill_id = $billId
    ";
    
    $billResult = $conn->query($billQuery);
    
    if ($billResult->num_rows === 0) {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'Bill not found']);
        exit;
    }
    
    $bill = $billResult->fetch_assoc();
    
    // Fetch bill items - simplified to use price and product_name from bill_items table
    $itemsQuery = "
        SELECT 
            bill_item_id,
            item_id,
            product_name,
            price,
            quantity,
            status
        FROM 
            bill_items
        WHERE 
            bill_id = $billId
    ";
    
    $itemsResult = $conn->query($itemsQuery);
    
    if (!$itemsResult) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $items = [];
    
    while ($item = $itemsResult->fetch_assoc()) {
        // Use product_name directly for display
        $item['item_name'] = $item['product_name'];
        $item['display_name'] = $item['product_name'];
        
        // Since we're not getting category from menu tables, set a default
        $item['item_category'] = 'Not categorized';
        
        $items[] = $item;
    }
    
    // Fetch bill payments
    $paymentsQuery = "
        SELECT 
            id,
            bill_id,
            payment_method,
            amount,
            card_id,
            created_at
        FROM 
            bill_payments
        WHERE 
            bill_id = $billId
        ORDER BY 
            created_at ASC
    ";
    
    $paymentsResult = $conn->query($paymentsQuery);
    $payments = [];
    
    while ($payment = $paymentsResult->fetch_assoc()) {
        $payments[] = $payment;
    }
    
    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'bill' => $bill,
        'items' => $items,
        'payments' => $payments
    ]);
    
} catch (Exception $e) {
    // Return error
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>