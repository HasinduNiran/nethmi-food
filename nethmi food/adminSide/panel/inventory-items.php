<?php
session_start();

// Check for flash messages
$success_message = null;
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

$error_message = null;
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

require_once "../config.php";

// Add new category
if (isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    
    if (!empty($category_name)) {
        // Check if category already exists
        $check_sql = "SELECT category FROM inventory_categories WHERE category = ?";
        $check_stmt = $link->prepare($check_sql);
        $check_stmt->bind_param('s', $category_name);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['error_message'] = "Category already exists!";
            header("Location: inventory-items.php");
            exit;
        } else {
            // Insert new category
            $sql = "INSERT INTO inventory_categories (category) VALUES (?)";
            $stmt = $link->prepare($sql);
            $stmt->bind_param('s', $category_name);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Category added successfully!";
                header("Location: inventory-items.php");
                exit;
            } else {
                $_SESSION['error_message'] = "Error: " . $link->error;
                header("Location: inventory-items.php");
                exit;
            }
        }
    }
}

// Delete category
if (isset($_POST['delete_category'])) {
    $category_id = $_POST['category_id'];
    
    // Check if category is being used in inventory items
    $check_sql = "SELECT itemid FROM inventory_items WHERE category = (SELECT category FROM inventory_categories WHERE id = ?)";
    $check_stmt = $link->prepare($check_sql);
    $check_stmt->bind_param('i', $category_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "Cannot delete category that is being used by items!";
        header("Location: inventory-items.php");
        exit;
    } else {
        $sql = "DELETE FROM inventory_categories WHERE id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param('i', $category_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Category deleted successfully!";
            header("Location: inventory-items.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Error: " . $link->error;
            header("Location: inventory-items.php");
            exit;
        }
    }
}

// Add new measurement
if (isset($_POST['add_measurement'])) {
    $mesuer_name = trim($_POST['measurement_name']);
    
    if (!empty($mesuer_name)) {
        // Check if measurement already exists
        $check_sql = "SELECT mesuer FROM mesuer WHERE mesuer = ?";
        $check_stmt = $link->prepare($check_sql);
        $check_stmt->bind_param('s', $mesuer_name);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['error_message'] = "Measurement already exists!";
            header("Location: inventory-items.php");
            exit;
        } else {
            // Insert new measurement
            $sql = "INSERT INTO mesuer (mesuer) VALUES (?)";
            $stmt = $link->prepare($sql);
            $stmt->bind_param('s', $mesuer_name);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Measurement added successfully!";
                header("Location: inventory-items.php");
                exit;
            } else {
                $_SESSION['error_message'] = "Error: " . $link->error;
                header("Location: inventory-items.php");
                exit;
            }
        }
    }
}

