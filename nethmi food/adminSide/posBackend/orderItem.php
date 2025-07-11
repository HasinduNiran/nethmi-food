<?php
session_start();
require_once '../config.php';
// include '../inc/dashHeader.php';

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

// Fetch values from GET parameters
$bill_id = $_GET['bill_id'] ?? null;
$table_id = $_GET['table_id'] ?? null;
$id = $_GET['id'] ?? 1;

$bill_id = $_GET['bill_id'] ?? '';
$id = $_GET['id'] ?? '';
$table_id = $_GET['table_id'] ?? '';

// Fetch unique item types
$item_type_query = "SELECT DISTINCT item_type FROM menu ORDER BY item_type";
$item_type_result = mysqli_query($link, $item_type_query);

// Initialize variables for customer information
$customer_name = '';
$phone_number = '';
$visit_message = '';

// Handle customer information submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['phone_number'])) {
    $phone_number = mysqli_real_escape_string($link, $_POST['phone_number']);
    $customer_name = mysqli_real_escape_string($link, $_POST['name']);

    // Check if the customer exists
    $customer_query = "SELECT * FROM customers WHERE phone_number = '$phone_number'";
    $customer_result = mysqli_query($link, $customer_query);

    if ($customer_result && mysqli_num_rows($customer_result) > 0) {
        // Existing customer: Update visit count and last visit with Sri Lanka time
        $customer = mysqli_fetch_assoc($customer_result);
        $visit_count = $customer['visit_count'] + 1;
        $current_datetime = date('Y-m-d H:i:s'); // Sri Lanka datetime

        $update_query = "UPDATE customers SET visit_count = $visit_count, last_visit = '$current_datetime' WHERE phone_number = '$phone_number'";
        mysqli_query($link, $update_query);

        if ($visit_count === 2) {
            $visit_message = "Second time visit";
        } elseif ($visit_count === 3) {
            $visit_message = "Third time visit";
        } elseif ($visit_count > 3) {
            $visit_message = "Visited $visit_count times";
        }
    } else {
        // New customer: Insert record with Sri Lanka time
        $current_datetime = date('Y-m-d H:i:s'); // Sri Lanka datetime
        $insert_query = "INSERT INTO customers (name, phone_number, visit_count, last_visit) VALUES ('$customer_name', '$phone_number', 1, '$current_datetime')";
        mysqli_query($link, $insert_query);

        $visit_message = "First time visit";
    }
}

// Default query for all menu items
$query = "SELECT * FROM menu ORDER BY item_id";
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search = mysqli_real_escape_string($link, $_POST['search']);
    $query = "SELECT * FROM menu WHERE item_type LIKE '%$search%' OR item_category LIKE '%$search%' OR item_name LIKE '%$search%' OR item_id LIKE '%$search%' ORDER BY item_id";
}

$result = mysqli_query($link, $query);
?>
<!DOCTYPE html>
<html>

