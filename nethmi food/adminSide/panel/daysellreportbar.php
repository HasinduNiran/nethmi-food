<?php

require_once "../config.php";


// Get date from GET parameter or use today's date
if (isset($_GET['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date'])) {
    $today = $_GET['date'];
} else {
    date_default_timezone_set('Asia/Colombo');
    $today = date("Y-m-d");
}

// Fetch all bill items along with product details in a single query
$sql = "
    SELECT 
        b.bill_id,
        bi.quantity,
        m.item_name,
        m.item_price,
        (bi.quantity * m.item_price) AS total
    FROM bills b
    JOIN bill_items bi ON b.bill_id = bi.bill_id
    JOIN menu m ON bi.item_id = m.item_id
    WHERE DATE(b.bill_time) = ? AND m.item_category = 'Drinks'
";

$stmt = $link->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

$sales = [];
while ($row = $result->fetch_assoc()) {
    $sales[] = $row;
}

$stmt->close();

$grandTotal = 0;
$grandQty = 0;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        tfoot td {
            font-weight: bold;
        }
    </style>
</head>

<body onload="window.print();">
    <h1>Daily Sales Report</h1>
    <p>Date: <?php echo $today; ?></p>

    <table>
        <thead>
            <tr>
                <th>Bill ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($sales)) : ?>
                <tr>
                    <td colspan="5">No sales data for today.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($sales as $sale) : 
                    $grandTotal += $sale['total'];
                    $grandQty += $sale['quantity'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sale['bill_id']); ?></td>
                        <td><?php echo htmlspecialchars($sale['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($sale['quantity']); ?></td>
                        <td><?php echo number_format($sale['item_price'], 2); ?></td>
                        <td><?php echo number_format($sale['total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total Sales Amount</td>
                <td><?php echo number_format($grandTotal, 2); ?></td>
            </tr>
            <tr>
                <td colspan="4">Total Products Sold</td>
                <td><?php echo $grandQty; ?></td>
            </tr>
        </tfoot>
    </table>
    
</body>

</html>
