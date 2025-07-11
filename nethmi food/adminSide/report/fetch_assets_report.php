<?php
// fetch_assets_report.php
ob_start();

require_once "../config.php"; 

header('Content-Type: application/json');

try {
    if (!$link) {
        throw new Exception("Database connection failed.");
    }

    $search = $_GET['search'] ?? '';
    $startDate = $_GET['start_date'] ?? '';
    $endDate = $_GET['end_date'] ?? '';

    $query = "SELECT 
                a.asset_name, 
                a.qty AS quantity, 
                a.description, 
                a.enter_date, 
                a.created_at
              FROM assets a
              WHERE 1=1";

    $params = [];
    $types = '';

    if (!empty($search)) {
        $query .= " AND a.asset_name LIKE ?";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $types .= 's';
    }
    if (!empty($startDate) && !empty($endDate)) {
        $query .= " AND a.enter_date BETWEEN ? AND ?";
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