<?php
// print_bill.php
// This file generates a printable bill receipt

require_once 'db_config.php'; // Use your existing connection

// Get the bill_id from the request
$billId = isset($_GET['bill_id']) ? intval($_GET['bill_id']) : 0;

if ($billId <= 0) {
    echo 'Invalid bill ID';
    exit;
}

try {
    // Fetch bill details with the same queries as in get_bill_details.php
    $billQuery = "
        SELECT 
            b.bill_id, 
            b.table_id, 
            b.bill_time, 
            b.payment_time, 
            b.status, 
            b.payment_amount, 
            b.paid_amount, 
            b.balance_amount, 
            b.discount_amount,
            b.hotel_type,
            c.name as customer_name,
            c.phone_number as customer_phone
        FROM 
            bills b
        LEFT JOIN 
            customers c ON b.customer_id = c.customer_id
        WHERE 
            b.bill_id = $billId
    ";
    
    $billResult = $conn->query($billQuery);
    
    if ($billResult->num_rows === 0) {
        echo 'Bill not found';
        exit;
    }
    
    $bill = $billResult->fetch_assoc();
    
    // Fetch bill items with item names
    $itemsQuery = "
        SELECT 
            bi.bill_item_id,
            bi.item_id,
            bi.quantity,
            COALESCE(m.item_name, bm.item_name) as item_name,
            COALESCE(
                CASE 
                    WHEN b.hotel_type = 1 THEN m.dining_price
                    WHEN b.hotel_type = 2 THEN m.takeaway_price
                    WHEN b.hotel_type = 3 THEN m.delivery_service_item_price
                    ELSE m.item_price
                END,
                CASE 
                    WHEN b.hotel_type = 1 THEN bm.dining_price
                    WHEN b.hotel_type = 2 THEN bm.takeaway_price
                    WHEN b.hotel_type = 3 THEN bm.delivery_service_item_price
                    ELSE bm.item_price
                END
            ) as price
        FROM 
            bill_items bi
        LEFT JOIN 
            bills b ON bi.bill_id = b.bill_id
        LEFT JOIN 
            menu m ON bi.item_id = m.item_id
        LEFT JOIN 
            bakery_menu bm ON bi.item_id = bm.item_id
        WHERE 
            bi.bill_id = $billId
    ";
    
    $itemsResult = $conn->query($itemsQuery);
    $items = [];
    
    while ($item = $itemsResult->fetch_assoc()) {
        $items[] = $item;
    }
    
    // Fetch bill payments
    $paymentsQuery = "
        SELECT 
            id,
            bill_id,
            payment_method,
            amount,
            card_id,
            created_at
        FROM 
            bill_payments
        WHERE 
            bill_id = $billId
        ORDER BY 
            created_at ASC
    ";
    
    $paymentsResult = $conn->query($paymentsQuery);
    $payments = [];
    
    while ($payment = $paymentsResult->fetch_assoc()) {
        $payments[] = $payment;
    }
    
    // Output the printable HTML
    // Note: You might want to add your company logo, address, etc.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill #<?php echo $billId; ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .receipt {
            max-width: 80mm;
            margin: 0 auto;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
        }
        .bill-info {
            margin-bottom: 15px;
        }
        .bill-info div {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px dotted #ddd;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 0;
            }
            .receipt {
                width: 100%;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="company-name">Your Restaurant Name</div>
            <div>123 Restaurant Street, City</div>
            <div>Tel: 123-456-7890</div>
        </div>
        
        <div class="bill-info">
            <div><strong>Bill #:</strong> <?php echo $billId; ?></div>
            <div><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($bill['bill_time'])); ?></div>
            <div><strong>Table:</strong> <?php echo $bill['table_id'] ?: 'N/A'; ?></div>
            <?php if ($bill['customer_name']): ?>
            <div><strong>Customer:</strong> <?php echo $bill['customer_name']; ?></div>
            <div><strong>Phone:</strong> <?php echo $bill['customer_phone']; ?></div>
            <?php endif; ?>
        </div>
        
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
                <?php 
                $subtotal = 0;
                foreach ($items as $item):
                    $itemTotal = $item['price'] * $item['quantity'];
                    $subtotal += $itemTotal;
                ?>
                <tr>
                    <td><?php echo $item['item_name']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo number_format($itemTotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Subtotal:</strong></td>
                    <td><?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <?php if ($bill['discount_amount'] > 0): ?>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Discount:</strong></td>
                    <td><?php echo number_format($bill['discount_amount'], 2); ?></td>
                </tr>
                <?php endif; ?>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td><?php echo number_format($bill['payment_amount'], 2); ?></td>
                </tr>
            </tfoot>
        </table>
        
        <?php if (count($payments) > 0): ?>
        <div><strong>Payments:</strong></div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?php echo date('M d h:i A', strtotime($payment['created_at'])); ?></td>
                    <td><?php echo $payment['payment_method']; ?></td>
                    <td><?php echo number_format($payment['amount'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: right;"><strong>Total Paid:</strong></td>
                    <td><?php echo number_format($bill['paid_amount'], 2); ?></td>
                </tr>
                <?php if ($bill['balance_amount'] > 0): ?>
                <tr>
                    <td colspan="2" style="text-align: right;"><strong>Balance:</strong></td>
                    <td><?php echo number_format($bill['balance_amount'], 2); ?></td>
                </tr>
                <?php endif; ?>
            </tfoot>
        </table>
        <?php endif; ?>
        
        <div class="footer">
            <div>Thank you for your business!</div>
            <div>Printed on <?php echo date('M d, Y h:i A'); ?></div>
        </div>
        
        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()">Print Receipt</button>
            <button onclick="window.close()">Close</button>
        </div>
    </div>
    
    <script>
        // Auto print when the page loads
        window.onload = function() {
            // Uncomment the line below to auto-print
            // window.print();
        };
    </script>
</body>
</html>
<?php
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>