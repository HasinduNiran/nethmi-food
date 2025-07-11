<?php
session_start(); // Start the session
require_once 'db_config.php'; 

// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Ensure database connection is established
if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Function to fetch username
function fetchUsername() {
    if (isset($_SESSION['username'])) {
        echo json_encode(['success' => true, 'username' => $_SESSION['username']]);
    } else {
        echo json_encode(['success' => false, 'username' => 'N/A', 'message' => 'User not logged in']);
    }
}

// Function to fetch opening balance
function fetchOpeningBalance($conn) {
    if (!isset($_SESSION['username'])) {
        echo json_encode(['success' => false, 'total_balance' => '0.00', 'message' => 'User not logged in']);
        return;
    }

    $username = $_SESSION['username'];
    $today = date("Y-m-d");

    $query = "SELECT total_balance FROM opening_balance WHERE username = ? AND date <= ? ORDER BY date DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        echo json_encode(['success' => false, 'total_balance' => '0.00', 'message' => 'Failed to prepare statement']);
        return;
    }

    mysqli_stmt_bind_param($stmt, "ss", $username, $today);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'total_balance' => $row['total_balance']]);
    } else {
        echo json_encode(['success' => true, 'total_balance' => '0.00']);
    }

    mysqli_stmt_close($stmt);
}

// Function to fetch day-end data (Total Net Amount, Total Discount, Total Bills, Total Bill Payments)
function fetchDayEndData($conn) {
    $today = date("Y-m-d"); // Current date

    $query = "
        SELECT 
            SUM(paid_amount) AS total_net,
            SUM(discount_amount) AS total_discount,
            COUNT(bill_id) AS total_bills,
            SUM(paid_amount) AS total_bill_payment -- Assuming this is the same as total_net for now
        FROM bills 
        WHERE DATE(bill_time) = ?
    ";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
        return;
    }

    mysqli_stmt_bind_param($stmt, "s", $today);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'success' => true,
            'total_net' => $row['total_net'] ? number_format($row['total_net'], 2, '.', '') : '0.00',
            'total_discount' => $row['total_discount'] ? number_format($row['total_discount'], 2, '.', '') : '0.00',
            'total_bills' => $row['total_bills'] ?: '0',
            'total_bill_payment' => $row['total_bill_payment'] ? number_format($row['total_bill_payment'], 2, '.', '') : '0.00'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'total_net' => '0.00',
            'total_discount' => '0.00',
            'total_bills' => '0',
            'total_bill_payment' => '0.00'
        ]);
    }

    mysqli_stmt_close($stmt);
}

// Function to fetch day-end hand balance
function fetchDayEndHandBalance($conn) {
    if (!isset($_SESSION['username'])) {
        echo json_encode(['success' => false, 'total_balance' => '0.00', 'message' => 'User not logged in']);
        return;
    }

    $username = $_SESSION['username'];
    $today = date("Y-m-d");

    $query = "SELECT total_balance FROM day_end_balance WHERE username = ? AND date = ? ORDER BY date DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        echo json_encode(['success' => false, 'total_balance' => '0.00', 'message' => 'Failed to prepare statement']);
        return;
    }

    mysqli_stmt_bind_param($stmt, "ss", $username, $today);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'success' => true,
            'total_balance' => number_format($row['total_balance'], 2, '.', '')
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'total_balance' => '0.00'
        ]);
    }

    mysqli_stmt_close($stmt);
}

// Placeholder for fetchTodayCashDrawerPayment (to be implemented if needed)
function fetchTodayCashDrawerPayment($conn) {
    // Placeholder: Replace with actual logic if you have a table for cash drawer payments
    echo json_encode(['success' => true, 'total_cash_drawer' => '0.00']);
}

// Placeholder for fetchPettyCash (to be implemented if needed)
function fetchPettyCash($conn) {
    // Placeholder: Replace with actual logic if you have a table for petty cash
    echo json_encode(['success' => true, 'petty_cash' => '0.00']);
}

// Handle POST requests
if (isset($_POST['getUsername']) && $_POST['getUsername'] === 'true') {
    fetchUsername();
} elseif (isset($_POST['getOpeningBalance']) && $_POST['getOpeningBalance'] === 'true') {
    fetchOpeningBalance($conn);
} elseif (isset($_POST['getDayEndData']) && $_POST['getDayEndData'] === 'true') {
    fetchDayEndData($conn);
} elseif (isset($_POST['getDayEndHandBalance']) && $_POST['getDayEndHandBalance'] === 'true') {
    fetchDayEndHandBalance($conn);
} elseif (isset($_POST['getTodayCashDrawerPayment']) && $_POST['getTodayCashDrawerPayment'] === 'true') {
    fetchTodayCashDrawerPayment($conn);
} elseif (isset($_POST['getPettyCashExpenses']) && $_POST['getPettyCashExpenses'] === 'true') {
    fetchPettyCash($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

// Close the database connection
mysqli_close($conn);
?>