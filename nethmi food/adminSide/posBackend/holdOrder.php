<?php
session_start();
require_once '../config.php';
// include '../inc/dashHeader.php';

// Ensure the user is logged in
if (!isset($_SESSION['roll'])) {
    echo '<script>
            alert("Access Denied: You must log in to proceed.");
            window.location.href = "../login.php";
          </script>';
    exit;
}

// Allow roles: Cashier (5), Admin (3), Manager (2)
$allowed_roles = [2, 3, 5];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Held Orders</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Held Orders</h2>
        <div class="text-right mb-3">
            <!-- Back button to navigate to the POS Panel -->
            <a href="posTable.php" class="btn btn-secondary">Back to POS Panel</a>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Bill ID</th>
                    <th>Table ID</th>
                    <th>Staff ID</th>
                    <th>Order Item Names</th>
                    <th>Total Quantity</th>
                    <th>Order Status</th>
                    <th>Payment Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch held orders
                $query = "
                    SELECT 
                        b.bill_id, b.table_id, b.staff_id, b.order_status, b.payment_amount,
                        GROUP_CONCAT(m.item_name SEPARATOR ', ') AS item_names,
                        SUM(hp.quantity) AS total_quantity
                    FROM bills b
                    LEFT JOIN held_payments hp ON b.bill_id = hp.bill_id
                    LEFT JOIN menu m ON hp.item_id = m.item_id
                    WHERE b.order_status = 'hold'
                    GROUP BY b.bill_id
                ";
                $result = $link->query($query);

                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                        $bill_id = $row['bill_id'];
                        $table_id = $row['table_id'];
                        $staff_id = $row['staff_id'];
                        $order_status = $row['order_status'];
                        $payment_amount = $row['payment_amount'];
                        $item_names = $row['item_names'] ?: 'No Items';
                        $total_quantity = $row['total_quantity'] ?: 0;
                ?>
                        <tr>
                            <td><?php echo $bill_id; ?></td>
                            <td><?php echo $table_id; ?></td>
                            <td><?php echo $staff_id; ?></td>
                            <td><?php echo $item_names; ?></td>
                            <td><?php echo $total_quantity; ?></td>
                            <td><?php echo ucfirst($order_status); ?></td>
                            <td>Rs. <?php echo number_format($payment_amount, 2); ?></td>
                            <td>
                                <td>
    <?php if (in_array($_SESSION['roll'], $allowed_roles)): ?>
        <a href="posCashPayment.php?bill_id=<?php echo $bill_id; ?>&table_id=<?php echo $table_id; ?>" class="btn btn-primary">Proceed Payment</a>
        <a href="deleteOrder.php?bill_id=<?php echo $bill_id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
    <?php else: ?>
        <span class="text-muted">No Actions Available</span>
    <?php endif; ?>
</td>
                            </td>
                            
                        </tr>
                <?php
                    endwhile;
                else:
                    echo '<tr><td colspan="8" class="text-center">No held orders found.</td></tr>';
                endif;
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>
