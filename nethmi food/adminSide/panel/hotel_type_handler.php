<?php
// Include the database connection file
require_once '../config.php';

// Response array
$response = [
    'success' => false,
    'message' => '',
    'hotel_types' => []
];

// Check connection
if($link->connect_error) {
    $response['message'] = "Connection failed: " . $link->connect_error;
    echo json_encode($response);
    exit;
}

// Check for AJAX request
if (!empty($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'get':
            // Get all hotel types or search by name
            if (!empty($_POST['search'])) {
                $search = '%' . $_POST['search'] . '%';
                $stmt = $link->prepare("SELECT * FROM holetype WHERE name LIKE ?");
                $stmt->bind_param("s", $search);
            } else {
                $stmt = $link->prepare("SELECT * FROM holetype ORDER BY id");
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $hotelTypes = [];
            
            while ($row = $result->fetch_assoc()) {
                $hotelTypes[] = $row;
            }
            
            $response['success'] = true;
            $response['hotel_types'] = $hotelTypes;
            break;
            
        case 'add':
            // Add new hotel type
            if (!empty($_POST['name'])) {
                $name = trim($_POST['name']);
                
                // Check if hotel type already exists
                $stmt = $link->prepare("SELECT COUNT(*) as count FROM holetype WHERE name = ?");
                $stmt->bind_param("s", $name);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                if ($row['count'] > 0) {
                    $response['message'] = "Hotel type '$name' already exists!";
                } else {
                    // Insert new hotel type
                    $stmt = $link->prepare("INSERT INTO holetype (name) VALUES (?)");
                    $stmt->bind_param("s", $name);
                    $result = $stmt->execute();
                    
                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = "Hotel type '$name' added successfully!";
                    } else {
                        $response['message'] = "Failed to add hotel type: " . $link->error;
                    }
                }
            } else {
                $response['message'] = "Hotel type name is required.";
            }
            break;
            
        case 'update':
            // Update hotel type
            if (!empty($_POST['id']) && !empty($_POST['name'])) {
                $id = $_POST['id'];
                $name = trim($_POST['name']);
                
                // Check if hotel type already exists (excluding the one being updated)
                $stmt = $link->prepare("SELECT COUNT(*) as count FROM holetype WHERE name = ? AND id != ?");
                $stmt->bind_param("si", $name, $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                if ($row['count'] > 0) {
                    $response['message'] = "Hotel type '$name' already exists!";
                } else {
                    // Update hotel type
                    $stmt = $link->prepare("UPDATE holetype SET name = ? WHERE id = ?");
                    $stmt->bind_param("si", $name, $id);
                    $result = $stmt->execute();
                    
                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = "Hotel type updated successfully!";
                    } else {
                        $response['message'] = "Failed to update hotel type: " . $link->error;
                    }
                }
            } else {
                $response['message'] = "Hotel type ID and name are required.";
            }
            break;
            
        case 'delete':
            // Delete hotel type
            if (!empty($_POST['id'])) {
                $id = $_POST['id'];
                
                $stmt = $link->prepare("DELETE FROM holetype WHERE id = ?");
                $stmt->bind_param("i", $id);
                $result = $stmt->execute();
                
                if ($result) {
                    $affected_rows = $link->affected_rows;
                    if ($affected_rows > 0) {
                        $response['success'] = true;
                        $response['message'] = "Hotel type deleted successfully!";
                    } else {
                        $response['message'] = "No hotel type found with that ID.";
                    }
                } else {
                    $response['message'] = "Failed to delete hotel type: " . $link->error;
                    
                    // Check if the error is due to foreign key constraints
                    if ($link->errno == 1451) {
                        $response['message'] = "Cannot delete this hotel type because it is being used in other tables.";
                    }
                }
            } else {
                $response['message'] = "Hotel type ID is required.";
            }
            break;
            
        default:
            $response['message'] = "Invalid action.";
            break;
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);