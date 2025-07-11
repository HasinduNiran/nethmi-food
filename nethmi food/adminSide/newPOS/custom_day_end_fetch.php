<?php
require_once '../config.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'reports' => [],
    'report' => null
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    switch ($action) {
        case 'getReportsByDate':
            getReportsByDate($link, $response);
            break;
        case 'getReportById':
            getReportById($link, $response);
            break;
        default:
            $response['message'] = 'Invalid action specified';
            break;
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);

function getReportsByDate($link, &$response) {
    if (!isset($_POST['date']) || empty($_POST['date'])) {
        $response['message'] = 'Date parameter is required';
        return;
    }

    $date = $_POST['date'];
    
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $response['message'] = 'Invalid date format. Please use YYYY-MM-DD';
        return;
    }

    try {
        $query = "SELECT * FROM day_end_shift_report 
                 WHERE DATE(created_at) = ? 
                 ORDER BY created_at DESC";
        
        $stmt = $link->prepare($query);
        $stmt->bind_param('s', $date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $reports = [];
            while ($row = $result->fetch_assoc()) {
                $reports[] = $row;
            }
            
            $response['reports'] = $reports;
            $response['success'] = true;
        } else {
            $response['message'] = 'No reports found for this date';
            $response['reports'] = [];
            $response['success'] = true; 
        }
    } catch (Exception $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
}


function getReportById($link, &$response) {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $response['message'] = 'Report ID is required';
        return;
    }
    
    $id = intval($_POST['id']);
    
    try {
        $query = "SELECT * FROM day_end_shift_report WHERE id = ?";
        
        $stmt = $link->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $report = $result->fetch_assoc();
            $response['report'] = $report;
            $response['success'] = true;
        } else {
            $response['message'] = 'Report not found';
        }
    } catch (Exception $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
}
?>