*<?php
session_start();
require_once '../config.php';

// Initialize variables
$bill_id = null;
$table_id = null;
$item_id = null;
$bill_item_id = null;
$id = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch parameters from the GET request
    $bill_id = $_GET['bill_id'] ?? null;
    $table_id = $_GET['table_id'] ?? null;
    $item_id = $_GET['item_id'] ?? null;
    $bill_item_id = $_GET['bill_item_id'] ?? null;
    $id = $_GET['id'] ?? null;

    if (!$bill_id || !$table_id || !$item_id || !$bill_item_id) {
        die("Missing required parameters.");
    }

    // Fetch item details
    $item_query = "SELECT bi.*, m.item_name 
                   FROM bill_items bi 
                   JOIN menu m ON bi.item_id = m.item_id 
                   WHERE bi.bill_item_id = $bill_item_id";
    $item_result = mysqli_query($link, $item_query);

    if ($item_result && mysqli_num_rows($item_result) > 0) {
        $item = mysqli_fetch_assoc($item_result);
    } else {
        die("Item not found.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch parameters from the POST request
    $bill_id = $_POST['bill_id'];
    $table_id = $_POST['table_id'];
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $reason = mysqli_real_escape_string($link, $_POST['reason']);
    $deleted_by = $_SESSION['logged_account_id'] ?? 0;
    $bill_item_id = $_POST['bill_item_id'];
    $id = $_POST['id'];

    // Debugging: Log the redirect URL
    error_log("Redirecting to: ../panel/pos-panel.php");

    // Save deletion in `deleted_items`
    $delete_query = "INSERT INTO deleted_items (bill_id, table_id, item_id, item_name, quantity, reason, deleted_by) 
                     VALUES ('$bill_id', '$table_id', '$item_id', '$item_name', '$quantity', '$reason', '$deleted_by')";
    if (mysqli_query($link, $delete_query)) {
        // Remove from `bill_items`
        $remove_query = "DELETE FROM bill_items WHERE bill_item_id = $bill_item_id";
        mysqli_query($link, $remove_query);

        
        // Redirect back to `posTable.php` with the current `id`
        header("Location: ../panel/pos-panel.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($link);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Item</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h3>Delete Item</h3>
    <form method="POST" action="deleteItem.php">
        <input type="hidden" name="bill_id" value="<?php echo htmlspecialchars($bill_id); ?>">
        <input type="hidden" name="table_id" value="<?php echo htmlspecialchars($table_id); ?>">
        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
        <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>">
        <input type="hidden" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>">
        <input type="hidden" name="bill_item_id" value="<?php echo htmlspecialchars($bill_item_id); ?>">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

        <div class="mb-3">
            <label for="reason" class="form-label">Reason for Deletion</label>
            <textarea class="form-control" id="reason" name="reason" required></textarea>
        </div>
        <button type="submit" class="btn btn-danger">Confirm Deletion</button>
        <a href="orderitem.php?bill_id=<?php echo $bill_id; ?>&table_id=<?php echo $table_id; ?>&id=<?php echo $id; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
