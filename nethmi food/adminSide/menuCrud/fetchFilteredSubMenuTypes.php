<?php
require_once "../config.php";

if (isset($_GET['parent_id'])) {
    $parent_id = intval($_GET['parent_id']);
    
    // Join query to get sub menu types with their parent menu type names filtered by parent_id
    $sql = "SELECT s.sub_type_id, s.sub_type_name, s.parent_type_id, m.item_type_name as parent_type_name 
            FROM sub_menu_type s
            JOIN menu_item_type m ON s.parent_type_id = m.item_type_id
            WHERE s.parent_type_id = ?
            ORDER BY s.sub_type_name";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $parent_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="sub-type-holder" id="sub-item-' . $row['sub_type_id'] . '">';
                echo '<div>';
                echo '<span>' . htmlspecialchars($row['sub_type_name']) . '</span>';
                echo '<span class="parent-type-label">Parent: ' . htmlspecialchars($row['parent_type_name']) . '</span>';
                echo '</div>';
                echo ' <button class="delete-btn del" onclick="deleteSubMenuItem(' . $row['sub_type_id'] . ')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-backspace-fill" viewBox="0 0 16 16">
                        <path d="M15.683 3a2 2 0 0 0-2-2h-7.08a2 2 0 0 0-1.519.698L.241 7.35a1 1 0 0 0 0 1.302l4.843 5.65A2 2 0 0 0 6.603 15h7.08a2 2 0 0 0 2-2zM5.829 5.854a.5.5 0 1 1 .707-.708l2.147 2.147 2.146-2.147a.5.5 0 1 1 .707.708L9.39 8l2.146 2.146a.5.5 0 0 1-.707.708L8.683 8.707l-2.147 2.147a.5.5 0 0 1-.707-.708L7.976 8z"/>
                    </svg>
                </button>';
                echo '</div>';
            }
        } else {
            echo '<p class="empty-placeholder-item-type">No sub menu types found for this parent.</p>';
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo '<p class="empty-placeholder-item-type">Error preparing statement: ' . mysqli_error($link) . '</p>';
    }
} else {
    echo '<p class="empty-placeholder-item-type">No parent ID specified.</p>';
}

mysqli_close($link);
?>