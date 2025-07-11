<?php
session_start();
require_once '../config.php';

// Check if a date filter and period are provided
$dateFilter = isset($_GET['date']) ? $_GET['date'] : null;
$period = isset($_GET['period']) ? $_GET['period'] : 'custom';

// If no specific date is provided, use current date
if (!$dateFilter) {
    $dateFilter = date('Y-m-d');
}

// Set date ranges based on period
$startDate = $dateFilter;
$endDate = $dateFilter;

if ($period !== 'custom') {
    switch ($period) {
        case 'today':
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
            break;
        case 'week':
            // Get the start of the week (Sunday)
            $startDate = date('Y-m-d', strtotime('last Sunday', strtotime($dateFilter)));
            $endDate = date('Y-m-d', strtotime($startDate . ' +6 days'));
            break;
        case 'month':
            $startDate = date('Y-m-01', strtotime($dateFilter));
            $endDate = date('Y-m-t', strtotime($dateFilter));
            break;
        case 'year':
            $startDate = date('Y-01-01', strtotime($dateFilter));
            $endDate = date('Y-12-31', strtotime($dateFilter));
            break;
    }
}

// Get the first day of current month and previous month for percentage comparisons
$currentMonthStart = date('Y-m-01', strtotime($dateFilter));
$previousMonthStart = date('Y-m-01', strtotime($currentMonthStart . ' -1 month'));
$currentMonthEnd = date('Y-m-t', strtotime($dateFilter));
$previousMonthEnd = date('Y-m-t', strtotime($previousMonthStart));

// Fetch data from the database
$data = [];

// Total Bills
$query = "SELECT COUNT(*) as totalBills FROM bills";
if ($period === 'custom') {
    $query .= " WHERE DATE(bill_time) = '$dateFilter'";
} else {
    $query .= " WHERE bill_time BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
}
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$data['totalBills'] = $row['totalBills'];

// Calculate bills for current month and previous month to get percentage change
$currentMonthBillsQuery = "SELECT COUNT(*) as count FROM bills WHERE bill_time BETWEEN '$currentMonthStart' AND '$currentMonthEnd 23:59:59'";
$previousMonthBillsQuery = "SELECT COUNT(*) as count FROM bills WHERE bill_time BETWEEN '$previousMonthStart' AND '$previousMonthEnd 23:59:59'";

$currentMonthBillsResult = mysqli_query($link, $currentMonthBillsQuery);
$previousMonthBillsResult = mysqli_query($link, $previousMonthBillsQuery);

$currentMonthBills = mysqli_fetch_assoc($currentMonthBillsResult)['count'];
$previousMonthBills = mysqli_fetch_assoc($previousMonthBillsResult)['count'];

$data['billsPercentChange'] = $previousMonthBills > 0 ? 
    round((($currentMonthBills - $previousMonthBills) / $previousMonthBills) * 100, 2) : 
    ($currentMonthBills > 0 ? 100 : 0);

// Total Customers
$query = "SELECT COUNT(*) as totalCustomers FROM customers";
if ($period === 'custom') {
    $query .= " WHERE DATE(last_visit) = '$dateFilter'";
} else {
    $query .= " WHERE last_visit BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
}
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$data['totalCustomers'] = $row['totalCustomers'];

// Calculate customers for current month and previous month
$currentMonthCustomersQuery = "SELECT COUNT(*) as count FROM customers WHERE last_visit BETWEEN '$currentMonthStart' AND '$currentMonthEnd 23:59:59'";
$previousMonthCustomersQuery = "SELECT COUNT(*) as count FROM customers WHERE last_visit BETWEEN '$previousMonthStart' AND '$previousMonthEnd 23:59:59'";

$currentMonthCustomersResult = mysqli_query($link, $currentMonthCustomersQuery);
$previousMonthCustomersResult = mysqli_query($link, $previousMonthCustomersQuery);

$currentMonthCustomers = mysqli_fetch_assoc($currentMonthCustomersResult)['count'];
$previousMonthCustomers = mysqli_fetch_assoc($previousMonthCustomersResult)['count'];

$data['customersPercentChange'] = $previousMonthCustomers > 0 ? 
    round((($currentMonthCustomers - $previousMonthCustomers) / $previousMonthCustomers) * 100, 2) : 
    ($currentMonthCustomers > 0 ? 100 : 0);

