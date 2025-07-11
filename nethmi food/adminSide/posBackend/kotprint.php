<?php
require_once '../config.php';

// Set the default timezone
date_default_timezone_set('Asia/Colombo'); // Replace with your desired timezone

// Validate required parameters
if (!isset($_GET['bill_id']) || !isset($_GET['table_id'])) {
    die("Error: Missing required parameters (bill_id or table_id).");
}

$bill_id = $_GET['bill_id'];
$table_id = $_GET['table_id'];
$selected_items = isset($_GET['selected_items']) ? $_GET['selected_items'] : []; // Array of selected item IDs

// Database connection validation
if (!$link) {
    die("Database Connection Error: " . mysqli_connect_error());
}

// Handle selected items for printing
$item_filter = "";
if (!empty($selected_items)) {
    $selected_items = array_map('intval', $selected_items); // Sanitize item IDs
    $item_filter = "AND bi.item_id IN (" . implode(',', $selected_items) . ")";
}

// Query to fetch bill details for selected items or all items
$bill_query = "
    SELECT bi.*, m.item_name, bs.bill_time
    FROM bill_items bi
    JOIN menu m ON bi.item_id = m.item_id
    JOIN bills bs ON bs.bill_id = bi.bill_id
    WHERE bi.bill_id = '$bill_id' 
      AND m.item_category != 'Drinks' $item_filter";
$bill_result = mysqli_query($link, $bill_query);

if (!$bill_result) {
    die("Error fetching bill details: " . mysqli_error($link));
}

// Fetch bill payment details
$payment_query = "SELECT * FROM bills WHERE bill_id = '$bill_id'";
$payment_result = mysqli_query($link, $payment_query);

if (!$payment_result) {
    die("Error fetching payment details: " . mysqli_error($link));
}

$payment_details = mysqli_fetch_assoc($payment_result);

// Format the date to match the correct timezone
$date = new DateTime($payment_details['bill_time']);
$date->setTimezone(new DateTimeZone('Asia/Colombo')); // Adjust timezone
$formatted_date = $date->format('Y-m-d H:i:s');

// Fetch hotel area details using table_id and hoteltype
$hotelArea = '';
$hotel_query = "
    SELECT ht.name AS hotel_area
    FROM restaurant_tables rt
    JOIN holetype ht ON rt.hoteltype = ht.id
    WHERE rt.table_id = $table_id
";
$hotel_result = mysqli_query($link, $hotel_query);

if ($hotel_result && mysqli_num_rows($hotel_result) > 0) {
    $hotel_row = mysqli_fetch_assoc($hotel_result);
    $hotelArea = $hotel_row['hotel_area'];
} else {
    $hotelArea = 'Unknown Area'; // Fallback if no match is found
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print KOT</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f9f9f9;
            text-align: center;
        }

        .invoice-container {
            max-width: 700px;
            width: 100%;
            text-align: center;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border: 2px solid black;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        .header {
            margin-bottom: 10px;
        }

        .header h4 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            color: #000;
            font-weight: bold;
        }

        .details {
            margin: 20px 0;
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }

        .table {
            width: 60%;
            border-collapse: collapse;
            margin: 0 auto;
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }

        .table th,
        .table td {
            border: 2px solid black;
            padding: 8px;
            text-align: center;
        }

        .table th {
            background-color: #f2f2f2;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                background-color: #fff;
                display: block;
            }

            .invoice-container {
                margin: 0 auto;
                box-shadow: none;
                border: none;
            }

            @page {
                size: auto;
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">

<div class="invoice-container">
    <div class="header">
        <h4>KOT - Kitchen Order Ticket</h4>
    </div>
<div class="details">
    <p><strong>Bill ID:</strong> <?php echo htmlspecialchars($bill_id); ?></p>
    <p><strong>Date:</strong> <?php echo htmlspecialchars($formatted_date); ?></p>
    <p><strong>Hotel Area:</strong> <?php echo htmlspecialchars($hotelArea); ?></p>
    <p><strong>Table Number:</strong> <?php echo htmlspecialchars($table_id); ?></p>

    <!-- Add KOT Notes -->
    <p><strong>KOT Notes:</strong> <?php echo isset($_GET['kot_notes']) ? htmlspecialchars($_GET['kot_notes']) : 'No notes provided'; ?></p>
</div>

    <table class="table">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($bill_result) > 0) {
                while ($row = mysqli_fetch_assoc($bill_result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['item_name']) . "</td>
                            <td>" . htmlspecialchars($row['quantity']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='2' style='text-align:center;'>No items found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
