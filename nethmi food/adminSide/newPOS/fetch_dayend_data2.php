<?php
session_start(); // Start the session
require_once 'db_config.php'; 

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

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
    $today = date("Y-m-d"); // Sri Lanka date

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
        echo json_encode([
            'success' => true, 
            'total_balance' => $row['total_balance'],
            'date' => $today
        ]);
    } else {
        echo json_encode([
            'success' => true, 
            'total_balance' => '0.00',
            'date' => $today
        ]);
    }

    mysqli_stmt_close($stmt);
}

// Function to fetch day-end data (Total Net Amount, Total Discount, Total Bills, Total Bill Payments)
function fetchDayEndData($conn) {
    $today = date("Y-m-d"); // Sri Lanka date
    $current_datetime = date("Y-m-d H:i:s"); // Sri Lanka datetime

    $query = "
        SELECT 
            SUM(payment_amount) AS total_net,
            SUM(service_charge) AS total_service_charge,
            SUM(balance_amount) AS total_balance,
            SUM(discount_amount) AS total_discount,
            COUNT(bill_id) AS total_bills,
            SUM(payment_amount) AS total_bill_payment -- Assuming this is the same as total_net for now
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
            'total_balance' => $row['total_balance'] ? number_format($row['total_balance'], 2, '.', '') : '0.00',
            'total_discount' => $row['total_discount'] ? number_format($row['total_discount'], 2, '.', '') : '0.00',
            'total_bills' => $row['total_bills'] ?: '0',
            'total_bill_payment' => $row['total_bill_payment'] ? number_format($row['total_bill_payment'], 2, '.', '') : '0.00',
            'total_service_charge' => $row['total_service_charge'] ? number_format($row['total_service_charge'], 2, '.', '') : '0.00',
            'date' => $today,
            'timestamp' => $current_datetime
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'total_net' => '0.00',
            'total_discount' => '0.00',
            'total_bills' => '0',
            'total_bill_payment' => '0.00',
            'total_service_charge' => '0.00',
            'date' => $today,
            'timestamp' => $current_datetime
        ]);
    }

    mysqli_stmt_close($stmt);
}

function fetchDayEndDisbursements($conn) {
    $today = date("Y-m-d"); 
    $query = "
        SELECT 
            SUM(issued_amount) AS total_disbursements,
            COUNT(*) AS total_disbursement_count
        FROM cash_disbursements 
        WHERE DATE(created_at) = ?
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
            'total_disbursements' => $row['total_disbursements'] ? number_format($row['total_disbursements'], 2, '.', '') : '0.00',
            'total_disbursement_count' => $row['total_disbursement_count'] ?: '0'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'total_disbursements' => '0.00',
            'total_disbursement_count' => '0'
        ]);
    }
    
    mysqli_stmt_close($stmt);
}

function fetchDayEndTotalExternalCash($conn) {
    $today = date("Y-m-d"); 
    $query = "
        SELECT 
            SUM(received_amount) AS total_external_income,
            COUNT(*) AS total_external_income_count
        FROM cash_receipts
        WHERE DATE(received_date) = ?
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
            'total_external_income' => $row['total_external_income'] ? number_format($row['total_external_income'], 2, '.', '') : '0.00',
            'total_external_income_count' => $row['total_external_income_count'] ?: '0'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'total_external_income' => '0.00',
            'total_external_income_count' => '0'
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
    $today = date("Y-m-d"); // Sri Lanka date

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
            'total_balance' => number_format($row['total_balance'], 2, '.', ''),
            'date' => $today
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'total_balance' => '0.00',
            'date' => $today
        ]);
    }

    mysqli_stmt_close($stmt);
}

// Function to fetch today's cash drawer payment
function fetchTodayCashDrawerPayment($conn) {
    // Placeholder: Replace with actual logic if you have a table for cash drawer payments
    echo json_encode(['success' => true, 'total_cash_drawer' => '0.00']);
}

// Placeholder for fetchPettyCash (to be implemented if needed)
function fetchPettyCash($conn) {
    // Placeholder: Replace with actual logic if you have a table for petty cash
    echo json_encode(['success' => true, 'petty_cash' => '0.00']);
}

// Function to fetch card machine details from day_end_balance
function fetchCardMachineTotal($conn) {
    if (!isset($_SESSION['username'])) {
        echo json_encode(['success' => false, 'card_machine_total' => '0.00', 'message' => 'User not logged in']);
        return;
    }

    $username = $_SESSION['username'];
    $today = date("Y-m-d"); // Sri Lanka date

    $query = "SELECT card_machines FROM day_end_balance WHERE username = ? AND date = ? ORDER BY id DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        echo json_encode(['success' => false, 'card_machine_total' => '0.00', 'message' => 'Failed to prepare statement']);
        return;
    }

    mysqli_stmt_bind_param($stmt, "ss", $username, $today);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $cardMachineTotal = 0;
    $cardMachinesArray = [];
    
    if ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['card_machines'])) {
            // Parse JSON data from card_machines column
            $cardMachines = json_decode($row['card_machines'], true);
            
            // Process each card machine
            if (is_array($cardMachines)) {
                foreach ($cardMachines as $machine) {
                    $amount = floatval($machine['amount'] ?? 0);
                    $cardMachineTotal += $amount;
                    
                    // Add to array with display details
                    $cardMachinesArray[] = [
                        'terminal_id' => $machine['terminal_id'] ?? 'Unknown',
                        'batch_number' => $machine['batch_number'] ?? '',
                        'bank' => $machine['bank'] ?? 'Unknown Bank',
                        'amount' => number_format($amount, 2, '.', '')
                    ];
                }
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'card_machines' => $cardMachinesArray,
        'card_machine_total' => number_format($cardMachineTotal, 2, '.', ''),
        'date' => $today
    ]);
    
    mysqli_stmt_close($stmt);
}

