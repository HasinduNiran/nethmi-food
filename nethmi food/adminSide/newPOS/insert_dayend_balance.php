<?php
session_start();
require_once 'db_config.php'; // Include your database configuration

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

// Ensure database connection is established
if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['total_balance'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid data received']));
}

// Prepare the data with Sri Lanka timezone
$userId = $_SESSION['username'];
$today = date("Y-m-d"); // Sri Lanka date
$currentDateTime = date("Y-m-d H:i:s"); // Sri Lanka datetime for more precise tracking
$totalBalance = $data['total_balance'];

// Extract card machines data
$cardMachines = [];
for ($i = 1; $i <= 3; $i++) {
    // Only add card machine if terminal_id is provided
    if (!empty($data["terminal_id_$i"])) {
        $cardMachines[] = [
            'terminal_id' => $data["terminal_id_$i"] ?? '',
            'batch_number' => $data["batch_number_$i"] ?? '',
            'bank' => $data["bank_$i"] ?? '',
            'amount' => $data["card_amount_$i"] ?? 0
        ];
    }
}

// Convert card machine data to JSON
$cardMachinesJson = json_encode($cardMachines);

// Define denominations with default values
$denom5000 = isset($data['denomination_5000']) ? (int)$data['denomination_5000'] : 0;
$denom1000 = isset($data['denomination_1000']) ? (int)$data['denomination_1000'] : 0;
$denom500 = isset($data['denomination_500']) ? (int)$data['denomination_500'] : 0;
$denom100 = isset($data['denomination_100']) ? (int)$data['denomination_100'] : 0;
$denom50 = isset($data['denomination_50']) ? (int)$data['denomination_50'] : 0;
$denom20 = isset($data['denomination_20']) ? (int)$data['denomination_20'] : 0;
$denom10 = isset($data['denomination_10']) ? (int)$data['denomination_10'] : 0;
$denom5 = isset($data['denomination_5']) ? (int)$data['denomination_5'] : 0;
$denom2 = isset($data['denomination_2']) ? (int)$data['denomination_2'] : 0;
$denom1 = isset($data['denomination_1']) ? (int)$data['denomination_1'] : 0;

// Prepare the SQL query with timestamp field (if available in your table)
$query = "INSERT INTO day_end_balance (
    username, date, total_balance, 
    denomination_5000, denomination_1000, denomination_500, 
    denomination_100, denomination_50, denomination_20, 
    denomination_10, denomination_5, denomination_2, denomination_1,
    card_machines
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    die(json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . mysqli_error($conn)]));
}

// Bind parameters - define variables first, then pass by reference
mysqli_stmt_bind_param(
    $stmt, 
    "ssdiiiiiiiiiis",
    $userId, 
    $today, 
    $totalBalance,
    $denom5000, 
    $denom1000, 
    $denom500,
    $denom100, 
    $denom50, 
    $denom20,
    $denom10, 
    $denom5, 
    $denom2, 
    $denom1,
    $cardMachinesJson
);

// Execute the query
if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'success' => true, 
        'message' => 'Day End Balance saved successfully',
        'date' => $today,
        'timestamp' => $currentDateTime
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save Day End Balance: ' . mysqli_stmt_error($stmt)]);
}

// Close the statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>