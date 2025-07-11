<?php
require_once '../config.php';


// Initialize data
$response = [
    "totalBills" => 0,
    "totalCustomers" => 0,
    "cashBalance" => 0.0,
    "itemsSold" => 0,
    "salesData" => [
        "labels" => [],
        "values" => []
    ],
    "salesDistribution" => []
];

// Fetch total bills
$totalBillsQuery = "SELECT COUNT(*) AS totalBills FROM bills";
$result = $link->query($totalBillsQuery);
if ($result) {
    $row = $result->fetch_assoc();
    $response["totalBills"] = $row["totalBills"];
}

// Fetch total customers
$totalCustomersQuery = "SELECT COUNT(*) AS totalCustomers FROM customers";
$result = $link->query($totalCustomersQuery);
if ($result) {
    $row = $result->fetch_assoc();
    $response["totalCustomers"] = $row["totalCustomers"];
}

// Fetch total cash balance
$cashBalanceQuery = "SELECT SUM(cash_amount) AS totalCash FROM cash_balance";
$result = $link->query($cashBalanceQuery);
if ($result) {
    $row = $result->fetch_assoc();
    $response["cashBalance"] = floatval($row["totalCash"]);
}

// Fetch total items sold
$itemsSoldQuery = "SELECT SUM(quantity) AS totalItemsSold FROM bill_items";
$result = $link->query($itemsSoldQuery);
if ($result) {
    $row = $result->fetch_assoc();
    $response["itemsSold"] = intval($row["totalItemsSold"]);
}

// Fetch daily sales for line and bar charts
$salesDataQuery = "
    SELECT DATE(bill_time) AS saleDate, SUM(payment_amount) AS totalSales
    FROM bills
    WHERE payment_time IS NOT NULL
    GROUP BY DATE(bill_time)
    ORDER BY DATE(bill_time) ASC
";
$result = $link->query($salesDataQuery);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $response["salesData"]["labels"][] = $row["saleDate"];
        $response["salesData"]["values"][] = floatval($row["totalSales"]);
    }
}

// Fetch sales distribution by category for pie chart
$salesDistributionQuery = "
    SELECT m.item_category AS category, SUM(bi.quantity * m.item_price) AS sales
    FROM bill_items bi
    JOIN menu m ON bi.item_id = m.item_id
    GROUP BY m.item_category
    ORDER BY sales DESC
";
$result = $link->query($salesDistributionQuery);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $response["salesDistribution"][] = [
            "category" => $row["category"],
            "sales" => floatval($row["sales"])
        ];
    }
}

// Return response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
