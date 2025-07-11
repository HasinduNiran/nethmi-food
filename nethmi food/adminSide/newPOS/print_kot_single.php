<?php
date_default_timezone_set('Asia/Colombo');
$currentDateTime = date("Y-m-d h:i A");

$cart = [];
$hotel_type = '';
$bill_id = 'SAMPLE/001';
$hotel_type_id = '';
$reference_number = '';
$table_number = '';

if (isset($_GET['cart'])) {
    $cartJSON = urldecode($_GET['cart']);
    $cart = json_decode($cartJSON, true);
    if ($cart === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "Error decoding cart data: " . json_last_error_msg();
        exit;
    }
}

if (isset($_GET['hotel_type'])) $hotel_type = urldecode($_GET['hotel_type']);
if (isset($_GET['bill_id'])) $bill_id = $_GET['bill_id'];
if (isset($_GET['hotel_type_id'])) $hotel_type_id = $_GET['hotel_type_id'];
if (isset($_GET['reference_number'])) $reference_number = urldecode($_GET['reference_number']);
if (isset($_GET['table_number'])) $table_number = urldecode($_GET['table_number']);

$isUberOrPickMe = ($hotel_type_id == '4' || $hotel_type_id == '6');
$serviceName = $hotel_type_id == '4' ? 'Uber' : ($hotel_type_id == '6' ? 'Pick Me' : '');
$cartJSON = json_encode($cart);

header('Content-Type: text/html');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KOT Print</title>
<style>
    body {
       width: 80mm; /* Set page content width */
        font-family: Arial, sans-serif;
        margin: 0 auto;
        padding: 5px;
        font-size: 14px;
    }
    .kot-header {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
        border-bottom: 1px dashed #000;
        padding-bottom: 5px;
    }
    .bill-info {
        margin-bottom: 10px;
        font-size: 13px;
    }
    .bill-info td {
        padding: 2px 5px;
    }
    .datetime {
        font-size: 13px;
        margin-top: 5px;
    }
    .order-items {
        width: 100%;
        border-collapse: collapse;
    }
    .order-items th, .order-items td {
        border-bottom: 1px dashed #000;
        text-align: left;
        padding: 5px;
    }
    .order-items th {
        font-size: 13px;
    }
    .order-items td {
        font-size: 14px;
    }
    .checkbox {
        margin-right: 8px;
        transform: scale(1.2);
    }
    button.qty-btn {
    padding: 2px 6px;
    margin: 0 2px;
    font-size: 12px;
    border: 1px solid #333;
    background: #eee;
    cursor: pointer;
}
button.qty-btn:hover {
    background: #ccc;
}

@media print {
    .qty-btn, .print-btn, .checkbox {
        display: none !important;
    }
}

    .print-btn {
        display: block;
        margin: 10px auto;
        padding: 5px 15px;
        font-size: 14px;
        background: #007BFF;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    @media print {
        .print-btn, .checkbox {
            display: none !important;
        }
    }
</style>
</head>
<body onafterprint="window.close();">
    <div class="kot-header">Kitchen Order Ticket</div>

    <table class="bill-info">
        <tr><td>Bill ID:</td><td><?php echo htmlspecialchars($bill_id); ?></td></tr>
        <tr><td>Order Type:</td><td><?php echo htmlspecialchars($hotel_type ?: 'Not Specified'); ?></td></tr>
        <tr><td>Table No:</td><td><?php echo htmlspecialchars($table_number ?: 'Not Specified'); ?></td></tr>
        <tr><td colspan="2" class="datetime">Printed: <?php echo $currentDateTime; ?></td></tr>
        <?php if ($isUberOrPickMe && !empty($reference_number)): ?>
            <tr><td><?php echo htmlspecialchars($serviceName); ?> Ref:</td><td><?php echo htmlspecialchars($reference_number); ?></td></tr>
        <?php endif; ?>
    </table>

    <table class="order-items">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($cart as $index => $item): ?>
        <?php
        $name = htmlspecialchars($item['name']);
        $qty = intval($item['quantity']) + intval($item['fc'] ?? 0);
        ?>
        <tr>
            <td>
                <input type="checkbox" class="checkbox" checked data-index="<?php echo $index; ?>">
                <?php echo $name; ?>
            </td>
            <td>
                <button type="button" class="qty-btn" onclick="decreaseQty(<?php echo $index; ?>)">-</button>
                <span id="qty-<?php echo $index; ?>">x<?php echo $qty; ?></span>
                <button type="button" class="qty-btn" onclick="increaseQty(<?php echo $index; ?>)">+</button>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>


    </table>

   
    
<button class="print-btn" onclick="printKOT()">Print KOT</button>

<script>
function printKOT() {
    const checkboxes = document.querySelectorAll('.checkbox');
    checkboxes.forEach(box => {
        if (!box.checked) {
            const row = box.closest('tr');
            row.style.display = 'none';
        }
    });

    window.print();

    // Close page automatically after printing
    window.onafterprint = function() {
        setTimeout(() => window.close(), 500);
    };
}

function increaseQty(index) {
    const qtySpan = document.getElementById('qty-' + index);
    let currentQty = parseInt(qtySpan.textContent.replace('x', ''));
    currentQty += 1;
    qtySpan.textContent = 'x' + currentQty;
}

function decreaseQty(index) {
    const qtySpan = document.getElementById('qty-' + index);
    let currentQty = parseInt(qtySpan.textContent.replace('x', ''));
    if (currentQty > 1) { // Prevent going below 1
        currentQty -= 1;
        qtySpan.textContent = 'x' + currentQty;
    }
}


</script>
</body>
</html>
