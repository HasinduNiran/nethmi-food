<?php
require_once "../config.php";

if (isset($_GET['menu_type'])) {
    $menu_type = $_GET['menu_type'];
    
    // First, get the menu type ID from the name
    $type_sql = "SELECT item_type_id FROM menu_item_type WHERE item_type_name = ?";
    if ($type_stmt = mysqli_prepare($link, $type_sql)) {
        mysqli_stmt_bind_param($type_stmt, "s", $menu_type);
        mysqli_stmt_execute($type_stmt);
        $type_result = mysqli_stmt_get_result($type_stmt);
        
        if ($type_row = mysqli_fetch_assoc($type_result)) {
            $parent_type_id = $type_row['item_type_id'];
            
            // Now get the sub menu types for this parent
            $sub_sql = "SELECT sub_type_id, sub_type_name FROM sub_menu_type 
                        WHERE parent_type_id = ? 
                        ORDER BY sub_type_name";
            
            if ($sub_stmt = mysqli_prepare($link, $sub_sql)) {
                mysqli_stmt_bind_param($sub_stmt, "i", $parent_type_id);
                mysqli_stmt_execute($sub_stmt);
                $sub_result = mysqli_stmt_get_result($sub_stmt);
                
                if (mysqli_num_rows($sub_result) > 0) {
                    while ($row = mysqli_fetch_assoc($sub_result)) {
                        echo '<option value="' . htmlspecialchars($row['sub_type_name']) . '">' . 
                             htmlspecialchars($row['sub_type_name']) . '</option>';
                    }
                } else {
                    echo '<option value="">No sub menu types found</option>';
                }
                
                mysqli_stmt_close($sub_stmt);
            }
        } else {
            echo '<option value="">Menu type not found</option>';
        }
        
        mysqli_stmt_close($type_stmt);
    }
} else {
    echo '<option value="">Invalid request</option>';
}

mysqli_close($link);
?>