<?php
require_once '../config.php';

// Get parameters (same as fetch_total_stock.php)
$supplier = $_GET['supplier'] ?? '';
$search = $_GET['search'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

// Base query (same as fetch_total_stock.php)
$query = "SELECT 
            item_name, 
            quantity, 
            cost_price, 
            item_price, 
            bakery_category, 
            supplier_id 
          FROM bakery_menu_stocks 
          WHERE 1=1";

$params = [];
$types = '';

// Add filters (same as fetch_total_stock.php)
if (!empty($supplier)) {
    $query .= " AND supplier_id = ?";
    $params[] = $supplier;
    $types .= 'i';
}

if (!empty($search)) {
    $query .= " AND (item_name LIKE ? OR item_id LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
}

if (!empty($startDate) && !empty($endDate)) {
    $query .= " AND created_at BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
    $types .= 'ss';
}

// Prepare and execute (similar to fetch_total_stock.php, but for HTML output)
try {
    $stmt = $link->prepare($query);
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $link->error);
    }

    if (count($params) > 0) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    // For printing, we'll display the error in HTML instead of JSON
    $data = []; // Empty data to avoid undefined variable errors in the HTML
    $error_message = $e->getMessage();
}

// Clean up
$stmt->close();
$link->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Total Stock Report</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        h1 { text-align: center; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
    <h1>Total Stock Report</h1>
    <?php if (isset($error_message)): ?>
        <p class="error">Error: <?= htmlspecialchars($error_message) ?></p>
    <?php elseif (empty($data)): ?>
        <p>No stock items found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Cost Price</th>
                    <th>Retail Price</th>
                    <th>Category</th>
                    <th>Supplier ID</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= $row['cost_price'] ?></td>
                    <td><?= $row['item_price'] ?></td>
                    <td><?= htmlspecialchars($row['bakery_category']) ?></td>
                    <td><?= $row['supplier_id'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>