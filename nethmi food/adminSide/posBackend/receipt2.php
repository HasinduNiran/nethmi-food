<?php
session_start();
require_once '../config.php';

// Validate bill_id
if (!isset($_GET['bill_id'])) {
    die('<div class="alert alert-danger">Invalid request. Missing Bill ID.</div>');
}

$bill_id = intval($_GET['bill_id']);

// Fetch bill details from bills table
$bill_query = "SELECT * FROM bills WHERE bill_id = '$bill_id'";
$bill_result = mysqli_query($link, $bill_query);

if (!$bill_result || mysqli_num_rows($bill_result) === 0) {
    die('<div class="alert alert-danger">Error: Bill details not found.</div>');
}

$bill_data = mysqli_fetch_assoc($bill_result);

// Fetch items from held_payments for the given bill_id
$items_query = "
    SELECT hp.item_id, hp.quantity, hp.price, hp.total, m.item_name
    FROM held_payments hp
    JOIN menu m ON hp.item_id = m.item_id
    WHERE hp.bill_id = '$bill_id'
";
$items_result = mysqli_query($link, $items_query);

if (!$items_result) {
    die('<div class="alert alert-danger">Error fetching held payment items: ' . mysqli_error($link) . '</div>');
}

$grand_total = 0; // Initialize grand total
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - Bill ID <?php echo $bill_id; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .container {
            width: 58mm; /* Adjusted for POS receipt */
            margin: auto;
            padding: 10px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            margin-bottom: 10px;
        }
        .header h2 { font-size: 14px; margin: 5px 0; }
        .header p { font-size: 10px; margin: 2px 0; }
        .info, table { font-size: 10px; }
        .info { margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 2px; text-align: left; }
        th.qty, td.qty, th.price, td.price, th.total, td.total { text-align: right; }
        .totals {
            font-size: 10px;
            font-weight: bold;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
    </style>
</head>
<body onload="window.print();">
<div class="container">
    <div class="header">
        <h2>Havok Foods (PVT) LTD.</h2>
        <p>No. 302, Havelock Road, Colombo 5</p>
        <p>Sri Lanka</p>
        <p>Hotline: 0706 227 227 / 0112 277 277</p>
    </div>

    <div class="info">
        <p><strong>Bill ID:</strong> <?php echo $bill_data['bill_id']; ?></p>
        <p><strong>Date:</strong> <?php echo $bill_data['bill_time']; ?></p>
        <p><strong>Table ID:</strong> <?php echo $bill_data['table_id']; ?></p>
        <p><strong>Staff ID:</strong> <?php echo $bill_data['staff_id']; ?></p>
        <p><strong>Payment Method:</strong> <?php echo strtoupper($bill_data['payment_method']); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="qty">Qty</th>
                <th class="price">Price</th>
                <th class="total">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($item_row = mysqli_fetch_assoc($items_result)) {
                $item_name = $item_row['item_name'];
                $price = $item_row['price'];
                $quantity = $item_row['quantity'];
                $total = $item_row['total'];
                $grand_total += $total;

                echo '<tr>
                        <td>' . htmlspecialchars($item_name) . '</td>
                        <td class="qty">' . $quantity . '</td>
                        <td class="price">Rs. ' . number_format($price, 2) . '</td>
                        <td class="total">Rs. ' . number_format($total, 2) . '</td>
                      </tr>';
            }
            ?>
        </tbody>
    </table>

    <div class="totals">
        <p><strong>Subtotal:</strong> Rs. <?php echo number_format($grand_total, 2); ?></p>
        <p><strong>Grand Total:</strong> Rs. <?php echo number_format($grand_total, 2); ?></p>
    </div>

    <div class="footer">
        <p>Thank you for dining with us!</p>
    </div>
</div>
</body>
</html>
