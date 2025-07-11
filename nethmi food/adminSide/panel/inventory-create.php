<?php
session_start();
require_once "../config.php";

// Add new stock functionality
if (isset($_POST['add_stock'])) {
    $itemid = $_POST['itemid'];
    $qty = $_POST['qty'];
    $value = $_POST['value'];
    $manufacturedate = $_POST['manufacturedate'];
    $expierdate = $_POST['expierdate'];
    $user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;
    
    // Start transaction
    $link->begin_transaction();
    
    try {
        // Get item details including the default measurement
        $item_sql = "SELECT itemname, category, default_mesuer FROM inventory_items WHERE itemid = ?";
        $item_stmt = $link->prepare($item_sql);
        $item_stmt->bind_param('i', $itemid);
        $item_stmt->execute();
        $item_result = $item_stmt->get_result();
        
        if ($item_result->num_rows == 0) {
            throw new Exception("Item not found!");
        }
        
        $item_row = $item_result->fetch_assoc();
        $itemname = $item_row['itemname'];
        $category = $item_row['category'];
        $mesuer = $item_row['default_mesuer'];
        
        if (!$mesuer) {
            throw new Exception("This item has no default measurement unit. Please set one in Item Management.");
        }
        
        // Convert quantity if measurement unit is 1 or 2
        if ($mesuer == 2 || $mesuer == 1) {
            $qty = $qty * 1000;
        }
        
        // Check if stock exists in inventory
        $check_sql = "SELECT id, qty FROM inventory WHERE iteamname = ? AND mesuer = ? AND category = ?";
        $check_stmt = $link->prepare($check_sql);
        $check_stmt->bind_param('sis', $itemname, $mesuer, $category);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Stock exists, update quantity
            $stock_row = $check_result->fetch_assoc();
            $update_sql = "UPDATE inventory SET qty = qty + ?, value = ?, manufacturedate = ?, expierdate = ? WHERE id = ?";
            $update_stmt = $link->prepare($update_sql);
            $update_stmt->bind_param('dsssi', $qty, $value, $manufacturedate, $expierdate, $stock_row['id']);
            $update_stmt->execute();
        } else {
            // Stock doesn't exist, create new
            $insert_sql = "INSERT INTO inventory (iteamname, qty, mesuer, value, category, manufacturedate, expierdate) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $link->prepare($insert_sql);
            $insert_stmt->bind_param('siidsss', $itemname, $qty, $mesuer, $value, $category, $manufacturedate, $expierdate);
            $insert_stmt->execute();
        }
        
        // Record the stock creation
        $creation_sql = "INSERT INTO inventory_creations (itemid, qty, mesuer, value, manufacturedate, expierdate, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $creation_stmt = $link->prepare($creation_sql);
        $creation_stmt->bind_param('ididisi', $itemid, $qty, $mesuer, $value, $manufacturedate, $expierdate, $user_id);
        $creation_stmt->execute();
        
        // Commit transaction
        $link->commit();
        
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Stock added successfully!'
                    }).then(() => {
                        window.location.href = 'inventory-table.php';
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

// Fetch all items with their measurement details
$items = [];
$sql = "SELECT i.*, m.mesuer as measurement_name 
        FROM inventory_items i 
        LEFT JOIN mesuer m ON i.default_mesuer = m.id 
        ORDER BY i.itemname";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

include '../inc/dashHeader.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create Inventory Stock</title>
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
        
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 15px;
        }
        
        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        input[readonly] {
            background-color: #f8f9fa;
            color: #495057;
            cursor: not-allowed;
        }
        
        button {
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        
        button:hover {
            background-color: #218838;
        }
        
        .autocomplete-container {
            position: relative;
        }
        
        .autocomplete-results {
            position: absolute;
            border: 1px solid #ddd;
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
            max-height: 200px;
            overflow-y: auto;
            background-color: white;
        }
        
        .autocomplete-results div {
            padding: 10px;
            cursor: pointer;
        }
        
        .autocomplete-results div:hover {
            background-color: #f1f1f1;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #17a2b8;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin-top: 5px;
            color: #495057;
        }
    </style>
</head>

<body>
    <div style="align-items: center; padding-left: 250px; padding-top: 100px; width: 100%;">
        <div class="container">
            <a href="inventory-table.php" class="back-link">← Back to Inventory</a>
            <h1>Create Inventory Stock</h1>
            
            <div class="card">
                <form method="post" id="createStockForm">
                    <div class="form-group">
                        <label for="item_search">Search Item:</label>
                        <div class="autocomplete-container">
                            <input type="text" id="item_search" placeholder="Start typing item name or ID...">
                            <div id="autocomplete_results" class="autocomplete-results" style="display: none;"></div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="itemid" id="itemid" required>
                    
                    <div class="form-group">
                        <label for="itemname">Item Name:</label>
                        <input type="text" id="itemname" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category:</label>
                        <input type="text" id="category" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Measurement Unit:</label>
                        <div id="measurement_display" class="info-box">Not selected</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="qty">Quantity:</label>
                        <input type="number" name="qty" id="qty" step="0.01" min="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="value">Value:</label>
                        <input type="number" name="value" id="value" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="manufacturedate">Manufacture Date:</label>
                        <input type="date" name="manufacturedate" id="manufacturedate" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="expierdate">Expiry Date:</label>
                        <input type="date" name="expierdate" id="expierdate" required>
                    </div>
                    
                    <button type="submit" name="add_stock">Add Stock</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemSearchInput = document.getElementById('item_search');
            const autocompleteResults = document.getElementById('autocomplete_results');
            const itemIdInput = document.getElementById('itemid');
            const itemnameInput = document.getElementById('itemname');
            const categoryInput = document.getElementById('category');
            const measurementDisplay = document.getElementById('measurement_display');
            
            // Today's date for default dates
            const today = new Date().toISOString().split('T')[0];
            const sixMonthsLater = new Date();
            sixMonthsLater.setMonth(sixMonthsLater.getMonth() + 6);
            const sixMonthsLaterStr = sixMonthsLater.toISOString().split('T')[0];
            
            // Set default dates
            document.getElementById('manufacturedate').value = today;
            document.getElementById('expierdate').value = sixMonthsLaterStr;
            
            // Item data from PHP
            const items = <?php echo json_encode($items); ?>;
            
            // Function to filter items based on search query
            function filterItems(query) {
                query = query.toLowerCase().trim();
                return items.filter(item => 
                    item.itemid.toString().includes(query) || 
                    item.itemname.toLowerCase().includes(query) ||
                    (item.category && item.category.toLowerCase().includes(query))
                );
            }
            
            // Function to display autocomplete results
            function showAutocompleteResults(filteredItems) {
                autocompleteResults.innerHTML = '';
                
                if (filteredItems.length > 0) {
                    filteredItems.forEach(item => {
                        const div = document.createElement('div');
                        const measurementText = item.measurement_name ? ` [${item.measurement_name}]` : '';
                        div.textContent = `${item.itemname} (${item.category || 'No Category'})${measurementText}`;
                        div.addEventListener('click', function() {
                            selectItem(item);
                        });
                        autocompleteResults.appendChild(div);
                    });
                    autocompleteResults.style.display = 'block';
                } else {
                    autocompleteResults.style.display = 'none';
                }
            }
            
            // Function to select an item
            function selectItem(item) {
                // Set the basic item data
                itemIdInput.value = item.itemid;
                itemnameInput.value = item.itemname;
                categoryInput.value = item.category || '';
                
                // Set the measurement display
                if (item.measurement_name) {
                    measurementDisplay.textContent = item.measurement_name;
                } else {
                    measurementDisplay.textContent = 'No measurement unit set for this item';
                    // Alert user about missing measurement
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Measurement',
                        text: 'This item has no default measurement unit. Please set one in Item Management before creating stock.'
                    });
                }
                
                // Clear search results
                autocompleteResults.style.display = 'none';
                itemSearchInput.value = '';
            }
            
            // Search input event listener
            itemSearchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                if (query.length > 0) {
                    const filteredItems = filterItems(query);
                    showAutocompleteResults(filteredItems);
                } else {
                    autocompleteResults.style.display = 'none';
                }
            });
            
            // Click outside to close autocomplete
            document.addEventListener('click', function(e) {
                if (!autocompleteResults.contains(e.target) && e.target !== itemSearchInput) {
                    autocompleteResults.style.display = 'none';
                }
            });
            
            // Form validation
            document.getElementById('createStockForm').addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent form submission to validate first
                
                // Check if an item is selected
                if (!itemIdInput.value) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select an item'
                    });
                    return;
                }
                
                // Find the selected item
                const selectedItem = items.find(item => item.itemid == itemIdInput.value);
                
                // Check if the item has a default measurement
                if (!selectedItem || !selectedItem.default_mesuer) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'This item has no measurement unit. Please add a default measurement in Item Management.'
                    });
                    return;
                }
                
                const qty = parseFloat(document.getElementById('qty').value);
                if (isNaN(qty) || qty <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please enter a valid quantity greater than zero'
                    });
                    return;
                }
                
                const value = parseFloat(document.getElementById('value').value);
                if (isNaN(value) || value < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please enter a valid value (must be 0 or greater)'
                    });
                    return;
                }
                
                const manufacturedate = new Date(document.getElementById('manufacturedate').value);
                const expierdate = new Date(document.getElementById('expierdate').value);
                
                if (expierdate <= manufacturedate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Expiry date must be after manufacture date'
                    });
                    return;
                }
                
                // If all validations pass, submit the form
                this.submit();
            });
        });
    </script>
</body>
</html>