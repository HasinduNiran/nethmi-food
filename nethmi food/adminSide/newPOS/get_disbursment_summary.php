<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logged_account_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in',
        'total' => '0.00',
        'count' => 0
    ]);
    exit;
}

$sri_lanka_timezone = new DateTimeZone('Asia/Colombo');
$current_date = new DateTime('now', $sri_lanka_timezone);
$selected_date = isset($_GET['date']) ? $_GET['date'] : $current_date->format('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid date format. Use YYYY-MM-DD',
        'total' => '0.00',
        'count' => 0
    ]);
    exit;
}

$sql = "SELECT 
            COUNT(*) as record_count,
            COALESCE(SUM(issued_amount), 0) as total_amount
        FROM cash_disbursements 
        WHERE issued_date = ?";

$stmt = $link->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Database preparation error: ' . $link->error,
        'total' => '0.00',
        'count' => 0
    ]);
    exit;
}

$stmt->bind_param("s", $selected_date);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'message' => 'Database execution error: ' . $stmt->error,
        'total' => '0.00',
        'count' => 0
    ]);
    exit;
}

$result = $stmt->get_result();
$summary = $result->fetch_assoc();

$response = [
    'success' => true,
    'total' => number_format($summary['total_amount'], 2),
    'count' => intval($summary['record_count']),
    'date' => $selected_date,
    'message' => 'Summary retrieved successfully for ' . $selected_date
];

$stats_sql = "SELECT 
                COUNT(*) as total_records,
                COALESCE(SUM(issued_amount), 0) as grand_total,
                MIN(issued_date) as first_record_date,
                MAX(issued_date) as last_record_date
              FROM cash_disbursements";

$stats_result = $link->query($stats_sql);
if ($stats_result && $stats = $stats_result->fetch_assoc()) {
    $response['statistics'] = [
        'total_records_all_time' => intval($stats['total_records']),
        'grand_total_all_time' => number_format($stats['grand_total'], 2),
        'first_record_date' => $stats['first_record_date'],
        'last_record_date' => $stats['last_record_date']
    ];
}

echo json_encode($response);
$stmt->close();
$link->close();
?>