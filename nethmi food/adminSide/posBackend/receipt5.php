<?php
require_once '../config.php';

// Get the bill ID from the GET request
$bill_id = isset($_GET['bill_id']) ? intval($_GET['bill_id']) : 0;

// Validate the bill ID
if ($bill_id === 0) {
    die('<div class="alert alert-danger">Invalid Bill ID.</div>');
}

// Fetch bill items for the given bill ID
$query = "
    SELECT bi.quantity, m.item_name, m.item_price 
    FROM bill_items bi
    JOIN menu m ON bi.item_id = m.item_id
    WHERE bi.bill_id = ?
";
$stmt = $link->prepare($query);
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize total
$grand_total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temporary Bill</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            text-align: center;
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            border-bottom: 1px dashed #000;
            padding: 5px;
            text-align: left;
        }
        th {
            font-weight: bold;
        }
        .totals {
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body onload="window.print();">
    <h3>Temporary Bill</h3>
    <p>Date: <?php echo date('Y-m-d H:i:s'); ?></p>

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                        $item_name = $row['item_name'];
                        $quantity = $row['quantity'];
                        $price = $row['item_price'];
                        $total = $price * $quantity;
                        $grand_total += $total;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item_name); ?></td>
                        <td><?php echo $quantity; ?></td>
                        <td>Rs. <?php echo number_format($price, 2); ?></td>
                        <td>Rs. <?php echo number_format($total, 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No items in this bill.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="totals">
        Grand Total: Rs. <?php echo number_format($grand_total, 2); ?>
    </div>
</body>
</html>
