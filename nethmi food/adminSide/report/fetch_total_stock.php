<?php
// Set the content type to JSON
header('Content-Type: application/json');

// Prevent any unexpected output (e.g., warnings) from corrupting JSON
ob_start();

require_once '../config.php';

// Get parameters
$supplier = $_GET['supplier'] ?? '';
$search = $_GET['search'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

// Base query
$query = "SELECT 
            item_name, 
            quantity, 
            cost_price, 
            item_price, 
            bakery_category, 
            supplier_id 
          FROM bakery_menu_stocks 
          WHERE 1=1";

$params = [];
$types = '';

// Add filters
if (!empty($supplier)) {
    $query .= " AND supplier_id = ?";
    $params[] = $supplier;
    $types .= 'i';
}

if (!empty($search)) {
    $query .= " AND (item_name LIKE ? OR item_id LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
}

if (!empty($startDate) && !empty($endDate)) {
    $query .= " AND created_at BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
    $types .= 'ss';
}

// Prepare and execute
try {
    $stmt = $link->prepare($query);
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $link->error);
    }

    if (count($params) > 0) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);

    // Clear any output buffer and send JSON
    ob_end_clean();
    echo json_encode($data);
} catch (Exception $e) {
    // Clear buffer and send error as JSON
    ob_end_clean();
    echo json_encode(['error' => $e->getMessage()]);
}

// Clean up
$stmt->close();
$link->close();
?>