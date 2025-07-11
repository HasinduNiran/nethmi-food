<?php
session_start(); // Ensure session is started
require_once '../config.php';

$bill_id = $_GET['bill_id'];

// Fetch bill details
$bill_query = "SELECT * FROM bills WHERE bill_id = '$bill_id'";
$bill_result = mysqli_query($link, $bill_query);
$bill_data = mysqli_fetch_assoc($bill_result);

// Fetch items for the given bill_id
$items_query = "SELECT bi.*, m.item_name, m.item_price FROM bill_items bi
                JOIN menu m ON bi.item_id = m.item_id
                WHERE bi.bill_id = '$bill_id'";
$items_result = mysqli_query($link, $items_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - Bill ID <?php echo $bill_id; ?></title>
    <style>
        body { font-family: Arial, sans-serif; }
        .receipt { max-width: 600px; margin: auto; }
        .header, .footer { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; padding: 8px; text-align: left; }
        .summary { font-weight: bold; }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="header">
            <h1>Diyakawa Restaurant</h1>
            <p>Receipt ID: <?php echo $bill_data['bill_id']; ?></p>
            <p>Date: <?php echo $bill_data['bill_time']; ?></p>
        </div>

        <p>Table ID: <?php echo $bill_data['table_id']; ?> | 
           Staff ID: <?php echo $bill_data['staff_id']; ?> | 
           Member ID: <?php echo $bill_data['member_id']; ?></p>
        <p>Payment Method: <?php echo strtoupper($bill_data['payment_method']); ?></p>
        
        <?php if (strtoupper($bill_data['payment_method']) === 'CARD') {
            $card_id = $bill_data['card_id'];
            $card_query = "SELECT card_number FROM card_payments WHERE card_id = '$card_id'";
            $card_result = mysqli_query($link, $card_query);
            $card_data = mysqli_fetch_assoc($card_result);
            $last4Digits = substr($card_data['card_number'], -4);
            echo '<p>Card Last 4 Digits: ' . $last4Digits . '</p>';
        } ?>

        <?php
        // Retrieve payment time from the Bills table
        $payment_time_query = "SELECT payment_time FROM bills WHERE bill_id = '$bill_id'";
        $payment_time_result = mysqli_query($link, $payment_time_query);
        $payment_time_row = mysqli_fetch_assoc($payment_time_result);
        $payment_time = $payment_time_row['payment_time'];
        ?>
        
        <p>Payment Time: <?php echo $payment_time; ?></p>

        <table>
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
            <?php
            $cart_total = 0;
            while ($item_row = mysqli_fetch_assoc($items_result)) {
                $item_name = $item_row['item_name'];
                $item_price = $item_row['item_price'];
                $quantity = $item_row['quantity'];
                $total = $item_price * $quantity;
                $cart_total += $total;
                echo "<tr>
                        <td>$item_name</td>
                        <td>Rs. " . number_format($item_price, 2) . "</td>
                        <td>$quantity</td>
                        <td>Rs. " . number_format($total, 2) . "</td>
                    </tr>";
            }
            ?>
        </table>

        <?php
        $before_tax = $cart_total;
        $tax_rate = 0.1;
        $tax_amount = $before_tax * $tax_rate;
        $after_tax = $before_tax + $tax_amount;
        ?>

        <table>
            <tr class="summary">
                <td colspan="3">Total</td>
                <td>Rs. <?php echo number_format($before_tax, 2); ?></td>
            </tr>
            <tr class="summary">
                <td colspan="3">Tax (<?php echo $tax_rate * 100; ?>%)</td>
                <td>Rs. <?php echo number_format($tax_amount, 2); ?></td>
            </tr>
            <tr class="summary">
                <td colspan="3">Grand Total</td>
                <td>Rs. <?php echo number_format($after_tax, 2); ?></td>
            </tr>
        </table>

        <div class="footer">
            <p>Thank you for dining with us!</p>
        </div>
    </div>
</body>
</html>
