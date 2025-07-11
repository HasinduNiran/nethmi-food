<?php
session_start();
include '../inc/dashHeader.php';
require_once "../config.php";

$input_item_id = $item_id_err = $item_id = "";

if (isset($_POST['submit'])) {
    if (empty($_POST['item_id'])) {
        $item_idErr = 'ID is required';
    } else {
        $item_id = filter_input(
            INPUT_POST,
            'item_id',
            FILTER_SANITIZE_FULL_SPECIAL_CHARS
        );
    }
}

// Initial fetch of bakery items
$conn = $link;
$query = "SELECT item_id, item_name, bakery_category, item_type, item_price, quantity, cost_price, supplier_id FROM bakery_menu_stocks ORDER BY created_at DESC";
$result = $conn->query($query);

// Fetch quick registration items
$quick_query = "SELECT item_id, item_name FROM bakery_items ORDER BY item_id";
$quick_result = $conn->query($quick_query);

// Fetch suppliers
$supplier_query = "SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name";
$supplier_result = $conn->query($supplier_query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create New  Item</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery first, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        /* [Previous styles remain unchanged] */
        .main-container {
            display: flex;
            gap: 20px;
            max-width: 1400px;
            padding: 20px;
            margin-top:30px;
        }

        .sidebar {
            flex: 0 0 200px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-top:20px;
        }

        .content-area {
            width:920px;
        }

        .quick-items-table-wrapper {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-top: 20px;
        }

        .quick-items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .quick-items-table th,
        .quick-items-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .quick-items-table th {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .quick-items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .quick-items-table tr:hover {
            background-color: #e9ecef;
        }

       
        .main-container {
            display: flex;
            gap: 20px;
            max-width: 1400px;
            padding: 20px;
            
        }

        .sidebar {
            flex: 0 0 300px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .content-area {
            flex: 1;
        }

        .container {
            padding: 20px;
        }

        .wrapper {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
            margin-bottom: 15px;
        }

        .form-column {
            flex: 0 0 20%;
            max-width: 20%;
            padding: 0 15px;
        }

        .form-column-medium {
            flex: 0 0 20%;
            max-width: 20%;
            padding: 0 12px;
        }

        .form-column-full {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 0 15px;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            outline: 0;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .btn {
            padding: 10px 20px;
            margin-top: 10px;
            cursor: pointer;
        }

        .btn-dark {
            background-color: #343a40;
            color: white;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .btn-dark:hover {
            background-color: #23272b;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }

        h3 {
            color: #333;
            margin-bottom: 10px;
        }

        p {
            color: #6c757d;
            margin-bottom: 20px;
        }

        .items-table-wrapper {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .items-table th {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .items-table tr:hover {
            background-color: #e9ecef;
        }

        .search-container {
            margin-bottom: 20px;
        }

        .search-input {
            max-width: 300px;
        }

        @media (max-width: 992px) {
            .main-container {
                flex-direction: column;
            }
            
            .sidebar {
                flex: 0 0 auto;
                width: 100%;
            }
            
            .form-column, .form-column-medium {
                flex: 0 0 50%;
                max-width: 50%;
                margin-bottom: 15px;
            }
        }

        @media (max-width: 768px) {
            .form-column, .form-column-medium {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 15px;
            }
            
            .container {
                padding: 10px;
            }
            
            .wrapper {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<div class="main-container" style="margin-left:220px;">
    <!-- Sidebar with Quick Registration Form and Items List -->
    <div class="sidebar">
        <h3>Register New Item</h3>
        <p>Please fill in the item details</p>
        
        <form method="POST" action="success_create_bakery.php" id="quickBakeryForm">
            <div class="form-group">
                <label for="quick_item_id">Item ID:</label>
                <input type="text" name="item_id" class="form-control <?php echo !$item_id_err ?: 'is-invalid'; ?>" 
                    id="quick_item_id" required placeholder="B88" value="<?php echo $item_id; ?>" tabindex="1">
                <div class="invalid-feedback">
                    Please provide a valid item ID.
                </div>
            </div>

            <div class="form-group">
                <label for="quick_item_name">Item Name:</label>
                <input type="text" name="item_name" id="quick_item_name" placeholder="Chocolate Cake" 
                    required class="form-control" tabindex="2">
            </div>

            <div class="form-group">
                <input type="button" onclick="quickSaveBakeryItem();" class="btn btn-dark" 
                    value="Register Item" tabindex="3" id="quickSubmitBtn">
            </div>
        </form>

        <!-- Quick Registered Items Table -->
        <div class="quick-items-table-wrapper">
            <table class="quick-items-table">
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Item Name</th>
                    </tr>
                </thead>
                <tbody id="quickItemsTableBody">
                    <?php
                    if ($quick_result && $quick_result->num_rows > 0) {
                        while ($row = $quick_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['item_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No items registered</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="content-area">
        <!-- Detailed Form Wrapper -->
        <div class="wrapper">
            <h3>Create New Item</h3>
            <p>Please fill  Item Information Properly</p>

            <form method="POST" action="success_create_bakery.php" class="ht-700 w-54" id="bakeryForm">
                <div class="form-row">
                    <div class="form-column-medium">
                        <div class="form-group">
                            <label for="item_id" class="form-label">Item ID :</label>
                            <input type="text" name="item_id" class="form-control <?php echo !$item_idErr ?: 'is-invalid'; ?>" 
                                id="item_id" required placeholder="B88" value="<?php echo $item_id; ?>" tabindex="1">
                            <div id="validationServerFeedback" class="invalid-feedback">
                                Please provide a valid item_id.
                            </div>
                        </div>
                    </div>
                    <div class="form-column-medium">
                        <div class="form-group">
                            <label for="item_name">Item Name :</label>
                            <input type="text" name="item_name" id="item_name" placeholder="Chocolate Cake" 
                                required class="form-control" tabindex="2">
                        </div>
                    </div>
                    <div class="form-column-medium">
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" placeholder="0.00" 
                                step="0.01" min="0" required class="form-control" tabindex="3">
                        </div>
                    </div>
                    <div class="form-column-medium">
                        <div class="form-group">
                            <label for="cost_price">Cost Price:</label>
                            <input type="number" name="cost_price" id="cost_price" placeholder="0.00" 
                                step="0.01" min="0" required class="form-control" tabindex="4">
                        </div>
                    </div>
                    <div class="form-column-medium">
                        <div class="form-group">
                            <label for="total_cost">Total Cost:</label>
                            <input type="number" name="total_cost" id="total_cost" placeholder="0.00" 
                                step="0.01" class="form-control" readonly tabindex="5">
                        </div>
                    </div>
                    <div class="form-column-medium">
                        <div class="form-group">
                            <label for="supplier_id">Supplier:</label>
                            <input type="text" name="supplier_name" id="supplier_name" placeholder="Type supplier name" 
                                class="form-control" tabindex="5">
                            <input type="hidden" name="supplier_id" id="supplier_id">
                        </div>
                    </div>
                    <div class="form-column-medium">
                        <div class="form-group">
                            <label for="item_type">Unit:</label>
                            <select name="item_type" id="item_type" class="form-control" required tabindex="6">
                                <option value="">Select Unit</option>
                                <option value="Bottle">Bottle</option>
                                <option value="Pieces">Pieces</option>
                                <option value="Tin">Tin</option>
                                <option value="Packet">Packet</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="item_price">Item Price :</label>
                            <input min='0.01' type="number" name="item_price" id="item_price" 
                                placeholder="12.34" step="0.01" required class="form-control" tabindex="8">
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="dining_price">Dining Price :</label>
                            <input min='0.01' type="number" name="dining_price" id="dining_price" 
                                placeholder="12.34" step="0.01" required class="form-control" tabindex="9">
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="takeaway_price">Takeaway Price :</label>
                            <input min='0.01' type="number" name="takeaway_price" id="takeaway_price" 
                                placeholder="12.34" step="0.01" required class="form-control" tabindex="10">
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="uber_pickme_price">Uber/PickMe Price :</label>
                            <input min='0.01' type="number" name="uber_pickme_price" id="uber_pickme_price" 
                                placeholder="12.34" step="0.01" required class="form-control" tabindex="11">
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="delivery_service_item_price">Delivery Price :</label>
                            <input min='0.01' type="number" name="delivery_service_item_price" 
                                id="delivery_service_item_price" placeholder="12.34" step="0.01" 
                                required class="form-control" tabindex="12">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-column-full">
                        <div class="form-group">
                            <label for="item_description">Item Description :</label>
                            <textarea name="item_description" id="item_description" rows="3" 
                                placeholder="A delicious bakery item..." required 
                                class="form-control" tabindex="13"></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-column-full">
                        <div class="form-group">
                            <input type="button" onclick="saveBakeryItem();" class="btn btn-dark" value="Create Item" tabindex="14" id="submitBtn">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- List Wrapper -->
        <div class="wrapper">
            <h3>Items List</h3>
            <div class="search-container">
                <input type="text" id="searchInput" class="form-control search-input" placeholder="Search bakery items...">
            </div>
            <div class="items-table-wrapper">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item ID</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Cost Price</th>
                            <th>Unit</th>
                            <th>Category</th>
                            <th>Selling Price</th>
                            <th>Supplier</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $supplier_name = '';
                                if ($row['supplier_id']) {
                                    $sup_query = "SELECT supplier_name FROM suppliers WHERE supplier_id = " . $row['supplier_id'];
                                    $sup_result = $conn->query($sup_query);
                                    if ($sup_result && $sup_result->num_rows > 0) {
                                        $sup_row = $sup_result->fetch_assoc();
                                        $supplier_name = $sup_row['supplier_name'];
                                    }
                                }
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['item_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                echo "<td>" . number_format($row['quantity'], 2) . "</td>";
                                echo "<td>" . number_format($row['cost_price'], 2) . "</td>";
                                echo "<td>" . htmlspecialchars($row['item_type']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['bakery_category']) . "</td>";
                                echo "<td>" . number_format($row['item_price'], 2) . "</td>";
                                echo "<td>" . htmlspecialchars($supplier_name) . "</td>";
                                echo "<td><button class='btn btn-sm btn-danger discard-btn' data-id='" . htmlspecialchars($row['item_id']) . "' data-name='" . htmlspecialchars($row['item_name']) . "' data-qty='" . $row['quantity'] . "'>Discard</button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9'>No bakery items found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Discard Modal -->
<div class="modal fade" id="discardModal" tabindex="-1" role="dialog" aria-labelledby="discardModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="discardModalLabel">Discard Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to discard <span id="discardItemName"></span>?</p>
                <form id="discardForm">
                    <input type="hidden" id="discardItemId">
                    <div class="form-group">
                        <label for="discardQuantity">Quantity to discard:</label>
                        <input type="number" class="form-control" id="discardQuantity" step="0.01" min="0.01" required>
                        <small class="text-muted">Available: <span id="availableQuantity"></span></small>
                    </div>
                    <div class="form-group">
                        <label for="discardReason">Reason for discard:</label>
                        <select class="form-control" id="discardReason" required>
                            <option value="">Select a reason</option>
                            <option value="Expired">Expired</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Quality Issue">Quality Issue</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group" id="otherReasonGroup" style="display:none;">
                        <label for="otherReason">Specify other reason:</label>
                        <textarea class="form-control" id="otherReason"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmDiscard">Save and Discard</button>
            </div>
        </div>
    </div>
</div>

<script>
function calculateTotalCost() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const costPrice = parseFloat(document.getElementById('cost_price').value) || 0;
    const totalCost = quantity * costPrice;
    document.getElementById('total_cost').value = totalCost.toFixed(2);
}
function saveBakeryItem() {
    var item_id = document.getElementById('item_id').value;
    var item_name = document.getElementById('item_name').value;
    var quantity = document.getElementById('quantity').value;
    var cost_price = document.getElementById('cost_price').value;
    var item_type = document.getElementById('item_type').value;
    var item_price = document.getElementById('item_price').value;
    var item_description = document.getElementById('item_description').value;
    var uber_pickme_price = document.getElementById('uber_pickme_price').value;
    var dining_price = document.getElementById('dining_price').value;
    var takeaway_price = document.getElementById('takeaway_price').value;
    var delivery_service_item_price = document.getElementById('delivery_service_item_price').value;
    var supplier_id = document.getElementById('supplier_id').value;

    var f = new FormData();
    f.append("item_id", item_id);
    f.append("item_name", item_name);
    f.append("quantity", quantity);
    f.append("cost_price", cost_price);
    f.append("item_type", item_type);
    f.append("item_price", item_price);
    f.append("item_description", item_description);
    f.append("uber_pickme_price", uber_pickme_price);
    f.append("dining_price", dining_price);
    f.append("takeaway_price", takeaway_price);
    f.append("delivery_service_item_price", delivery_service_item_price);
    f.append("supplier_id", supplier_id);

    var x = new XMLHttpRequest();
    x.onreadystatechange = function() {
        if (x.readyState === 4) {
            if (x.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Bakery item created successfully!',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to create bakery item: ' + x.responseText
                });
            }
        }
    };
    x.open("POST", "success_create_bakery.php", true);
    x.send(f);
}
function quickSaveBakeryItem() {
    var item_id = document.getElementById('quick_item_id').value;
    var item_name = document.getElementById('quick_item_name').value;

    var f = new FormData();
    f.append("item_id", item_id);
    f.append("item_name", item_name);
    f.append("is_quick", "true");

    var x = new XMLHttpRequest();
    x.onreadystatechange = function() {
        if (x.readyState === 4) {
            if (x.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Bakery item registered successfully!',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to register bakery item: ' + x.responseText
                });
            }
        }
    };
    x.open("POST", "success_create_bakery.php", true);
    x.send(f);
}

function searchItems(searchValue) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('itemsTableBody').innerHTML = xhr.responseText;
        }
    };
    xhr.open("GET", "search_bakery_items.php?search=" + encodeURIComponent(searchValue), true);
    xhr.send();
}

function fetchItemName(item_id) {
    if (item_id.length > 0) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.item_name) {
                    document.getElementById('item_name').value = response.item_name;
                    document.getElementById('item_name').focus();
                } else {
                    document.getElementById('item_name').value = '';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Not Found',
                        text: 'No item found with this ID'
                    });
                }
            }
        };
        xhr.open("GET", "fetch_item_name.php?item_id=" + encodeURIComponent(item_id), true);
        xhr.send();
    } else {
        document.getElementById('item_name').value = '';
    }
}

