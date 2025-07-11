<?php
// fetch_sales_report.php
ob_start();

require_once "../config.php"; 

header('Content-Type: application/json');

try {
    if (!$link) {
        throw new Exception("Database connection failed.");
    }

    $staffId = $_GET['staff_id'] ?? '';
    $startDate = $_GET['start_date'] ?? '';
    $endDate = $_GET['end_date'] ?? '';

    $query = "SELECT 
                b.bill_id, 
                b.staff_id, 
                b.payment_method, 
                b.bill_time, 
                b.payment_amount, 
                b.paid_amount, 
                b.balance_amount, 
                b.total_before_discount, 
                b.discount_amount
              FROM bills b
              WHERE 1=1";

    $params = [];
    $types = '';

    if (!empty($staffId)) {
        $query .= " AND b.staff_id = ?";
        $params[] = $staffId;
        $types .= 'i';
    }
    if (!empty($startDate) && !empty($endDate)) {
        $query .= " AND b.bill_time BETWEEN ? AND ?";
        $params[] = $startDate . ' 00:00:00'; // Adjust for datetime
        $params[] = $endDate . ' 23:59:59';
        $types .= 'ss';
    }

    $stmt = $link->prepare($query);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $link->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    ob_end_clean();
    echo json_encode($data);
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$link->close();
?>