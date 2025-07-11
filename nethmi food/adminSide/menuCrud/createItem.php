<?php
session_start();
include '../inc/dashHeader.php';
require_once "../config.php";

// Fetch all menu types
$sql = "SELECT * FROM menu_item_type";
$result = mysqli_query($link, $sql);

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
?>

<head>
    <meta charset="UTF-8">
    <title>Create New Item</title>
    <link rel="stylesheet" href="style.css">
    <!-- Add SweetAlert CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #3b7ddd;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --shadow-sm: 0 .125rem .25rem rgba(0,0,0,.075);
            --shadow: 0 .5rem 1rem rgba(0,0,0,.15);
            --transition: all .2s ease-in-out;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            color: #444;
            line-height: 1.6;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 2rem;
            padding: 2rem 1rem;
            max-width: 1400px;
            margin: 0 auto;
            margin-left:200px;
            margin-top:40px;
        }

        .form-panel {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 2rem;
            width: 100%;
            max-width: 800px;
        }

        .side-panel {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 500px;
            position: sticky;
            top: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,.125);
            padding: 1.25rem 1.5rem;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section-title {
            margin-bottom: 1.5rem;
            color: var(--dark-color);
            font-weight: 600;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
            display: inline-block;
        }

        .form-control {
            border-radius: 6px;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(59, 125, 221, 0.25);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #555;
        }

        .page-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .page-title {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--secondary-color);
            font-weight: 400;
        }

        .price-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .pricing-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
            padding: 1.25rem;
            border-top: 3px solid var(--primary-color);
            transition: var(--transition);
        }

        .pricing-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .pricing-type {
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary-color);
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
        }

        .pricing-fields {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background-color: #2e63b8;
            border-color: #2e63b8;
            transform: translateY(-2px);
        }

        .btn-icon {
            margin-right: 0.5rem;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: 8px;
             
        }

        .table th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            padding: 1rem;
        }

        .table td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody{
            padding-bottom:50px;
            margin-bottom:100px;
        }

        .table tbody tr:hover {
            background-color: rgba(59, 125, 221, 0.05);
        }

        .dropdown {
            position: relative;
            width: 100%;
        }

        .dropdown-list {
            position: absolute;
            z-index: 1000;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: var(--shadow);
            display: none;
        }

        .dropdown-list div {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .dropdown-list div:hover {
            background-color: rgba(59, 125, 221, 0.1);
        }

        .text-danger {
            color: var(--danger-color) !important;
        }

        .text-success {
            color: var(--success-color) !important;
        }

        .alert-info {
            background-color: rgba(23, 162, 184, 0.1);
            border-color: rgba(23, 162, 184, 0.2);
            color: #0c5460;
            border-radius: 8px;
            padding: 1rem;
        }

        .delete-btn {
            color: var(--danger-color);
            cursor: pointer;
            transition: var(--transition);
        }

        .delete-btn:hover {
            color: #bd2130;
            transform: scale(1.1);
        }

        @media (max-width: 1200px) {
            .price-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 992px) {
            .container {
                flex-direction: column;
                align-items: center;
            }

            .form-panel, .side-panel {
                max-width: 100%;
            }

            .side-panel {
                position: static;
                margin-top: 2rem;
            }
        }

        @media (max-width: 768px) {
            .price-grid {
                grid-template-columns: 1fr;
            }
        }

    </style>
</head>
<body>

<div class="container">
    <div class="form-panel">
        <div class="page-header">
            <h2 class="page-title">Create New Item</h2>
            <p class="page-subtitle">Please fill in item information properly</p>
        </div>

        <?php if(isset($_SESSION['branch_name']) && !empty($_SESSION['branch_name'])): ?>
        <div class="alert alert-info mb-4">
            <strong><i class="fas fa-store-alt me-2"></i>Branch:</strong> <?php echo htmlspecialchars($_SESSION['branch_name']); ?>
            <input type="hidden" id="branch_id" value="<?php echo htmlspecialchars($_SESSION['branch_id'] ?? ''); ?>">
        </div>
        <?php endif; ?>

        <form method="POST" action="success_create.php">
            <!-- Basic Information Section -->
            <div class="form-section">
                <h4 class="form-section-title">Basic Information</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="item_id" class="form-label">Item ID</label>
                        <input type="text" name="item_id" class="form-control <?php echo !$item_idErr ?: 'is-invalid'; ?>" 
                            id="item_id" required item_id="item_id" placeholder="H88" value="<?php echo $item_id; ?>">
                        <div class="invalid-feedback">
                            Please provide a valid item ID.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="item_name" class="form-label">Item Name</label>
                        <input type="text" name="item_name" id="item_name" placeholder="Spaghetti" required 
                            class="form-control <?php echo (!empty($itemname_err)) ? 'is-invalid' : ''; ?>">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row">
    <div class="col-md-4 mb-3">
        <label for="item_category" class="form-label">Item Category</label>
        <select name="item_category" id="item_category" 
            class="form-control <?php echo (!empty($itemcategory_err)) ? 'is-invalid' : ''; ?>" required>
            <option value="">Select Item Category</option>
            <option value="Main Dishes">Main Dishes</option>
            <option value="Outdoor">Outdoor</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-4 mb-3">
        <label for="item_type" class="form-label">Menu Type</label>
        <select name="item_type" id="item_type" 
            class="form-control <?php echo (!empty($itemtype_err)) ? 'is-invalid' : ''; ?>" 
            required onchange="loadSubMenuTypes(this.value)">
            <option value="">Select Menu Type</option>
            <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . htmlspecialchars($row['item_type_name']) . '">' . htmlspecialchars($row['item_type_name']) . '</option>';
                    }
                } else {
                    echo '<option value="">No Item Types Found</option>';
                }
            ?>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-4 mb-3">
        <label for="sub_item_type" class="form-label">Sub Menu Type</label>
        <select name="sub_item_type" id="sub_item_type" 
            class="form-control <?php echo (!empty($subitemtype_err)) ? 'is-invalid' : ''; ?>">
            <option value="">Select Menu Type First</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
