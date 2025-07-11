<?php
// Start output buffering to prevent stray output
ob_start();

require_once "../config.php"; 

// Set content type
header('Content-Type: application/json');

// Error handling
try {
    if (!$link) {
        throw new Exception("Database connection failed.");
    }

    $category = $_GET['category'] ?? '';
    $search = $_GET['search'] ?? '';
    $startDate = $_GET['start_date'] ?? '';
    $endDate = $_GET['end_date'] ?? '';

    // Adjusted query to match new table structure
    $query = "SELECT 
                i.iteamname AS item_name, 
                i.qty AS quantity, 
                i.mesuer AS measure, 
                i.value, 
                i.manufacturedate, 
                i.expierdate AS expire_date, 
                i.wastage, 
                i.category
              FROM inventory i
              WHERE 1=1";

    $params = [];
    $types = '';

    if (!empty($category)) {
        $query .= " AND i.category = ?";
        $params[] = $category;
        $types .= 's';
    }
    if (!empty($search)) {
        $query .= " AND i.iteamname LIKE ?";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $types .= 's';
    }
    if (!empty($startDate) && !empty($endDate)) {
        $query .= " AND i.expierdate BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
        $types .= 'ss';
    }

    $stmt = $link->prepare($query);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $link->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    ob_end_clean();
    echo json_encode($data);
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$link->close();
?>