<?php
// Turn on error reporting for debugging but buffer it to prevent output
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Start the session
    session_start();
    
    // Include database configuration
    require_once 'db_config.php';
    
    // Check if the request is POST
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception('Invalid request method');
    }
    
    // Check database connection
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Get and validate the JSON data
    $jsonInput = file_get_contents('php://input');
    if (empty($jsonInput)) {
        throw new Exception('No data received');
    }
    
    $data = json_decode($jsonInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }
    
    if (!isset($data['saveDayEndReport'])) {
        throw new Exception('Missing required parameter: saveDayEndReport');
    }
    
    // Prepare the data with validation
    $username = isset($data['username']) ? $data['username'] : 'N/A';
    date_default_timezone_set('Asia/Colombo');
    $today = date("Y-m-d H:i:s");
    
    // Extract and convert all required fields
    $fields = [
        'opening_balance' => 0.00,
        'total_gross' => 0.00,
        'total_net' => 0.00,
        'total_discount' => 0.00,
        'total_bills' => 0,
        'total_cash' => 0.00,
        'total_bank' => 0.00,
        'total_card' => 0.00,
        'total_credit_card' => 0.00,
        'total_debit_card' => 0.00,
        'total_credit' => 0.00,
        'bill_payment' => 0.00,
        'cash_drawer' => 0.00,
        'voucher_payment' => 0.00,
        'free_payment' => 0.00,
        'total_balance' => 0.00,
        'petty_cash' => 0.00,
        'day_end_hand_balance' => 0.00,
        'cash_balance' => 0.00,
        'today_balance' => 0.00,
        'difference_hand' => 0.00,
        'card_machine_total' => 0.00,
        'uber_payment' => 0.00,
        'pickme_payment' => 0.00,
        'total_dispatched_balance' => 0.00,
        'service_charge_income' => 0.00,
        'total_external_income' => 0.00,
        'non_cash_total' => 0.00,
        'cash_in_hand' => 0.00,
    ];
    
    foreach ($fields as $field => $default) {
        if (is_int($default)) {
            $$field = isset($data[$field]) ? intval($data[$field]) : $default;
        } else {
            $$field = isset($data[$field]) ? floatval($data[$field]) : $default;
        }
    }
    
    // Ensure we have the correct columns in our query
    // Check if the table structure matches our expected columns
    $tableCheckQuery = "DESCRIBE day_end_shift_report";
    $tableResult = mysqli_query($conn, $tableCheckQuery);
    
    if (!$tableResult) {
        throw new Exception('Failed to check table structure: ' . mysqli_error($conn));
    }
    
    $columns = [];
    while ($row = mysqli_fetch_assoc($tableResult)) {
        $columns[] = $row['Field'];
    }
    
    // Dynamically build the query based on available columns
    $insertColumns = ["username", "created_at"];
    $insertValues = ["?", "?"];
    $bindTypes = "ss"; // string, string for username and created_at
    $bindParams = [$username, $today];
    
    foreach ($fields as $field => $default) {
        if (in_array($field, $columns)) {
            $insertColumns[] = $field;
            $insertValues[] = "?";
            $bindTypes .= is_int($default) ? "i" : "d"; // i for int, d for float/double
            $bindParams[] = $$field;
        }
    }
    
    $query = "INSERT INTO day_end_shift_report (" . implode(", ", $insertColumns) . ") 
              VALUES (" . implode(", ", $insertValues) . ")";
    
    // Prepare statement
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . mysqli_error($conn));
    }
    
    // Bind parameters dynamically
    $bindParamsRef = [];
    $bindParamsRef[] = &$bindTypes;
    
    foreach ($bindParams as $key => $value) {
        $bindParamsRef[] = &$bindParams[$key];
    }
    
    call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bindParamsRef));
    
    // Execute the query
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to execute query: ' . mysqli_stmt_error($stmt));
    }
    
    // Clean up
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    // Clear any output buffer
    ob_end_clean();
    
    // Return success
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Day End Shift Report saved successfully']);
    
} catch (Exception $e) {
    // Capture the error output
    $error = ob_get_clean();
    
    // Log the detailed error
    error_log("Save Day End Report Error: " . $e->getMessage() . "\n" . $error);
    
    // Return a clean JSON error response
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'debug' => $error]);
}
?>