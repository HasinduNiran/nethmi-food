<?php
require_once 'db_config.php'; // Use your existing connection

// Get query parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
$offset = ($page - 1) * $limit;

// Get filter parameters
$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : '';
$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the query
$query = "
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
        c.name as customer_name
    FROM 
        bills b
    LEFT JOIN 
        customers c ON b.customer_id = c.customer_id
    WHERE 1=1
";

// Apply filters
if (!empty($dateFrom)) {
    $query .= " AND DATE(b.bill_time) >= '$dateFrom'";
}

if (!empty($dateTo)) {
    $query .= " AND DATE(b.bill_time) <= '$dateTo'";
}

if (!empty($status)) {
    $query .= " AND b.status = '$status'";
}

if (!empty($search)) {
    $searchParam = "%$search%";
    $query .= " AND (b.bill_id LIKE '$searchParam' OR c.name LIKE '$searchParam')";
}

// Order by bill_time descending (most recent first)
$query .= " ORDER BY b.bill_time DESC";

// Add pagination
$query .= " LIMIT $limit OFFSET $offset";

// Execute the query using the existing mysqli connection
try {
    $result = $conn->query($query);
    
    if ($result === false) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    
    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode(['transactions' => $transactions]);
} catch (Exception $e) {
    // Return error
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>