<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logged_account_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in',
        'records' => []
    ]);
    exit;
}

if (!isset($_GET['date']) || empty($_GET['date'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Date parameter is required',
        'records' => []
    ]);
    exit;
}

$selected_date = $_GET['date'];

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid date format. Use YYYY-MM-DD',
        'records' => []
    ]);
    exit;
}

$sql = "SELECT record_id, issued_amount, issued_reason, issued_date, issuer_account_id, issuer_name, created_at 
        FROM cash_disbursements 
        WHERE issued_date = ? 
        ORDER BY created_at DESC";

$stmt = $link->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Database preparation error: ' . $link->error,
        'records' => []
    ]);
    exit;
}

$stmt->bind_param("s", $selected_date);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'message' => 'Database execution error: ' . $stmt->error,
        'records' => []
    ]);
    exit;
}

$result = $stmt->get_result();
$records = [];
$total_amount = 0;

while ($row = $result->fetch_assoc()) {
    $row['issued_amount'] = number_format($row['issued_amount'], 2);
    $total_amount += floatval(str_replace(',', '', $row['issued_amount']));
    $row['issued_date'] = date('Y-m-d', strtotime($row['issued_date']));
    $row['created_at'] = date('Y-m-d H:i:s', strtotime($row['created_at']));
    
    $records[] = $row;
}

$response = [
    'success' => true,
    'message' => count($records) . ' record(s) found for ' . $selected_date,
    'records' => $records,
    'total_amount' => number_format($total_amount, 2),
    'record_count' => count($records),
    'selected_date' => $selected_date
];

echo json_encode($response);

$stmt->close();
$link->close();
?>