// Cash Balance - Updated to join bills and bill_payments tables
$query = "SELECT SUM(b.payment_amount) as cashBalance 
          FROM bills b
          JOIN bill_payments bp ON b.bill_id = bp.bill_id
          WHERE bp.payment_method = 'cash'";
if ($period === 'custom') {
    $query .= " AND DATE(b.bill_time) = '$dateFilter'";
} else {
    $query .= " AND b.bill_time BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
}
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$data['cashBalance'] = $row['cashBalance'] ?? 0;

// Calculate cash balance for current month and previous month - Updated to join bills and bill_payments
$currentMonthCashQuery = "SELECT SUM(b.payment_amount) as total 
                          FROM bills b
                          JOIN bill_payments bp ON b.bill_id = bp.bill_id
                          WHERE bp.payment_method = 'cash' 
                          AND b.bill_time BETWEEN '$currentMonthStart' AND '$currentMonthEnd 23:59:59'";
                          
$previousMonthCashQuery = "SELECT SUM(b.payment_amount) as total 
                           FROM bills b
                           JOIN bill_payments bp ON b.bill_id = bp.bill_id
                           WHERE bp.payment_method = 'cash' 
                           AND b.bill_time BETWEEN '$previousMonthStart' AND '$previousMonthEnd 23:59:59'";

$currentMonthCashResult = mysqli_query($link, $currentMonthCashQuery);
$previousMonthCashResult = mysqli_query($link, $previousMonthCashQuery);

$currentMonthCash = mysqli_fetch_assoc($currentMonthCashResult)['total'] ?? 0;
$previousMonthCash = mysqli_fetch_assoc($previousMonthCashResult)['total'] ?? 0;

$data['cashPercentChange'] = $previousMonthCash > 0 ? 
    round((($currentMonthCash - $previousMonthCash) / $previousMonthCash) * 100, 2) : 
    ($currentMonthCash > 0 ? 100 : 0);

// Items Sold (from bill_items table)
$query = "SELECT SUM(quantity) as itemsSold FROM bill_items";
if ($period === 'custom') {
    $query .= " WHERE bill_id IN (SELECT bill_id FROM bills WHERE DATE(bill_time) = '$dateFilter')";
} else {
    $query .= " WHERE bill_id IN (SELECT bill_id FROM bills WHERE bill_time BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59')";
}
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$data['itemsSold'] = $row['itemsSold'] ?? 0;

// Calculate items sold for current month and previous month
$currentMonthItemsQuery = "SELECT SUM(bi.quantity) as total FROM bill_items bi 
                           JOIN bills b ON bi.bill_id = b.bill_id 
                           WHERE b.bill_time BETWEEN '$currentMonthStart' AND '$currentMonthEnd 23:59:59'";
$previousMonthItemsQuery = "SELECT SUM(bi.quantity) as total FROM bill_items bi 
                            JOIN bills b ON bi.bill_id = b.bill_id 
                            WHERE b.bill_time BETWEEN '$previousMonthStart' AND '$previousMonthEnd 23:59:59'";

$currentMonthItemsResult = mysqli_query($link, $currentMonthItemsQuery);
$previousMonthItemsResult = mysqli_query($link, $previousMonthItemsQuery);

$currentMonthItems = mysqli_fetch_assoc($currentMonthItemsResult)['total'] ?? 0;
$previousMonthItems = mysqli_fetch_assoc($previousMonthItemsResult)['total'] ?? 0;

$data['itemsPercentChange'] = $previousMonthItems > 0 ? 
    round((($currentMonthItems - $previousMonthItems) / $previousMonthItems) * 100, 2) : 
    ($currentMonthItems > 0 ? 100 : 0);

// Sales Data for Bar Chart - Show data based on selected period
$salesData = ['labels' => [], 'values' => []];

$groupBy = "DATE(bill_time)"; // Default grouping by day
if ($period === 'year') {
    $groupBy = "MONTH(bill_time)"; // Group by month for yearly view
}

$query = "SELECT $groupBy as date_group, SUM(payment_amount) as sales FROM bills";
if ($period === 'custom') {
    $query .= " WHERE DATE(bill_time) = '$dateFilter'";
} else {
    $query .= " WHERE bill_time BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
}
$query .= " GROUP BY $groupBy ORDER BY date_group ASC";
$result = mysqli_query($link, $query);

while ($row = mysqli_fetch_assoc($result)) {
    // Format label based on the grouping
    $label = $row['date_group'];
    if ($period === 'year') {
        $monthNum = intval($label);
        $dateObj = DateTime::createFromFormat('!m', $monthNum);
        $label = $dateObj->format('F'); // Month name
    }
    
    $salesData['labels'][] = $label;
    $salesData['values'][] = (float)$row['sales'];
}
$data['salesData'] = $salesData;