// Delete measurement
if (isset($_POST['delete_measurement'])) {
    $mesuer_id = $_POST['mesuer_id'];
    
    // Check if measurement is being used in inventory items
    $check_sql = "SELECT itemid FROM inventory_items WHERE default_mesuer = ?";
    $check_stmt = $link->prepare($check_sql);
    $check_stmt->bind_param('i', $mesuer_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    // Also check if used in inventory
    $check_inventory_sql = "SELECT id FROM inventory WHERE mesuer = ?";
    $check_inventory_stmt = $link->prepare($check_inventory_sql);
    $check_inventory_stmt->bind_param('i', $mesuer_id);
    $check_inventory_stmt->execute();
    $inventory_result = $check_inventory_stmt->get_result();
    
    if ($result->num_rows > 0 || $inventory_result->num_rows > 0) {
        $_SESSION['error_message'] = "Cannot delete measurement that is being used by items!";
        header("Location: inventory-items.php");
        exit;
    } else {
        $sql = "DELETE FROM mesuer WHERE id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param('i', $mesuer_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Measurement deleted successfully!";
            header("Location: inventory-items.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Error: " . $link->error;
            header("Location: inventory-items.php");
            exit;
        }
    }
}

// Add new item
if (isset($_POST['add_item'])) {
    $itemname = trim($_POST['itemname']);
    $category_id = $_POST['category_id'];
    $mesuer_id = $_POST['mesuer_id'];
    
    // Check if item already exists
    $check_sql = "SELECT itemid FROM inventory_items WHERE itemname = ?";
    $check_stmt = $link->prepare($check_sql);
    $check_stmt->bind_param('s', $itemname);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "Item already exists!";
        header("Location: inventory-items.php");
        exit;
    } else {
        // Get category name
        $cat_sql = "SELECT category FROM inventory_categories WHERE id = ?";
        $cat_stmt = $link->prepare($cat_sql);
        $cat_stmt->bind_param('i', $category_id);
        $cat_stmt->execute();
        $cat_result = $cat_stmt->get_result();
        $cat_row = $cat_result->fetch_assoc();
        $category = $cat_row['category'];
        
        // Insert new item
        $sql = "INSERT INTO inventory_items (itemname, category, default_mesuer) VALUES (?, ?, ?)";
        $stmt = $link->prepare($sql);
        $stmt->bind_param('ssi', $itemname, $category, $mesuer_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Item added successfully!";
            header("Location: inventory-items.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Error: " . $link->error;
            header("Location: inventory-items.php");
            exit;
        }
    }
}

// Update item
if (isset($_POST['update_item'])) {
    $itemid = $_POST['itemid'];
    $itemname = trim($_POST['itemname']);
    $category_id = $_POST['category_id'];
    $mesuer_id = $_POST['mesuer_id'];
    
    // Get category name
    $cat_sql = "SELECT category FROM inventory_categories WHERE id = ?";
    $cat_stmt = $link->prepare($cat_sql);
    $cat_stmt->bind_param('i', $category_id);
    $cat_stmt->execute();
    $cat_result = $cat_stmt->get_result();
    $cat_row = $cat_result->fetch_assoc();
    $category = $cat_row['category'];
    
    $sql = "UPDATE inventory_items SET itemname = ?, category = ?, default_mesuer = ? WHERE itemid = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('ssii', $itemname, $category, $mesuer_id, $itemid);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Item updated successfully!";
        header("Location: inventory-items.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Error: " . $link->error;
        header("Location: inventory-items.php");
        exit;
    }
}

// Delete item
if (isset($_POST['delete_item'])) {
    $itemid = $_POST['itemid'];
    
    // Check if item is referenced in inventory
    $check_sql = "SELECT id FROM inventory WHERE iteamname = (SELECT itemname FROM inventory_items WHERE itemid = ?)";
    $check_stmt = $link->prepare($check_sql);
    $check_stmt->bind_param('i', $itemid);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "Cannot delete item that is used in inventory!";
        header("Location: inventory-items.php");
        exit;
    } else {
        $sql = "DELETE FROM inventory_items WHERE itemid = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param('i', $itemid);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Item deleted successfully!";
            header("Location: inventory-items.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Error: " . $link->error;
            header("Location: inventory-items.php");
            exit;
        }
    }
}

// Create inventory_categories table if it doesn't exist
$check_categories_table = "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'inventory_categories'";
$result = $link->query($check_categories_table);
if ($result->num_rows == 0) {
    $create_categories_table = "CREATE TABLE `inventory_categories` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `category` varchar(50) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `category` (`category`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $link->query($create_categories_table);
    
    // Migrate existing categories from inventory table
    $migrate_categories = "INSERT IGNORE INTO inventory_categories (category) 
                         SELECT DISTINCT category FROM inventory WHERE category != ''";
    $link->query($migrate_categories);
}

// Add default_mesuer column to inventory_items if it doesn't exist
$check_default_mesuer = "SELECT 1 FROM information_schema.columns 
                        WHERE table_schema = DATABASE() 
                        AND table_name = 'inventory_items' 
                        AND column_name = 'default_mesuer'";
$result = $link->query($check_default_mesuer);
if ($result->num_rows == 0) {
    $add_default_mesuer = "ALTER TABLE inventory_items ADD COLUMN default_mesuer int(11) DEFAULT NULL";
    $link->query($add_default_mesuer);
}

// Fetch all items
$items = [];
$sql = "SELECT i.*, m.mesuer as measurement_name 
        FROM inventory_items i 
        LEFT JOIN mesuer m ON i.default_mesuer = m.id
        ORDER BY i.itemname";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

// Fetch categories for dropdown
$categories = [];
$sql = "SELECT * FROM inventory_categories ORDER BY category";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch measurements for dropdown
$measurements = [];
$sql = "SELECT * FROM mesuer ORDER BY mesuer";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
    $measurements[] = $row;
}

