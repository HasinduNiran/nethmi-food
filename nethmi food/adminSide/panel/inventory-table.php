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

// Add Functionality for Regular Inventory
if (isset($_POST['add'])) {
    echo "<script>console.log('Add item form submitted');</script>";
    $itemid = isset($_POST['itemid']) ? $_POST['itemid'] : null;
    $iteamname = $_POST['iteamname'];
    $qty = $_POST['qty'];
    $mesuer = isset($_POST['mesuer']) ? $_POST['mesuer'] : 0;
    $value = $_POST['value'];
    $category = $_POST['category'];
    $manufacturedate = $_POST['manufacturedate'];
    $expierdate = $_POST['expierdate'];

    // if ($mesuer == 2 || $mesuer == 1) {
    //     $qty = $qty * 1000;
    // }

    // Start transaction
    $link->begin_transaction();

    try {
        // Check if the item exists in inventory_items, if not create it
        if (!$itemid) {
            $check_item_sql = "SELECT itemid FROM inventory_items WHERE itemname = ?";
            $check_item_stmt = $link->prepare($check_item_sql);
            $check_item_stmt->bind_param('s', $iteamname);
            $check_item_stmt->execute();
            $check_item_result = $check_item_stmt->get_result();

            if ($check_item_result->num_rows > 0) {
                // Item exists, get its ID
                $item_row = $check_item_result->fetch_assoc();
                $itemid = $item_row['itemid'];
            } else {
                // Create new item in inventory_items
                $insert_item_sql = "INSERT INTO inventory_items (itemname, category, default_mesuer) VALUES (?, ?, ?)";
                $insert_item_stmt = $link->prepare($insert_item_sql);
                $insert_item_stmt->bind_param('ssi', $iteamname, $category, $mesuer);
                $insert_item_stmt->execute();
                $itemid = $link->insert_id;
            }
        }

        // Check if stock exists in inventory
        $check_sql = "SELECT id, qty FROM inventory WHERE item_id = ? AND mesuer = ? AND category = ?";
        $check_stmt = $link->prepare($check_sql);
        $check_stmt->bind_param('sis', $itemid, $mesuer, $category);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Stock exists, update quantity
            $stock_row = $check_result->fetch_assoc();
            $update_sql = "UPDATE inventory SET qty = qty + ?, value = ?, manufacturedate = ?, expierdate = ?, item_id = ? WHERE id = ?";
            $update_stmt = $link->prepare($update_sql);
            $update_stmt->bind_param('dsssii', $qty, $value, $manufacturedate, $expierdate, $itemid, $stock_row['id']);
            $update_stmt->execute();
        } else {
            // Stock doesn't exist, create new
            $insert_sql = "INSERT INTO inventory (iteamname, qty, mesuer, value, category, manufacturedate, expierdate, item_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $link->prepare($insert_sql);
            $insert_stmt->bind_param('siidsssi', $iteamname, $qty, $mesuer, $value, $category, $manufacturedate, $expierdate, $itemid);
            $insert_stmt->execute();
        }

        // Record the stock creation in inventory_creations
        $creation_sql = "INSERT INTO inventory_creations (itemid, qty, mesuer, value, manufacturedate, expierdate, created_by) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;
        $creation_stmt = $link->prepare($creation_sql);
        $creation_stmt->bind_param('ididssi', $itemid, $qty, $mesuer, $value, $manufacturedate, $expierdate, $user_id);
        $creation_stmt->execute();

        // Commit transaction
        $link->commit();

        $_SESSION['success_message'] = "Item added successfully!";
        header("Location: inventory-table.php");
        exit;
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

// Add Functionality for Damaged Inventory with Inventory Reduction
if (isset($_POST['adddamage'])) {
    // Debug: Log the received POST data
    echo "<script>console.log('POST data: " . json_encode($_POST) . "');</script>";
    // Get and sanitize input
    $itemid = isset($_POST['itemid']) ? $_POST['itemid'] : null;
    $iteamname = trim($_POST['iteamname'] ?? '');
    $qty = floatval($_POST['qty'] ?? 0);
    $mesuer = intval($_POST['mesuer'] ?? 0);
    $value = floatval($_POST['value'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $manufacturedate = $_POST['manufacturedate'] ?? '';
    $expierdate = $_POST['expierdate'] ?? '';
    // Validate required fields
    if (empty($iteamname) || $qty <= 0 || $mesuer <= 0 || empty($category)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please fill all required fields correctly!'
                    });
                });
              </script>";
        return;
    }
    // Convert quantity if measurement unit is 1 or 2
    // $original_qty = $qty;
    // if ($mesuer == 1 || $mesuer == 2) {
    //     $qty = $qty * 1000;
    // }
    // Debug: Log the converted values
    echo "<script>console.log('Converted qty: $qty, Measure: $mesuer');</script>";
    $link->begin_transaction();
    try {
        // If itemid is not set, get it from inventory_items
        // if (!$itemid) {
        //     $check_item_sql = "SELECT itemid FROM inventory_items WHERE itemname = ?";
        //     $check_item_stmt = $link->prepare($check_item_sql);
        //     $check_item_stmt->bind_param('s', $iteamname);
        //     $check_item_stmt->execute();
        //     $check_item_result = $check_item_stmt->get_result();

        //     if ($check_item_result->num_rows > 0) {
        //         // Item exists, get its ID
        //         $item_row = $check_item_result->fetch_assoc();
        //         $itemid = $item_row['itemid'];
        //     } else {
        //         // Create new item in inventory_items
        //         $insert_item_sql = "INSERT INTO inventory_items (itemname, category, default_mesuer) VALUES (?, ?, ?)";
        //         $insert_item_stmt = $link->prepare($insert_item_sql);
        //         $insert_item_stmt->bind_param('ssi', $iteamname, $category, $mesuer);
        //         $insert_item_stmt->execute();
        //         $itemid = $link->insert_id;
        //     }
        // }

        // Check available quantity in inventory
        $check_sql = "SELECT id, qty FROM inventory WHERE item_id = ? AND mesuer = ? AND category = ?";
        $check_stmt = $link->prepare($check_sql);
        if (!$check_stmt) {
            throw new Exception("Prepare failed: " . $link->error);
        }
        $check_stmt->bind_param('sis', $itemid, $mesuer, $category);
        if (!$check_stmt->execute()) {
            throw new Exception("Execute failed: " . $check_stmt->error);
        }
        $result = $check_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row['qty'] >= $qty) {
                // Update inventory
                $update_sql = "UPDATE inventory SET qty = qty - ? WHERE id = ?";
                $update_stmt = $link->prepare($update_sql);
                if (!$update_stmt) {
                    throw new Exception("Update prepare failed: " . $link->error);
                }
                $update_stmt->bind_param('ii', $qty, $row['id']);
                if (!$update_stmt->execute()) {
                    throw new Exception("Update execute failed: " . $update_stmt->error);
                }
                // Debug: Log update success
                echo "<script>console.log('Inventory updated, rows affected: " . $update_stmt->affected_rows . "');</script>";
                // Insert into damage_inventory
                $insert_sql = "INSERT INTO damage_inventory (iteamname, qty, mesuer, value, category, manufacturedate, expierdate, damage_date, item_id) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
                $insert_stmt = $link->prepare($insert_sql);
                if (!$insert_stmt) {
                    throw new Exception("Insert prepare failed: " . $link->error);
                }
                $insert_stmt->bind_param('siidsssi', $iteamname, $qty, $mesuer, $value, $category, $manufacturedate, $expierdate, $itemid);
                if (!$insert_stmt->execute()) {
                    throw new Exception("Insert execute failed: " . $insert_stmt->error);
                }
                // Removed inventory_creations entry for damaged items as requested
                // Debug: Log insert success
                echo "<script>console.log('Damage inventory inserted, ID: " . $link->insert_id . "');</script>";
                $link->commit();
                $_SESSION['success_message'] = "Damaged item added and inventory updated successfully!";
                header("Location: inventory-table.php");
                exit;
            } else {
                throw new Exception("Insufficient quantity in inventory! Available: " . $row['qty'] . ", Requested: " . $qty);
            }
        } else {
            throw new Exception("Item not found in inventory with specified parameters!");
        }
    } catch (Exception $e) {
        $link->rollback();
        $error_msg = addslashes($e->getMessage());
        echo "<script>console.log('Error: $error_msg');</script>";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '$error_msg'
                    });
                });
              </script>";
    } finally {
        // Clean up
        if (isset($check_stmt)) $check_stmt->close();
        if (isset($update_stmt)) $update_stmt->close();
        if (isset($insert_stmt)) $insert_stmt->close();
        if (isset($creation_stmt)) $creation_stmt->close();
    }
}

