<?php
require_once '../config.php';

function deductIngredientsFromInventory($menu_item_id, $portion_size, $quantity, $link) {
    if (empty($menu_item_id) || $quantity <= 0) {
        return [
            'success' => false,
            'message' => 'Invalid menu item or quantity'
        ];
    }

    try {
        $link->begin_transaction();
        $sql = "SELECT mi.ingredient_id, mi.quantity, mi.measurement 
                FROM menu_ingredients mi 
                WHERE mi.menu_item_id = ? 
                AND (mi.portion_size = ? OR mi.portion_size IS NULL)";
        
        $stmt = $link->prepare($sql);
        $stmt->bind_param("ss", $menu_item_id, $portion_size);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            $link->rollback();
            return [
                'success' => false,
                'message' => 'No ingredients found for this menu item and portion size'
            ];
        }
        
        $deduction_log = [];
        $errors = [];
        while ($row = $result->fetch_assoc()) {
            $ingredient_id = $row['ingredient_id'];
            $required_qty = $row['quantity'] * $quantity; 
            
            $inv_sql = "SELECT id, qty, mesuer FROM inventory WHERE id = ?";
            $inv_stmt = $link->prepare($inv_sql);
            $inv_stmt->bind_param("i", $ingredient_id);
            $inv_stmt->execute();
            $inv_result = $inv_stmt->get_result();
            
            if ($inv_result->num_rows == 0) {
                $errors[] = "Ingredient ID $ingredient_id not found in inventory";
                continue;
            }
            
            $inv_row = $inv_result->fetch_assoc();
            $current_qty = floatval($inv_row['qty']);
            
            if ($current_qty < $required_qty) {
                $errors[] = "Insufficient quantity for ingredient ID $ingredient_id. Required: $required_qty, Available: $current_qty";
                continue;
            }
            
           
            $new_qty = $current_qty - $required_qty;
            $update_sql = "UPDATE inventory SET qty = ? WHERE id = ?";
            $update_stmt = $link->prepare($update_sql);
            $update_stmt->bind_param("si", $new_qty, $ingredient_id);
            $update_result = $update_stmt->execute();
            
            if (!$update_result) {
                $errors[] = "Failed to update inventory for ingredient ID $ingredient_id";
                continue;
            }
            
            $deduction_log[] = [
                'ingredient_id' => $ingredient_id,
                'deducted' => $required_qty,
                'remaining' => $new_qty
            ];
        }
        
        
        if (!empty($errors)) {
            $link->rollback();
            return [
                'success' => false,
                'message' => 'Failed to deduct some ingredients',
                'errors' => $errors
            ];
        }
        
    
        $link->commit();
        
        return [
            'success' => true,
            'message' => 'Ingredients deducted successfully',
            'deductions' => $deduction_log
        ];
        
    } catch (Exception $e) {
        
        $link->rollback();
        return [
            'success' => false,
            'message' => 'Exception occurred: ' . $e->getMessage()
        ];
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }
   
    $results = [];
    
    if (isset($data['items']) && is_array($data['items'])) {
        foreach ($data['items'] as $item) {
            if (isset($item['item_id']) && isset($item['quantity']) && isset($item['portion_size'])) {
                $result = deductIngredientsFromInventory(
                    $item['item_id'],
                    $item['portion_size'],
                    $item['quantity'],
                    $link
                );
                $results[$item['item_id']] = $result;
            } else {
                $results[] = [
                    'success' => false,
                    'message' => 'Missing required fields (item_id, quantity, or portion_size)'
                ];
            }
        }
        
        echo json_encode([
            'success' => !in_array(false, array_column($results, 'success')),
            'results' => $results
        ]);
    } 
   
    else if (isset($data['item_id']) && isset($data['quantity']) && isset($data['portion_size'])) {
        $result = deductIngredientsFromInventory(
            $data['item_id'],
            $data['portion_size'],
            $data['quantity'],
            $link
        );
        echo json_encode($result);
    } 
    else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields (item_id, quantity, or portion_size)'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>