include '../inc/dashHeader.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Items Management</title>
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
            padding: 20px;
            margin-bottom: 25px;
            transition: var(--transition);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-group {
            position: relative;
            margin-bottom: 10px;
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
            padding: 8px 12px;
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
            font-size:15px;
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
        }

        .badge-light {
            color: var(--dark-color);
            background-color: var(--light-color);
            font-size:15px;
        }

        .badge-default {
            color: var(--dark-color);
            background-color: #e5e7eb;
            font-size:15px;
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

        .nav-tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 25px;
            overflow-x: auto;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        .nav-tabs::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .nav-item {
            margin-bottom: -1px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            font-weight: 500;
            color: var(--secondary-color);
            background-color: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            transition: var(--transition);
            white-space: nowrap;
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .nav-link.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .nav-link i {
            font-size: 16px;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
            max-width: 500px;
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

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 48px;
            color: #e5e7eb;
            margin-bottom: 15px;
        }

        .empty-state p {
            font-size: 16px;
            margin-bottom: 20px;
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
            
            .nav-link {
                padding: 10px 15px;
            }
            
            .btn {
                padding: 8px 12px;
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
        <?php if ($success_message): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: '<?= $success_message ?>',
                        timer: 3000,
                        showConfirmButton: false
                    });
                });
            </script>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '<?= $error_message ?>',
                        timer: 3000
                    });
                });
            </script>
        <?php endif; ?>

        <a href="inventory-table.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>

        <div class="page-header">
            <h1 class="page-title">Inventory Items Management</h1>
        </div>

        <div class="nav-tabs">
            <button type="button" class="nav-link active" id="items-tab" onclick="showTab('items')">
                <i class="fas fa-box"></i> Items
            </button>
            <button type="button" class="nav-link" id="categories-tab" onclick="showTab('categories')">
                <i class="fas fa-tags"></i> Categories
            </button>
            <!-- <button type="button" class="nav-link" id="measurements-tab" onclick="showTab('measurements')">
                <i class="fas fa-ruler"></i> Measurements
            </button> -->
        </div>

        <!-- Items Tab -->
        <div id="items-content" class="tab-content active">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Add New Item</h2>
                </div>
                <div class="card-body">
                    <form method="post" id="addItemForm">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="itemname">Item Name</label>
                                <input type="text" class="form-control" name="itemname" id="itemname" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="category_id">Category</label>
                                <select class="form-control" name="category_id" id="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="mesuer_id">Default Measurement Unit</label>
                                <select class="form-control" name="mesuer_id" id="mesuer_id" required>
                                    <option value="">Select Measurement</option>
                                    <?php foreach ($measurements as $mesuer): ?>
                                        <option value="<?= $mesuer['id'] ?>"><?= htmlspecialchars($mesuer['mesuer']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="add_item" class="btn btn-primary btn-with-icon">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </form>
                </div>
            </div>

            <div class="search-box">
                <i class="fas fa-search inventory-search-icon"></i>
                <input type="text" id="itemSearch" placeholder="Search items..." onkeyup="filterItems()">
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Items List</h2>
                </div>
                <div class="table-responsive">
                    <table id="itemsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Default Measurement</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($items) > 0): ?>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= $item['itemid'] ?></td>
                                    <td><strong><?= htmlspecialchars($item['itemname']) ?></strong></td>
                                    <td><span class="badge badge-light"><?= htmlspecialchars($item['category'] ?? 'N/A') ?></span></td>
                                    <td>
                                        <?php if (!empty($item['measurement_name'])): ?>
                                            <span class="badge badge-success"><?= htmlspecialchars($item['measurement_name']) ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-default">Not set</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $item['created_at'] ?></td>
                                    <td class="actions">
                                        <button type="button" class="btn btn-info btn-sm" onclick="openEditModal(
                                            <?= $item['itemid'] ?>, 
                                            '<?= htmlspecialchars(addslashes($item['itemname'])) ?>', 
                                            '<?= htmlspecialchars(addslashes($item['category'] ?? '')) ?>', 
                                            '<?= $item['default_mesuer'] ?>'
                                        )">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteItem(<?= $item['itemid'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <i class="fas fa-box-open"></i>
                                            <p>No items found</p>
                                            <button class="btn btn-primary btn-with-icon" onclick="document.getElementById('itemname').focus()">
                                                <i class="fas fa-plus"></i> Add your first item
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Categories Tab -->
        <div id="categories-content" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Add New Category</h2>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="category_name">Category Name</label>
                                <input type="text" class="form-control" name="category_name" id="category_name" required>
                            </div>
                        </div>
                        <button type="submit" name="add_category" class="btn btn-primary btn-with-icon">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Categories List</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($categories) > 0): ?>
                                <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?= $cat['id'] ?></td>
                                    <td><span class="badge badge-light"><?= htmlspecialchars($cat['category']) ?></span></td>
                                    <td><?= $cat['created_at'] ?></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteCategory(<?= $cat['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">
                                        <div class="empty-state">
                                            <i class="fas fa-tags"></i>
                                            <p>No categories found</p>
                                            <button class="btn btn-primary btn-with-icon" onclick="document.getElementById('category_name').focus()">
                                                <i class="fas fa-plus"></i> Add your first category
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Measurements Tab -->
        <div id="measurements-content" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Add New Measurement</h2>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="measurement_name">Measurement Name</label>
                                <input type="text" class="form-control" name="measurement_name" id="measurement_name" required>
                            </div>
                        </div>
                        <button type="submit" name="add_measurement" class="btn btn-primary btn-with-icon">
                            <i class="fas fa-plus"></i> Add Measurement
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Measurements List</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Measurement Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($measurements) > 0): ?>
                                <?php foreach ($measurements as $mesuer): ?>
                                <tr>
                                    <td><?= $mesuer['id'] ?></td>
                                    <td><span class="badge badge-success"><?= htmlspecialchars($mesuer['mesuer']) ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteMeasurement(<?= $mesuer['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">
                                        <div class="empty-state">
                                            <i class="fas fa-ruler"></i>
                                            <p>No measurements found</p>
                                            <button class="btn btn-primary btn-with-icon" onclick="document.getElementById('measurement_name').focus()">
                                                <i class="fas fa-plus"></i> Add your first measurement
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Edit Item Modal -->
        <div id="editModal" class="modal-backdrop">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Edit Item</h3>
                    <button type="button" class="modal-close" onclick="closeModal('editModal')">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="post" id="editItemForm">
                        <input type="hidden" name="itemid" id="edit_itemid">
                        <div class="form-group">
                            <label class="form-label" for="edit_itemname">Item Name</label>
                            <input type="text" class="form-control" name="itemname" id="edit_itemname" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_category_id">Category</label>
                            <select class="form-control" name="category_id" id="edit_category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit_mesuer_id">Default Measurement Unit</label>
                            <select class="form-control" name="mesuer_id" id="edit_mesuer_id" required>
                                <option value="">Select Measurement</option>
                                <?php foreach ($measurements as $mesuer): ?>
                                    <option value="<?= $mesuer['id'] ?>"><?= htmlspecialchars($mesuer['mesuer']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                            <button type="submit" name="update_item" class="btn btn-primary">Update Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Item Form (hidden) -->
        <form id="deleteItemForm" method="post" style="display: none;">
            <input type="hidden" name="itemid" id="delete_item_id">
            <input type="hidden" name="delete_item" value="1">
        </form>

        <!-- Delete Category Form (hidden) -->
        <form id="deleteCategoryForm" method="post" style="display: none;">
            <input type="hidden" name="category_id" id="delete_category_id">
            <input type="hidden" name="delete_category" value="1">
        </form>

        <!-- Delete Measurement Form (hidden) -->
        <form id="deleteMeasurementForm" method="post" style="display: none;">
            <input type="hidden" name="mesuer_id" id="delete_mesuer_id">
            <input type="hidden" name="delete_measurement" value="1">
        </form>
    </div>

    <script>
        // Show tab function
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(function(content) {
                content.classList.remove('active');
            });
            
            // Deactivate all tabs
            document.querySelectorAll('.nav-link').forEach(function(tab) {
                tab.classList.remove('active');
            });
            
            // Show selected tab content and activate the tab
            document.getElementById(tabName + '-content').classList.add('active');
            document.getElementById(tabName + '-tab').classList.add('active');
        }

        // Open edit modal
        function openEditModal(itemid, itemname, category, mesuer_id) {
            document.getElementById('edit_itemid').value = itemid;
            document.getElementById('edit_itemname').value = itemname;
            
            // Find category ID by name
            const categorySelect = document.getElementById('edit_category_id');
            for (let i = 0; i < categorySelect.options.length; i++) {
                if (categorySelect.options[i].text === category) {
                    categorySelect.selectedIndex = i;
                    break;
                }
            }
            
            // Set measurement
            document.getElementById('edit_mesuer_id').value = mesuer_id || '';
            
            // Show modal
            document.getElementById('editModal').classList.add('show');
        }

        // Close modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        // Filter items in the table
        function filterItems() {
            const input = document.getElementById('itemSearch');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('itemsTable');
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 1; i < tr.length; i++) {
                let txtValue = "";
                const tds = tr[i].getElementsByTagName('td');
                
                // Concatenate text from first 4 columns (ID, Name, Category, Measurement)
                for (let j = 0; j < 4; j++) {
                    if (tds[j]) {
                        txtValue += tds[j].textContent || tds[j].innerText;
                    }
                }
                
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = '';
                } else {
                    tr[i].style.display = 'none';
                }
            }
        }

        // Confirm delete item
        function confirmDeleteItem(id) {
            Swal.fire({
                title: 'Delete Item?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete_item_id').value = id;
                    document.getElementById('deleteItemForm').submit();
                }
            });
        }

        // Confirm delete category
        function confirmDeleteCategory(id) {
            Swal.fire({
                title: 'Delete Category?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete_category_id').value = id;
                    document.getElementById('deleteCategoryForm').submit();
                }
            });
        }

        // Confirm delete measurement
        function confirmDeleteMeasurement(id) {
            Swal.fire({
                title: 'Delete Measurement?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete_mesuer_id').value = id;
                    document.getElementById('deleteMeasurementForm').submit();
                }
            });
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeModal('editModal');
            }
        });

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to elements
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