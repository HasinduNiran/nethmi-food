<?php
require_once "../config.php";

$sql = "SELECT * FROM menu_item_type";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="item-type-holder" id="item-' . $row['item_type_id'] . '" onclick="filterSubMenuTypes(' . $row['item_type_id'] . ', \'' . htmlspecialchars($row['item_type_name'], ENT_QUOTES) . '\')">';
        echo '<span>' . htmlspecialchars($row['item_type_name']) . '</span>';
        echo ' <button class="delete-btn del" onclick="deleteMenuItem(' . $row['item_type_id'] . '); event.stopPropagation();">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-backspace-fill" viewBox="0 0 16 16">
                <path d="M15.683 3a2 2 0 0 0-2-2h-7.08a2 2 0 0 0-1.519.698L.241 7.35a1 1 0 0 0 0 1.302l4.843 5.65A2 2 0 0 0 6.603 15h7.08a2 2 0 0 0 2-2zM5.829 5.854a.5.5 0 1 1 .707-.708l2.147 2.147 2.146-2.147a.5.5 0 1 1 .707.708L9.39 8l2.146 2.146a.5.5 0 0 1-.707.708L8.683 8.707l-2.147 2.147a.5.5 0 0 1-.707-.708L7.976 8z"/>
            </svg>
        </button>';
        echo '</div>';
    }
} else {
    echo '<p style="empty-placeholder-item-type">No item types found.</p>';
}

mysqli_close($link);
?>