// Update Functionality
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $iteamname = $_POST['iteamname'];
    $qty = $_POST['qty'];
    $mesuer = $_POST['mesuer'];
    $value = $_POST['value'];
    $category = $_POST['category'];
    $manufacturedate = $_POST['manufacturedate'];
    $expierdate = $_POST['expierdate'];
    if ($mesuer == 2 || $mesuer == 1) {
        $qty = $qty * 1000;
    }
    $link->begin_transaction();

    try {
        // Get original qty before update for comparison
        $original_sql = "SELECT qty FROM inventory WHERE id = ?";
        $original_stmt = $link->prepare($original_sql);
        $original_stmt->bind_param('i', $id);
        $original_stmt->execute();
        $original_result = $original_stmt->get_result();
        $original = $original_result->fetch_assoc();
        $qty_diff = $qty - $original['qty'];

        // Find the item ID for this item name
        $find_itemid_sql = "SELECT itemid FROM inventory_items WHERE itemname = ?";
        $find_itemid_stmt = $link->prepare($find_itemid_sql);
        $find_itemid_stmt->bind_param('s', $iteamname);
        $find_itemid_stmt->execute();
        $find_itemid_result = $find_itemid_stmt->get_result();
        
        if ($find_itemid_result->num_rows > 0) {
            $item = $find_itemid_result->fetch_assoc();
            $itemid = $item['itemid'];
            
            // Update inventory
            $sql = "UPDATE inventory SET 
                    iteamname = ?, qty = ?, mesuer = ?, value = ?, category = ?, 
                    manufacturedate = ?, expierdate = ?, item_id = ? 
                    WHERE id = ?";
            $stmt = $link->prepare($sql);
            $stmt->bind_param('siidsssii', $iteamname, $qty, $mesuer, $value, $category, 
                            $manufacturedate, $expierdate, $itemid, $id);

            if ($stmt->execute()) {
                // If there's a quantity change, record it in inventory_creations
                if ($qty_diff != 0) {
                    $user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

                    // Add entry to inventory_creations
                    $creation_sql = "INSERT INTO inventory_creations (itemid, qty, mesuer, value, manufacturedate, expierdate, created_by) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $creation_stmt = $link->prepare($creation_sql);
                    $creation_stmt->bind_param('ididisi', $itemid, $qty_diff, $mesuer, $value, $manufacturedate, $expierdate, $user_id);
                    $creation_stmt->execute();
                }

                $link->commit();

                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Item updated successfully!'
                            }).then(() => {
                                window.location.href = 'inventory-table.php'; // Redirect to refresh the page
                            });
                        });
                    </script>";
            } else {
                throw new Exception($link->error);
            }
        } else {
            throw new Exception("Item not found in inventory_items. Please select a valid item.");
        }
    } catch (Exception $e) {
        $link->rollback();

        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        header("Location: inventory-table.php");
        exit;
    }
}

