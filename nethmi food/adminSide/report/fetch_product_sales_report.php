<?php
require_once "../config.php";

header('Content-Type: application/json');

// Get and sanitize parameters
$category = isset($_GET['category']) ? $link->real_escape_string($_GET['category']) : '';
$search = isset($_GET['search']) ? $link->real_escape_string($_GET['search']) : '';
$startDate = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $link->real_escape_string($_GET['start_date']) : '1970-01-01';
$endDate = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $link->real_escape_string($_GET['end_date']) . ' 23:59:59' : date('Y-m-d H:i:s');

// Build the SQL query - Focus only on bill_items table to avoid collation issues
$sql = "SELECT 
            bi.item_id, 
            bi.product_name,
            bi.portion_size,
            bi.price,
            SUM(bi.quantity) as total_quantity,
            SUM(bi.price * bi.quantity) as total_sales
        FROM 
            bill_items bi
        JOIN 
            bills b ON bi.bill_id = b.bill_id
        WHERE 
            b.bill_time BETWEEN '$startDate' AND '$endDate'";

// Add search filter if specified
if (!empty($search)) {
    $sql .= " AND (bi.product_name LIKE '%$search%' OR bi.item_id LIKE '%$search%')";
}

// If category filter is needed, we need to use a subquery to get item_ids from menu table with matching category
if (!empty($category)) {
    // Get all item IDs that match the category
    $categoryItemsQuery = "SELECT item_id FROM menu WHERE item_category = '$category'";
    $categoryItemsResult = $link->query($categoryItemsQuery);
    
    if ($categoryItemsResult && $categoryItemsResult->num_rows > 0) {
        $itemIds = [];
        while ($row = $categoryItemsResult->fetch_assoc()) {
            $itemIds[] = "'" . $link->real_escape_string($row['item_id']) . "'";
        }
        
        if (!empty($itemIds)) {
            $itemIdsList = implode(',', $itemIds);
            $sql .= " AND bi.item_id IN ($itemIdsList)";
        }
    } else {
        // No items found for this category
        echo json_encode([]);
        exit;
    }
}

// Group by product and add sorting
$sql .= " GROUP BY bi.item_id, bi.product_name, bi.portion_size, bi.price
         ORDER BY total_sales DESC";

$result = $link->query($sql);

if (!$result) {
    echo json_encode(['error' => 'Database error: ' . $link->error]);
    exit;
}

$products = [];
while ($row = $result->fetch_assoc()) {
    // If we need category info, we can fetch it separately
    $products[] = $row;
}

echo json_encode($products);
$link->close();
?>
