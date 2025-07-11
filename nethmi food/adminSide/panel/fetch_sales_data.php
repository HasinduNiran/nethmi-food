<?php
// Include database connection
require_once '../config.php';
$db_conn = $link;

// Set SQL_BIG_SELECTS=1 to allow large joins
$db_conn->query("SET SQL_BIG_SELECTS=1");

// Get filter parameters from the request
$staff_id = isset($_GET['staff_id']) ? $_GET['staff_id'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-2 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$paymentType = isset($_GET['payment_type']) ? $_GET['payment_type'] : '';
$productSearch = isset($_GET['product_search']) ? $_GET['product_search'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 100;
$offset = ($page - 1) * $itemsPerPage;

// Base query
$query = "
    SELECT
        b.bill_id,
        bi.product_name,
        bi.quantity,
        bi.price,
        (bi.price * bi.quantity) AS subtotal,
        b.bill_time,
        a.email AS staff_email,
        bp.payment_method
    FROM bill_items bi
    JOIN bills b ON bi.bill_id = b.bill_id
    LEFT JOIN accounts a ON b.staff_id = a.account_id
    LEFT JOIN bill_payments bp ON b.bill_id = bp.bill_id
    WHERE DATE(b.bill_time) BETWEEN ? AND ?
";

$params = ['ss', $startDate, $endDate];

// Apply filters
if (!empty($staff_id)) {
    $query .= " AND b.staff_id = ?";
    $params[0] .= 's';
    $params[] = $staff_id;
}
if (!empty($paymentType)) {
    $query .= " AND bp.payment_method = ?";
    $params[0] .= 's';
    $params[] = $paymentType;
}
if (!empty($productSearch)) {
    $query .= " AND bi.product_name LIKE ?";
    $params[0] .= 's';
    $params[] = '%' . $productSearch . '%';
}

$query .= " ORDER BY b.bill_time DESC LIMIT ? OFFSET ?";
$params[0] .= 'ii';
$params[] = $itemsPerPage;
$params[] = $offset;

// Prepare and execute the statement
$stmt = $db_conn->prepare($query);
if ($stmt) {
    $stmt->bind_param(...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $salesData = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $salesData = ['error' => 'Failed to prepare statement: ' . $db_conn->error];
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($salesData);
?>