// Delete Functionality
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $link->begin_transaction();

    try {
        // Get the item details before deletion
        $get_item_sql = "SELECT item_id FROM inventory WHERE id = ?";
        $get_item_stmt = $link->prepare($get_item_sql);
        $get_item_stmt->bind_param('i', $id);
        $get_item_stmt->execute();
        $get_item_result = $get_item_stmt->get_result();
        $item = $get_item_result->fetch_assoc();

        // Delete from inventory
        $sql = "DELETE FROM inventory WHERE id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            // If item has item_id, record deletion in inventory_creations
            if (isset($item['item_id']) && $item['item_id']) {
                $itemid = $item['item_id'];
                $user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

                // Get the deleted item's details
                $get_details_sql = "SELECT qty, mesuer, value, manufacturedate, expierdate FROM inventory WHERE id = ?";
                $get_details_stmt = $link->prepare($get_details_sql);
                $get_details_stmt->bind_param('i', $id);
                $get_details_stmt->execute();
                $get_details_result = $get_details_stmt->get_result();

                if ($details = $get_details_result->fetch_assoc()) {
                    $neg_qty = -$details['qty']; // Negative quantity for deletion

                    // Add entry to inventory_creations
                    $creation_sql = "INSERT INTO inventory_creations (itemid, qty, mesuer, value, manufacturedate, expierdate, created_by) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $creation_stmt = $link->prepare($creation_sql);
                    $creation_stmt->bind_param('ididisi', $itemid, $neg_qty, $details['mesuer'], $details['value'], 
                                            $details['manufacturedate'], $details['expierdate'], $user_id);
                    $creation_stmt->execute();
                }
            }

            $link->commit();

            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Item deleted successfully!'
                        }).then(() => {
                            window.location.href = 'inventory-table.php'; // Redirect to refresh the page
                        });
                    });
                  </script>";
        } else {
            throw new Exception($link->error);
        }
    } catch (Exception $e) {
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

// Sync Functionality
if (isset($_POST['sync'])) {
    $currentDate = date('Y-m-d');
    $billQuery = "SELECT b.bill_id, bi.item_id, bi.quantity, pl.iteam_id, pl.qty AS required_qty
                  FROM bills b
                  INNER JOIN bill_items bi ON b.bill_id = bi.bill_id
                  INNER JOIN product_listing pl ON bi.item_id = pl.menu_id
                  WHERE DATE(b.bill_time) = '$currentDate'";
    $billResult = mysqli_query($link, $billQuery);
    if ($billResult && mysqli_num_rows($billResult) > 0) {
        $success = true;
        $link->begin_transaction();

        try {
            while ($row = mysqli_fetch_assoc($billResult)) {
                $ingredientId = $row['iteam_id'];
                $usedQty = $row['quantity'] * $row['required_qty'];
                // Update inventory
                $updateInventoryQuery = "UPDATE inventory 
                                        SET qty = qty - $usedQty, wastage = wastage + $usedQty 
                                        WHERE id = $ingredientId";
                if (!mysqli_query($link, $updateInventoryQuery)) {
                    throw new Exception($link->error);
                }

                // Find the item_id for recording in inventory_creations
                $inventory_sql = "SELECT i.mesuer, i.value, i.manufacturedate, i.expierdate, i.item_id 
                                 FROM inventory i WHERE i.id = ?";
                $inventory_stmt = $link->prepare($inventory_sql);
                $inventory_stmt->bind_param('i', $ingredientId);
                $inventory_stmt->execute();
                $inventory_result = $inventory_stmt->get_result();

                if ($inventory = $inventory_result->fetch_assoc()) {
                    $itemid = $inventory['item_id'];
                    
                    if ($itemid) {
                        $user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;
                        $neg_qty = -$usedQty; // Negative quantity for usage/wastage
    
                        // Add entry to inventory_creations
                        $creation_sql = "INSERT INTO inventory_creations (itemid, qty, mesuer, value, manufacturedate, expierdate, created_by) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $creation_stmt = $link->prepare($creation_sql);
                        $creation_stmt->bind_param('ididisi', $itemid, $neg_qty, $inventory['mesuer'], $inventory['value'], 
                                                $inventory['manufacturedate'], $inventory['expierdate'], $user_id);
                        $creation_stmt->execute();
                    }
                }
            }

            $link->commit();

            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Inventory synchronized successfully!'
                        }).then(() => {
                            window.location.href = 'inventory-table.php'; // Redirect to refresh the page
                        });
                    });
                  </script>";
        } catch (Exception $e) {
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
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No bills found for the current day!'
                    });
                });
              </script>";
    }
}

