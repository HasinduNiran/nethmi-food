<?php
require_once '../config.php';

if (isset($_GET['item_type'])) {
    $item_type = mysqli_real_escape_string($link, $_GET['item_type']);
    $bill_id = $_GET['bill_id'];
    $table_id = $_GET['table_id'];
    $id = $_GET['id'];

    // Fetch items for the selected item type
    $query = "SELECT * FROM menu WHERE item_type = '$item_type'";
    $result = mysqli_query($link, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo '<table class="table table-bordered table-striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Item Name</th>';
        echo '<th>Category</th>';
        echo '<th>Price</th>';
        echo '<th>Add</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['item_id'] . '</td>';
            echo '<td>' . $row['item_name'] . '</td>';
            echo '<td>' . $row['item_category'] . '</td>';
            echo '<td>Rs. ' . number_format($row['item_price'], 2) . '</td>';
            echo '<td>';
            echo '<form method="POST" action="addItem.php">';
            echo '<input type="hidden" name="bill_id" value="' . $bill_id . '">';
            echo '<input type="hidden" name="table_id" value="' . $table_id . '">';
            echo '<input type="hidden" name="item_id" value="' . $row['item_id'] . '">';
            echo '<div class="input-group">';
            echo '<button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity(this)">-</button>';
            echo '<input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="1000" required>';
            echo '<button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity(this)">+</button>';
            echo '</div>';
            echo '<button type="submit" class="btn btn-success mt-2">Add</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<div class="alert alert-danger"><em>No items found for this type.</em></div>';
    }
}
?>

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
</script>
