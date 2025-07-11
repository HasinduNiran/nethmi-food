<?php
session_start();

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

$user_name = $_SESSION['username'] ?? 'Unknown User';
$cart = [];
$hotel_type_id = '';
$hotel_type_name = '';
$bill_id = 'TEMP/001'; // Default temporary bill ID
$reference_number = '';

if (isset($_GET['cart'])) {
    $cartJSON = urldecode($_GET['cart']);
    $cart = json_decode($cartJSON, true);
    if ($cart === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "Error decoding cart data: " . json_last_error_msg();
        exit;
    }
}

if (isset($_GET['hotel_type_id'])) {
    $hotel_type_id = urldecode($_GET['hotel_type_id']);
}

if (isset($_GET['hotel_type_name'])) {
    $hotel_type_name = urldecode($_GET['hotel_type_name']);
}

if (isset($_GET['bill_id'])) {
    $bill_id = $_GET['bill_id'];
}

if (isset($_GET['reference_number'])) {
    $reference_number = urldecode($_GET['reference_number']);
}

if (isset($_GET['table_number'])) {
    $table_number = urldecode($_GET['table_number']);
}

// Check if this is Uber or Pick Me
$isUberOrPickMe = ($hotel_type_id == '4' || $hotel_type_id == '6');
$serviceName = '';

if ($hotel_type_id == '4') {
    $serviceName = 'Uber';
} elseif ($hotel_type_id == '6') {
    $serviceName = 'Pick Me';
}

// If reference number is not passed but bill_id is valid, try to get it from database
if ($isUberOrPickMe && empty($reference_number) && is_numeric($bill_id)) {
    // Include database connection only if needed
    if (file_exists("./db_config.php")) {
        include "./db_config.php";
        
        $refStmt = $conn->prepare("SELECT reference_number FROM bills WHERE bill_id = ?");
        if ($refStmt) {
            $refStmt->bind_param("i", $bill_id);
            $refStmt->execute();
            $refResult = $refStmt->get_result();
            if ($row = $refResult->fetch_assoc()) {
                $reference_number = $row['reference_number'];
            }
            $refStmt->close();
        }
    }
}

function formatAmount($amount) {
    return number_format($amount, 2, '.', '');
}

// Separate purchase items (quantity > 0) and collect free items
$purchaseItems = [];
$freeItems = [];

foreach ($cart as $item) {
    // Only include items with quantity > 0 in purchase items
    if ($item['quantity'] > 0) {
        $purchaseItems[] = $item;
    }
    
    // Check if this item has free items (fc > 0) regardless of purchase quantity
    if (isset($item['fc']) && $item['fc'] > 0) {
        // Create free item entry
        $freeItem = [
            'name' => $item['name'] . ' (FREE)',
            'quantity' => $item['fc'],
            'price' => 0,
            'parent_item' => $item['name']
        ];
        $freeItems[] = $freeItem;
    }
}

$totalItems = 0;
$grossAmount = 0;
foreach ($purchaseItems as $item) {
    $totalItems += $item['quantity'];
    $grossAmount += $item['price'] * $item['quantity'];
}

// Get current Sri Lanka date and time
$current_date = date('Y-m-d');
$current_time = date('h:i A');
$current_datetime = date('Y-m-d H:i:s');

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
            font-size: 14px;
        }
        th {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 16px;
            background-color: #f0f0f0;
            border-bottom: 1px solid #000; /* Full line for table header */
        }
        .item-row td {
            border-bottom: 1px dashed #ccc; /* Dashed line for item separation */
        }
        .free-section {
            margin-top: 15px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .free-section-header {
            background-color: #e8e8e8;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px;
            margin-bottom: 5px;
        }
        .free-item-row td {
            border-bottom: 1px dashed #ccc;
            font-style: italic;
        }
        .summary {
            margin-top: 10px;
            font-weight: bold;
        }
        .summary td {
            font-size: 18px;
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
        .total-box span{
            font-size: 18px;
        }
        .total-box span b{
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
        .reference-row {
            font-weight: bold;
            background-color: #f9f9f9;
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
        <img src="../images/skl.jpeg" alt="comp-logo" class="comp-logo">
        <div style="text-align: center; font-size: 14px; line-height: 1.5; margin-top: 5px; margin-bottom: 10px;">
            
            <div>Tel: 077 777 5377 / 011 736 1633</div>
        </div>
        <div class="highlight">Temporary Bill - <?php echo htmlspecialchars($hotel_type_name ? $hotel_type_name : 'Not Specified'); ?></div>
    </div>

    <table style="margin-bottom: 8px;">
        <tr>
            <td style="text-align: left; width: 50%;">Date: <?php echo $current_date; ?></td>
            <td style="text-align: right; width: 50%;">Time: <?php echo $current_time; ?></td>
        </tr>
        <tr>
            <td style="text-align: left;">User: <?php echo htmlspecialchars($user_name); ?></td>
            <td style="text-align: right;">Counter: <?php echo str_pad('1', 3, '0', STR_PAD_LEFT); ?></td>
        </tr>
        <tr>
            <td style="text-align: left;" colspan="2">Invoice: <?php echo htmlspecialchars($bill_id); ?></td>
        </tr>
        <?php if ($isUberOrPickMe && !empty($reference_number)): ?>
        <tr class="reference-row">
            <td style="text-align: left;" colspan="2"><?php echo htmlspecialchars($serviceName); ?> Ref No: <?php echo htmlspecialchars($reference_number); ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($table_number)): ?>
        <tr class="table-row">
            <td style="text-align: left;" colspan="2">Table No: <?php echo htmlspecialchars($table_number); ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <!-- Purchase Items Section -->
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
            <?php foreach ($purchaseItems as $index => $item): ?>
                <tr class="item-row">
                    <td><?php echo $index + 1; ?>.</td>
                    <td style="text-align: left;"><?php 
                        $displayName = $item['name'];
                        if (strpos($displayName, '(Regular)') !== false) {
                            $displayName = str_replace('(Regular)', '(Family)', $displayName);
                        }
                        echo htmlspecialchars($displayName); 
                    ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo formatAmount($item['price']); ?></td>
                    <td><?php echo formatAmount($item['price'] * $item['quantity']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Free Items Section -->
    <?php if (!empty($freeItems)): ?>
    <div class="free-section">
        <div class="free-section-header">Free Items</div>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($freeItems as $index => $item): ?>
                    <tr class="free-item-row">
                        <td style="text-align: left;"><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <table class="summary">
        <tr>
            <td>Total Items</td>
            <td><?php echo count($purchaseItems); ?></td>
        </tr>
        <tr>
            <td>Total Quantity</td>
            <td><?php echo $totalItems; ?></td>
        </tr>
        <?php if (!empty($freeItems)): ?>
        <tr>
            <td>Free Items</td>
            <td><?php echo array_sum(array_column($freeItems, 'quantity')); ?></td>
        </tr>
        <?php endif; ?>
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
        <p><strong>Food Yard By Nethmi</strong></p>
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