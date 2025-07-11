<?php
// reopen_bill.php
// This file handles reopening a completed bill

require_once 'db_config.php'; // Use your existing connection

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST method is allowed']);
    exit;
}

// Get request body
$requestData = json_decode(file_get_contents('php://input'), true);
$billId = isset($requestData['bill_id']) ? intval($requestData['bill_id']) : 0;

if ($billId <= 0) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid bill ID']);
    exit;
}

try {
    // First check if the bill exists and is in completed status
    $checkQuery = "SELECT status FROM bills WHERE bill_id = $billId";
    $checkResult = $conn->query($checkQuery);
    
    if ($checkResult->num_rows === 0) {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'Bill not found']);
        exit;
    }
    
    $bill = $checkResult->fetch_assoc();
    
    if ($bill['status'] !== 'completed') {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'Only completed bills can be reopened']);
        exit;
    }
    
    // Update bill status to active
    $updateQuery = "UPDATE bills SET status = 'active' WHERE bill_id = $billId";
    $updateResult = $conn->query($updateQuery);
    
    if ($updateResult === false) {
        throw new Exception("Update failed: " . $conn->error);
    }
    
    // Return success
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Bill reopened successfully']);
    
} catch (Exception $e) {
    // Return error
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>