</div>
            </div>

            <!-- Pricing Sections -->
            <div class="form-section">
                <h4 class="form-section-title">Pricing Information</h4>

                <!-- Dining-in Pricing -->
                <div class="pricing-card mb-4">
                    <h5 class="pricing-type"><i class="fas fa-utensils me-2"></i>Dining-in Pricing</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="regular_price" class="form-label">Family Price</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input min="0.01" type="number" name="regular_price" id="regular_price" 
                                    placeholder="12.34" step="0.01" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="medium_price" class="form-label">Medium Price</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input min="0.01" type="number" name="medium_price" id="medium_price" 
                                    placeholder="12.34" step="0.01" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="large_price" class="form-label">Large Price</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input min="0.01" type="number" name="large_price" id="large_price" 
                                    placeholder="12.34" step="0.01" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Takeaway Pricing -->
                <div class="pricing-card mb-4">
                    <h5 class="pricing-type"><i class="fas fa-shopping-bag me-2"></i>Takeaway Pricing</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="takeaway_regular" class="form-label">Family Price</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input min="0.01" type="number" name="takeaway_regular" id="takeaway_regular" 
                                    placeholder="12.34" step="0.01" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="takeaway_medium" class="form-label">Medium Price</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input min="0.01" type="number" name="takeaway_medium" id="takeaway_medium" 
                                    placeholder="12.34" step="0.01" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="takeaway_large" class="form-label">Large Price</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input min="0.01" type="number" name="takeaway_large" id="takeaway_large" 
                                    placeholder="12.34" step="0.01" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Uber/PickMe Pricing -->
                <div class="pricing-card mb-4">
                    <h5 class="pricing-type"><i class="fas fa-car me-2"></i>Uber/PickMe Pricing</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="uber_pickme_regular" class="form-label">Family Price</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input min="0.01" type="number" name="uber_pickme_regular" id="uber_pickme_regular" 
                                    placeholder="12.34" step="0.01" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="uber_pickme_medium" class="form-label">Medium Price</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input min="0.01" type="number" name="uber_pickme_medium" id="uber_pickme_medium" 
                                    placeholder="12.34" step="0.01" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="uber_pickme_large" class="form-label">Large Price</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input min="0.01" type="number" name="uber_pickme_large" id="uber_pickme_large" 
                                    placeholder="12.34" step="0.01" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Service Pricing -->
                <div class="pricing-card mb-4">
                    <h5 class="pricing-type"><i class="fas fa-truck me-2"></i>Delivery Service Pricing</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="delivery_service_regular" class="form-label">Family Price</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input min="0.01" type="number" name="delivery_service_regular" id="delivery_service_regular" 
                                    placeholder="12.34" step="0.01" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="delivery_service_medium" class="form-label">Medium Price</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input min="0.01" type="number" name="delivery_service_medium" id="delivery_service_medium" 
                                    placeholder="12.34" step="0.01" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="delivery_service_large" class="form-label">Large Price</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input min="0.01" type="number" name="delivery_service_large" id="delivery_service_large" 
                                    placeholder="12.34" step="0.01" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                

            </div>

            <!-- Description Section -->
            <div class="form-section">
                <h4 class="form-section-title">Description</h4>
                <div class="mb-4">
                    <label for="item_description" class="form-label">Item Description</label>
                    <textarea name="item_description" id="item_description" rows="3" placeholder="Describe the dish..." 
                        required class="form-control <?php echo (!empty($itemdescription_err)) ? 'is-invalid' : ''; ?>"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <div class="form-section text-center">
                <button type="button" onclick="saveIteamData();" class="btn btn-primary">
                    <i class="fas fa-save btn-icon"></i>Create Item
                </button>
            </div>
        </form>
    </div>

    <div class="side-panel">
        <!-- Ingredients Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0"><i class="fas fa-list me-2"></i>Add Ingredients</h4>
            </div>
            <div class="card-body">
                <p id="cardDescriptioncurrency" class="text-danger"></p>
                <div class="table-responsive">
                    <table class="table" id="currencyTable">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Measurement</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="currencyTableBody">
                            <tr>
                                <td>
                                    <div class="dropdown">
                                        <input type="text" id="searchInputGuide0" class="form-control" placeholder="Search..."
                                            onclick="filterFunctionProductclick(0)" onkeyup="filterFunctionGuide(0);">
                                        <input name="vendorCommission" type="hidden" id="guidId0" value="0">
                                        <div id="dropdownListGuide0" class="dropdown-list">
                                            <?php
                                            $query = "SELECT * FROM `inventory`";
                                            $result = $link->query($query);
                                            if ($result) {
                                                for ($i = 0; $i < $result->num_rows; $i++) {
                                                    $rowin = $result->fetch_assoc();
                                                    $mid = $rowin['mesuer'];
                                                    $mersuer = '';
                                                    $querym = "SELECT * FROM `mesuer` WHERE id = $mid;";
                                                    $resultm = $link->query($querym);
                                                    if ($resultm->num_rows > 0) {
                                                        $rowm = $resultm->fetch_assoc();
                                                        if ($rowm['mesuer'] == 'KG') {
                                                            $mersuer = 'g';
                                                        } else if ($rowm['mesuer'] == 'L') {
                                                            $mersuer = 'ml';
                                                        } else {
                                                            $mersuer = $rowm['mesuer'];
                                                        }
                                                    }
                                            ?>
                                                    <div onclick="selectOptionGuide('<?php echo $rowin['iteamname']; ?>',0,'<?php echo $mersuer; ?>','<?php echo $rowin['id']; ?>','<?php echo $rowin['qty']; ?>');"
                                                        value="<?php echo $rowin['id']; ?>">
                                                        <?php echo $rowin['iteamname']; ?>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo "Error: " . $link->error;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="mesuer" class="form-control text-end"
                                        id="exchangeRate0" disabled placeholder="KG">
                                </td>
                                <td>
                                    <input name="qty" type="number" min="1" max="10"
                                        class="form-control text-end" id="amount0" placeholder="0">
                                </td>
                                <td class="text-center">
                                    <i class="fas fa-trash delete-btn" onclick="deleteRow(this)"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-end mt-3">
                        <button onclick="addRow();" class="btn btn-primary">
                            <i class="fas fa-plus btn-icon"></i>Add Row
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Dishes Card -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0"><i class="fas fa-utensils me-2"></i>Add Side Dishes</h4>
            </div>
            <div class="card-body">
                <p id="cardDescriptionError" class="text-danger"></p>
                <div class="table-responsive">
                    <table class="table" id="sideDishesTable">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Measurement</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="sideDishesTableBody">
                            <tr>
                                <td>
                                    <div class="dropdown">
                                        <input type="text" id="searchInputGuide10" class="form-control" placeholder="Search..." 
                                            onclick="filterFunctionProductclick1(0)" onkeyup="filterFunctionGuide1(0);">
                                        <input type="hidden" name="vendorCommission" id="guidId10" value="0">
                                        <div id="dropdownListGuide10" class="dropdown-list">
                                            <?php
                                            $query = "SELECT * FROM `side_menu`";
                                            $result = $link->query($query);
                                            if ($result) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $measurementId = $row['mesuer'];
                                                    $measurementName = '';
                                                    $queryMeasurement = "SELECT * FROM `mesuer` WHERE id = $measurementId";
                                                    $resultMeasurement = $link->query($queryMeasurement);
                                                    if ($resultMeasurement && $resultMeasurement->num_rows > 0) {
                                                        $measurementRow = $resultMeasurement->fetch_assoc();
                                                        if ($measurementRow['mesuer'] == 'KG') {
                                                            $measurementName = 'g';
                                                        } else if ($measurementRow['mesuer'] == 'L') {
                                                            $measurementName = 'ml';
                                                        } else {
                                                            $measurementName = $measurementRow['mesuer'];
                                                        }
                                                    }
                                            ?>
                                                    <div onclick="selectOptionGuides('<?php echo $row['item_name']; ?>', 0, '<?php echo $measurementName; ?>', '<?php echo $row['side_item_id']; ?>', '<?php echo $row['qty']; ?>');" value="<?php echo $row['side_item_id']; ?>">
                                                        <?php echo $row['item_name']; ?>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo "Error: " . $link->error;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="mesuer" class="form-control text-end" id="exchangeRate10" disabled placeholder="KG">
                                </td>
                                <td>
                                    <input name="qty" type="number" min="1" max="10" class="form-control text-end" id="amount10" placeholder="0">
                                </td>
                                <td class="text-center">
                                    <i class="fas fa-trash delete-btn" onclick="deleteRow(this)"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-end mt-3">
                        <button onclick="addRoww();" class="btn btn-primary">
                            <i class="fas fa-plus btn-icon"></i>Add Row
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.12/sweetalert2.all.min.js"></script>
<script>

function loadSubMenuTypes(menuType) {
    if (!menuType) {
        // If no menu type is selected, reset sub menu type dropdown
        document.getElementById('sub_item_type').innerHTML = '<option value="">Select Menu Type First</option>';
        return;
    }
    
    // Fetch sub menu types for the selected menu type
    fetch('fetchSubMenuTypesForSelect.php?menu_type=' + encodeURIComponent(menuType))
        .then(response => response.text())
        .then(data => {
            let subMenuSelect = document.getElementById('sub_item_type');
            subMenuSelect.innerHTML = '<option value="">Select Sub Menu Type</option>' + data;
        })
        .catch(error => {
            console.error('Error fetching sub menu types:', error);
            document.getElementById('sub_item_type').innerHTML = '<option value="">Error loading sub menu types</option>';
        });
}

// Add this to the existing saveIteamData function
function saveIteamData() {
    var item_id = document.getElementById('item_id').value;
    var item_name = document.getElementById('item_name').value;
    var item_category = document.getElementById('item_category').value;
    var item_type = document.getElementById('item_type').value;
    var sub_item_type = document.getElementById('sub_item_type').value; // Add this line
    var item_description = document.getElementById('item_description').value;
    var uber_pickme_regular = document.getElementById('uber_pickme_regular').value;
    var uber_pickme_medium = document.getElementById('uber_pickme_medium').value;
    var uber_pickme_large = document.getElementById('uber_pickme_large').value;
    var takeaway_regular = document.getElementById('takeaway_regular').value;
    var takeaway_medium = document.getElementById('takeaway_medium').value;
    var takeaway_large = document.getElementById('takeaway_large').value;
    var delivery_service_regular = document.getElementById('delivery_service_regular').value;
    var delivery_service_medium = document.getElementById('delivery_service_medium').value;
    var delivery_service_large = document.getElementById('delivery_service_large').value;
    var regular_price = document.getElementById('regular_price').value;
    var medium_price = document.getElementById('medium_price').value;
    var large_price = document.getElementById('large_price').value;
    var branch_id = document.getElementById('branch_id') ? document.getElementById('branch_id').value : '';

    var f = new FormData();
    f.append("item_id", item_id);
    f.append("item_name", item_name);
    f.append("item_category", item_category);
    f.append("item_type", item_type);
    f.append("sub_item_type", sub_item_type); // Add this line
    f.append("item_description", item_description);
    f.append("uber_pickme_regular", uber_pickme_regular);
    f.append("uber_pickme_medium", uber_pickme_medium);
    f.append("uber_pickme_large", uber_pickme_large);
    f.append("takeaway_regular", takeaway_regular);
    f.append("takeaway_medium", takeaway_medium);
    f.append("takeaway_large", takeaway_large);
    f.append("delivery_service_regular", delivery_service_regular);
    f.append("delivery_service_medium", delivery_service_medium);
    f.append("delivery_service_large", delivery_service_large);
    f.append("regular_price", regular_price);
    f.append("medium_price", medium_price);
    f.append("large_price", large_price);
    if (branch_id) {
        f.append("branch_id", branch_id);
    }

    var x = new XMLHttpRequest();
    x.onreadystatechange = function() {
        if (x.readyState === 4) {
            if (x.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: x.responseText,
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
                    text: x.responseText || 'Failed to create item'
                });
            }
        }
    };
    x.open("POST", "success_create.php", true);
    x.send(f);
}
    function saveData(menuItemId) {
        const rows = document.querySelectorAll("#currencyTableBody tr");
        let data = [];

        rows.forEach((row) => {
            const ingredientId = row.querySelector('input[name="vendorCommission"]').value;
            const measurement = row.querySelector('input[name="mesuer"]').value;
            const quantity = row.querySelector('input[name="qty"]').value;

            if (ingredientId && measurement && menuItemId && quantity && ingredientId !== "0") {
                data.push({
                    menu_item_id: menuItemId,
                    ingredient_id: ingredientId,
                    quantity: quantity,
                    measurement: measurement
                });
            }
        });

        if (data.length > 0) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "save_ingredients.php", true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.send(JSON.stringify(data));
        }
    }

    function saveDatasubIteam(id) {
        const rows = document.querySelectorAll("#sideDishesTableBody tr");
        let data = [];

        rows.forEach((row) => {
            const iteamId = row.querySelector('input[name="vendorCommission"]').value;
            const mesuer = row.querySelector('input[name="mesuer"]').value;
            const qty = row.querySelector('input[name="qty"]').value;

            if (iteamId && mesuer && id && qty && iteamId !== "0") {
                data.push({
                    iteamId: iteamId,
                    mesuer: mesuer,
                    id: id,
                    qty: qty
                });
            }
        });

        if (data.length > 0) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "savesubiteamdata.php", true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.send(JSON.stringify(data));
        }
    }
    
    // Dropdown functions
    function filterFunctionProductclick(id) {
        var input, filter, dropdownList, div, i;
        input = document.getElementById("searchInputGuide" + id);
        filter = input.value.toLowerCase();
        dropdownList = document.getElementById("dropdownListGuide" + id);
        div = dropdownList.getElementsByTagName("div");
        dropdownList.style.display = filter ? "block" : "none";
        let hasVisibleDiv = false;
        for (i = 0; i < div.length; i++) {
            if (div[i].innerHTML.toLowerCase().indexOf(filter) > -1) {
                div[i].style.display = "";
                hasVisibleDiv = true;
            } else {
                div[i].style.display = "none";
            }
        }
        if (!hasVisibleDiv) {
            dropdownList.innerHTML = "<div>No products found</div>";
        } else {
            dropdownList.style.display = "block";
        }
    }

    function filterFunctionGuide(id) {
        var input, filter, dropdownList, div, i;
        input = document.getElementById("searchInputGuide" + id);
        filter = input.value.toLowerCase();
        dropdownList = document.getElementById("dropdownListGuide" + id);
        div = dropdownList.getElementsByTagName("div");
        dropdownList.style.display = filter ? "block" : "none";
        for (i = 0; i < div.length; i++) {
            if (div[i].innerHTML.toLowerCase().indexOf(filter) > -1) {
                div[i].style.display = "";
            } else {
                div[i].style.display = "none";
            }
        }
    }
    
    function filterFunctionProductclick1(id) {
        var input, filter, dropdownList, div, i;
        input = document.getElementById("searchInputGuide1" + id);
        filter = input.value.toLowerCase();
        dropdownList = document.getElementById("dropdownListGuide1" + id);
        div = dropdownList.getElementsByTagName("div");
        dropdownList.style.display = filter ? "block" : "none";
        let hasVisibleDiv = false;
        for (i = 0; i < div.length; i++) {
            if (div[i].innerHTML.toLowerCase().indexOf(filter) > -1) {
                div[i].style.display = "";
                hasVisibleDiv = true;
            } else {
                div[i].style.display = "none";
            }
        }
        if (!hasVisibleDiv) {
            dropdownList.innerHTML = "<div>No products found</div>";
        } else {
            dropdownList.style.display = "block";
        }
    }
    
    function filterFunctionGuide1(id) {
        var input, filter, dropdownList, div, i;
        input = document.getElementById("searchInputGuide1" + id);
        filter = input.value.toLowerCase();
        dropdownList = document.getElementById("dropdownListGuide1" + id);
        div = dropdownList.getElementsByTagName("div");
        dropdownList.style.display = filter ? "block" : "none";
        for (i = 0; i < div.length; i++) {
            if (div[i].innerHTML.toLowerCase().indexOf(filter) > -1) {
                div[i].style.display = "";
            } else {
                div[i].style.display = "none";
            }
        }
    }
    
    function selectOptionGuide(option, id, mersuer, iteam, qty) {
        document.getElementById("searchInputGuide" + id).value = option;
        document.getElementById("guidId" + id).value = iteam;
        document.getElementById("exchangeRate" + id).value = mersuer;
        document.getElementById("amount" + id).max = qty;
        document.getElementById("dropdownListGuide" + id).style.display = "none";
    }
    
    function selectOptionGuides(option, id, mersuer, iteam, qty) {
        document.getElementById("searchInputGuide1" + id).value = option;
        document.getElementById("guidId1" + id).value = iteam;
        document.getElementById("exchangeRate1" + id).value = mersuer;
        document.getElementById("amount1" + id).max = qty;
        document.getElementById("dropdownListGuide1" + id).style.display = "none";
    }
    
    // Row management functions
    function addRow() {
        rowCounts++;
        const newRow = `
    <tr id="row${rowCounts}">
        <td>
            <div class="dropdown">
                <input type="text" id="searchInputGuide${rowCounts}" class="form-control" placeholder="Search..." 
                    onclick="filterFunctionProductclick(${rowCounts})" onkeyup="filterFunctionGuide(${rowCounts});">
                <input name="vendorCommission" type="hidden" id="guidId${rowCounts}" value="0">
                <div id="dropdownListGuide${rowCounts}" class="dropdown-list">
                    <?php
                    $query = "SELECT * FROM `inventory`";
                    $result = $link->query($query);
                    if ($result) {
                        for ($i = 0; $i < $result->num_rows; $i++) {
                            $rowin = $result->fetch_assoc();
                            $mid = $rowin['mesuer'];
                            $mersuer = '';
                            $querym = "SELECT * FROM `mesuer` WHERE id = $mid;";
                            $resultm = $link->query($querym);
                            if ($resultm->num_rows > 0) {
                                $rowm = $resultm->fetch_assoc();
                                if ($rowm['mesuer'] == 'KG') {
                                    $mersuer = 'g';
                                } else if ($rowm['mesuer'] == 'L') {
                                    $mersuer = 'ml';
                                } else {
                                    $mersuer = $rowm['mesuer'];
                                }
                            }
                    ?>
                        <div onclick="selectOptionGuide('<?php echo $rowin['iteamname']; ?>',${rowCounts},'<?php echo $mersuer; ?>','<?php echo $rowin['id']; ?>','<?php echo $rowin['qty']; ?>');" value="<?php echo $rowin['id']; ?>"> <?php echo $rowin['iteamname']; ?></div>
                    <?php
                        }
                    } else {
                        echo "Error: " . $link->error;
                    }
                    ?>
                </div>
            </div>
        </td>
        <td><input type="text" name="mesuer" class="form-control text-end" id="exchangeRate${rowCounts}" disabled placeholder="KG"></td>
        <td><input name="qty" type="number" min="1" max="10" class="form-control text-end" id="amount${rowCounts}" placeholder="0"></td>
        <td class="text-center"><i class="fas fa-trash delete-btn" onclick="deleteRow(this)"></i></td>
    </tr>
    `;
        document.getElementById("currencyTableBody").insertAdjacentHTML("beforeend", newRow);
    }
    
    function addRoww() {
        rowCountss++;
        const newRow = `
    <tr id="row${rowCountss}">
        <td>
            <div class="dropdown">
                <input type="text" id="searchInputGuide1${rowCountss}" class="form-control" placeholder="Search..." 
                    onclick="filterFunctionProductclick1(${rowCountss})" onkeyup="filterFunctionGuide1(${rowCountss});">
                <input name="vendorCommission" type="hidden" id="guidId1${rowCountss}" value="0">
                <div id="dropdownListGuide1${rowCountss}" class="dropdown-list">
                    <?php
                    $query1 = "SELECT * FROM `side_menu`";
                    $result1 = $link->query($query1);
                    if ($result1) {
                        for ($i = 0; $i < $result1->num_rows; $i++) {
                            $rowinn = $result1->fetch_assoc();
                            $midd = $rowinn['mesuer'];
                            $mersuerr = '';
                            $querymm = "SELECT * FROM `mesuer` WHERE id = $midd;";
                            $resultmm = $link->query($querymm);
                            if ($resultmm->num_rows > 0) {
                                $rowmm = $resultmm->fetch_assoc();
                                if ($rowmm['mesuer'] == 'KG') {
                                    $mersuerr = 'g';
                                } else if ($rowmm['mesuer'] == 'L') {
                                    $mersuerr = 'ml';
                                } else {
                                    $mersuerr = $rowmm['mesuer'];
                                }
                            }
                    ?>
                        <div onclick="selectOptionGuides('<?php echo $rowinn['item_name']; ?>',${rowCountss},'<?php echo $mersuerr; ?>','<?php echo $rowinn['side_item_id']; ?>','<?php echo $rowinn['qty']; ?>');" value="<?php echo $rowinn['side_item_id']; ?>"> <?php echo $rowinn['item_name']; ?></div>
                    <?php
                        }
                    } else {
                        echo "Error: " . $link->error;
                    }
                    ?>
                </div>
            </div>
        </td>
        <td><input type="text" name="mesuer" class="form-control text-end" id="exchangeRate1${rowCountss}" disabled placeholder="KG"></td>
        <td><input name="qty" type="number" min="1" max="10" class="form-control text-end" id="amount1${rowCountss}" placeholder="0"></td>
        <td class="text-center"><i class="fas fa-trash delete-btn" onclick="deleteRow(this)"></i></td>
    </tr>
    `;
        document.getElementById("sideDishesTableBody").insertAdjacentHTML("beforeend", newRow);
    }
    
    function deleteRow(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }
    
    
    
    let rowCounts = 0;
    let rowCountss = 0;
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        var dropdowns = document.getElementsByClassName('dropdown-list');
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.style.display === 'block' && !event.target.closest('.dropdown')) {
                openDropdown.style.display = 'none';
            }
        }
    });
    
    // Adjust dropdown width to match input
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.dropdown input[type="text"]');
        inputs.forEach(input => {
            const dropdown = input.nextElementSibling.nextElementSibling;
            if (dropdown && dropdown.classList.contains('dropdown-list')) {
                dropdown.style.width = input.offsetWidth + 'px';
            }
        });
    });
</script>
</body>
</html>

