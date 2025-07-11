<?php
session_start();
require_once '../config.php';
include '../inc/dashHeader.php';

if (!isset($_GET['bill_id'])) {
    die('<div class="alert alert-danger">Invalid request. Missing parameters.</div>');
}

$bill_id = intval($_GET['bill_id']);

// Fetch held items for the given bill_id
$held_query = "
    SELECT hp.item_id, hp.quantity, hp.price, hp.total, m.item_name
    FROM held_payments hp
    JOIN menu m ON hp.item_id = m.item_id
    WHERE hp.bill_id = $bill_id
";
$held_result = mysqli_query($link, $held_query);

if (!$held_result) {
    die('<div class="alert alert-danger">Error fetching held payment data: ' . mysqli_error($link) . '</div>');
}

$grand_total = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalize_payment'])) {
    $payment_method = $_POST['payment_method'];
    $payment_amount = floatval($_POST['payment_amount']);
    $discount_percentage = floatval($_POST['discount']); // Get discount percentage
    $final_total = floatval($_POST['final_total']);

    // Calculate the discount and net total
    $discount_amount = ($final_total * $discount_percentage) / 100;
    $net_total = $final_total - $discount_amount;

    // Validate customer payment
    if ($payment_amount >= $net_total) {
        $change = $payment_amount - $net_total;

        // Update the database
        $currentTime = date('Y-m-d H:i:s');
        $updateQuery = "UPDATE bills 
                        SET payment_method = '$payment_method', 
                            payment_time = '$currentTime',
                            payment_amount = $net_total, 
                            discount = $discount_amount,
                            paid_amount = $payment_amount,
                            balance_amount = $change, 
                            order_status = 'complete'
                        WHERE bill_id = $bill_id";

        if ($link->query($updateQuery) === TRUE) {
            echo '<div class="alert alert-success">
                    Payment finalized successfully! Balance: Rs. ' . number_format($change, 2) . '
                  </div>
                  <a href="https://havok.nexarasolutions.site/adminSide/panel/pos-panel.php" class="btn btn-dark">Back to POS Table</a>
                  <a href="receipt2.php?bill_id=' . $bill_id . '" class="btn btn-light">Print Receipt</a>';
        } else {
            echo '<div class="alert alert-danger">Error updating bill: ' . $link->error . '</div>';
        }
    } else {
        $remaining = number_format($net_total - $payment_amount, 2);
        echo '<div class="alert alert-warning">Insufficient payment. Rs. ' . $remaining . ' more required.</div>';
    }
}

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
                                    <th>Item Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($held_result)) {
                                    $item_name = $row['item_name'];
                                    $price = $row['price'];
                                    $quantity = $row['quantity'];
                                    $total = $row['total'];
                                    $grand_total += $total;

                                    echo "<tr>
                                          <td>$item_name</td>
                                          <td>Rs. " . number_format($price, 2) . "</td>
                                          <td>$quantity</td>
                                          <td>Rs. " . number_format($total, 2) . "</td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="text-right">
                        <?php
                        $final_total = $grand_total;
                        echo "<strong>Grand Total:</strong> Rs. " . number_format($final_total, 2);
                        ?>
                    </div>

                    <!-- Payment Form -->
                   <form action="" method="post">
    <input type="hidden" name="bill_id" value="<?php echo $bill_id; ?>">
    <input type="hidden" name="final_total" value="<?php echo $final_total; ?>">

    <!-- Discount Field -->
    <div class="form-group mt-3">
        <label for="discount">Discount</label>
        <select id="discount" name="discount" class="form-control" onchange="calculateGrandTotal()" required>
            <option value="0">No Discount</option>
            <option value="5">5% Customer</option>
            <option value="10">10% Best Customer</option>
            <option value="15">15% VIP Customer</option>
            <option value="20">20% Staff</option>
        </select>
    </div>

    <!-- Payment Method -->
    <div class="form-group">
        <label for="payment_method">Payment Method</label>
        <select id="payment_method" name="payment_method" class="form-control" required>
            <option value="cash">Cash</option>
            <option value="card">Card</option>
        </select>
    </div>

    <!-- Payment Amount -->
    <div class="form-group">
        <label for="payment_amount">Enter Payment Amount</label>
        <input type="number" id="payment_amount" name="payment_amount" class="form-control" required>
    </div>

    <button type="submit" name="finalize_payment" class="btn btn-dark mt-3">Finalize Payment</button>
</form>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid fixed-bottom py-3" style="background-color: #f8f9fa; border-top: 2px solid #ccc;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalize_payment'])) {
                $payment_method = $_POST['payment_method'];
                $payment_amount = floatval($_POST['payment_amount']);
                $final_total = floatval($_POST['final_total']);

                if ($payment_amount >= $final_total) {
                    $change = $payment_amount - $final_total;

                    $currentTime = date('Y-m-d H:i:s');
                    $updateQuery = "UPDATE bills 
                                    SET payment_method = '$payment_method', 
                                        payment_time = '$currentTime',
                                        payment_amount = $final_total, 
                                        order_status = 'complete'
                                    WHERE bill_id = $bill_id";

                    if ($link->query($updateQuery) === TRUE) {
                        echo '<div class="alert alert-success">Payment finalized successfully. Change: Rs. ' . number_format($change, 2) . '</div>
                              <a href="https://havok.nexarasolutions.site/adminSide/panel/pos-panel.php" class="btn btn-dark">Back to POS Table</a>
                              <a href="receipt2.php?bill_id=' . $bill_id . '" class="btn btn-light">Print Receipt</a>';
                    } else {
                        echo '<div class="alert alert-danger">Error updating bill: ' . $link->error . '</div>';
                    }
                } else {
                    $remaining = number_format($final_total - $payment_amount, 2);
                    echo '<div class="alert alert-warning">Insufficient payment. Rs. ' . $remaining . ' more required.</div>';
                }
            }
            ?>
        </div>
    </div>
</div>
<script>
    function calculateGrandTotal() {
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const originalTotal = parseFloat(<?php echo $grand_total; ?>);

        if (isNaN(discount) || isNaN(originalTotal) || originalTotal <= 0) {
            document.getElementById('payment_amount').placeholder = originalTotal.toFixed(2);
            return;
        }

        const discountAmount = (originalTotal * discount) / 100;
        const netTotal = originalTotal - discountAmount;

        document.getElementById('payment_amount').placeholder = netTotal.toFixed(2);
    }
</script>


<?php include '../inc/dashFooter.php'; ?>