// Function to fetch Uber and PickMe payments
function fetchDeliveryAppPayments($conn) {
    $today = date("Y-m-d"); // Sri Lanka date
    
    // Query for Uber payments (hotel_type = 4)
    $uberQuery = "SELECT SUM(payment_amount) AS total FROM bills 
                 WHERE DATE(payment_time) = ? AND hotel_type = 4 AND status = 'completed'";
    $uberStmt = mysqli_prepare($conn, $uberQuery);
    if (!$uberStmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare Uber statement']);
        return;
    }
    
    mysqli_stmt_bind_param($uberStmt, "s", $today);
    mysqli_stmt_execute($uberStmt);
    $uberResult = mysqli_stmt_get_result($uberStmt);
    
    $uberPayment = 0;
    if ($uberRow = mysqli_fetch_assoc($uberResult)) {
        $uberPayment = floatval($uberRow['total'] ?? 0);
    }
    mysqli_stmt_close($uberStmt);
    
    // Query for PickMe payments (hotel_type = 6)
    $pickmeQuery = "SELECT SUM(payment_amount) AS total FROM bills 
                   WHERE DATE(payment_time) = ? AND hotel_type = 6 AND status = 'completed'";
    $pickmeStmt = mysqli_prepare($conn, $pickmeQuery);
    if (!$pickmeStmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare PickMe statement']);
        return;
    }
    
    mysqli_stmt_bind_param($pickmeStmt, "s", $today);
    mysqli_stmt_execute($pickmeStmt);
    $pickmeResult = mysqli_stmt_get_result($pickmeStmt);
    
    $pickmePayment = 0;
    if ($pickmeRow = mysqli_fetch_assoc($pickmeResult)) {
        $pickmePayment = floatval($pickmeRow['total'] ?? 0);
    }
    mysqli_stmt_close($pickmeStmt);
    
    echo json_encode([
        'success' => true,
        'uber_payment' => number_format($uberPayment, 2, '.', ''),
        'pickme_payment' => number_format($pickmePayment, 2, '.', ''),
        'date' => $today
    ]);
}

// Function to fetch payment method totals
function fetchPaymentMethodTotals($conn) {
    $today = date('Y-m-d'); // Sri Lanka date
    $response = [
        'success' => true,
        'cash' => '0.00',
        'credit_card' => '0.00',
        'debit' => '0.00',
        'credit' => '0.00',
        'bank' => '0.00',
        'card' => '0.00',  // Added card payment method
        'date' => $today
    ];
    
    // $query = "SELECT payment_method, SUM(amount) as total FROM bill_payments 
    //           WHERE DATE(created_at) = ? 
    //           GROUP BY payment_method";
    
    // $stmt = mysqli_prepare($conn, $query);
    // if (!$stmt) {
    //     echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
    //     return;
    // }

    mysqli_stmt_bind_param($stmt, "s", $today);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $method = strtolower($row['payment_method']);
        $amount = floatval($row['total']);
        
        switch ($method) {
            case 'cash':
                $response['cash'] = number_format($amount, 2, '.', '');
                break;
            case 'credit': // Credit card payments stored as 'credit'
                $response['credit'] = number_format($amount, 2, '.', '');
                $response['card'] = number_format($amount, 2, '.', ''); // Also store as 'card'
                break;
            case 'bank': // Bank payments stored as 'bank'
                $response['bank'] = number_format($amount, 2, '.', '');
                break;
            case 'debit':
                $response['debit'] = number_format($amount, 2, '.', '');
                break;
            case 'cre': // Credit payments stored as 'cre'
                $response['cre'] = number_format($amount, 2, '.', '');
                break;
        }
    }
    
    mysqli_stmt_close($stmt);
    echo json_encode($response);
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
} elseif (isset($_POST['getTotalCashDisbursements']) && $_POST['getTotalCashDisbursements'] === 'true') {
    fetchDayEndDisbursements($conn);
} elseif (isset($_POST['getTotalExternalCashIncomes']) && $_POST['getTotalExternalCashIncomes'] === 'true') {
    fetchDayEndTotalExternalCash($conn);
} 
// Handle card machine total request
elseif (isset($_POST['getCardMachineTotal']) && $_POST['getCardMachineTotal'] === 'true') {
    fetchCardMachineTotal($conn);
} 
// Handle delivery app payments request
elseif (isset($_POST['getDeliveryAppPayments']) && $_POST['getDeliveryAppPayments'] === 'true') {
    fetchDeliveryAppPayments($conn);
} 
// Handle payment method totals request
elseif (isset($_POST['getPaymentMethodTotals']) && $_POST['getPaymentMethodTotals'] === 'true') {
    fetchPaymentMethodTotals($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

// Close the database connection
mysqli_close($conn);
?>