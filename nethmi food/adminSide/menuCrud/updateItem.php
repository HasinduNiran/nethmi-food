<?php
session_start(); // Ensure the session is started
require_once "../config.php"; // Include the database configuration

// Initialize variables
$item_id = $item_name = $item_type = $sub_item_type = $item_category = $item_description = "";
$regular_price = $medium_price = $large_price = "";
$takeaway_regular = $takeaway_medium = $takeaway_large = "";
$uber_pickme_regular = $uber_pickme_medium = $uber_pickme_large = "";
$delivery_service_regular = $delivery_service_medium = $delivery_service_large = "";
$error_message = "";

// Fetch item types from menu_item_type table
$item_types = array();
$item_type_sql = "SELECT item_type_id, item_type_name FROM menu_item_type ORDER BY item_type_name ASC";
if ($item_type_result = mysqli_query($link, $item_type_sql)) {
    while ($item_type_row = mysqli_fetch_assoc($item_type_result)) {
        $item_types[] = $item_type_row;
    }
    mysqli_free_result($item_type_result);
} else {
    $error_message = "Error retrieving item types.";
}

// Create a mapping of item type names to their IDs for later use
$item_type_name_to_id = array();
foreach ($item_types as $type) {
    $item_type_name_to_id[$type['item_type_name']] = $type['item_type_id'];
}

// Fetch all sub menu types for use with JavaScript later
$all_sub_types = array();
$sub_type_sql = "SELECT smt.sub_type_id, smt.parent_type_id, smt.sub_type_name 
                FROM sub_menu_type smt 
                ORDER BY smt.sub_type_name ASC";
if ($sub_type_result = mysqli_query($link, $sub_type_sql)) {
    while ($sub_type_row = mysqli_fetch_assoc($sub_type_result)) {
        $parent_id = $sub_type_row['parent_type_id'];
        if (!isset($all_sub_types[$parent_id])) {
            $all_sub_types[$parent_id] = array();
        }
        $all_sub_types[$parent_id][] = $sub_type_row;
    }
    mysqli_free_result($sub_type_result);
} else {
    $error_message = "Error retrieving sub menu types.";
}

