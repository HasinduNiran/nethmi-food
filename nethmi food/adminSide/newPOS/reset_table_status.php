<?php
require_once 'db_config.php';

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$table_id = $data['table_id'];

// Start transaction
$conn->begin_transaction();

try {
    // Check if table exists and is in dirty status
    $check_sql = "SELECT status FROM restaurant_tables WHERE table_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $table_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if ($row['status'] === 'dirty') {
            // Update table status to 'available'
            $update_sql = "UPDATE restaurant_tables SET status = 'available' WHERE table_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $table_id);
            $update_stmt->execute();
            
            // Commit transaction
            $conn->commit();
            
            echo json_encode(["success" => true]);
        } else {
            // Table is not in 'dirty' status
            $conn->rollback();
            echo json_encode([
                "success" => false, 
                "message" => "Table is not in 'dirty' status. Current status: " . $row['status']
            ]);
        }
    } else {
        // Table not found
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Table not found"]);
    }
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>