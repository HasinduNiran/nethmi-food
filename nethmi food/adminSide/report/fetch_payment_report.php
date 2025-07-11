<?php
// Initialize the session
session_start();

// Error reporting for debugging
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Handle CORS if needed
header('Content-Type: application/json');

try {
    // Include database connection
    require_once "../config.php"; 
    
    if (!$link) {
        throw new Exception("Database connection failed");
    }

    // Get search parameters
    $paymentMethod = isset($_GET['payment_method']) ? trim($_GET['payment_method']) : '';
    $startDate = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
    $endDate = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;

    // Base SQL query
    $sql = "
        SELECT 
            bp.bill_id,
            bp.payment_method,
            b.payment_amount,
            bp.card_id,
            bp.created_at as payment_time,
            b.bill_time,
            b.customer_name
        FROM 
            bill_payments bp
        JOIN 
            bills b ON bp.bill_id = b.bill_id
        WHERE 
            1=1
    ";

    // Add payment method filter if provided
    if (!empty($paymentMethod)) {
        $sql .= " AND bp.payment_method = ?";
    }

    // Add date range if dates are provided
    if ($startDate) {
        $sql .= " AND DATE(b.bill_time) >= ?";
    }
    
    if ($endDate) {
        $sql .= " AND DATE(b.bill_time) <= ?";
    }

    // Order by
    $sql .= " ORDER BY b.bill_time DESC";

    // Prepare the statement
    $stmt = $link->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("SQL preparation failed: " . $link->error);
    }

    // Bind parameters based on conditions
    $types = '';
    $params = [];

    if (!empty($paymentMethod)) {
        $types .= 's';
        $params[] = $paymentMethod;
    }

    if ($startDate) {
        $types .= 's';
        $params[] = $startDate;
    }
    
    if ($endDate) {
        $types .= 's';
        $params[] = $endDate;
    }

    // Bind parameters if any
    if (!empty($params)) {
        $bindResult = $stmt->bind_param($types, ...$params);
        if (!$bindResult) {
            throw new Exception("Parameter binding failed: " . $stmt->error);
        }
    }

    // Execute the query
    $executeResult = $stmt->execute();
    
    if (!$executeResult) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }
    
    // Get result
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Result retrieval failed: " . $stmt->error);
    }
    
    // Fetch all rows as associative array
    $data = $result->fetch_all(MYSQLI_ASSOC);
    
    // Calculate totals
    $totalAmount = 0;
    $countByMethod = [];
    
    foreach($data as $item) {
        $totalAmount += $item['payment_amount'];
        
        $method = $item['payment_method'];
        if (!isset($countByMethod[$method])) {
            $countByMethod[$method] = [
                'count' => 0,
                'total' => 0
            ];
        }
        $countByMethod[$method]['count']++;
        $countByMethod[$method]['total'] += $item['payment_amount'];
    }
    
    // Add summary data
    $summary = [
        'totalAmount' => $totalAmount,
        'totalTransactions' => count($data),
        'methodBreakdown' => $countByMethod
    ];

    // Return data with summary as JSON
    echo json_encode([
        'payments' => $data,
        'summary' => $summary
    ]);

} catch (Exception $e) {
    // Return error message as proper JSON
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// Close connection if it exists
if (isset($link) && $link) {
    $link->close();
}
?>