// Check if `id` is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $item_id = $_GET['id'];

    // Fetch item details from the database
    $sql = "SELECT * FROM menu WHERE item_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $item_id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);
                $item_name = $row['item_name'];
                $item_type = $row['item_type'];
                $sub_item_type = $row['sub_item_type'];
                $item_category = $row['item_category'];
                $item_description = $row['item_description'];
                
                // Get all price fields
                $regular_price = $row['regular_price'];
                $medium_price = $row['medium_price'];
                $large_price = $row['large_price'];
                
                $takeaway_regular = $row['takeaway_regular'];
                $takeaway_medium = $row['takeaway_medium'];
                $takeaway_large = $row['takeaway_large'];
                
                $uber_pickme_regular = $row['uber_pickme_regular'];
                $uber_pickme_medium = $row['uber_pickme_medium'];
                $uber_pickme_large = $row['uber_pickme_large'];
                
                $delivery_service_regular = $row['delivery_service_regular'];
                $delivery_service_medium = $row['delivery_service_medium'];
                $delivery_service_large = $row['delivery_service_large'];
                
            } else {
                $error_message = "Item not found.";
            }
        } else {
            $error_message = "Error retrieving item details.";
        }
        mysqli_stmt_close($stmt);
    }
} else {
    header("Location: ../panel/menu-panel.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and validate input data
    $item_name = trim($_POST["item_name"]);
    $item_type = trim($_POST["item_type"]);
    $sub_item_type = trim($_POST["sub_item_type"]);
    $item_category = trim($_POST["item_category"]);
    $item_description = trim($_POST["item_description"]);
    
    // Get all price fields
    $regular_price = !empty($_POST["regular_price"]) ? floatval($_POST["regular_price"]) : NULL;
    $medium_price = !empty($_POST["medium_price"]) ? floatval($_POST["medium_price"]) : NULL;
    $large_price = !empty($_POST["large_price"]) ? floatval($_POST["large_price"]) : NULL;
    
    $takeaway_regular = !empty($_POST["takeaway_regular"]) ? floatval($_POST["takeaway_regular"]) : NULL;
    $takeaway_medium = !empty($_POST["takeaway_medium"]) ? floatval($_POST["takeaway_medium"]) : NULL;
    $takeaway_large = !empty($_POST["takeaway_large"]) ? floatval($_POST["takeaway_large"]) : NULL;
    
    $uber_pickme_regular = !empty($_POST["uber_pickme_regular"]) ? floatval($_POST["uber_pickme_regular"]) : NULL;
    $uber_pickme_medium = !empty($_POST["uber_pickme_medium"]) ? floatval($_POST["uber_pickme_medium"]) : NULL;
    $uber_pickme_large = !empty($_POST["uber_pickme_large"]) ? floatval($_POST["uber_pickme_large"]) : NULL;
    
    $delivery_service_regular = !empty($_POST["delivery_service_regular"]) ? floatval($_POST["delivery_service_regular"]) : NULL;
    $delivery_service_medium = !empty($_POST["delivery_service_medium"]) ? floatval($_POST["delivery_service_medium"]) : NULL;
    $delivery_service_large = !empty($_POST["delivery_service_large"]) ? floatval($_POST["delivery_service_large"]) : NULL;

    // Update the database with all fields
    $update_sql = "UPDATE menu SET 
                   item_name = ?, 
                   item_type = ?, 
                   sub_item_type = ?,
                   item_category = ?, 
                   item_description = ?,
                   regular_price = ?,
                   medium_price = ?,
                   large_price = ?,
                   takeaway_regular = ?,
                   takeaway_medium = ?,
                   takeaway_large = ?,
                   uber_pickme_regular = ?,
                   uber_pickme_medium = ?,
                   uber_pickme_large = ?,
                   delivery_service_regular = ?,
                   delivery_service_medium = ?,
                   delivery_service_large = ?
                   WHERE item_id = ?";
                   
    if ($stmt = mysqli_prepare($link, $update_sql)) {
        mysqli_stmt_bind_param(
            $stmt, 
            "sssssdddddddddddds", 
            $item_name, 
            $item_type, 
            $sub_item_type,
            $item_category, 
            $item_description,
            $regular_price,
            $medium_price,
            $large_price,
            $takeaway_regular,
            $takeaway_medium,
            $takeaway_large,
            $uber_pickme_regular,
            $uber_pickme_medium,
            $uber_pickme_large,
            $delivery_service_regular,
            $delivery_service_medium,
            $delivery_service_large,
            $item_id
        );

        if (mysqli_stmt_execute($stmt)) {
            // Redirect to the menu panel after a successful update
            header("Location: ../panel/menu-panel.php");
            exit();
        } else {
            $error_message = "Error updating item. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Menu Item</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --shadow-sm: 0 .125rem .25rem rgba(0,0,0,.075);
            --shadow: 0 .5rem 1rem rgba(0,0,0,.15);
            --border-radius: 8px;
            --transition: all 0.3s ease;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0;
        }

        .form-container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 30px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--secondary-color);
        }

        .form-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            padding-bottom: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .form-control {
            border-radius: var(--border-radius);
            padding: 10px 15px;
            border: 1px solid #ddd;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .price-section {
            background-color: #f8f9fa;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid var(--secondary-color);
        }

        .price-section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            color: var(--primary-color);
        }

        .btn-custom {
            padding: 10px 25px;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-success:hover {
            background-color: #27ae60;
            border-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .form-actions {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .price-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .form-group label {
            font-weight: 500;
        }

        .alert {
            border-radius: var(--border-radius);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .price-grid {
                grid-template-columns: 1fr;
            }
            
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h2 class="form-title">Update Menu Item</h2>
                <p class="text-muted">Make changes to the menu item details and pricing information</p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- Basic Item Information -->
                <h4 class="section-title">Basic Information</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="item_name">Item Name:</label>
                            <input type="text" name="item_name" id="item_name" class="form-control" value="<?php echo htmlspecialchars($item_name); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="item_id">Item ID:</label>
                            <input type="text" id="item_id" class="form-control" value="<?php echo htmlspecialchars($item_id); ?>" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="item_category">Item Category:</label>
                            <select name="item_category" id="item_category" class="form-control" required>
                                <option value="">Select Item Category</option>
                                <option value="Main Dishes" <?php echo ($item_category == 'Main Dishes') ? 'selected' : ''; ?>>Main Dishes</option>
                                <option value="Drinks" <?php echo ($item_category == 'Drinks') ? 'selected' : ''; ?>>Drinks</option>
                                <option value="Side Snacks" <?php echo ($item_category == 'Side Snacks') ? 'selected' : ''; ?>>Side Snacks</option>
                                <option value="Outdoor" <?php echo ($item_category == 'Outdoor') ? 'selected' : ''; ?>>Outdoor</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="item_type">Item Type:</label>
                            <select name="item_type" id="item_type" class="form-control" required>
                                <option value="">Select Item Type</option>
                                <?php foreach ($item_types as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type['item_type_name']); ?>" 
                                            <?php echo ($item_type == $type['item_type_name']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type['item_type_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sub_item_type">Sub Item Type:</label>
                            <select name="sub_item_type" id="sub_item_type" class="form-control">
                                <option value="">Select Sub Item Type</option>
                                <?php 
                                // Get the parent type ID for the current item
                                $parent_type_id = isset($item_type_name_to_id[$item_type]) ? $item_type_name_to_id[$item_type] : 0;
                                
                                // Display sub-types for the current parent type
                                if (isset($all_sub_types[$parent_type_id])) {
                                    foreach ($all_sub_types[$parent_type_id] as $sub_type) {
                                        $selected = ($sub_item_type == $sub_type['sub_type_name']) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($sub_type['sub_type_name']) . '" ' . $selected . '>';
                                        echo htmlspecialchars($sub_type['sub_type_name']);
                                        echo '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="item_description">Item Description:</label>
                    <textarea name="item_description" id="item_description" class="form-control" rows="3"><?php echo htmlspecialchars($item_description); ?></textarea>
                </div>
                
                <!-- Dine-in Prices -->
                <div class="price-section">
                    <div class="price-section-header">
                        <i class="fas fa-utensils"></i>
                        <h5 class="mb-0">Dine-in Prices</h5>
                    </div>
                    <div class="price-grid">
                        <div class="form-group">
                            <label for="regular_price">Family Price:</label>
                            <input type="number" name="regular_price" id="regular_price" class="form-control" value="<?php echo htmlspecialchars($regular_price); ?>" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="medium_price">Medium Price:</label>
                            <input type="number" name="medium_price" id="medium_price" class="form-control" value="<?php echo htmlspecialchars($medium_price); ?>" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="large_price">Large Price:</label>
                            <input type="number" name="large_price" id="large_price" class="form-control" value="<?php echo htmlspecialchars($large_price); ?>" min="0" step="0.01">
                        </div>
                    </div>
                </div>
                
                <!-- Takeaway Prices -->
                <div class="price-section">
                    <div class="price-section-header">
                        <i class="fas fa-shopping-bag"></i>
                        <h5 class="mb-0">Takeaway Prices</h5>
                    </div>
                    <div class="price-grid">
                        <div class="form-group">
                            <label for="takeaway_regular">Family Price:</label>
                            <input type="number" name="takeaway_regular" id="takeaway_regular" class="form-control" value="<?php echo htmlspecialchars($takeaway_regular); ?>" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="takeaway_medium">Medium Price:</label>
                            <input type="number" name="takeaway_medium" id="takeaway_medium" class="form-control" value="<?php echo htmlspecialchars($takeaway_medium); ?>" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="takeaway_large">Large Price:</label>
                            <input type="number" name="takeaway_large" id="takeaway_large" class="form-control" value="<?php echo htmlspecialchars($takeaway_large); ?>" min="0" step="0.01">
                        </div>
                    </div>
                </div>
                
                
                <!-- Uber/PickMe Prices -->
                <div class="price-section">
                    <div class="price-section-header">
                        <i class="fas fa-car"></i>
                        <h5 class="mb-0">Uber/PickMe Prices</h5>
                    </div>
                    <div class="price-grid">
                        <div class="form-group">
                            <label for="uber_pickme_regular">Family Price:</label>
                            <input type="number" name="uber_pickme_regular" id="uber_pickme_regular" class="form-control" value="<?php echo htmlspecialchars($uber_pickme_regular); ?>" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="uber_pickme_medium">Medium Price:</label>
                            <input type="number" name="uber_pickme_medium" id="uber_pickme_medium" class="form-control" value="<?php echo htmlspecialchars($uber_pickme_medium); ?>" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="uber_pickme_large">Large Price:</label>
                            <input type="number" name="uber_pickme_large" id="uber_pickme_large" class="form-control" value="<?php echo htmlspecialchars($uber_pickme_large); ?>" min="0" step="0.01">
                        </div>
                    </div>
                </div>


                <!-- Delivery Service Prices -->
                <div class="price-section">
                    <div class="price-section-header">
                        <i class="fas fa-truck"></i>
                        <h5 class="mb-0">Delivery Service Prices</h5>
                    </div>
                    <div class="price-grid">
                        <div class="form-group">
                            <label for="delivery_service_regular">Family Price:</label>
                            <input type="number" name="delivery_service_regular" id="delivery_service_regular" class="form-control" value="<?php echo htmlspecialchars($delivery_service_regular); ?>" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="delivery_service_medium">Medium Price:</label>
                            <input type="number" name="delivery_service_medium" id="delivery_service_medium" class="form-control" value="<?php echo htmlspecialchars($delivery_service_medium); ?>" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="delivery_service_large">Large Price:</label>
                            <input type="number" name="delivery_service_large" id="delivery_service_large" class="form-control" value="<?php echo htmlspecialchars($delivery_service_large); ?>" min="0" step="0.01">
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-success btn-custom">
                        <i class="fas fa-save mr-2"></i>Update Item
                    </button>
                    <a href="../panel/menu-panel.php" class="btn btn-danger btn-custom">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Store all sub types in JavaScript
        const allSubTypes = <?php echo json_encode($all_sub_types); ?>;
        const itemTypeMap = <?php echo json_encode($item_type_name_to_id); ?>;
        
        // Function to update sub item types based on selected item type
        function updateSubItemTypes() {
            const itemTypeSelect = document.getElementById('item_type');
            const subItemTypeSelect = document.getElementById('sub_item_type');
            const selectedItemType = itemTypeSelect.value;
            const selectedItemTypeId = itemTypeMap[selectedItemType];
            
            // Clear existing options
            subItemTypeSelect.innerHTML = '<option value="">Select Sub Item Type</option>';
            
            // If there are sub-types for this parent, add them
            if (selectedItemTypeId && allSubTypes[selectedItemTypeId]) {
                allSubTypes[selectedItemTypeId].forEach(subType => {
                    const option = document.createElement('option');
                    option.value = subType.sub_type_name;
                    option.textContent = subType.sub_type_name;
                    subItemTypeSelect.appendChild(option);
                });
            }
        }
        
        // Set up event listener for item type changes
        document.getElementById('item_type').addEventListener('change', updateSubItemTypes);
    </script>
</body>
</html>