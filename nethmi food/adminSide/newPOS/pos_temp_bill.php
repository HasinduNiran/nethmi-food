<?php
session_start();
$user_name = $_SESSION['username'] ?? 'Unknown User';
$cart = [];
$hotel_type = '';
$bill_id = 'TEMP/001'; // Default temporary bill ID

if (isset($_GET['cart'])) {
    $cartJSON = urldecode($_GET['cart']);
    $cart = json_decode($cartJSON, true);
    if ($cart === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "Error decoding cart data: " . json_last_error_msg();
        exit;
    }
}

if (isset($_GET['hotel_type'])) {
    $hotel_type = urldecode($_GET['hotel_type']);
}

if (isset($_GET['bill_id'])) {
    $bill_id = $_GET['bill_id'];
}

function formatAmount($amount) {
    return number_format($amount, 2, '.', '');
}

$totalItems = 0;
$grossAmount = 0;
foreach ($cart as $item) {
    $totalItems += $item['quantity'];
    $grossAmount += $item['price'] * $item['quantity'];
}

header('Content-Type: text/html');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temporary POS Bill</title>
    <style>
        * {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        body {
            width: 80mm;
            margin: 0 auto;
            padding: 10px 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #000; /* Full line for header separation */
        }
        .header img {
            max-width: 100%;
            height: auto;
        }
        .highlight {
            background-color: #000;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            padding: 4px 0;
            text-transform: uppercase;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        th, td {
            padding: 3px;
            font-size: 11px;
        }
        th {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            background-color: #f0f0f0;
            border-bottom: 1px solid #000; /* Full line for table header */
        }
        .item-row td {
            border-bottom: 1px dashed #ccc; /* Dashed line for item separation */
        }
        .summary {
            margin-top: 10px;
            font-weight: bold;
        }
        .summary td {
            font-size: 12px;
            padding: 4px 8px;
            text-align: right;
        }
        .summary td:first-child {
            text-align: left;
        }
        .total-box {
            text-align: center;
            margin: 10px auto;
            border: 2px solid #000; /* Full line for total box */
            padding: 8px 15px;
            font-size: 18px;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
            border-top: 1px solid #000; /* Full line for footer separation */
            padding-top: 5px;
        }
        .footer p {
            margin: 3px 0;
        }
        .print-btn {
            display: block;
            margin: 10px auto;
            padding: 5px 15px;
            background-color: #1a5f7a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        @media print{
            .print-btn {
           display: none !important;
        }
        }
    </style>
        <script>
        // Automatically trigger print dialog when the page loads
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        };
    </script>
</head>
<body>
    <div class="header">
        <img src="../images/logo-massimo.png" alt="Restaurant Logo">
        <div class="highlight">Temporary Bill - <?php echo htmlspecialchars($hotel_type ? $hotel_type : 'Not Specified'); ?></div>
    </div>

    <table style="margin-bottom: 8px;">
        <tr>
            <td style="text-align: left; width: 50%;">Date: <?php echo date('Y-m-d'); ?></td>
            <td style="text-align: right; width: 50%;">Time: <?php echo date('h:iA'); ?></td>
        </tr>
        <tr>
            <td style="text-align: left;">User: <?php echo htmlspecialchars($user_name); ?></td>
            <td style="text-align: right;">Counter: <?php echo str_pad('1', 3, '0', STR_PAD_LEFT); ?></td>
        </tr>
        <tr>
            <td style="text-align: left;" colspan="2">Invoice: <?php echo htmlspecialchars($bill_id); ?></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart as $index => $item): ?>
                <tr class="item-row">
                    <td><?php echo $index + 1; ?>.</td>
                    <td style="text-align: left;"><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo formatAmount($item['price']); ?></td>
                    <td><?php echo formatAmount($item['price'] * $item['quantity']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td>Total Items</td>
            <td><?php echo count($cart); ?></td>
        </tr>
        <tr>
            <td>Total Quantity</td>
            <td><?php echo $totalItems; ?></td>
        </tr>
        <tr>
            <td>Total Amount</td>
            <td><?php echo formatAmount($grossAmount); ?></td>
        </tr>
    </table>

    <div class="total-box">
        <span>Total Amount<br><b><?php echo formatAmount($grossAmount); ?></b></span>
    </div>

    <div class="footer">
        <p>Thank You for Your Order!</p>
        <p><strong>Jungle cat Resturant</strong></p>
    </div>

    <button class="print-btn" onclick="printBill()">Print Temporary Bill</button>

    <script>
        function printBill() {
            const printWindow = window.open('', '', 'height=600,width=800');
            const styles = Array.from(document.querySelectorAll('style'))
                .map(style => style.outerHTML)
                .join('');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Temporary Bill</title>
                        ${styles}
                        <style>
                            body {
                                margin: 0;
                                padding: 10px 0;
                                width: 80mm;
                                font-family: Arial, sans-serif;
                                margin: 0 auto;
                            }
                            .print-btn {
                                display: none;
                            }
                        </style>
                    </head>
                    <body>
                        ${document.body.innerHTML}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
            printWindow.close();
        }
    </script>
</body>
</html>