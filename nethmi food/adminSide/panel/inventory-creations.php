<?php
session_start();
require_once "../config.php";

// Update inventory creation
if (isset($_POST['update_creation'])) {
    $creation_id = $_POST['creation_id'];
    $qty = $_POST['qty'];
    $mesuer = $_POST['mesuer'];
    $value = $_POST['value'];
    $manufacturedate = $_POST['manufacturedate'];
    $expierdate = $_POST['expierdate'];
    
    // if ($mesuer == 2 || $mesuer == 1) {
    //     $qty = $qty * 1000;
    // }
    
    // Start transaction
    $link->begin_transaction();
    
    try {
        // Get the original creation data
        $original_sql = "SELECT ic.*, ii.itemname FROM inventory_creations ic 
                        JOIN inventory_items ii ON ic.itemid = ii.itemid 
                        WHERE ic.id = ?";
        $original_stmt = $link->prepare($original_sql);
        $original_stmt->bind_param('i', $creation_id);
        $original_stmt->execute();
        $original_result = $original_stmt->get_result();
        $original = $original_result->fetch_assoc();
        
        if (!$original) {
            throw new Exception("Creation record not found");
        }
        
        // Calculate the difference in quantity
        $qty_diff = $qty - $original['qty'];
        
        // Update the inventory
        $inventory_sql = "SELECT id, qty FROM inventory 
                        WHERE iteamname = ? AND mesuer = ?";
        $inventory_stmt = $link->prepare($inventory_sql);
        $inventory_stmt->bind_param('si', $original['itemname'], $mesuer);
        $inventory_stmt->execute();
        $inventory_result = $inventory_stmt->get_result();
        $inventory = $inventory_result->fetch_assoc();
        
        if ($inventory) {
            // Update existing inventory record
            $update_inventory_sql = "UPDATE inventory 
                                    SET qty = qty + ?, value = ?, manufacturedate = ?, expierdate = ? 
                                    WHERE id = ?";
            $update_inventory_stmt = $link->prepare($update_inventory_sql);
            $update_inventory_stmt->bind_param('dsssi', $qty_diff, $value, $manufacturedate, $expierdate, $inventory['id']);
            $update_inventory_stmt->execute();
            
            // Check if inventory would go negative
            if ($inventory['qty'] + $qty_diff < 0) {
                throw new Exception("Cannot reduce quantity below zero");
            }
        } else {
            throw new Exception("Inventory record not found");
        }
        
        // Update the creation record
        $update_creation_sql = "UPDATE inventory_creations 
                                SET qty = ?, mesuer = ?, value = ?, manufacturedate = ?, expierdate = ? 
                                WHERE id = ?";
        $update_creation_stmt = $link->prepare($update_creation_sql);
        $update_creation_stmt->bind_param('didssi', $qty, $mesuer, $value, $manufacturedate, $expierdate, $creation_id);
        $update_creation_stmt->execute();
        
        // Commit transaction
        $link->commit();
        
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Inventory creation updated successfully!'
                    }).then(() => {
                        window.location.href = 'inventory-creations.php';
                    });
                });
              </script>";
    } catch (Exception $e) {
        // Rollback transaction
        $link->rollback();
        
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error: " . addslashes($e->getMessage()) . "'
                    });
                });
              </script>";
    }
}

// Delete inventory creation
if (isset($_POST['delete_creation'])) {
    $creation_id = $_POST['creation_id'];
    
    // Start transaction
    $link->begin_transaction();
    
    try {
        // Get the creation data
        $creation_sql = "SELECT ic.*, ii.itemname FROM inventory_creations ic 
                        JOIN inventory_items ii ON ic.itemid = ii.itemid 
                        WHERE ic.id = ?";
        $creation_stmt = $link->prepare($creation_sql);
        $creation_stmt->bind_param('i', $creation_id);
        $creation_stmt->execute();
        $creation_result = $creation_stmt->get_result();
        $creation = $creation_result->fetch_assoc();
        
        if (!$creation) {
            throw new Exception("Creation record not found");
        }
        
        // Update the inventory (subtract the quantity)
        $inventory_sql = "SELECT id, qty FROM inventory 
                        WHERE iteamname = ? AND mesuer = ?";
        $inventory_stmt = $link->prepare($inventory_sql);
        $inventory_stmt->bind_param('si', $creation['itemname'], $creation['mesuer']);
        $inventory_stmt->execute();
        $inventory_result = $inventory_stmt->get_result();
        $inventory = $inventory_result->fetch_assoc();
        
        if ($inventory) {
            // Check if inventory would go negative
            if ($inventory['qty'] < $creation['qty']) {
                throw new Exception("Cannot delete this record as it would make inventory negative");
            }
            
            // Update inventory
            $update_inventory_sql = "UPDATE inventory SET qty = qty - ? WHERE id = ?";
            $update_inventory_stmt = $link->prepare($update_inventory_sql);
            $update_inventory_stmt->bind_param('di', $creation['qty'], $inventory['id']);
            $update_inventory_stmt->execute();
        } else {
            throw new Exception("Inventory record not found");
        }
        
        // Delete the creation record
        $delete_sql = "DELETE FROM inventory_creations WHERE id = ?";
        $delete_stmt = $link->prepare($delete_sql);
        $delete_stmt->bind_param('i', $creation_id);
        $delete_stmt->execute();
        
        // Commit transaction
        $link->commit();
        
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Inventory creation deleted successfully!'
                    }).then(() => {
                        window.location.href = 'inventory-creations.php';
                    });
                });
              </script>";
    } catch (Exception $e) {
        // Rollback transaction
        $link->rollback();
        
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error: " . addslashes($e->getMessage()) . "'
                    });
                });
              </script>";
    }
}

