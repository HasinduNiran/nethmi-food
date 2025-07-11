<?php
require_once "../config.php";

$sql = "SELECT item_type_id, item_type_name FROM menu_item_type ORDER BY item_type_name";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['item_type_id'] . '">' . htmlspecialchars($row['item_type_name']) . '</option>';
    }
} else {
    echo '<option value="" disabled>No menu types available</option>';
}

mysqli_close($link);
?>