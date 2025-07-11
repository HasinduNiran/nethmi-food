<?php
// Initialize the session
session_start();

// Error reporting for debugging
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Handle CORS if needed
header('Content-Type: application/json');

try {
    // Include database connection
    require_once "../config.php"; 
    
    if (!$link) {
        throw new Exception("Database connection failed");
    }

    // Get search parameters
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $startDate = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
    $endDate = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;

    // Base SQL query - modified to include cost_price for profit calculation
    $sql = "
        SELECT 
            bm.item_id,
            bm.item_name,
            bm.item_price,
            bm.cost_price,
            bm.bakery_category,
            SUM(bi.quantity) as total_quantity,
            SUM(bi.quantity * bm.item_price) as total_sales,
            SUM(bi.quantity * bm.cost_price) as total_cost
        FROM 
            bill_items bi
        JOIN 
            bakery_menu_stocks bm ON bi.item_id = bm.item_id
        JOIN 
            bills b ON bi.bill_id = b.bill_id
        WHERE 
            1=1
    ";

    // Add search condition if search parameter is provided
    if (!empty($search)) {
        $sql .= " AND (bm.item_name LIKE ? OR bm.item_id LIKE ?)";
    }

    // Add date range if dates are provided
    if ($startDate) {
        $sql .= " AND DATE(b.bill_time) >= ?";
    }
    
    if ($endDate) {
        $sql .= " AND DATE(b.bill_time) <= ?";
    }

    // Group by and order by
    $sql .= " GROUP BY bm.item_id ORDER BY total_sales DESC";

    // Prepare the statement
    $stmt = $link->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("SQL preparation failed: " . $link->error);
    }

    // Bind parameters based on conditions
    $types = '';
    $params = [];

    if (!empty($search)) {
        $searchParam = "%$search%";
        $types .= 'ss';
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    if ($startDate) {
        $types .= 's';
        $params[] = $startDate;
    }
    
    if ($endDate) {
        $types .= 's';
        $params[] = $endDate;
    }

    // Bind parameters if any
    if (!empty($params)) {
        $bindResult = $stmt->bind_param($types, ...$params);
        if (!$bindResult) {
            throw new Exception("Parameter binding failed: " . $stmt->error);
        }
    }

    // Execute the query
    $executeResult = $stmt->execute();
    
    if (!$executeResult) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }
    
    // Get result
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Result retrieval failed: " . $stmt->error);
    }
    
    // Fetch all rows as associative array
    $data = $result->fetch_all(MYSQLI_ASSOC);
    
    // Calculate profit for each item and totals
    $totalSales = 0;
    $totalCost = 0;
    $totalQuantity = 0;
    
    foreach($data as &$item) {
        $item['profit'] = $item['total_sales'] - $item['total_cost'];
        $totalSales += $item['total_sales'];
        $totalCost += $item['total_cost'];
        $totalQuantity += $item['total_quantity'];
    }
    
    // Add summary data
    $summary = [
        'totalSales' => $totalSales,
        'totalCost' => $totalCost,
        'totalProfit' => $totalSales - $totalCost,
        'totalQuantity' => $totalQuantity,
        'profitMargin' => $totalSales > 0 ? (($totalSales - $totalCost) / $totalSales) * 100 : 0
    ];

    // Return data with summary as JSON
    echo json_encode([
        'items' => $data,
        'summary' => $summary
    ]);

} catch (Exception $e) {
    // Return error message as proper JSON
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// Close connection if it exists
if (isset($link) && $link) {
    $link->close();
}
?>
