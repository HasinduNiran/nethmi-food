<?php
require_once "../config.php";

$search = isset($_GET['search']) ? $_GET['search'] : '';
$conn = $link;

$query = "SELECT item_id, item_name, quantity, cost_price, item_type, bakery_category, item_price 
          FROM bakery_menu_stocks 
          WHERE item_name LIKE ? OR item_id LIKE ? 
          ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$search_param = "%$search%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['item_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
        echo "<td>" . number_format($row['quantity'], 2) . "</td>";
        echo "<td>$" . number_format($row['cost_price'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($row['item_type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['bakery_category']) . "</td>";
        echo "<td>$" . number_format($row['item_price'], 2) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>No bakery items found</td></tr>";
}

$stmt->close();
$conn->close();
?>