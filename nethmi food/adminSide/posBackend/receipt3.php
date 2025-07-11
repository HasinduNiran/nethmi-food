<?php
// Include database connection
require_once '../config.php';

// Get the bill ID from the GET request
$bill_id = isset($_GET['bill_id']) ? intval($_GET['bill_id']) : 0;

// Fetch bill details
$query = "SELECT bi.quantity, m.item_name, m.item_price 
          FROM bill_items bi 
          JOIN menu m ON bi.item_id = m.item_id 
          WHERE bi.bill_id = '$bill_id'";
$result = mysqli_query($link, $query);

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
            width: 58mm;
            margin: 0;
            padding: 10px;
            text-align: center;
        }
        h3, h4 {
            margin: 0;
        }
        table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
            margin-top: 10px;
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
            margin-top: 10px;
            font-size: 14px;
        }
        .footer {
            margin-top: 10px;
            font-size: 12px;
            border-top: 1px dashed #000;
        }
    </style>
</head>
<body onload="window.print();">
    <h3>Temporary Bill</h3>
    <p><?php echo date('Y-m-d H:i:s'); ?></p>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
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
        <p><strong>Grand Total:</strong> Rs. <?php echo number_format($grand_total, 2); ?></p>
    </div>
    <div class="footer">
        <p>Thank you for visiting!</p>
    </div>
</body>
</html>
