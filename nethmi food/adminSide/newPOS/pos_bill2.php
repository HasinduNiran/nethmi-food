<?php
include "./db_config.php";
$cart = [];
$date = isset($_GET['date']) ? $_GET['date'] : 'sample'; 
$billId = isset($_GET['billId']) ? $_GET['billId'] : 'sample/001'; 
$tableId = isset($_GET['tableId']) ? $_GET['tableId'] : 'sample/tb'; 
$paidAmount = isset($_GET['paidAmount']) ? $_GET['paidAmount'] : 0; 
$totalAmount = isset($_GET['totalAmount']) ? $_GET['totalAmount'] : 0;
$hotelTypeId = isset($_GET['hotelTypeId']) ? $_GET['hotelTypeId'] : '';
$hotelTypeName = isset($_GET['hotelTypeName']) ? $_GET['hotelTypeName'] : '';
$referenceNumber = isset($_GET['referenceNumber']) ? $_GET['referenceNumber'] : '';
$hasAdvancePayment = isset($_GET['hasAdvancePayment']) ? $_GET['hasAdvancePayment'] : false;
$advancePaymentAmount = isset($_GET['advancePaymentAmount']) ? $_GET['advancePaymentAmount'] : 0.0;
$creditPaymentflag = isset($_GET['creditPaymentflag']) ? $_GET['creditPaymentflag'] : false;
$serviceCharge = isset($_GET['serviceCharge']) ? $_GET['serviceCharge'] : false;