<head>
    <link href="../css/pos.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    /* General Styling */
    body {
        background-color: #000; /* Black background for the entire page */
        color: #fff; /* White font color for all text */
        font-family: Arial, sans-serif; /* Add a clean, readable font */
    }

    .customer-info {
        margin-top: 20px;
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #fff; /* White border for contrast */
        border-radius: 5px;
        background-color: #111; /* Slightly lighter black for the section */
    }

    .customer-info form {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .customer-info .alert {
        margin-top: 10px;
        background-color: #222; /* Dark background for alert */
        color: #fff;
    }

    .cart-section-container {
        position: fixed;
        top: 10%;
        left: 10%;
        width: 80%;
        max-height: 80%;
        background: #111; /* Slightly lighter black for the cart container */
        border: 1px solid #fff;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(255, 255, 255, 0.2);
        z-index: 1000;
        overflow-y: auto;
    }

    .cart-section {
        max-height: 70vh;
        overflow-y: auto;
        color: #fff; /* Ensure font is white */
    }

    .cart-section h3 {
        margin-top: 15px;
        font-size: 24px; /* Adjust header font size */
    }

    .container {
        text-align: center;
        padding: 20px;
    }

    .header-section {
        margin-bottom: 40px;
    }

    .header-section h4 {
        font-size: 28px; /* Increase header font size */
    }

    .item-buttons {
        display: flex;
        flex-wrap: wrap; /* Allow buttons to wrap if they exceed the width */
        gap: 10px; /* Space between buttons */
        justify-content: center; /* Center the buttons horizontally */
        position: sticky; /* Keep buttons fixed at the top while scrolling */
        top: 0;
        z-index: 1000; /* Ensure buttons stay above other elements */
        background-color: #000; /* Match the page background color */
        padding: 10px;
        border-bottom: 1px solid #fff; /* Optional: Add a separator line */
    }

    .item-buttons button {
        width: 100%;
        background-color: #444; /* Dark gray buttons */
        color: #fff;
        border: 1px solid #fff;
        border-radius: 5px;
        font-size: 20px; /* Increase button font size */
    }

    .item-buttons button:hover {
        background-color: #555; /* Slightly lighter gray on hover */
    }

    .item-table {
        margin-top: 20px;
        min-width: 300px;
    }

    .item-table th,
    .item-table td {
        text-align: center;
        border: 1px solid #fff;
        padding: 10px;
        font-size: 18px; /* Adjust table font size */
    }

    table {
        color: #fff;
        border-collapse: collapse;
        width: 100%;
    }

    th {
        background-color: #222; /* Darker header row */
    }

    /* Responsive Styling for Mobile Devices */
    @media (max-width: 768px) {
        body {
            font-size: 32px; /* Large base font size for mobile */
        }

        .item-buttons {
            margin-bottom: 20px;
        }

        .item-buttons button {
            font-size: 32px; /* Larger buttons for mobile */
        }

        .item-table th,
        .item-table td {
            font-size: 28px; /* Larger table text for readability */
        }

        .header-section h4 {
            font-size: 36px; /* Larger header for mobile */
        }

        .customer-info input,
        .customer-info button {
            font-size: 32px; /* Increase font size for inputs and buttons */
        }

        .cart-section h3 {
            font-size: 32px; /* Adjust section headers */
        }
    }

    @media (max-width: 576px) {
        .cart-section-container {
            width: 100%; /* Full width for smaller devices */
            left: 0;
            top: 5%;
            padding: 10px; /* Adjust padding */
        }

        .item-table {
            min-width: 100%;
        }

        .header-section h4 {
            font-size: 36px; /* Adjust header size for smaller devices */
        }

        .item-buttons {
            flex-direction: column; /* Stack buttons vertically */
            gap: 20px; /* Increase gap between buttons */
        }

        .customer-info input,
        .customer-info button {
            font-size: 36px; /* Further increase for very small screens */
        }

        .item-buttons button {
            font-size: 36px; /* Larger buttons for smaller screens */
        }

        .item-table th,
        .item-table td {
            font-size: 32px; /* Even larger table text for small devices */
        }

        .cart-section h3 {
            font-size: 36px; /* Larger section headers */
        }
    }
</style>


 
</head>

</div>
<body>
    <div class="container-fluid">
        <div class="row">
            
            <!-- Left Section for Item Buttons -->
            <div class="col-md-3">
                <div class="header-section">
                    <h4>Massimo's Coffee House & Pizzeria POS PANEL</h4>
                </div>
                
                <div class="item-buttons">
                    <button id="view-cart-btn" class="btn mt-3" style="background-color: gray; color: white;">View Cart</button>
                    <?php
                    if ($item_type_result) {
                        while ($row = mysqli_fetch_assoc($item_type_result)) {
                            $item_type = $row['item_type'];
                            echo "<button class='btn btn-primary' onclick=\"loadItems('$item_type')\">$item_type</button>";
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- Right Section for Item Table -->
            <div class="col-md-9">
                <div class="mb-3">
                    <form method="POST" action="#">
                        <div class="row">
                            <div class="col-md-6">
                                 <div class="customer-info">
                                    
                                <input type="text" id="search" name="search" class="form-control" placeholder="Search Foods & Drinks">
                           
                            
                          
                            
                            
                        </div>
                    </form>
                </div>
                <div class="d-flex align-items-center mt-3">
    <?php
    $id = $_GET['id'];
    echo '<form action="newCustomer.php" method="get" style="margin-right: 10px;">'; // Add margin for spacing
    echo '<input type="hidden" name="table_id" value="' . $table_id . '">';
    echo '<input type="hidden" name="id" value="' . $id . '">';
    echo '<button type="submit" name="new_customer" value="true" class="btn btn-warning">New Customer</button>';
    echo '</form>';
?>


    <!-- Shortcut button to navigate to holdOrder.php -->
    <button onclick="window.location.href=''" class="btn btn-secondary" style="margin-right: 10px;">Press The F10 View Held Orders</button>



    <!-- Search button -->
    <button type="submit" class="btn btn-dark">Search</button>
</div>

                <!-- Customer Information Section -->
        <div class="customer-info">
    <form method="POST" action="">
        
        <div class="row">
            
            <div class="col-md-5">
                <input type="text" name="name" class="form-control" placeholder="Customer Name" required>
            </div>
            <div class="col-md-5">
                <input type="text" name="phone_number" class="form-control" placeholder="Phone Number" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary"> Submit </--Submit--></button>
        
            
                
            </div>
               

            <div class="back-button-container">
    <button class="btn btn-secondary" onclick="window.location.href='/adminSide/panel/pos-panel.php';">Back</button>
</div>
        </div>
    </form>
    <!-- Optional: Alert message -->
    <?php if (!empty($visit_message)) { ?>
        <div class="alert alert-info mt-2"><?php echo $visit_message; ?></div>
    <?php } ?>
</div>


                <!-- Item Table Section -->
                 <div class="customer-info">
                <div id="item-details" class="table-responsive item-table">
                    
                    <?php
                    $query = "SELECT * FROM menu ORDER BY item_id"; // Default query for all items
                    if (isset($_POST['search']) && !empty($_POST['search'])) {
                        $search = mysqli_real_escape_string($link, $_POST['search']);
                        $query = "SELECT * FROM menu WHERE item_type LIKE '%$search%' OR item_category LIKE '%$search%' OR item_name LIKE '%$search%' OR item_id LIKE '%$search%' ORDER BY item_id;";
                    } elseif (isset($_GET['item_type'])) {
                        $item_type = mysqli_real_escape_string($link, $_GET['item_type']);
                        $query = "SELECT * FROM menu WHERE item_type = '$item_type'";
                    }

                    $result = mysqli_query($link, $query);

                    if ($result) {
                        if (mysqli_num_rows($result) > 0) {
                            echo '<table class="table table-bordered table-striped">';
                            echo "<thead>";
                            echo "<tr>";
                            echo "<th>ID</th>";
                            echo "<th>Item Name</th>";
                            echo "<th>Category</th>";
                            echo "<th>Price</th>";
                            echo "<th>Add</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";

                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['item_id'] . "</td>";
                                echo "<td>" . $row['item_name'] . "</td>";
                                echo "<td>" . $row['item_category'] . "</td>";
                                echo "<td>" . number_format($row['item_price'], 2) . "</td>";

                                // Check if the bill has been paid
                                $payment_time_query = "SELECT payment_time FROM bills WHERE bill_id = '$bill_id'";
                                $payment_time_result = mysqli_query($link, $payment_time_query);
                                $has_payment_time = false;

                                if ($payment_time_result && mysqli_num_rows($payment_time_result) > 0) {
                                    $payment_time_row = mysqli_fetch_assoc($payment_time_result);
                                    if (!empty($payment_time_row['payment_time'])) {
                                        $has_payment_time = true;
                                    }
                                }

                                if (!$has_payment_time) {
                                    echo '<td>
                                        <form method="get" action="addItem.php">
                                            <input type="hidden" name="table_id" value="' . $table_id . '">
                                            <input type="hidden" name="id" value="' . $id . '">
                                            <input type="hidden" name="item_id" value="' . $row['item_id'] . '">
                                            <input type="hidden" name="bill_id" value="' . $bill_id . '">
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity(this)">-</button>
                                                <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="1000" required>
                                                <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity(this)">+</button>
                                            </div>
                                            <input type="hidden" name="addToCart" value="1">
                                            <button type="submit" class="btn btn-primary mt-2">Add to Cart</button>
                                        </form>
                                    </td>';
                                } else {
                                    echo '<td>Bill Paid</td>';
                                }

                                echo "</tr>";
                            }

                            echo "</tbody>";
                            echo "</table>";
                        } else {
                            echo '<div class="alert alert-danger"><em>No menu items were found.</em></div>';
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Increment/Decrement Buttons -->
     <!-- JavaScript for Increment/Decrement Buttons -->
    <script>
        function increaseQuantity(button) {
            const quantityInput = button.parentNode.querySelector('input[name="quantity"]');
            let value = parseInt(quantityInput.value, 10);
            value = isNaN(value) ? 1 : value + 1;
            quantityInput.value = value;
        }

        function decreaseQuantity(button) {
            const quantityInput = button.parentNode.querySelector('input[name="quantity"]');
            let value = parseInt(quantityInput.value, 10);
            value = isNaN(value) ? 1 : value - 1;
            quantityInput.value = value < 1 ? 1 : value; // Prevent value less than 1
        }

        function loadItems(itemType) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('item_type', itemType);
            window.location.search = urlParams.toString();
        }
    </script>


  
<!-- Cart Section Popup -->
<div id="cart-section-container" class="cart-section-container" style="display: none;">
    <div class="cart-section">
        <div class="container">
            <!--<button onclick="window.location.href='holdOrder.php'" class="btn btn-secondary mt-3">View Held Orders</button>-->
        </div>

        <hr>
        <h3>Kitchen Items</h3>
        <form id="printSelectedKOTForm" method="GET" action="kotprint.php" target="_blank">
            <input type="hidden" name="bill_id" value="<?php echo htmlspecialchars($bill_id); ?>">
            <input type="hidden" name="table_id" value="<?php echo htmlspecialchars($table_id); ?>">
            
            <!-- New Text Input Field -->
            <div class="form-group mb-3">
                <label for="kot_notes">Enter Notes for KOT:</label>
                <input type="text" id="kot_notes" name="kot_notes" class="form-control" placeholder="Enter any notes for the kitchen">
            </div>
            
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query for KOT items
                    $kot_query = "SELECT bi.*, m.item_name, m.item_price FROM bill_items bi
                                  JOIN menu m ON bi.item_id = m.item_id
                                  WHERE bi.bill_id = '$bill_id' AND m.item_category != 'Drinks'";
                    $kot_result = mysqli_query($link, $kot_query);
                    $kot_total = 0;

                    if ($kot_result && mysqli_num_rows($kot_result) > 0) {
                        while ($row = mysqli_fetch_assoc($kot_result)) {
                            $item_id = $row['item_id'];
                            $item_name = $row['item_name'];
                            $item_price = $row['item_price'];
                            $quantity = $row['quantity'];
                            $total = $item_price * $quantity;
                            $kot_total += $total;
                            $bill_item_id = $row['bill_item_id'];

                            echo '<tr>';
                            echo '<td><input type="checkbox" name="selected_items[]" value="' . htmlspecialchars($item_id) . '"></td>';
                            echo '<td>' . htmlspecialchars($item_id) . '</td>';
                            echo '<td>' . htmlspecialchars($item_name) . '</td>';
                            echo '<td>Rs. ' . number_format($item_price, 2) . '</td>';
                            echo '<td>' . htmlspecialchars($quantity) . '</td>';
                            echo '<td>Rs. ' . number_format($total, 2) . '</td>';

                            echo '<td>';
                            echo '<button class="btn btn-primary" onclick="window.open(\'kotprint.php?bill_id=' . $bill_id . '&table_id=' . $table_id . '&item_id=' . $item_id . '\', \'_blank\')">Print KOT</button>';

                            $payment_time_query = "SELECT payment_time FROM bills WHERE bill_id = '$bill_id'";
                            $payment_time_result = mysqli_query($link, $payment_time_query);
                            $has_payment_time = false;

                            if ($payment_time_result && mysqli_num_rows($payment_time_result) > 0) {
                                $payment_time_row = mysqli_fetch_assoc($payment_time_result);
                                if (!empty($payment_time_row['payment_time'])) {
                                    $has_payment_time = true;
                                }
                            }

                            if (!$has_payment_time) {
                                echo ' <a class="btn btn-dark" href="deleteItem.php?bill_id=' . $bill_id . '&table_id=' . $table_id . '&bill_item_id=' . $bill_item_id . '&item_id=' . $item_id . '&id=' . $id .'">Delete</a>';

                            } else {
                                echo ' <span class="badge badge-success">Bill Paid</span>';
                            }

                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7">No Kitchen Items in Cart.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>

            <div class="col-12">
                <!-- Print selected KOT items button -->
                <button type="submit" class="btn btn-success">Print Selected KOT</button>
            </div>
        </form>
        <a href="receipt3.php?bill_id=<?php echo $bill_id; ?>" target="_blank" class="btn btn-warning">Generate Temporary Bill</a>

<hr>
<h3>Outdoor Items</h3>
<form id="printSelectedForm" method="GET" action="barprint.php" target="_blank">
    <input type="hidden" name="bill_id" value="<?php echo htmlspecialchars($bill_id); ?>">
    <input type="hidden" name="table_id" value="<?php echo htmlspecialchars($table_id); ?>">

    <!-- Add OOT Notes Input Field -->
    <div class="form-group">
        <label for="oot_notes">OOT Notes:</label>
        <input type="text" name="oot_notes" id="oot_notes" class="form-control" placeholder="Enter notes for OOT" maxlength="255">
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Select</th>
                <th>Item ID</th>
                <th>Item Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody style="max-height: 40rem; overflow-y: auto;">
            <?php
            // Query for BOT items
            $bot_query = "SELECT bi.*, m.item_name, m.item_price FROM bill_items bi
                          JOIN menu m ON bi.item_id = m.item_id
                          WHERE bi.bill_id = '$bill_id' AND m.item_category = 'Drinks'";
            $bot_result = mysqli_query($link, $bot_query);
            $bot_total = 0;

            if ($bot_result && mysqli_num_rows($bot_result) > 0) {
                while ($row = mysqli_fetch_assoc($bot_result)) {
                    $item_id = $row['item_id'];
                    $item_name = $row['item_name'];
                    $item_price = $row['item_price'];
                    $quantity = $row['quantity'];
                    $total = $item_price * $quantity;
                    $bot_total += $total;
                    $bill_item_id = $row['bill_item_id'];

                    echo '<tr>';
                    echo '<td><input type="checkbox" name="selected_items[]" value="' . htmlspecialchars($item_id) . '"></td>';
                    echo '<td>' . htmlspecialchars($item_id) . '</td>';
                    echo '<td>' . htmlspecialchars($item_name) . '</td>';
                    echo '<td>Rs. ' . number_format($item_price, 2) . '</td>';
                    echo '<td>' . htmlspecialchars($quantity) . '</td>';
                    echo '<td>Rs. ' . number_format($total, 2) . '</td>';
                    
                    echo '<td>';
                    
                    // Add Print OOT button for individual items
                    echo '<button class="btn btn-primary" onclick="window.open(\'barprint.php?bill_id=' . $bill_id . '&table_id=' . $table_id . '&item_id=' . $item_id . '&quantity=' . $quantity . '&item_name=' . urlencode($item_name) . '\', \'_blank\')">Print OOT</button>';

                    // Add delete button if payment is not completed
                    $payment_time_query = "SELECT payment_time FROM bills WHERE bill_id = '$bill_id'";
                    $payment_time_result = mysqli_query($link, $payment_time_query);
                    $has_payment_time = false;

                    if ($payment_time_result && mysqli_num_rows($payment_time_result) > 0) {
                        $payment_time_row = mysqli_fetch_assoc($payment_time_result);
                        if (!empty($payment_time_row['payment_time'])) {
                            $has_payment_time = true;
                        }
                    }

                    if (!$has_payment_time) {
                        echo ' <a class="btn btn-dark" href="deleteItem.php?bill_id=' . $bill_id . '&table_id=' . $table_id . '&bill_item_id=' . $bill_item_id . '&item_id=' . $item_id . '&id=' . $id .'">Delete</a>';
                    } else {
                        echo ' <span class="badge badge-success">Bill Paid</span>';
                    }

                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="7">No Bar Items in Cart.</td></tr>';
            }
            ?>
        </tbody>
    </table>

    <div class="col-12">
        <!-- Print selected OOT items button -->
        <button type="submit" class="btn btn-success">Print Selected OOT</button>
    </div>
</form>



<hr>
<div class="table-responsive">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <td><strong>Kitchen Total</strong></td>
                <td>Rs. <?php echo number_format($kot_total, 2); ?></td>
            </tr>
            <tr>
                <td><strong>Outdoor Total</strong></td>
                <td>Rs. <?php echo number_format($bot_total, 2); ?></td>
            </tr>
            <tr>
                <td><strong>Grand Total</strong></td>
                <td>Rs. <?php echo number_format($kot_total + $bot_total, 2); ?></td>
            </tr>
        </tbody>
    </table>
</div>

<?php
$grand_total = $kot_total + $bot_total;

// Check if there is a valid total amount
if ($grand_total > 0) {
    echo '<div class="col-12">';
    
    // Role-based visibility
    if ($_SESSION['roll'] == 1) { // Waiter
        // Hold Payment button only
        echo '<a href="holdPayment.php?bill_id=' . $bill_id . '&table_id=' . $table_id . '" class="btn btn-secondary">Hold Payment</a>';
    } elseif ($_SESSION['roll'] == 5 || $_SESSION['roll'] == 2 || $_SESSION['roll'] == 3) { // Cashier, Manager, or Admin
        // Pay Bill button
        echo '<a href="posCashPayment.php?bill_id=' . $bill_id . '&staff_id=' . ($_SESSION['logged_account_id'] ?? '') . '&member_id=1&reservation_id=1111111" class="btn btn-success">Pay Bill</a>';
        
        // Hold Payment button
        
    } elseif ($_SESSION['roll'] == 4) { // Chef
        // No buttons for chefs
        echo '<div class="alert alert-info">Chef cannot process payments.</div>';
    }
    
    echo '</div>';
} else {
    echo '<br><h3>Add Item To Cart to Proceed</h3>';
}
?>




                    </div>
                </div>

            </div>
        </div>
    </div>




    <script>
      
        // Add a keyboard shortcut (F10) to navigate to holdOrder.php
        document.addEventListener('keydown', function(event) {
            if (event.key === '') {
                event.preventDefault(); // Prevent default browser behavior for F10
                window.location.href = 'holdOrder.php';
            }
        });
        function printKOT() {
    const billId = '<?php echo $bill_id; ?>';
    const tableId = '<?php echo $table_id; ?>';
  

    if (!billId || !tableId) {
        alert('Missing required parameters for KOT print.');
        return;
    }

    const url = `kotprint.php?bill_id=${billId}&id=<?php echo $id; ?>&table_id=${tableId}`;
    console.log('Opening KOT Print URL:', url);
    window.open(url, '_blank');
}
        
        // Toggle Cart Section Visibility
    document.getElementById('view-cart-btn').addEventListener('click', function () {
        const cartSection = document.getElementById('cart-section-container');
        cartSection.style.display = cartSection.style.display === 'none' ? 'block' : 'none';
    });
    </script>
    

    <?php include '../inc/dashFooter.php'; ?>