<?php
$cart = [];
$hotel_type = '';
$bill_id = 'SAMPLE/001'; // Default value

if (isset($_GET['cart'])) {
    $cartJSON = urldecode($_GET['cart']);
    $cart = json_decode($cartJSON, true);
    if ($cart === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "Error decoding cart data: " . json_last_error_msg();
        exit;
    }
}

if (isset($_GET['hotel_type'])) {
    $hotel_type = urldecode($_GET['hotel_type']); // Decode the URL-encoded text
}

if (isset($_GET['bill_id'])) {
    $bill_id = $_GET['bill_id'];
}

header('Content-Type: text/html');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Order Ticket</title>
    <link rel="stylesheet" href="./print_kot.styles.css">
</head>
<body>
    <div class="kot-container">
        <!-- main kitchen -->
        <div class="kot-partition" id="main-kitchen">
            <div class="kot-header">
                <img src="../images/logo-massimo.png" class="kot-logo">
                <span class="header-kot-text">Main Kitchen KOT</span>
            </div>
            <table class="bill-info-tb">
                <thead>
                    <tr>
                        <td>Bill ID: </td>
                        <td><?php echo htmlspecialchars($bill_id); ?></td>
                    </tr>
                    <tr>
                        <td>Order Type: </td>
                        <td><?php echo htmlspecialchars($hotel_type ? $hotel_type : 'Not Specified'); ?></td>
                    </tr>
                </thead>
            </table>
            
            <table class="order-item-tb">
                <tbody id="main-kitchen-cont"></tbody>
            </table>
        </div>
        
        <!-- outdoor kitchen -->
        <div class="kot-partition" id="outdoor-kitchen">
            <div class="kot-header">
                <img src="../images/logo-massimo.png" class="kot-logo">
                <span class="header-kot-text">Outdoor Kitchen KOT</span>
            </div>
            <table class="bill-info-tb">
                <thead>
                    <tr>
                        <td>Bill ID: </td>
                        <td><?php echo htmlspecialchars($bill_id); ?></td>
                    </tr>
                    <tr>
                        <td>Order Type: </td>
                        <td><?php echo htmlspecialchars($hotel_type ? $hotel_type : 'Not Specified'); ?></td>
                    </tr>
                </thead>
            </table>

            <table class="order-item-tb">
                <thead>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <tbody id="outdoor-kitchen-cont"></tbody>
            </table>
        </div>

        <button class="print-btn-kot" onclick="printTickets()">Print</button>
    </div>

    <script>
    let mainKitchenOrders = [];
    let outdoorKitchenOrders = [];
    
    document.addEventListener('DOMContentLoaded', () => {
        const cart = <?php echo json_encode($cart ?? []); ?>;
        cart.forEach((item) => {
            if(item.itemCategory === 'Main Dishes') {
                mainKitchenOrders.push(item);
            } else {
                outdoorKitchenOrders.push(item);
            }
        });

        const mainKitchenTableBody = document.getElementById('main-kitchen-cont');
        const outdoorKitchenTableBody = document.getElementById('outdoor-kitchen-cont');
    
        // Populate Main Kitchen KOT
        mainKitchenOrders.forEach((item) => {
            const firstRow = document.createElement('tr');
            const itemTD = document.createElement('td');
            itemTD.setAttribute('colspan', '4');
            itemTD.style.padding = "5px";
            itemTD.style.marginTop = "5px";
            itemTD.innerHTML = `${item.name}<br>(${item.remarks ? item.remarks : 'As Usual'})`;
            firstRow.appendChild(itemTD);
            mainKitchenTableBody.appendChild(firstRow);
            
            const secondRow = document.createElement('tr');
            const qty = document.createElement('td');
            qty.setAttribute('colspan', '2');
            qty.style.textAlign = "center";
            qty.innerText = `x${item.quantity}`;
            const price = document.createElement('td');
            price.setAttribute('colspan', '2');
            price.style.textAlign = "center";
            price.innerText = `LKR${parseFloat(item.price).toFixed(2)}`;
            secondRow.appendChild(qty);
            secondRow.appendChild(price);
            mainKitchenTableBody.appendChild(secondRow);
        });

        // Populate Outdoor Kitchen KOT
        outdoorKitchenOrders.forEach((item) => {
            const firstRow = document.createElement('tr');
            const itemTD = document.createElement('td');
            itemTD.setAttribute('colspan', '4');
            itemTD.style.padding = "5px";
            itemTD.style.marginTop = "5px";
            itemTD.innerHTML = `${item.name}<br>(${item.remarks ? item.remarks : 'As Usual'})`;
            firstRow.appendChild(itemTD);
            outdoorKitchenTableBody.appendChild(firstRow);
            
            const secondRow = document.createElement('tr');
            const qty = document.createElement('td');
            qty.setAttribute('colspan', '2');
            qty.style.textAlign = "center";
            qty.innerText = `x${item.quantity}`;
            const price = document.createElement('td');
            price.setAttribute('colspan', '2');
            price.style.textAlign = "center";
            price.innerText = `LKR${parseFloat(item.price).toFixed(2)}`;
            secondRow.appendChild(qty);
            secondRow.appendChild(price);
            outdoorKitchenTableBody.appendChild(secondRow);
        });
    });

    function printTickets() {
        const tickets = document.querySelectorAll('.kot-partition');
        tickets.forEach((ticket, index) => {
            const printWindow = window.open('', '', 'height=600,width=800');
            const styles = Array.from(document.querySelectorAll('style, link[rel="stylesheet"]'))
                .map(style => style.outerHTML)
                .join('');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Ticket ${index + 1}</title>
                        ${styles}
                        <style>
                            body {
                                margin: 0;
                                padding: 0;
                                width: 80mm;
                                font-family: Arial, sans-serif;
                                margin: 0 auto;
                                width: 800px;
                                height:100vh;
                                display: flex;
                                justify-content: center;
                                align-items:flex-start;
                            }
                            .kot-partition {
                                width: 80mm;
                                min-height: 200px;
                                padding: 10px;
                                display: flex;
                                flex-direction: column;
                                justify-content: flex-start;
                                align-items: flex-start;
                            }
                            .kot-logo {
                                width: 100%;
                            }
                            .header-kot-text {
                                display: block;
                                margin: 0 auto;
                                font-family: monospace;
                                text-align: center;
                                text-transform: capitalize;
                                margin-left: 50px;
                                font-size: 1.3em;
                            }
                            .bill-info-tb {
                                display: table;
                                margin: 20px auto;
                                width: 80%;
                                font-family: monospace;
                                text-align: center;
                                border: 1px solid black;
                                padding: 5px 10px;
                            }
                            .order-item-tb {
                                display: table;
                                margin: 0 auto;
                                width: 100%;
                                font-family: monospace;
                                text-align: left;
                                padding: 5px 10px;
                            }
                            #main-kitchen-cont,
                            #outdoor-kitchen-cont {
                                font-family: monospace;
                                font-size: 1.1em;
                            }
                            .print-btn-kot {
                                display: none;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="kot-partition">${ticket.innerHTML}</div>
                    </body>
                </html>
            `);

            printWindow.document.close();
            printWindow.print();
            printWindow.close();
        });
    }
    </script>
</body>
</html>