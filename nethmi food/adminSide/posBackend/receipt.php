<?php
session_start();
require_once '../config.php';

if (!isset($_GET['bill_id'])) {
    die('<div class="alert alert-danger">Invalid request. Missing Bill ID.</div>');
}

$bill_id = intval($_GET['bill_id']);

// Fetch bill details
$bill_query = "SELECT * FROM bills WHERE bill_id = ?";
$stmt = $link->prepare($bill_query);
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$bill_result = $stmt->get_result();

if (!$bill_result || $bill_result->num_rows === 0) {
    die('<div class="alert alert-danger">Error: Bill details not found.</div>');
}

$bill_data = $bill_result->fetch_assoc();
$discount = isset($bill_data['discount']) ? (float)$bill_data['discount'] : 0.0;
$payment_amount = isset($bill_data['payment_amount']) ? (float)$bill_data['payment_amount'] : 0.0;
$paid_amount = isset($bill_data['paid_amount']) ? (float)$bill_data['paid_amount'] : 0.0;
$balance_amount = isset($bill_data['balance_amount']) ? (float)$bill_data['balance_amount'] : 0.0;

// Fetch items for the given bill_id
$items_query = "SELECT bi.*, m.item_name, m.item_price FROM bill_items bi
                JOIN menu m ON bi.item_id = m.item_id
                WHERE bi.bill_id = ?";
$stmt = $link->prepare($items_query);
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$items_result = $stmt->get_result();

if (!$items_result) {
    die('<div class="alert alert-danger">Error fetching items: ' . $link->error . '</div>');
}

// Fetch payment methods breakdown
$payment_query = "SELECT payment_method, SUM(amount) AS total_amount FROM bill_payments WHERE bill_id = ? GROUP BY payment_method";
$stmt = $link->prepare($payment_query);
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$payment_result = $stmt->get_result();

$payment_breakdown = [];
while ($payment_row = $payment_result->fetch_assoc()) {
    $payment_breakdown[] = $payment_row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Bill ID <?php echo $bill_id; ?></title>
    <style>
        /* Global Styles */
        body {
            font-family: "Arial", sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
            color: #333;
        }

        .container {
            width: 58mm;
            margin: 10px auto;
            background: #fff;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Header Section */
        .header {
            text-align: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        .header img {
            width: 180px;
            height: auto;
            margin-bottom: 10px;
        }

        .header p {
            margin: 0;
            font-size: 12px;
            color: #888;
        }

        /* Info Section */
        .info {
            margin: 10px 0;
            font-size: 12px;
        }

        .info p {
            margin: 4px 0;
        }

        /* Table Section */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        th, td {
            text-align: left;
            padding: 5px;
            font-size: 12px;
            border-bottom: 1px solid #eee;
        }

        th {
            font-weight: bold;
            color: #444;
        }

        td {
            color: #555;
        }

        .text-right {
            text-align: right;
        }

        /* Totals Section */
        .totals {
            font-size: 12px;
            margin: 10px 0;
        }

        .totals p {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }

        .totals strong {
            color: #333;
        }

        /* Footer Section */
        .footer {
            text-align: center;
            font-size: 11px;
            color: #888;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            margin-top: 10px;
        }

        /* Print Media */
        @media print {
            body {
                background: #fff;
            }

            .container {
                box-shadow: none;
                border-radius: 0;
            }

            @page {
                size: 58mm auto;
                margin: 5mm;
            }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <img src="jcat.png" alt="Havok Foods Logo">
            <p>Mirissa 
</p>
            <p>Sri Lanka</p>
            <p>Hotline: 0713121061</p>
        </div>

        <!-- Info Section -->
        <div class="info">
            <p><strong>Bill ID:</strong> <?php echo $bill_data['bill_id']; ?></p>
            <p><strong>Date:</strong> <?php echo $bill_data['bill_time']; ?></p>
            <p><strong>Table ID:</strong> <?php echo $bill_data['table_id']; ?></p>
            <p><strong>Staff ID:</strong> <?php echo $bill_data['staff_id']; ?></p>
            <p><strong>Payment Method:</strong> <?php echo strtoupper($bill_data['payment_method']); ?></p>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $cart_total = 0;
                while ($item_row = $items_result->fetch_assoc()) {
                    $item_name = $item_row['item_name'];
                    $item_price = $item_row['item_price'];
                    $quantity = $item_row['quantity'];
                    $total = $item_price * $quantity;
                    $cart_total += $total;

                    echo '<tr>
                            <td>' . htmlspecialchars($item_name) . '</td>
                            <td class="text-right">' . $quantity . '</td>
                            <td class="text-right">Rs. ' . number_format($item_price, 2) . '</td>
                            <td class="text-right">Rs. ' . number_format($total, 2) . '</td>
                        </tr>';
                }
                ?>
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals">
            <p><span>Subtotal:</span> <strong>Rs. <?php echo number_format($cart_total, 2); ?></strong></p>
            <p><span>Discount:</span> <strong>Rs. <?php echo number_format($discount, 2); ?></strong></p>
            <p><span>Net Total:</span> <strong>Rs. <?php echo number_format($payment_amount, 2); ?></strong></p>
            <p><span>Paid Amount:</span> <strong>Rs. <?php echo number_format($paid_amount, 2); ?></strong></p>
            <p><span>Balance Amount:</span> <strong>Rs. <?php echo number_format($balance_amount, 2); ?></strong></p>
        </div>

        <!-- Payment Breakdown Section -->
        <div class="totals">
            <p><strong>Payment Breakdown:</strong></p>
            <?php foreach ($payment_breakdown as $payment): ?>
                <p><span><?php echo ucfirst($payment['payment_method']); ?>:</span> <strong>Rs. <?php echo number_format($payment['total_amount'], 2); ?></strong></p>
            <?php endforeach; ?>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p>Thank you for dining with us!</p>
        </div>
    </div>
</body>
</html>
