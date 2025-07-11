<?php
// Initialize the session
session_start();

// Error reporting for debugging
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set content type to JSON
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

    // First, check if price_type column exists in bill_items table
    $checkColumnQuery = "SHOW COLUMNS FROM bill_items LIKE 'price_type'";
    $columnResult = $link->query($checkColumnQuery);
    $hasPriceTypeColumn = $columnResult && $columnResult->num_rows > 0;

    // Base SQL query modified to use hotel_type from bills table directly
    $sql = "
        SELECT 
            m.item_id,
            m.item_name,
            m.item_category,
            m.item_type,
            bi.portion_size,
            " . ($hasPriceTypeColumn ? "bi.price_type," : "'regular' as price_type,") . "
            CONCAT(
                UPPER(SUBSTRING(bi.portion_size, 1, 1)), 
                SUBSTRING(bi.portion_size, 2),
                ' (',
                CASE 
                    WHEN b.hotel_type = 1 THEN 'Dine'
                    WHEN b.hotel_type = 4 THEN 'Uber'
                    WHEN b.hotel_type = 6 THEN 'PickMe'
                    WHEN b.hotel_type = 7 THEN 'Takeaway'
                    WHEN b.hotel_type = 11 THEN 'Delivery'
                    ELSE 'Regular'
                END,
                ')'
            ) as display_size,
            bi.price as actual_price,
            SUM(bi.quantity) as total_quantity,
            SUM(bi.price * bi.quantity) as total_sales,
            b.hotel_type
        FROM 
            bill_items bi
        JOIN 
            menu m ON bi.item_id = CONVERT(m.item_id USING utf8mb4) COLLATE utf8mb4_general_ci
        JOIN 
            bills b ON bi.bill_id = b.bill_id
        WHERE 
            1=1
    ";

    // Add search condition if search parameter is provided
    if (!empty($search)) {
        $sql .= " AND (m.item_name LIKE ? OR m.item_id LIKE ?)";
    }

    // Add date range if dates are provided
    if ($startDate) {
        $sql .= " AND DATE(b.bill_time) >= ?";
    }
    
    if ($endDate) {
        $sql .= " AND DATE(b.bill_time) <= ?";
    }

    // Group by and order by
    if ($hasPriceTypeColumn) {
        $sql .= " GROUP BY m.item_id, m.item_name, m.item_category, m.item_type, bi.portion_size, bi.price_type, bi.price, b.hotel_type
                ORDER BY total_sales DESC";
    } else {
        $sql .= " GROUP BY m.item_id, m.item_name, m.item_category, m.item_type, bi.portion_size, bi.price, b.hotel_type
                ORDER BY total_sales DESC";
    }

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
    
    // Calculate totals
    $totalSales = 0;
    $totalQuantity = 0;
    
    foreach($data as $item) {
        $totalSales += $item['total_sales'];
        $totalQuantity += $item['total_quantity'];
    }
    
    // Add summary data
    $summary = [
        'totalSales' => $totalSales,
        'totalQuantity' => $totalQuantity,
        'itemCount' => count($data)
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
