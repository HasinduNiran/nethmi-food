<?php
session_start();
require_once 'db_config.php'; // Include your database configuration

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

if (!isset($_SESSION['username'])) {
    die(json_encode(["success" => false, "message" => "User not logged in."]));
}

$user_id = $_SESSION['username'];

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!$input) {
    die(json_encode(["success" => false, "message" => "Invalid JSON payload."]));
}

$total_balance = isset($input['total_balance']) ? floatval($input['total_balance']) : null;
$denomination_5000 = isset($input['denomination_5000']) ? intval($input['denomination_5000']) : 0;
$denomination_1000 = isset($input['denomination_1000']) ? intval($input['denomination_1000']) : 0;
$denomination_500 = isset($input['denomination_500']) ? intval($input['denomination_500']) : 0;
$denomination_100 = isset($input['denomination_100']) ? intval($input['denomination_100']) : 0;
$denomination_50 = isset($input['denomination_50']) ? intval($input['denomination_50']) : 0;
$denomination_20 = isset($input['denomination_20']) ? intval($input['denomination_20']) : 0;
$denomination_10 = isset($input['denomination_10']) ? intval($input['denomination_10']) : 0;
$denomination_5 = isset($input['denomination_5']) ? intval($input['denomination_5']) : 0;
$denomination_2 = isset($input['denomination_2']) ? intval($input['denomination_2']) : 0;
$denomination_1 = isset($input['denomination_1']) ? intval($input['denomination_1']) : 0;

if (is_null($total_balance)) {
    die(json_encode(["success" => false, "message" => "Total balance is required."]));
}

// Get Sri Lanka date and time
$date = date("Y-m-d"); // Sri Lanka date
$current_datetime = date("Y-m-d H:i:s"); // Sri Lanka datetime for precise tracking

// // Fetch the store from the signup table based on the username
// $fetchStoreStmt = $conn->prepare("SELECT store FROM signup WHERE username = ?");
// $fetchStoreStmt->bind_param("s", $user_id);
// $fetchStoreStmt->execute();
// $fetchStoreStmt->bind_result($store);
// $fetchStoreStmt->fetch();
// $fetchStoreStmt->close();

// if (!$store) {
//     die(json_encode(["success" => false, "message" => "Store not found for the user."]));
// }

// Check if opening balance already exists for today (Sri Lanka date)
$checkStmt = $conn->prepare("SELECT id FROM opening_balance WHERE username = ? AND date = ?");
$checkStmt->bind_param("ss", $user_id, $date);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    die(json_encode([
        "success" => false, 
        "message" => "You have already submitted an opening balance today.",
        "date" => $date
    ]));
}
$checkStmt->close();

// Insert opening balance with Sri Lanka date
$stmt = $conn->prepare("INSERT INTO opening_balance (
    username, 
    date, 
    total_balance, 
    denomination_5000, 
    denomination_1000, 
    denomination_500, 
    denomination_100, 
    denomination_50, 
    denomination_20, 
    denomination_10, 
    denomination_5, 
    denomination_2, 
    denomination_1
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssdiidiiiiiii", 
    $user_id, 
    $date, 
    $total_balance, 
    $denomination_5000, 
    $denomination_1000, 
    $denomination_500, 
    $denomination_100, 
    $denomination_50, 
    $denomination_20, 
    $denomination_10, 
    $denomination_5, 
    $denomination_2, 
    $denomination_1,
    // $store
);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true, 
        "message" => "Data inserted successfully.",
        "date" => $date,
        "timestamp" => $current_datetime
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Error inserting data: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>