// Fetch all inventory creations
$creations = [];
$sql = "SELECT ic.*, ii.itemname, m.mesuer as mesuer_name 
        FROM inventory_creations ic 
        JOIN inventory_items ii ON ic.itemid = ii.itemid 
        LEFT JOIN mesuer m ON ic.mesuer = m.id 
        ORDER BY ic.created_at DESC";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
    $creations[] = $row;
}

include '../inc/dashHeader.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock History</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #4a6cf7;
            --secondary-color: #6b7280;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --light-color: #f3f4f6;
            --dark-color: #1f2937;
            --border-color: #e5e7eb;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
            color: #374151;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
            transition: var(--transition);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--secondary-color);
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            line-height: 1.5;
            color: var(--dark-color);
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(74, 108, 247, 0.25);
        }

        .form-control[readonly] {
            background-color: #f3f4f6;
            opacity: 1;
        }

        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            padding: 10px 15px;
            font-size: 14px;
            line-height: 1.5;
            border-radius: 6px;
            transition: var(--transition);
            border: none;
            white-space: nowrap;
        }

        .btn-primary {
            color: #fff;
            background-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #3452d9;
        }

        .btn-success {
            color: #fff;
            background-color: var(--success-color);
        }

        .btn-success:hover {
            background-color: #0ca678;
        }

        .btn-danger {
            color: #fff;
            background-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-info {
            color: #fff;
            background-color: var(--info-color);
        }

        .btn-info:hover {
            background-color: #2563eb;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 12px;
        }

        .btn-with-icon {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-with-icon i {
            font-size: 16px;
        }

        .table-responsive {
            overflow-x: auto;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            font-weight: 600;
            color: var(--secondary-color);
            background-color: #f9fafb;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table tr:hover {
            background-color: #f3f4f6;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 75%;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .badge-success {
            color: #fff;
            background-color: var(--success-color);
        }

        .badge-danger {
            color: #fff;
            background-color: var(--danger-color);
        }

        .badge-warning {
            color: #fff;
            background-color: var(--warning-color);
        }

        .badge-info {
            color: #fff;
            background-color: var(--info-color);
            font-size:14px;
            padding:5px 7px;
        }

        .badge-light {
            color: var(--dark-color);
            background-color: var(--light-color);
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 300px;
            width: 100%;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
            pointer-events: none;
            z-index: 2;
        }

        .search-box input {
            width: 100%;
            padding: 10px 12px 10px 35px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            transition: var(--transition);
        }

        .search-box input:focus {
            border-color: var(--primary-color);
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(74, 108, 247, 0.25);
        }

        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1050;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .modal-backdrop.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            transform: translateY(-20px);
            transition: var(--transition);
        }

        .modal-backdrop.show .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
            color: var(--secondary-color);
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px;
            border-top: 1px solid var(--border-color);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
            transition: var(--transition);
        }

        .back-link:hover {
            color: #3452d9;
        }

        .back-link i {
            font-size: 16px;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .quantity-positive {
            /* color: var(--success-color); */
            font-weight: 600;
        }

        .quantity-negative {
            color: var(--danger-color);
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: flex-end;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .actions .btn {
                width: 100%;
            }
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .inventory-search-icon{
            margin-right:10px;
        }
    </style>
</head>
<body>
    <div class="container" style="padding-left: 250px; padding-top: 100px;">
        <a href="inventory-table.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>

        <div class="page-header">
            <h1 class="page-title">Stock History</h1>
        </div>

        <div class="search-box">
            <i class="fas fa-search inventory-search-icon"></i>
            <input type="text" id="searchInput" placeholder="Search by item name, date, quantity..." onkeyup="searchRecords()">
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Stock Movement Records</h2>
            </div>
            <div class="table-responsive">
                <table id="creationsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Value</th>
                            <th>Manufacture Date</th>
                            <th>Expiry Date</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($creations as $creation): ?>
                            <tr>
                                <td><?= $creation['id'] ?></td>
                                <td><strong><?= htmlspecialchars($creation['itemname']) ?></strong></td>
                                <td class="<?= $creation['qty'] > 0 ? 'quantity-positive' : ($creation['qty'] < 0 ? 'quantity-negative' : '') ?>">
                                    <?php
                                    // Format quantity based on measurement unit
                                    $displayQty = $creation['qty'];
                                    echo ($displayQty > 0 ? '+' : '') . number_format($displayQty, 2);
                                    ?>
                                </td>
                                <td><span class="badge badge-info"><?= htmlspecialchars($creation['mesuer_name'] ?? 'N/A') ?></span></td>
                                <td><?= number_format($creation['value'], 2) ?></td>
                                <td><?= $creation['manufacturedate'] ?></td>
                                <td><?= $creation['expierdate'] ?></td>
                                <td><?= $creation['created_at'] ?></td>
                                <td class="actions">
                                    <button type="button" class="btn btn-info btn-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode($creation), ENT_QUOTES, 'UTF-8') ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $creation['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="editModal" class="modal-backdrop">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Edit Stock Record</h3>
                    <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="post" id="editForm">
                        <input type="hidden" name="creation_id" id="edit_creation_id">
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="edit_itemname">Item</label>
                                <input type="text" class="form-control" id="edit_itemname" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="edit_qty">Quantity</label>
                                <input type="number" class="form-control" name="qty" id="edit_qty" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="edit_mesuer">Measurement Unit</label>
                                <select class="form-control" name="mesuer" id="edit_mesuer" required>
                                    <?php
                                    $queryMesuer = "SELECT * FROM mesuer";
                                    $resultMesuer = $link->query($queryMesuer);
                                    if ($resultMesuer) {
                                        while ($row = $resultMesuer->fetch_assoc()) {
                                            echo "<option value='{$row['id']}'>{$row['mesuer']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="edit_value">Value</label>
                                <input type="number" class="form-control" name="value" id="edit_value" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="edit_manufacturedate">Manufacture Date</label>
                                <input type="date" class="form-control" name="manufacturedate" id="edit_manufacturedate" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="edit_expierdate">Expiry Date</label>
                                <input type="date" class="form-control" name="expierdate" id="edit_expierdate" required>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                            <button type="submit" name="update_creation" class="btn btn-primary">Update Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Form (hidden) -->
        <form id="deleteForm" method="post" style="display: none;">
            <input type="hidden" name="creation_id" id="delete_id">
            <input type="hidden" name="delete_creation" value="1">
        </form>
    </div>

    <script>
        // Search functionality
        function searchRecords() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('creationsTable');
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 1; i < tr.length; i++) {
                let found = false;
                const tds = tr[i].getElementsByTagName('td');
                
                for (let j = 0; j < tds.length; j++) {
                    const txtValue = tds[j].textContent || tds[j].innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                
                tr[i].style.display = found ? '' : 'none';
            }
        }

        // Open edit modal
        function openEditModal(creation) {
            // If creation is passed as a string, parse it
            if (typeof creation === 'string') {
                creation = JSON.parse(creation);
            }
            
            // Set values in the form
            document.getElementById('edit_creation_id').value = creation.id;
            document.getElementById('edit_itemname').value = creation.itemname;
            
            // Format quantity based on measurement unit
            let displayQty = creation.qty;
            document.getElementById('edit_qty').value = displayQty;
            
            document.getElementById('edit_mesuer').value = creation.mesuer;
            document.getElementById('edit_value').value = creation.value;
            document.getElementById('edit_manufacturedate').value = creation.manufacturedate;
            document.getElementById('edit_expierdate').value = creation.expierdate;
            
            // Show the modal
            document.getElementById('editModal').classList.add('show');
        }

        // Close modal
        function closeModal() {
            document.getElementById('editModal').classList.remove('show');
        }

        // Confirm delete
        function confirmDelete(id) {
            Swal.fire({
                title: 'Delete Record?',
                text: "This will adjust the inventory. This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete_id').value = id;
                    document.getElementById('deleteForm').submit();
                }
            });
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeModal();
            }
        });

        // Add animation to cards
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-in animation to cards
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.3s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
    </script>
</body>
</html>