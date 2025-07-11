<?php
session_start();
require_once '../config.php';
include '../inc/dashHeader.php';

$bill_id = intval($_GET['bill_id']);
$staff_id = intval($_GET['staff_id']);
$member_id = intval($_GET['member_id']);
$reservation_id = intval($_GET['reservation_id']);

// Begin wrapping output in a container
echo '<div class="bottom-right-container">';

if (isset($_POST['pay'])) {
    // Validate inputs
    if (empty($_POST['payment_method']) || empty($_POST['payment_amount'])) {
        echo '<div class="alert alert-danger">Invalid payment details. Please try again.</div>';
    } else {
        $payment_methods = $_POST['payment_method'];
        $payment_amounts = $_POST['payment_amount'];

        $total_paid = array_sum($payment_amounts);
        $discount_percentage = floatval($_POST['discount']);
        $cart_total = floatval($_POST['CARTTOTAL']);
        $discount_amount = ($cart_total * $discount_percentage) / 100;
        $net_total = $cart_total - $discount_amount;
        $balance_amount = $total_paid - $net_total;

        // Validate total payment
        if ($total_paid < $net_total) {
            echo '<div class="alert alert-danger">Total payment is less than the bill amount. Please pay the full amount.</div>';
        } else {
            $currentTime = date('Y-m-d H:i:s');

            // Start transaction
            $link->begin_transaction();

            try {
               $selected_payment_method = $payment_methods[0]; // Use the first payment method

$updateQuery = "UPDATE bills 
                SET payment_time = ?, payment_amount = ?, discount = ?, 
                    paid_amount = ?, balance_amount = ?, staff_id = ?, 
                    member_id = ?, reservation_id = ?, payment_method = ? 
                WHERE bill_id = ?";
$stmt = $link->prepare($updateQuery);
$stmt->bind_param(
    "sddddiiisi",
    $currentTime,
    $net_total,
    $discount_amount,
    $total_paid,
    $balance_amount,
    $staff_id,
    $member_id,
    $reservation_id,
    $selected_payment_method,
    $bill_id
);
$stmt->execute();


                // Insert each payment method and amount into bill_payments table
                $insertQuery = "INSERT INTO bill_payments (bill_id, payment_method, amount) VALUES (?, ?, ?)";
                $stmt = $link->prepare($insertQuery);

                foreach ($payment_methods as $index => $method) {
                    $stmt->bind_param("isd", $bill_id, $method, $payment_amounts[$index]);
                    $stmt->execute();
                }

                // Inventory Deduction Logic
                $cart_query = "SELECT bi.quantity, pl.iteam_id, pl.qty AS required_qty 
                               FROM bill_items bi
                               JOIN product_listing pl ON bi.item_id = pl.menu_id
                               WHERE bi.bill_id = ?";
                $stmt = $link->prepare($cart_query);
                $stmt->bind_param("i", $bill_id);
                $stmt->execute();
                $cart_result = $stmt->get_result();

                if ($cart_result && $cart_result->num_rows > 0) {
                    while ($row = $cart_result->fetch_assoc()) {
                        $ingredient_id = $row['iteam_id']; // Ingredient ID from product_listing
                        $required_qty = $row['required_qty'] * $row['quantity']; // Total quantity needed

                        // Fetch current inventory quantity
                        $inventory_query = "SELECT qty FROM inventory WHERE id = ?";
                        $stmt_inventory = $link->prepare($inventory_query);
                        $stmt_inventory->bind_param("i", $ingredient_id);
                        $stmt_inventory->execute();
                        $inventory_result = $stmt_inventory->get_result();

                        if ($inventory_result && $inventory_result->num_rows > 0) {
                            $inventory_row = $inventory_result->fetch_assoc();
                            $current_qty = $inventory_row['qty'];

                            if ($current_qty >= $required_qty) {
                                // Deduct inventory and update wastage
                                $updateInventoryQuery = "UPDATE inventory 
                                                          SET qty = qty - ?, wastage = wastage + ? 
                                                          WHERE id = ?";
                                $stmt_update = $link->prepare($updateInventoryQuery);
                                $stmt_update->bind_param("ddi", $required_qty, $required_qty, $ingredient_id);
                                $stmt_update->execute();
                            } else {
                                echo '<div class="alert alert-warning">
                                        Not enough inventory for ingredient ID: ' . $ingredient_id . '. Required: ' . $required_qty . ', Available: ' . $current_qty . '
                                      </div>';
                            }
                        } else {
                            echo '<div class="alert alert-warning">
                                    Ingredient ID: ' . $ingredient_id . ' not found in inventory.
                                  </div>';
                        }
                    }
                } else {
                    echo '<div class="alert alert-warning">No items found for deduction in this bill.</div>';
                }

                // Commit transaction
                $link->commit();

                echo '<div class="alert alert-success">
                        Payment recorded successfully! Balance: Rs. ' . number_format($balance_amount, 2) . '
                      </div>';
            } catch (Exception $e) {
                $link->rollback();
                echo '<div class="alert alert-danger">Error processing payment. Please try again.</div>';
            }
        }
    }
}

// Retrieve and display the payment breakdown
$query = "SELECT payment_method, SUM(amount) AS total_amount FROM bill_payments WHERE bill_id = ? GROUP BY payment_method";
$stmt = $link->prepare($query);
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$result = $stmt->get_result();