// Fetch all inventory items
$inventoryItems = [];
$sql = "SELECT i.*, it.itemname 
        FROM inventory i
        JOIN inventory_items it ON i.item_id = it.itemid";
if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $inventoryItems[] = $row;
    }
}

// Fetch all damaged inventory items
$damageInventoryItems = [];
$sql = "SELECT * FROM damage_inventory";
if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $damageInventoryItems[] = $row;
    }
}

// Fetch categories for filter
$categories = [];
$sql = "SELECT DISTINCT category FROM inventory";
if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Fetch inventory items for dropdown with measurement information
$items = [];
$sql = "SELECT ii.*, m.mesuer as measurement_name, m.id as mesuer_id 
        FROM inventory_items ii 
        LEFT JOIN mesuer m ON ii.default_mesuer = m.id 
        ORDER BY ii.itemname";
$result = $link->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}
?>
<?php include '../inc/dashHeader.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
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

        .btn-warning {
            color: #fff;
            background-color: var(--warning-color);
        }

        .btn-warning:hover {
            background-color: #d97706;
        }

        .btn-info {
            color: #fff;
            background-color: var(--info-color);
        }

        .btn-info:hover {
            background-color: #2563eb;
        }

        .btn-with-icon {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-with-icon i {
            font-size: 16px;
        }

        .table-container {
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

        .filter-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
            width: 100%;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 300px;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
            pointer-events: none; /* Ensure icon doesn't interfere with input */
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

        .filter-dropdown {
            min-width: 150px;
        }

        /* Autocomplete styles */
        .autocomplete-container {
            position: relative;
        }

        .autocomplete-results {
            position: absolute;
            z-index: 1000;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-radius: 0 0 6px 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .autocomplete-item {
            padding: 10px 12px;
            cursor: pointer;
            transition: var(--transition);
        }

        .autocomplete-item:hover {
            background-color: #f3f4f6;
        }

        .selected-item-display {
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #e1effe;
            border-radius: 6px;
            border-left: 3px solid var(--primary-color);
            display: none;
        }

        /* Modal styles */
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

        /* Toast/notification styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1060;
        }

        .toast {
            width: 350px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            overflow: hidden;
            animation: slideIn 0.3s ease-in-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast-header {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .toast-icon {
            margin-right: 10px;
            font-size: 18px;
        }

        .toast-success .toast-icon {
            color: var(--success-color);
        }

        .toast-error .toast-icon {
            color: var(--danger-color);
        }

        .toast-title {
            font-weight: 600;
            flex-grow: 1;
        }

        .toast-close {
            background: none;
            border: none;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            color: var(--secondary-color);
        }

        .toast-body {
            padding: 15px;
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
            
            .filter-container {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .search-box, .filter-dropdown {
                width: 100%;
            }
        }

        /* Animated loading indicator */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(74, 108, 247, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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

        <div class="page-header">
            <h1 class="page-title">Inventory Management</h1>
            <div class="action-buttons">
                <a href="inventory-items.php" class="btn btn-info btn-with-icon">
                    <i class="fas fa-boxes"></i> Manage Items
                </a>
                <a href="inventory-creations.php" class="btn btn-primary btn-with-icon">
                    <i class="fas fa-history"></i> View Stock History
                </a>
            </div>
        </div>

        <!-- Add Stock Form -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Add New Stock</h2>
            </div>
            <form method="post" id="addStockForm">
                <div class="form-grid">
                    <div class="form-group autocomplete-container">
                        <label class="form-label" for="item_search">Search Item</label>
                        <input type="text" id="item_search" class="form-control" placeholder="Search by name or category...">
                        <div id="item_search_results" class="autocomplete-results"></div>
                        <div id="selected_item" class="selected-item-display"></div>
                        <input type="hidden" name="itemid" id="itemid">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="iteamname">Item Name</label>
                        <input type="text" class="form-control" name="iteamname" id="iteamname" readonly required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="qty">Quantity</label>
                        <input type="number" class="form-control" name="qty" id="qty" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="mesuer_display">Measurement</label>
                        <select id="mesuer_display" class="form-control" disabled>
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
                        <input type="hidden" name="mesuer" id="mesuer" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="value">Value</label>
                        <input type="number" class="form-control" name="value" id="value" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="category">Category</label>
                        <input type="text" class="form-control" name="category" id="category" readonly required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="manufacturedate">Manufacture Date</label>
                        <input type="date" class="form-control" name="manufacturedate" id="manufacturedate" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="expierdate">Expiry Date</label>
                        <input type="date" class="form-control" name="expierdate" id="expierdate" required>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="submit" name="add" class="btn btn-success btn-with-icon">
                        <i class="fas fa-plus-circle"></i> Add Item
                    </button>
                    <button type="submit" name="adddamage" class="btn btn-danger btn-with-icon">
                        <i class="fas fa-trash-alt"></i> Add as Wastage
                    </button>
                </div>
            </form>
        </div>

        <!-- Inventory Table -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Current Inventory</h2>
                <button type="button" class="btn btn-primary btn-with-icon" id="syncButton" style="display:none;">
                    <i class="fas fa-sync-alt"></i> Sync Inventory
                </button>
            </div>
            
            <div class="filter-container">
                <div class="search-box">
                    <i class="fas fa-search inventory-search-icon"></i>
                    <input type="text" id="inventorySearch" placeholder="Search inventory..." onkeyup="filterTable('inventorySearch', 'inventoryTable')">
                </div>
                
                <select id="inventoryCategoryFilter" class="form-control filter-dropdown" onchange="filterTableByCategory('inventoryTable', this.value)">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="table-container">
                <table id="inventoryTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Measure</th>
                            <th>Category</th>
                            <th>Manufacture Date</th>
                            <th>Expiry Date</th>
                            <!-- <th>Actions</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventoryItems as $item) : ?>
                            <tr>
                                <?php
                                if (isset($item['qty'])) {
                                    $qty = $item['qty'];
                                } else {
                                    $qty = 0;
                                }
                                ?>
                                <td><?= isset($item['id']) ? htmlspecialchars($item['id']) : 'N/A' ?></td>
                                <td><?= isset($item['itemname']) ? htmlspecialchars($item['itemname']) : 'Unnamed Item' ?></td>
                                <td><?= htmlspecialchars($qty) ?></td>
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
                                <td><?= isset($item['category']) ? htmlspecialchars($item['category']) : 'N/A' ?></td>
                                <td><?= $item['manufacturedate'] ?></td>
                                <td><?= $item['expierdate'] ?></td>
                                <!-- <td>
                                    <button type="button" class="btn btn-info btn-sm" onclick="openEditModal(
                                        <?= $item['id'] ?>, 
                                        '<?= addslashes($item['iteamname']) ?>', 
                                        <?= $qty ?>, 
                                        <?= $item['mesuer'] ?>, 
                                        '<?= $item['value'] ?>', 
                                        '<?= addslashes($item['category']) ?>',
                                        '<?= $item['manufacturedate'] ?>', 
                                        '<?= $item['expierdate'] ?>'
                                    )">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $item['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td> -->
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Damage Inventory Table -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Wastage Inventory</h2>
            </div>
            
            <div class="filter-container">
                <div class="search-box">
                    <i class="fas fa-search inventory-search-icon"></i>
                    <input type="text" id="damageSearch" placeholder="Search wastage..." onkeyup="filterTable('damageSearch', 'damageTable')">
                </div>
                
                <select id="damageCategoryFilter" class="form-control filter-dropdown" onchange="filterTableByCategory('damageTable', this.value)">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="table-container">
                <table id="damageTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Measure</th>
                            <th>Value</th>
                            <th>Category</th>
                            <th>Manufacture Date</th>
                            <th>Expiry Date</th>
                            <th>Damage Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($damageInventoryItems as $item) : ?>
                            <tr>
                                <?php
                                if (isset($item['mesuer'], $item['qty'])) {
                                    $qty = $item['qty'];
                                } else {
                                    $qty = 0;
                                }
                                ?>
                                <td><?= isset($item['id']) ? htmlspecialchars($item['id']) : 'N/A' ?></td>
                                <td><?= isset($item['iteamname']) ? htmlspecialchars($item['iteamname']) : 'Unnamed Item' ?></td>
                                <td><?= htmlspecialchars($qty) ?></td>
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
                                <td><?= isset($item['category']) ? htmlspecialchars($item['category']) : 'N/A' ?></td>
                                <td><?= $item['manufacturedate'] ?></td>
                                <td><?= $item['expierdate'] ?></td>
                                <td><?= $item['damage_date'] ?></td>
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
                    <h3 class="modal-title">Edit Inventory Item</h3>
                    <button type="button" class="modal-close" onclick="closeModal('editModal')">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="post" id="updateForm">
                        <input type="hidden" name="id" id="update_id">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="update_iteamname">Item Name</label>
                                <input type="text" class="form-control" name="iteamname" id="update_iteamname" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="update_qty">Quantity</label>
                                <input type="number" class="form-control" name="qty" id="update_qty" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="update_mesuer">Measurement</label>
                                <select name="mesuer" id="update_mesuer" class="form-control" required>
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
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="update_value">Value</label>
                                <input type="number" class="form-control" name="value" id="update_value" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="update_category">Category</label>
                                <input type="text" class="form-control" name="category" id="update_category" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="update_manufacturedate">Manufacture Date</label>
                                <input type="date" class="form-control" name="manufacturedate" id="update_manufacturedate" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="update_expierdate">Expiry Date</label>
                                <input type="date" class="form-control" name="expierdate" id="update_expierdate" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                            <button type="submit" name="update" class="btn btn-primary">Update Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Form (hidden) -->
        <form id="deleteForm" method="post" style="display: none;">
            <input type="hidden" name="id" id="delete_id">
            <input type="hidden" name="delete" value="1">
        </form>

        <!-- Sync Form (hidden) -->
        <form id="syncForm" method="post" style="display: none;">
            <input type="hidden" name="sync" value="1">
        </form>
    </div>

    <script>
        // All inventory items from PHP
        const inventoryItems = <?= json_encode($items) ?>;

        // Item search and selection
        document.addEventListener('DOMContentLoaded', function() {
            const itemSearchInput = document.getElementById('item_search');
            const itemSearchResults = document.getElementById('item_search_results');
            const selectedItemDiv = document.getElementById('selected_item');
            const itemIdInput = document.getElementById('itemid');
            const itemNameInput = document.getElementById('iteamname');
            const categoryInput = document.getElementById('category');
            const mesuerSelect = document.getElementById('mesuer');
            const mesuerDisplay = document.getElementById('mesuer_display');

            // No default dates set for manufacture and expiry dates per requirements

            // Function to search items
            itemSearchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                if (query.length < 2) {
                    itemSearchResults.style.display = 'none';
                    return;
                }

                // Filter items based on query
                const filteredItems = inventoryItems.filter(item => 
                    item.itemname?.toLowerCase().includes(query) || 
                    (item.category && item.category.toLowerCase().includes(query))
                );

                // Display results
                itemSearchResults.innerHTML = '';
                if (filteredItems.length > 0) {
                    filteredItems.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        const measurementText = item.measurement_name ? ` [${item.measurement_name}]` : '';
                        div.textContent = `${item.itemname} (${item.category || 'No Category'})${measurementText}`;
                        div.addEventListener('click', function() {
                            selectItem(item);
                        });
                        itemSearchResults.appendChild(div);
                    });
                    itemSearchResults.style.display = 'block';
                } else {
                    itemSearchResults.style.display = 'none';
                }
            });

            // Function to select an item
            function selectItem(item) {
                itemIdInput.value = item.itemid;
                itemNameInput.value = item.itemname;
                categoryInput.value = item.category || '';

                // Set measurement if available
                if (item.default_mesuer) {
                    mesuerDisplay.value = item.default_mesuer;
                    mesuerSelect.value = item.default_mesuer;
                } else if (item.mesuer_id) {
                    mesuerDisplay.value = item.mesuer_id;
                    mesuerSelect.value = item.mesuer_id;
                }

                // Update the selected item display
                selectedItemDiv.textContent = `Selected: ${item.itemname} ${item.measurement_name ? '('+item.measurement_name+')' : ''}`;
                selectedItemDiv.style.display = 'block';

                // Clear search and hide results
                itemSearchInput.value = '';
                itemSearchResults.style.display = 'none';
            }

            // Close results when clicking outside
            document.addEventListener('click', function(e) {
                if (!itemSearchInput.contains(e.target) && !itemSearchResults.contains(e.target)) {
                    itemSearchResults.style.display = 'none';
                }
            });

            // Sync button event
            document.getElementById('syncButton').addEventListener('click', function() {
                Swal.fire({
                    title: 'Sync Inventory',
                    text: 'This will sync inventory with today\'s bills. Continue?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, sync now',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('syncForm').submit();
                    }
                });
            });
        });

        // Open edit modal
        function openEditModal(id, iteamname, qty, mesuer_id, value, category, manufacturedate, expierdate) {
            document.getElementById('update_id').value = id;
            document.getElementById('update_iteamname').value = iteamname;
            document.getElementById('update_qty').value = qty;
            document.getElementById('update_value').value = value;
            document.getElementById('update_category').value = category;
            document.getElementById('update_manufacturedate').value = manufacturedate;
            document.getElementById('update_expierdate').value = expierdate;
            document.getElementById('update_mesuer').value = mesuer_id;
            
            document.getElementById('editModal').classList.add('show');
        }

        // Close any modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        // Confirm delete
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently remove this item from inventory!",
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

        // Table filtering functions
        function filterTable(searchId, tableId) {
            const input = document.getElementById(searchId);
            const filter = input.value.toLowerCase();
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName("tr");
            
            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName("td");
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    if (cell) {
                        const txtValue = cell.textContent || cell.innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                rows[i].style.display = found ? "" : "none";
            }
        }

        function filterTableByCategory(tableId, category) {
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName("tr");
            
            // Use the correct index based on which table we're filtering
            // In the inventory table, category is at index 4
            // In the damage/wastage table, category is at index 5
            const categoryColumnIndex = (tableId === 'damageTable') ? 5 : 4;
            
            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName("td");
                if (cells.length > categoryColumnIndex) {
                    const categoryCell = cells[categoryColumnIndex];
                    const txtValue = categoryCell.textContent || categoryCell.innerText;
                    rows[i].style.display = (category === '' || txtValue === category) ? "" : "none";
                }
            }
        }

        // Initialize tooltips (if using Bootstrap)
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Form validation
        document.getElementById('addStockForm').addEventListener('submit', function(event) {
            const itemName = document.getElementById('iteamname').value.trim();
            const qty = parseFloat(document.getElementById('qty').value);
            const mesuer = document.getElementById('mesuer').value;
            const category = document.getElementById('category').value.trim();
            
            if (!itemName || isNaN(qty) || qty <= 0 || !mesuer || mesuer === '0' || !category) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fill all required fields correctly!'
                });
            }
        });

        // Handle expiry date warning
        document.getElementById('expierdate').addEventListener('change', function() {
            const expiryDate = new Date(this.value);
            const today = new Date();
            const differenceInDays = Math.floor((expiryDate - today) / (1000 * 60 * 60 * 24));
            
            if (differenceInDays < 30 && differenceInDays >= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Short Expiry',
                    text: `This item will expire in ${differenceInDays} days!`
                });
            } else if (differenceInDays < 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Expired Item Detected',
                    text: 'This item has already expired! The expiry date is before today.'
                });
                // Clear the invalid expiry date
                //this.value = "";
            }
        });
    </script>
</body>
</html>