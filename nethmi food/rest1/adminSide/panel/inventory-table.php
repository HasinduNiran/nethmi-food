<?php
require_once "../config.php";

// Insert function
if (isset($_POST['add'])) {
    $iteamname = $_POST['iteamname'];
    $qty = $_POST['qty'];
    $mesuer = $_POST['mesuer'];
    $value = $_POST['value'];
    $manufacturedate = $_POST['manufacturedate'];
    $expierdate = $_POST['expierdate'];

    $sql = "INSERT INTO inventory (iteamname, qty, mesuer, value, manufacturedate, expierdate) 
            VALUES (?, ?, ?, ?, ?, ?)";
    if ($stmt = $link->prepare($sql)) {
        $stmt->bind_param('siidss', $iteamname, $qty, $mesuer, $value, $manufacturedate, $expierdate);
        $stmt->execute();
    } else {
        echo "Error: " . $link->error;
    }
}

// Update function
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $iteamname = $_POST['iteamname'];
    $qty = $_POST['qty'];
    $mesuer = $_POST['mesuer']; // Use measure ID
    $value = $_POST['value'];
    $manufacturedate = $_POST['manufacturedate'];
    $expierdate = $_POST['expierdate'];

    $sql = "UPDATE inventory SET 
            iteamname = ?, qty = ?, mesuer = ?, value = ?, manufacturedate = ?, expierdate = ? 
            WHERE id = ?";
    if ($stmt = $link->prepare($sql)) {
        $stmt->bind_param('siidssi', $iteamname, $qty, $mesuer, $value, $manufacturedate, $expierdate, $id);
        $stmt->execute();
    } else {
        echo "Error: " . $link->error;
    }
}

// Delete function
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM inventory WHERE id = ?";
    if ($stmt = $link->prepare($sql)) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
    } else {
        echo "Error: " . $link->error;
    }
}

// Fetch all inventory items
$inventoryItems = [];
$sql = "SELECT * FROM inventory";
if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $inventoryItems[] = $row;
    }
}
?>

<?php include '../inc/dashHeader.php' ?>

<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
    <style>
        /* General styles for page layout */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        form,
        table {
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Styling for form inputs */
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 14%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 15px;
            border: none;
            color: #fff;
            background-color: #28a745;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            align-items: flex-end;
        }

        button:hover {
            background-color: #218838;
        }

        button.delete {
            background-color: #dc3545;
        }

        button.delete:hover {
            background-color: #c82333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 1300px;
            width: 100%;
        }

        .modal-close {
            font-size: 20px;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
    <script>
        function openModal(id, iteamname, qty, mesuer_id, value, manufacturedate, expierdate) {
            document.getElementById('modal').style.display = 'flex';
            document.getElementById('update_id').value = id;
            document.getElementById('update_iteamname').value = iteamname;
            document.getElementById('update_qty').value = qty;
            document.getElementById('update_value').value = value;
            document.getElementById('update_manufacturedate').value = manufacturedate;
            document.getElementById('update_expierdate').value = expierdate;

            // Set the selected measure ID in the dropdown
            document.getElementById('update_mesuer').value = mesuer_id;
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</head>

<div style="align-items: center; padding-left: 250px; padding-top: 100px; width: 100%;">
    <h1>Inventory Management</h1>

    <!-- Add Item Form -->
    <form method="post" style="width: 100%;">
        <h2>Add New Item</h2>
        <input type="text" name="iteamname" placeholder="Item Name" required>
        <input type="number" name="qty" placeholder="Quantity" required>
        <select name="mesuer" required>
            <option value="0">Select</option>
            <?php
            $queryCategory = "SELECT * FROM mesuer";
            $resultCategory = $link->query($queryCategory);
            if ($resultCategory) {
                while ($row = $resultCategory->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['mesuer']}</option>";
                }
            } else {
                echo "Error: " . $link->error;
            }
            ?>
        </select>
        <input type="text" name="value" placeholder="Value" required>
        <input type="date" name="manufacturedate" required>
        <input type="date" name="expierdate" required>
        <button type="submit" name="add">Add Item</button>
    </form>

    <!-- Inventory Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Measure Name</th>
            <th>Value</th>
            <th>Manufacture Date</th>
            <th>Expire Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($inventoryItems as $item) : ?>
            <tr>
                <td><?= $item['id'] ?></td>
                <td><?= $item['iteamname'] ?></td>
                <td><?= $item['qty'] ?></td>
                <td>
                    <?php
                    $measureQuery = "SELECT mesuer FROM mesuer WHERE id = " . $item['mesuer'];
                    $measureResult = $link->query($measureQuery);
                    if ($measureResult) {
                        $measureRow = $measureResult->fetch_assoc();
                        echo $measureRow['mesuer'];
                    } else {
                        echo "Error: " . $link->error;
                    }
                    ?>
                </td>
                <td><?= $item['value'] ?></td>
                <td><?= $item['manufacturedate'] ?></td>
                <td><?= $item['expierdate'] ?></td>
                <td>
                    <button type="button" onclick="openModal(
                        <?= $item['id'] ?>, 
                        '<?= $item['iteamname'] ?>', 
                        <?= $item['qty'] ?>, 
                        <?= $item['mesuer'] ?>, 
                        '<?= $item['value'] ?>', 
                        '<?= $item['manufacturedate'] ?>', 
                        '<?= $item['expierdate'] ?>'
                    )">Edit</button>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <button type="submit" name="delete" class="delete">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Update Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2>Update Item</h2>
            <form method="post">
                <input type="hidden" name="id" id="update_id">
                <input type="text" name="iteamname" id="update_iteamname" placeholder="Item Name" required>
                <input type="number" name="qty" id="update_qty" placeholder="Quantity" required>
                <select name="mesuer" id="update_mesuer" required>
                    <option value="0">Select</option>
                    <?php
                    $queryCategory = "SELECT * FROM mesuer";
                    $resultCategory = $link->query($queryCategory);
                    if ($resultCategory) {
                        while ($row = $resultCategory->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['mesuer']}</option>";
                        }
                    } else {
                        echo "Error: " . $link->error;
                    }
                    ?>
                </select>
                <input type="text" name="value" id="update_value" placeholder="Value" required>
                <input type="date" name="manufacturedate" id="update_manufacturedate" required>
                <input type="date" name="expierdate" id="update_expierdate" required>
                <button type="submit" name="update">Update Item</button>
            </form>
        </div>
    </div>
</div>