echo '<h5>Payment Breakdown:</h5>';
echo '<table class="table table-bordered">';
echo '<thead><tr><th>Payment Method</th><th>Amount</th></tr></thead>';
echo '<tbody>';
while ($row = $result->fetch_assoc()) {
    echo '<tr><td>' . ucfirst($row['payment_method']) . '</td><td>Rs. ' . number_format($row['total_amount'], 2) . '</td></tr>';
}
echo '</tbody></table>';

// Action Buttons
echo '<a href="receipt.php?bill_id=' . $bill_id . '" target="_blank" class="btn btn-warning mt-2">Print Receipt</a>';
echo '<a href="../panel/pos-panel.php" class="btn btn-dark mt-2">Back to Tables</a>';
echo '</div>'; // Close bottom-right-container
?>







<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Bill Payment</h3>
                </div>
                <div class="card-body">
                    <h5>Bill ID: <?php echo $bill_id; ?></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item ID</th>
                                    <th>Item Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cart_query = "SELECT bi.*, m.item_name, m.item_price FROM bill_items bi
                                               JOIN menu m ON bi.item_id = m.item_id
                                               WHERE bi.bill_id = ?";
                                $stmt = $link->prepare($cart_query);
                                $stmt->bind_param("i", $bill_id);
                                $stmt->execute();
                                $cart_result = $stmt->get_result();
                                $cart_total = 0;

                                if ($cart_result && $cart_result->num_rows > 0) {
                                    while ($cart_row = $cart_result->fetch_assoc()) {
                                        $item_id = $cart_row['item_id'];
                                        $item_name = $cart_row['item_name'];
                                        $item_price = $cart_row['item_price'];
                                        $quantity = $cart_row['quantity'];
                                        $total = $item_price * $quantity;
                                        $cart_total += $total;

                                        echo '<tr>';
                                        echo '<td>' . $item_id . '</td>';
                                        echo '<td>' . $item_name . '</td>';
                                        echo '<td>Rs. ' . number_format($item_price, 2) . '</td>';
                                        echo '<td>' . $quantity . '</td>';
                                        echo '<td>Rs. ' . number_format($total, 2) . '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="6">No Items in Cart.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="text-right">
                        <?php
                        echo "<strong>Cart Total:</strong> Rs. " . number_format($cart_total, 2) . "<br>";
                        ?>
                    </div>
                </div>
            </div>

            <div id="cash-payment" class="container-fluid mt-5 pt-5">
                <div class="row">
                    <div class="col-md-6">
                        <h1>Payment</h1>
                       <form action="" method="post">
    <div id="payment-section">
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select name="payment_method[]" class="form-control" required>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="payment_amount">Amount</label>
            <input type="number" step="0.01" name="payment_amount[]" class="form-control" required>
        </div>
    </div>
    <button type="button" id="addPayment" class="btn btn-secondary">Add Payment Method</button>
    <div class="form-group mt-3">
        <label for="discount">Discount</label>
        <select id="discount" name="discount" class="form-control" required>
            <option value="0">No Discount</option>
            <option value="5">5% Customer</option>
            <option value="10">10% Best Customer</option>
            <option value="15">15% VIP Customer</option>
            <option value="20">20% Staff</option>
        </select>
    </div>
    <input type="hidden" name="CARTTOTAL" value="<?php echo $cart_total; ?>">
    <button type="submit" name="pay" class="btn btn-dark mt-2">Pay</button>
</form>

                        <div class="text-right mt-3">
    <a href="receipt5.php?bill_id=<?php echo $bill_id; ?>" target="_blank" class="btn btn-warning">Generate Temporary Bill</a>
</div>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function calculateGrandTotal() {
        const discount = parseFloat(document.getElementById('discount').value);
        const originalTotal = parseFloat(<?php echo $cart_total; ?>);
        const discountField = document.getElementById('discount_amount');
        const grandTotalField = document.getElementById('grand_total');

        if (isNaN(discount) || isNaN(originalTotal) || originalTotal <= 0) {
            discountField.textContent = "0.00";
            grandTotalField.value = originalTotal.toFixed(2);
            return;
        }

        const discountAmount = (originalTotal * discount) / 100;
        const netTotal = originalTotal - discountAmount;

        discountField.textContent = discountAmount.toFixed(2);
        grandTotalField.value = netTotal.toFixed(2);
    }
    
   document.getElementById('addPayment').addEventListener('click', function () {
    const paymentSection = document.getElementById('payment-section');
    const newPaymentDiv = document.createElement('div');
    newPaymentDiv.innerHTML = `
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select name="payment_method[]" class="form-control" required>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="payment_amount">Amount</label>
            <input type="number" step="0.01" name="payment_amount[]" class="form-control" required>
        </div>`;
    paymentSection.appendChild(newPaymentDiv);
});


</script>


<style>
.bottom-right-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 550px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        padding: 15px;
        text-align: right;
        overflow: auto;
    }

    .bottom-right-container h5 {
        font-size: 14px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .bottom-right-container table {
        width: 100%;
        margin-bottom: 10px;
    }

    .bottom-right-container th,
    .bottom-right-container td {
        text-align: left;
        font-size: 14px;
        padding: 5px;
    }

    .bottom-right-container .btn {
        display: inline-block;
        margin-top: 5px;
        font-size: 14px;
    }
</style>





<?php include '../inc/dashFooter.php'; ?>
