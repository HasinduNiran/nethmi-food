<?php
require_once "../config.php";

$q = isset($_GET['q']) ? $_GET['q'] : '';

// First try to find matching items in inventory_items table
$sql = "SELECT ii.itemid, ii.itemname, ii.category 
        FROM inventory_items ii 
        WHERE ii.itemname LIKE ? OR ii.itemid LIKE ? 
        LIMIT 10";
$stmt = $link->prepare($sql);
$searchTerm = "%$q%";
$stmt->bind_param('ss', $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    // For existing inventory items, check if they already have inventory entries
    $check_sql = "SELECT i.iteamname, i.manufacturedate, i.expierdate, i.category, i.mesuer 
                  FROM inventory i 
                  WHERE i.iteamname = ? 
                  LIMIT 1";
    $check_stmt = $link->prepare($check_sql);
    $check_stmt->bind_param('s', $row['itemname']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_row = $check_result->fetch_assoc()) {
        // Merge data from both tables
        $items[] = [
            'itemid' => $row['itemid'],
            'iteamname' => $row['itemname'],
            'manufacturedate' => $check_row['manufacturedate'],
            'expierdate' => $check_row['expierdate'],
            'category' => $check_row['category'] ?: $row['category'],
            'mesuer' => $check_row['mesuer']
        ];
    } else {
        // Item exists in items table but not in inventory yet
        $items[] = [
            'itemid' => $row['itemid'],
            'iteamname' => $row['itemname'],
            'manufacturedate' => date('Y-m-d'),
            'expierdate' => date('Y-m-d', strtotime('+6 months')),
            'category' => $row['category'],
            'mesuer' => '' // Default empty, user will need to select
        ];
    }
}

// If we didn't find any items in the items table, check the inventory table directly
if (empty($items)) {
    $fallback_sql = "SELECT i.iteamname, i.manufacturedate, i.expierdate, i.category, i.mesuer
                     FROM inventory i 
                     WHERE i.iteamname LIKE ? 
                     LIMIT 10";
    $fallback_stmt = $link->prepare($fallback_sql);
    $fallback_stmt->bind_param('s', $searchTerm);
    $fallback_stmt->execute();
    $fallback_result = $fallback_stmt->get_result();
    
    while ($row = $fallback_result->fetch_assoc()) {
        $items[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($items);
?>