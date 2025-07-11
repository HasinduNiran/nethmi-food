<?php
include "./db_config.php";

// Set response header to JSON
header('Content-Type: application/json');

// Check if bill_id is provided
if (!isset($_GET['bill_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Bill ID is required'
    ]);
    exit;
}

// Get bill_id from request
$billId = intval($_GET['bill_id']);

// Start transaction
$conn->begin_transaction();

try {
    // Get bill details
    $billStmt = $conn->prepare("
        SELECT b.*, h.name as hotel_type_name 
        FROM bills b 
        LEFT JOIN holetype h ON b.hotel_type = h.id
        WHERE b.bill_id = ?
    ");
    
    if (!$billStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $billStmt->bind_param("i", $billId);
    $billStmt->execute();
    $billResult = $billStmt->get_result();
    
    if ($billResult->num_rows === 0) {
        throw new Exception("Bill not found");
    }
    
    $bill = $billResult->fetch_assoc();
    $billStmt->close();
    
    // Get bill items
    $itemsStmt = $conn->prepare("
        SELECT * FROM bill_items 
        WHERE bill_id = ?
    ");
    
    if (!$itemsStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $itemsStmt->bind_param("i", $billId);
    $itemsStmt->execute();
    $itemsResult = $itemsStmt->get_result();
    
    $billItems = [];
    while ($item = $itemsResult->fetch_assoc()) {
        $billItems[] = $item;
    }
    $itemsStmt->close();
    
    // Get payment details
    $paymentsStmt = $conn->prepare("
        SELECT payment_method, amount 
        FROM bill_payments 
        WHERE bill_id = ?
    ");
    
    if (!$paymentsStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $paymentsStmt->bind_param("i", $billId);
    $paymentsStmt->execute();
    $paymentsResult = $paymentsStmt->get_result();
    
    $payments = [];
    while ($payment = $paymentsResult->fetch_assoc()) {
        $payments[] = $payment;
    }
    $paymentsStmt->close();
    
    // Commit transaction
    $conn->commit();
    
    // Format date
    $billDate = new DateTime($bill['bill_time']);
    $formattedDate = $billDate->format('M d, Y h:i A');
    
    // Determine if this is Uber or Pick Me
    $isUberOrPickMe = ($bill['hotel_type'] == 4 || $bill['hotel_type'] == 6);
    $serviceName = '';
    
    if ($bill['hotel_type'] == 4) {
        $serviceName = 'Uber';
    } elseif ($bill['hotel_type'] == 6) {
        $serviceName = 'Pick Me';
    }
    
    // Calculate subtotal
    $subTotal = 0;
    foreach ($billItems as $item) {
        $subTotal += $item['price'] * $item['quantity'];
    }
    
    // If total_before_discount is available and greater than zero, use it
    if (isset($bill['total_before_discount']) && $bill['total_before_discount'] > 0) {
        $subTotal = $bill['total_before_discount'];
    }
    
    // Calculate discount
    $discountAmount = 0;
    if (isset($bill['discount_amount'])) {
        $discountAmount = $bill['discount_amount'];
    } else {
        $discountAmount = $subTotal - $bill['payment_amount'];
    }
    
    // Generate HTML for the bill
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>POS BILL #<?php echo $billId; ?></title>
        <style>
            body {
                /* font-family: Arial, sans-serif; */
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
                font-family: monospace;
            }
            .pos-bill-container {
                width: 80mm;
                margin: 0 auto;
                background-color: white;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                padding: 10px;
            }
            .comp-logo {
                display: block;
                max-width: 98%;
                height: auto;
                margin: 0 auto 10px;
            }
            .bill-info-tb {
                width: 95%;
                margin: 15px auto;
                border-collapse: collapse;
            }
            .bill-info-tb td {
                padding: 3px 0;
                font-size: 14px;
            }
            .bill-contents {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
                border-top: 1px dashed #999;
                border-bottom: 1px dashed #999;
                padding: 5px 0;
            }
            .bill-contents th {
                font-size: 14px;
                text-align: left;
                padding: 5px 0;
            }
            .bill-contents td {
                font-size: 14px;
                padding: 2px 0;
            }
            .payment-info {
                width: 55%;
                margin: 4px auto;
                font-size: 14px;
            }
            .payment-info td {
                padding: 2px 0;
            }
            .section-title {
                font-size: 16px;
                font-weight: bold;
                text-align: center;
                margin: 10px 0 5px;
            }
        </style>
    </head>
    <body>
        <div class="pos-bill-container">
            <img src="../images/logo-massimo.png" alt="comp-logo" class="comp-logo">
            
            <table class="bill-info-tb">
                <tr>
                    <td style="font-weight:bold;">Bill ID</td>
                    <td><?php echo htmlspecialchars($billId); ?></td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">Date</td>
                    <td><?php echo htmlspecialchars($formattedDate); ?></td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">Table ID</td>
                    <td><?php echo htmlspecialchars($bill['table_id'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">Staff ID</td>
                    <td><?php echo htmlspecialchars($bill['staff_id'] ?? 'N/A'); ?></td>
                </tr>
                <?php if (!empty($bill['hotel_type_name'])): ?>
                <tr>
                    <td style="font-weight:bold;">Service Type</td>
                    <td><?php echo htmlspecialchars($bill['hotel_type_name']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($isUberOrPickMe && !empty($bill['reference_number'])): ?>
                <tr>
                    <td style="font-weight:bold;"><?php echo htmlspecialchars($serviceName); ?> Ref No</td>
                    <td><?php echo htmlspecialchars($bill['reference_number']); ?></td>
                </tr>
                <?php endif; ?>
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
                    <?php foreach($billItems as $item): 
                        $displayName = $item['product_name'];
                        if (strpos($displayName, '(Regular)') !== false) {
                            $displayName = str_replace('(Regular)', '(Family)', $displayName);
                        }
                    ?>
                        <tr>
                            <td colspan="3"><?php echo htmlspecialchars($displayName); ?></td>
                        </tr>
                        <tr>
                            <td> x<?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td><?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    
            <!-- bill fees info -->
            <table class="bill-info-tb">
                <tr>
                    <td style="font-weight:bold;">Sub Total:</td>
                    <td><?php echo number_format($subTotal, 2); ?></td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">Discount:</td>
                    <td><?php echo number_format($discountAmount, 2); ?></td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">Net Total:</td>
                    <td><?php echo number_format($bill['payment_amount'], 2); ?></td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">Paid Amount:</td>
                    <td><?php echo number_format($bill['paid_amount'], 2); ?></td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">Balance Amount:</td>
                    <td><?php echo number_format($bill['balance_amount'], 2); ?></td>
                </tr>
            </table>
    
            <div class="section-title">Payment Breakdown</div>
            <table class="payment-info">
                <?php foreach($payments as $payment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                        <td><?php echo number_format($payment['amount'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <script>
            window.onload = function() {
                window.print();
            };
        </script>
    </body>
    </html>
    <?php
    $html = ob_get_clean();
    
    // Return success response with HTML content
    echo json_encode([
        'success' => true,
        'html' => $html
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close connection
$conn->close();
?>