// If not passed in URL parameters, try to fetch from database for existing bills
if (empty($hotelTypeId) || empty($referenceNumber)) {
    $billInfoStmt = $conn->prepare("SELECT h.name as hotel_type_name, b.reference_number 
                                    FROM bills b 
                                    LEFT JOIN holetype h ON b.hotel_type = h.id
                                    WHERE b.bill_id = ?");
    if ($billInfoStmt) {
        $billInfoStmt->bind_param("i", $billId);
        $billInfoStmt->execute();
        $billInfoResult = $billInfoStmt->get_result();
        
        if ($row = $billInfoResult->fetch_assoc()) {
            if (empty($hotelTypeName)) {
                $hotelTypeName = $row['hotel_type_name'];
            }
            if (empty($referenceNumber)) {
                $referenceNumber = $row['reference_number'];
            }
        }
        $billInfoStmt->close();
    }
}

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

// Get staff ID from the bill
$staffId = 'sample';
$staffStmt = $conn->prepare("SELECT staff_id FROM bills WHERE bill_id = ?");
if ($staffStmt) {
    $staffStmt->bind_param("i", $billId);
    $staffStmt->execute();
    $staffResult = $staffStmt->get_result();
    if ($row = $staffResult->fetch_assoc()) {
        $staffId = $row['staff_id'];
    }
    $staffStmt->close();
}

// Determine if this is Uber or Pick Me
$isUberOrPickMe = ($hotelTypeId == '4' || $hotelTypeId == '6');
$serviceName = '';

if ($hotelTypeId == '4') {
    $serviceName = 'Uber';
} elseif ($hotelTypeId == '6') {
    $serviceName = 'Pick Me';
}

header('Content-Type: text/html');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS BILL</title>
    <link rel="stylesheet" href="./pos_bill.styles.css">
    <script>
        // Automatically trigger print dialog when the page loads
        window.onload = function() {
            // Small delay to ensure page is fully loaded
            setTimeout(function() {
                window.print();
            }, 500);
        };
        
        // Handle after print for auto-close 
        window.addEventListener('afterprint', function() {
            setTimeout(function() {
                window.close();
            }, 500);
        });
        
        // Backup method for browsers that don't support afterprint
        // window.addEventListener('beforeprint', function() {
        //     setTimeout(function() {
        //         // Check if we're still here after 3 seconds (user likely finished printing)
        //         setTimeout(function() {
        //             window.close();
        //         }, 3000);
        //     }, 1000);
        // });
    </script>
</head>
<body>
    <div class="pos-bill-container">
        <img src="../images/skl.jpeg" alt="comp-logo" class="comp-logo">
        <div style="text-align: center; font-size: 14px; line-height: 1.5; margin-top: 5px; margin-bottom: 10px;">
           
            <div>Tel:071 0355101 / 0710355119</div>
        </div>
        
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
                <td style="font-weight:bold;">Hotel Type</td>
                <td><?php echo htmlspecialchars($hotelTypeName); ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Table ID</td>
                <td><?php echo htmlspecialchars($tableId); ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Staff ID</td>
                <td><?php echo htmlspecialchars($staffId); ?></td>
            </tr>
            <?php if ($isUberOrPickMe && !empty($referenceNumber)): ?>
            <tr>
                <td style="font-weight:bold;"><?php echo htmlspecialchars($serviceName); ?> Ref No</td>
                <td><?php echo htmlspecialchars($referenceNumber); ?></td>
            </tr>
            <?php endif; ?>
        </table>

        <!-- Purchase Items -->
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
                    $freeItems = []; // Array to store free items
                    
                    foreach($cart as $item){
                        // Only display items with quantity > 0
                        if ($item['quantity'] > 0) {
                            $grossTotal += $item['price'] * $item['quantity'];
                            $displayName = $item['name'];
                            if (strpos($displayName, '(Regular)') !== false) {
                                $displayName = str_replace('(Regular)', '(Family)', $displayName);
                            }
                            echo "
                                <tr>
                                    <td colspan='3'>". htmlspecialchars($displayName) ."</td>
                                </tr>
                                <tr>
                                    <td> x". htmlspecialchars($item['quantity']) ."</td>
                                    <td>". number_format($item['price'], 2) ."</td>
                                    <td>". number_format($item['price'] * $item['quantity'], 2) ."</td>
                                </tr>
                            ";
                        }
                        
                        // Collect free items if fc > 0
                        if (isset($item['fc']) && $item['fc'] > 0) {
                            $freeItems[] = $item;
                        }
                    }
                } else {
                    echo "<tr><td colspan='3'>No items in cart</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <?php if (!empty($freeItems)): ?>
        <!-- Free Items Section -->
        <div style="margin: 5px 0; padding-top: 10px;">
            <h3 style="text-align: center; margin: 8px 0; font-size: 1.1em;">Free Items</h3>
            <table style="margin: 12px auto; display:table; width:95%; border-collapse: collapse;">
                <tbody>
                    <?php 
                    foreach($freeItems as $freeItem){
                        $displayName = $freeItem['name'];
                        if (strpos($displayName, '(Regular)') !== false) {
                            $displayName = str_replace('(Regular)', '(Family)', $displayName);
                        }
                        echo "
                            <tr>
                                <td style='padding: 5px; text-align: left; vertical-align: top; width: 85%;'>". htmlspecialchars($displayName) ." (FREE)</td>
                                <td style='padding: 5px; text-align: center; vertical-align: top; width: 15%; font-weight: bold;'>x". htmlspecialchars($freeItem['fc']) ."</td>
                            </tr>
                        ";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- bill fees info -->
        <table style="margin: 15px auto; display:table; width:95%" class="bill-info-tb">
            <tr>
                <td style="font-weight:bold;">Sub Total:</td>
                <td><?php echo number_format($grossTotal , 2) ?></td>
            </tr>
            <?php 
                $discountAmount = ($grossTotal - $advancePaymentAmount) - $totalAmount;
                if($discountAmount > 0){
                    echo '
                     <tr>
                        <td style="font-weight:bold;">Discount:</td>
                        <td>'. number_format($discountAmount , 2) .'</td>
                    </tr>
                    ';
                }
            ?>
            <?php
                // if($hasAdvancePayment){
                //     echo '
                //      <tr>
                //         <td style="font-weight:bold;">Advance Payment:</td>
                //         <td>'. number_format($advancePaymentAmount , 2) .'</td>
                //     </tr>
                //     ';
                // }
            ?>
            <tr>
                <td style="font-weight:bold;">Net Total:</td>
                <td><?php echo number_format($totalAmount , 2) ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Service Charge:</td>
                <td><?php echo number_format($serviceCharge , 2) ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Sub Total:</td>
                <td><?php echo number_format($serviceCharge + $totalAmount , 2) ?></td>
            </tr>
            <tr>
            <td style="font-weight:bold;">
                <?php 
                    echo ($creditPaymentflag == 'false') ? 'Paid Amount' : 'Credit Amount';
                ?>
            </td>

                <td><?php echo number_format($paidAmount , 2) ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Balance Amount:</td>
                <td><?php echo number_format($paidAmount - ($serviceCharge + $totalAmount) , 2) ?></td>
            </tr>
        </table>

        <span style="font-size: 1.2em;">Payment Breakdown</span>
        <table style="margin: 4px auto; display:table; width:55%">
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

        <!-- Print Button (hidden during print) -->
        <button class="print-btn" onclick="printBill()" style="display: block; margin: 10px auto; padding: 5px 15px; background-color: #1a5f7a; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 12px;">Print Bill</button>
    </div>

    <script>
        function printBill() {
            const printWindow = window.open('', '', 'height=600,width=800');
            const styles = Array.from(document.querySelectorAll('link[rel="stylesheet"]'))
                .map(link => `<link rel="stylesheet" href="${link.href}">`)
                .join('');
            
            printWindow.document.write(`
                <html>
                    <head>
                        <title>POS Bill</title>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        ${styles}
                        <style>
                            body {
                                margin: 0;
                                padding: 10px 0;
                                font-family: Arial, sans-serif;
                            }
                            .print-btn {
                                display: none !important;
                            }
                            @media print {
                                .print-btn {
                                    display: none !important;
                                }
                                body {
                                    margin: 0;
                                    padding: 0;
                                }
                                .pos-bill-container {
                                    page-break-inside: avoid;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        ${document.querySelector('.pos-bill-container').outerHTML}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
            printWindow.close();
        }
    </script>

    <style>
        @media print {
            .print-btn {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .pos-bill-container {
                page-break-inside: avoid;
            }
        }
    </style>
</body>
</html>