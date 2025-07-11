<?php
include "./db_config.php";
$cart = [];
$date = isset($_GET['date']) ? $_GET['date'] : 'sample'; 
$billId = isset($_GET['billId']) ? $_GET['billId'] : 'sample/001'; 
$tableId = isset($_GET['tableId']) ? $_GET['tableId'] : 'sample/tb'; 
$paidAmount = isset($_GET['paidAmount']) ? $_GET['paidAmount'] : 0; 
$totalAmount = isset($_GET['totalAmount']) ? $_GET['totalAmount'] : 0; 

if (isset($_GET['cart'])) {
    $cartJSON = $_GET['cart'];
    $cart = json_decode($cartJSON, true);
    if ($cart === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "Error decoding cart data: " . json_last_error_msg();
        exit;
    }
}

$stmt = $conn->prepare("SELECT payment_method, amount FROM bill_payments WHERE bill_id = ?");
$stmt->bind_param("i", $billId);
$stmt->execute();
$result = $stmt->get_result();
    
$payments = [];
while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
}
$stmt->close();

header('Content-Type: text/html');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS BILL</title>
    <link rel="stylesheet" href="./pos_bill.styles.css">
</head>
<body onload="window.print()">
    <div class="pos-bill-container">
        <img src="../images/logo-massimo.png" alt="comp-logo" class="comp-logo">
        
        <table style="margin: 15px auto; display:table; width:95%" class="bill-info-tb">
            <tr>
                <td style="font-weight:bold;">Bill ID</td>
                <td><?php echo htmlspecialchars($billId); ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Date</td>
                <td><?php echo htmlspecialchars($date); ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Table ID</td>
                <td><?php echo htmlspecialchars($tableId); ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Staff ID</td>
                <td>sample</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Payment Method</td>
                <td>Cash</td>
            </tr>
        </table>

        <table class="bill-contents">
            <thead>
                <tr>
                    <th>Item & Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (is_array($cart)) {
                    $grossTotal = 0;
                    foreach($cart as $item){
                        $grossTotal += $item['price'] * $item['quantity'];
                        echo "
                            <tr>
                                <td colspan='3'>". htmlspecialchars($item['name']) ."</td>
                            </tr>
                            <tr>
                                <td> x". htmlspecialchars($item['quantity']) ."</td>
                                <td>". number_format($item['price'], 2) ."</td>
                                <td>". number_format($item['price'] * $item['quantity'], 2) ."</td>
                            </tr>
                        ";
                    }
                } else {
                    echo "<tr><td colspan='3'>No items in cart</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- bill fees info -->
        <table style="margin: 15px auto; display:table; width:95%" class="bill-info-tb">
            <tr>
                <td style="font-weight:bold;">Sub Total:</td>
                <td><?php echo number_format($grossTotal , 2) ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Discount:</td>
                <td><?php echo number_format($grossTotal - $totalAmount , 2) ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Net Total:</td>
                <td><?php echo number_format($totalAmount , 2) ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Paid Amount:</td>
                <td><?php echo number_format($paidAmount , 2) ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Balance Amount:</td>
                <td><?php echo number_format($paidAmount - $totalAmount , 2) ?></td>
            </tr>
        </table>

        <span style="font-size: 1.2em;">Payment Breakdown</span>
        <table  style="margin: 4px auto; display:table; width:55%" >
            <?php 
                foreach($payments as $payment){
                    echo"
                        <tr>
                            <td>".$payment["payment_method"]."</td>  
                            <td>".$payment["amount"]."</td>  
                        </tr>
                    ";
                }
            ?>
        </table>
    </div>
</body>
</html>
