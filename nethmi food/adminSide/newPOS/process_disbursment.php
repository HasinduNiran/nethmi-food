<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logged_account_id']) || !isset($_SESSION['logged_staff_name'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

if (!isset($_POST['issued_amount']) || !isset($_POST['issued_reason'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

$issued_amount = trim($_POST['issued_amount']);
$issued_reason = trim($_POST['issued_reason']);

if (!is_numeric($issued_amount) || $issued_amount <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid amount. Please enter a valid positive number.'
    ]);
    exit;
}

if (empty($issued_reason)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a reason for the disbursement'
    ]);
    exit;
}

$sri_lanka_timezone = new DateTimeZone('Asia/Colombo');
$current_date = new DateTime('now', $sri_lanka_timezone);
$issued_date = $current_date->format('Y-m-d');

$issuer_account_id = $_SESSION['logged_account_id'];
$issuer_name = $_SESSION['logged_staff_name'];

$sql = "INSERT INTO cash_disbursements (issued_amount, issued_reason, issued_date, issuer_account_id, issuer_name) VALUES (?, ?, ?, ?, ?)";
$stmt = $link->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Database preparation error: ' . $link->error
    ]);
    exit;
}

$stmt->bind_param("dssis", $issued_amount, $issued_reason, $issued_date, $issuer_account_id, $issuer_name);

if ($stmt->execute()) {
    $record_id = $link->insert_id;
    echo json_encode([
        'success' => true,
        'message' => 'Disbursement record added successfully (ID: ' . $record_id . ')',
        'record_id' => $record_id,
        'amount' => number_format($issued_amount, 2),
        'date' => $issued_date
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $stmt->error
    ]);
}

$stmt->close();
$link->close();
?>