<?php
session_start();
require_once "../config.php";

// Add Asset Functionality
if (isset($_POST['add'])) {
    $asset_name = $_POST['asset_name'];
    $qty = $_POST['qty'];
    $description = $_POST['description'];
    $enter_date = $_POST['enter_date'];

    // Check if asset already exists
    $check_sql = "SELECT id FROM assets WHERE asset_name = ?";
    if ($check_stmt = $link->prepare($check_sql)) {
        $check_stmt->bind_param('s', $asset_name);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            echo "<script>Swal.fire('Error', 'Asset with the same name already exists!', 'error');</script>";
        } else {
            $sql = "INSERT INTO assets (asset_name, qty, description, enter_date) 
                    VALUES (?, ?, ?, ?)";
            if ($stmt = $link->prepare($sql)) {
                $stmt->bind_param('sdss', $asset_name, $qty, $description, $enter_date);
                $stmt->execute();
                echo "<script>Swal.fire('Success', 'Asset added successfully!', 'success');</script>";
            }
        }
    }
}

// Add Damaged Asset Functionality with Inventory Reduction
if (isset($_POST['adddamage'])) {
    $asset_name = $_POST['asset_name'];
    $qty = $_POST['qty'];
    $description = $_POST['description'];
    $enter_date = $_POST['enter_date'];

    $check_sql = "SELECT id, qty FROM assets WHERE asset_name = ?";
    if ($check_stmt = $link->prepare($check_sql)) {
        $check_stmt->bind_param('s', $asset_name);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row['qty'] >= $qty) {
                // Update asset quantity
                $update_sql = "UPDATE assets SET qty = qty - ? WHERE id = ?";
                if ($update_stmt = $link->prepare($update_sql)) {
                    $update_stmt->bind_param('di', $qty, $row['id']);
                    $update_stmt->execute();
                }

                // Insert into damages table (using enter_date as damage_date)
                $sql = "INSERT INTO damages (asset_id, damage_qty, damage_description, damage_date) 
                        VALUES (?, ?, ?, ?)";
                if ($stmt = $link->prepare($sql)) {
                    $stmt->bind_param('idss', $row['id'], $qty, $description, $enter_date);
                    $stmt->execute();
                    echo "<script>Swal.fire('Success', 'Damaged asset added and inventory updated successfully!', 'success');</script>";
                }
            } else {
                echo "<script>Swal.fire('Error', 'Insufficient quantity in assets!', 'error');</script>";
            }
        } else {
            echo "<script>Swal.fire('Error', 'Asset not found!', 'error');</script>";
        }
    }
}

// Update Asset Functionality
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $asset_name = $_POST['asset_name'];
    $qty = $_POST['qty'];
    $description = $_POST['description'];
    $enter_date = $_POST['enter_date'];

    $sql = "UPDATE assets SET 
            asset_name = ?, qty = ?, description = ?, enter_date = ? 
            WHERE id = ?";
    if ($stmt = $link->prepare($sql)) {
        $stmt->bind_param('sdssi', $asset_name, $qty, $description, $enter_date, $id);
        $stmt->execute();
        echo "<script>Swal.fire('Success', 'Asset updated successfully!', 'success');</script>";
    }
}

// Delete Functionality
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM assets WHERE id = ?";
    if ($stmt = $link->prepare($sql)) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        echo "<script>Swal.fire('Success', 'Asset deleted successfully!', 'success');</script>";
    }
}

// Fetch all assets
$assets = [];
$sql = "SELECT * FROM assets";
if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $assets[] = $row;
    }
}

// Fetch all damages
$damages = [];
$sql = "SELECT d.*, a.asset_name FROM damages d JOIN assets a ON d.asset_id = a.id";
if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $damages[] = $row;
    }
}
?>