// Sales Distribution for Pie Chart - Just use item IDs since 'items' table doesn't exist
$query = "SELECT bi.item_id, SUM(bi.quantity) as total 
          FROM bill_items bi";
if ($period === 'custom') {
    $query .= " WHERE bi.bill_id IN (SELECT bill_id FROM bills WHERE DATE(bill_time) = '$dateFilter')";
} else {
    $query .= " WHERE bi.bill_id IN (SELECT bill_id FROM bills WHERE bill_time BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59')";
}
$query .= " GROUP BY bi.item_id ORDER BY total DESC LIMIT 10";
$result = mysqli_query($link, $query);
$salesDistribution = [];
while ($row = mysqli_fetch_assoc($result)) {
    $salesDistribution[] = [
        'category' => $row['item_id'],
        'categoryName' => 'Item #' . $row['item_id'],
        'sales' => (float)$row['total']
    ];
}
$data['salesDistribution'] = $salesDistribution;

// Recent Transactions
$query = "SELECT b.bill_id, b.bill_time, b.payment_amount, 
          c.name as customer_name, COUNT(bi.item_id) as item_count,
          CASE 
            WHEN b.status = 'completed' THEN 'Completed' 
            WHEN b.status = 'active' THEN 'Pending' 
            WHEN b.status = 'locked' THEN 'Locked'
            ELSE 'Cancelled' 
          END as status
          FROM bills b
          LEFT JOIN customers c ON b.customer_id = c.customer_id
          LEFT JOIN bill_items bi ON b.bill_id = bi.bill_id
          GROUP BY b.bill_id
          ORDER BY b.bill_time DESC
          LIMIT 5";
$result = mysqli_query($link, $query);

$recentTransactions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $recentTransactions[] = [
        'billId' => $row['bill_id'],
        'dateTime' => date('M d, Y H:i', strtotime($row['bill_time'])),
        'customer' => $row['customer_name'],
        'amount' => $row['payment_amount'],
        'itemCount' => $row['item_count'],
        'status' => $row['status']
    ];
}
$data['recentTransactions'] = $recentTransactions;

// Get sales for specific hotel types
$hotelTypes = [
    1 => 'Dine Station',
    7 => 'Takeaway',
    11 => 'Delivery Service', 
    6 => 'Pick Me',
    4 => 'Uber'
];

$hotelTypeSales = [];
$hotelTypePercentChanges = [];

foreach ($hotelTypes as $typeId => $typeName) {
    // Current period sales for this hotel type
    $query = "SELECT SUM(payment_amount) as total FROM bills WHERE hotel_type = $typeId";
    if ($period === 'custom') {
        $query .= " AND DATE(bill_time) = '$dateFilter'";
    } else {
        $query .= " AND bill_time BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
    }
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($result);
    $hotelTypeSales[$typeId] = $row['total'] ?? 0;

    // Current month sales for this hotel type
    $currentMonthQuery = "SELECT SUM(payment_amount) as total FROM bills 
                          WHERE hotel_type = $typeId AND bill_time BETWEEN '$currentMonthStart' AND '$currentMonthEnd 23:59:59'";
    $previousMonthQuery = "SELECT SUM(payment_amount) as total FROM bills 
                           WHERE hotel_type = $typeId AND bill_time BETWEEN '$previousMonthStart' AND '$previousMonthEnd 23:59:59'";

    $currentMonthResult = mysqli_query($link, $currentMonthQuery);
    $previousMonthResult = mysqli_query($link, $previousMonthQuery);

    $currentMonthTotal = mysqli_fetch_assoc($currentMonthResult)['total'] ?? 0;
    $previousMonthTotal = mysqli_fetch_assoc($previousMonthResult)['total'] ?? 0;

    $hotelTypePercentChanges[$typeId] = $previousMonthTotal > 0 ? 
        round((($currentMonthTotal - $previousMonthTotal) / $previousMonthTotal) * 100, 2) : 
        ($currentMonthTotal > 0 ? 100 : 0);
}

$data['hotelTypeSales'] = $hotelTypeSales;
$data['hotelTypeNames'] = $hotelTypes;
$data['hotelTypePercentChanges'] = $hotelTypePercentChanges;

// Return JSON response
header('Content-Type: application/json');
echo json_encode($data);
?>