$(document).ready(function() {
    // Handle discard button click
    $(document).on('click', '.discard-btn', function() {
        const itemId = $(this).data('id');
        const itemName = $(this).data('name');
        const availableQty = $(this).data('qty');
        
        $('#discardItemId').val(itemId);
        $('#discardItemName').text(itemName);
        $('#availableQuantity').text(availableQty);
        $('#discardQuantity').attr('max', availableQty);
        $('#discardQuantity').val('');
        $('#discardReason').val('');
        $('#otherReason').val('');
        $('#otherReasonGroup').hide();
        
        // Show the modal using Bootstrap's modal function
        $('#discardModal').modal('show');
    });
    
    // Handle reason select change
    $('#discardReason').change(function() {
        if($(this).val() === 'Other') {
            $('#otherReasonGroup').show();
        } else {
            $('#otherReasonGroup').hide();
        }
    });
    
    // Handle confirm discard button
    $('#confirmDiscard').click(function() {
        const itemId = $('#discardItemId').val();
        const quantity = $('#discardQuantity').val();
        const reasonSelect = $('#discardReason').val();
        let reason = reasonSelect;
        
        // Debug info
        console.log("Item ID:", itemId);
        console.log("Quantity:", quantity);
        console.log("Reason Select:", reasonSelect);
        
        // Validate item ID
        if(!itemId || itemId.trim() === '') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Item ID is missing.'
            });
            return;
        }
        
        // Validate quantity
        if(!quantity || quantity.trim() === '' || isNaN(parseFloat(quantity)) || parseFloat(quantity) <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a valid quantity greater than zero.'
            });
            $('#discardQuantity').focus();
            return;
        }
        
        // Validate reason
        if(!reasonSelect || reasonSelect.trim() === '') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select a reason for the discard.'
            });
            $('#discardReason').focus();
            return;
        }
        
        // Handle "Other" reason
        if(reasonSelect === 'Other') {
            const otherReason = $('#otherReason').val();
            if(!otherReason || otherReason.trim() === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please specify a reason when selecting "Other".'
                });
                $('#otherReason').focus();
                return;
            }
            reason = otherReason;
        }
        
        console.log("Final reason:", reason);
        
        const availableQty = parseFloat($('#availableQuantity').text());
        if(parseFloat(quantity) > availableQty) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Discard quantity cannot exceed available quantity.'
            });
            return;
        }
        
        // Show loading state
        Swal.fire({
            title: 'Processing...',
            text: 'Saving discard information',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Send discard request
        $.ajax({
            url: 'discard_bakery_item.php',
            type: 'POST',
            data: {
                item_id: itemId,
                quantity: quantity,
                reason: reason
            },
            success: function(response) {
                console.log("Server response:", response);
                let result;
                
                // Check if response is already an object
                if (typeof response === 'object') {
                    result = response;
                } else {
                    try {
                        result = JSON.parse(response);
                    } catch(e) {
                        console.error("Error parsing response:", e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Invalid response from server. Check console for details.'
                        });
                        return;
                    }
                }
                
                if(result.success) {
                    $('#discardModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Item discarded successfully!',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Failed to discard item.'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to connect to server: ' + error
                });
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Main form handling
    document.getElementById('bakeryForm').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            if (document.activeElement === document.getElementById('item_id')) {
                e.preventDefault();
                fetchItemName(document.getElementById('item_id').value);
            } else if (document.activeElement !== document.getElementById('item_description')) {
                e.preventDefault();
                document.getElementById('submitBtn').click();
            }
        }
        $("#supplier_name").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "fetch_suppliers.php",
                type: "GET",
                data: { term: request.term },
                dataType: "json",
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 1,
        select: function(event, ui) {
            $("#supplier_id").val(ui.item.id);
            $("#supplier_name").val(ui.item.value);
            return false;
        }
    });
    });
    
    const formElements = document.querySelectorAll('#bakeryForm input, #bakeryForm select, #bakeryForm textarea, #bakeryForm button');
    
    formElements.forEach(function(element) {
        element.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
                e.preventDefault();
                const currentIndex = Array.from(formElements).indexOf(document.activeElement);
                const nextElement = formElements[currentIndex + 1] || formElements[0];
                nextElement.focus();
            } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
                e.preventDefault();
                const currentIndex = Array.from(formElements).indexOf(document.activeElement);
                const prevElement = formElements[currentIndex - 1] || formElements[formElements.length - 1];
                prevElement.focus();
            }
        });
    });

    // Quick form handling
    document.getElementById('quickBakeryForm').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('quickSubmitBtn').click();
        }
    });
    
    const quickFormElements = document.querySelectorAll('#quickBakeryForm input, #quickBakeryForm button');
    
    quickFormElements.forEach(function(element) {
        element.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
                e.preventDefault();
                const currentIndex = Array.from(quickFormElements).indexOf(document.activeElement);
                const nextElement = quickFormElements[currentIndex + 1] || quickFormElements[0];
                nextElement.focus();
            } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
                e.preventDefault();
                const currentIndex = Array.from(quickFormElements).indexOf(document.activeElement);
                const prevElement = quickFormElements[currentIndex - 1] || quickFormElements[quickFormElements.length - 1];
                prevElement.focus();
            }
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    let timeout = null;
    
    searchInput.addEventListener('input', function(e) {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            searchItems(e.target.value);
        }, 300);
    });

    // Total cost calculation
    const quantityInput = document.getElementById('quantity');
    const costPriceInput = document.getElementById('cost_price');
    
    quantityInput.addEventListener('input', calculateTotalCost);
    costPriceInput.addEventListener('input', calculateTotalCost);

    document.getElementById('quick_item_id').focus();
});
</script>
</body>
</html>