<?php include '../inc/dashHeader.php' ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Asset Management</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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

        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea,
        select {
            width: 14%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
            vertical-align: top;
        }

        button {
            padding: 10px 15px;
            border: none;
            color: #fff;
            background-color: #28a745;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
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

        .table-container {
            max-height: 300px;
            overflow-y: auto;
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
            max-width: 1000px;
            width: 100%;
        }

        .modal-close {
            font-size: 20px;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .autocomplete-suggestions {
            position: absolute;
            border: 1px solid #ddd;
            background: #fff;
            max-height: 200px;
            overflow-y: auto;
            width: 8%;
            z-index: 1000;
        }

        .autocomplete-suggestion {
            padding: 8px;
            cursor: pointer;
        }

        .autocomplete-suggestion:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
<div style="align-items: center; padding-left: 250px; padding-top: 100px; width: 100%;">
    <h1>Asset Management</h1>

    <!-- Unified Add Asset/Damage Form with Autocomplete -->
    <form method="post" style="width: 100%;">
        <h2>Add New Asset or Damage</h2>
        <input type="text" name="asset_name" id="asset_name" placeholder="Asset Name" required style="position: relative;">
        <div id="suggestions" class="autocomplete-suggestions"></div>
        <input type="number" name="qty" placeholder="Quantity" step="0.01" required>
        <input type="text" name="description" placeholder="Description">
        <input type="date" name="enter_date" id="enter_date" required>
        <button type="submit" name="add">Add Asset</button>
        <button type="submit" name="adddamage" style="background-color: #ff4444;">Add as discard</button>
    </form>

    <!-- Assets Table -->
    <div class="table-container" <?php echo count($assets) > 7 ? 'style="max-height: 300px; overflow-y: auto;"' : ''; ?>>
        <table>
            <tr>
                <th>ID</th>
                <th>Asset Name</th>
                <th>Quantity</th>
                <th>Description</th>
                <th>Enter Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($assets as $asset) : ?>
                <tr>
                    <td><?= htmlspecialchars($asset['id']) ?></td>
                    <td><?= htmlspecialchars($asset['asset_name']) ?></td>
                    <td><?= htmlspecialchars($asset['qty']) ?></td>
                    <td><?= htmlspecialchars($asset['description']) ?></td>
                    <td><?= htmlspecialchars($asset['enter_date']) ?></td>
                    <td>
                        <button type="button" onclick="openModal(
                            <?= $asset['id'] ?>, 
                            '<?= addslashes($asset['asset_name']) ?>', 
                            <?= $asset['qty'] ?>, 
                            '<?= addslashes($asset['description']) ?>', 
                            '<?= $asset['enter_date'] ?>'
                        )">Edit</button>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $asset['id'] ?>">
                            <button type="submit" name="delete" class="delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Damages Table -->
    <h1>Damaged Assets</h1>
    <div class="table-container" <?php echo count($damages) > 7 ? 'style="max-height: 300px; overflow-y: auto;"' : ''; ?>>
        <table>
            <tr>
                <th>ID</th>
                <th>Asset Name</th>
                <th>Damage Qty</th>
                <th>Description</th>
                <th>Damage Date</th>
            </tr>
            <?php foreach ($damages as $damage) : ?>
                <tr>
                    <td><?= htmlspecialchars($damage['id']) ?></td>
                    <td><?= htmlspecialchars($damage['asset_name']) ?></td>
                    <td><?= htmlspecialchars($damage['damage_qty']) ?></td>
                    <td><?= htmlspecialchars($damage['damage_description']) ?></td>
                    <td><?= htmlspecialchars($damage['damage_date']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Update Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">×</span>
            <h2>Update Asset</h2>
            <form method="post">
                <input type="hidden" name="id" id="update_id">
                <input type="text" name="asset_name" id="update_asset_name" placeholder="Asset Name" required>
                <input type="number" name="qty" id="update_qty" placeholder="Quantity" step="0.01" required>
                <input type="text" name="description" id="update_description" placeholder="Description">
                <input type="date" name="enter_date" id="update_enter_date" required>
                <button type="submit" name="update">Update Asset</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(id, asset_name, qty, description, enter_date) {
        document.getElementById('modal').style.display = 'flex';
        document.getElementById('update_id').value = id;
        document.getElementById('update_asset_name').value = asset_name;
        document.getElementById('update_qty').value = qty;
        document.getElementById('update_description').value = description;
        document.getElementById('update_enter_date').value = enter_date;
    }

    function closeModal() {
        document.getElementById('modal').style.display = 'none';
    }

    function autocomplete(input) {
        let query = input.value;
        let suggestions = document.getElementById('suggestions');

        if (query.length < 2) {
            suggestions.innerHTML = '';
            return;
        }

        fetch(`getAssets.php?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                suggestions.innerHTML = '';
                if (data.length === 0) {
                    suggestions.innerHTML = ''; // Close suggestion box if no results
                } else {
                    data.forEach(asset => {
                        let div = document.createElement('div');
                        div.className = 'autocomplete-suggestion';
                        div.textContent = `${asset.asset_name} - ${asset.enter_date}`; // Show name and date
                        div.onclick = function() {
                            document.getElementById('asset_name').value = asset.asset_name; // Only populate asset_name
                            suggestions.innerHTML = ''; // Close suggestion box on selection
                        };
                        suggestions.appendChild(div);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                suggestions.innerHTML = ''; // Close suggestion box on error
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('asset_name').addEventListener('keyup', function() {
            autocomplete(this);
        });
    });
</script>
</body>
</html>