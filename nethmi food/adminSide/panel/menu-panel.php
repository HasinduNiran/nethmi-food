<?php
session_start(); // Ensure session is started
require_once '../posBackend/checkIfLoggedIn.php';

require_once "../config.php";

$sql = "SELECT * FROM menu_item_type";
$result = mysqli_query($link, $sql);
?>
<?php include '../inc/dashHeader.php'; ?>

<style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #3498db;
        --accent-color: #e74c3c;
        --light-color: #ecf0f1;
        --dark-color: #2c3e50;
        --success-color: #2ecc71;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --text-color: #333;
        --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 4px 8px rgba(0, 0, 0, 0.12);
        --border-radius: 8px;
        --transition: all 0.3s ease;
    }

    body {
        background-color: #f5f7fa;
        color: var(--text-color);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .page-wrapper {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
        margin-top:60px;
        margin-left:210px;
        width:80%;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--secondary-color);
    }

    .page-title {
        color: var(--primary-color);
        font-weight: 600;
        font-size: 28px;
        margin: 0;
    }

    .btn-actions {
        display: flex;
        gap: 10px;
    }

    .btn-custom {
        padding: 10px 15px;
        border-radius: var(--border-radius);
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        box-shadow: var(--shadow-sm);
    }

    .btn-custom i {
        margin-right: 8px;
    }

    .btn-primary-custom {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary-custom:hover {
        background-color: #1a252f;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-success-custom {
        background-color: var(--success-color);
        color: white;
    }

    .btn-success-custom:hover {
        background-color: #27ae60;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-light-custom {
        background-color: var(--light-color);
        color: var(--dark-color);
    }

    .btn-light-custom:hover {
        background-color: #d6dbdf;
        transform: translateY(-2px);
    }

    .search-container {
        background-color: white;
        border-radius: var(--border-radius);
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-sm);
    }

    .search-form {
        display: flex;
        gap: 15px;
        align-items: flex-end;
    }

    .form-group {
        flex: 1;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--dark-color);
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: var(--border-radius);
        background-color: #f9f9f9;
        transition: var(--transition);
    }

    .form-control:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.25);
        outline: none;
    }

    .table-container {
        background-color: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .menu-table {
        width: 100%;
        border-collapse: collapse;
    }

    .menu-table th, 
    .menu-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #eee;
        vertical-align: top;
    }

    .menu-table th {
        background-color: var(--primary-color);
        color: white;
        font-weight: 500;
        white-space: nowrap;
    }

    .menu-table tr:hover {
        background-color: rgba(52, 152, 219, 0.05);
    }

    .item-id {
        font-weight: 600;
        color: var(--primary-color);
    }

    .item-name {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 5px;
    }

    .item-description {
        color: #666;
        font-size: 13px;
        margin-top: 5px;
    }

    .item-type, 
    .item-category {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 13px;
        background-color: #f0f0f0;
        margin-right: 8px;
        margin-top: 5px;
    }
    
    .label-tag {
        font-weight: 600;
        color: var(--primary-color);
        font-size: 13px;
        margin-right: 5px;
    }

    .service-type {
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .price-label {
        font-size: 13px;
        color: #666;
        margin-top: 10px;
        margin-bottom: 3px;
    }
    
    .price-label:first-of-type {
        margin-top: 5px;
    }

    .price-value {
        font-weight: 500;
        color: var(--dark-color);
        margin-bottom: 8px;
    }

    .price-value.empty {
        color: #aaa;
        font-style: italic;
        font-weight: normal;
    }

    .actions-cell {
        white-space: nowrap;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--light-color);
        color: var(--dark-color);
        border: none;
        cursor: pointer;
        transition: var(--transition);
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .btn-action:hover {
        background-color: var(--secondary-color);
        color: white;
        transform: translateY(-2px);
    }

    .btn-action.delete {
        background-color: var(--light-color);
        color: var(--danger-color);
    }

    .btn-action.delete:hover {
        background-color: var(--danger-color);
        color: white;
    }

    .empty-message {
        padding: 30px;
        text-align: center;
        color: #666;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border-radius: var(--border-radius);
        width: 400px;
        box-shadow: var(--shadow-md);
        text-align: center;
    }

    .modal-header {
        margin-bottom: 15px;
        color: var(--danger-color);
    }

    .modal-body {
        margin-bottom: 20px;
    }

    .modal-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .btn-modal {
        padding: 8px 20px;
        border-radius: var(--border-radius);
        font-weight: 500;
        cursor: pointer;
        border: none;
    }

    .btn-danger {
        background-color: var(--danger-color);
        color: white;
    }

    .btn-cancel {
        background-color: var(--light-color);
        color: var(--dark-color);
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .btn-actions {
            width: 100%;
            justify-content: space-between;
        }
        
        .search-form {
            flex-direction: column;
        }
    }
</style>

<div class="page-wrapper">
    <div class="page-header">
        <h1 class="page-title">Menu Items</h1>
        <div class="btn-actions">
            <a href="../menuCrud/createItem.php" class="btn-custom btn-primary-custom">
                <i class="fa fa-plus"></i> Add Menu Item
            </a>
            <a href="../menuCrud/createsideItem.php" class="btn-custom btn-success-custom">
                <i class="fa fa-plus"></i> Add Side Menu
            </a>
            <a href="../menuCrud/createItemType.php" class="btn btn-warning"><i class="fa fa-plus"></i> New Menu Type</a>
        </div>
    </div>

    <div class="search-container">
        <form method="POST" action="#" class="search-form">
            <div class="form-group">
                <label for="search" class="form-label">Filter by Type or Category</label>
                <select name="search" id="search" class="form-control">
                    <option value="">Select Item Type or Item Category</option>
                    <?php
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="' . htmlspecialchars($row['item_type_name']) . '">' . htmlspecialchars($row['item_type_name']) . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No Item Types Found</option>';
                                }
                            ?>LKR
                </select>
            </div>
            <button type="submit" class="btn-custom btn-primary-custom">
                <i class="fa fa-search"></i> Search
            </button>
            <a href="menu-panel.php" class="btn-custom btn-light-custom">
                <i class="fa fa-refresh"></i> Show All
            </a>
        </form>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fa fa-exclamation-triangle"></i> Delete Menu Item</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this menu item?</p>
                <p><strong>This action cannot be undone.</strong></p>
            </div>
            <div class="modal-actions">
                <form id="deleteForm" method="POST" action="../menuCrud/delete_menu_item.php">
                    <input type="hidden" id="delete_item_id" name="item_id" value="">
                    <button type="button" class="btn-modal btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-modal btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    // Include config file
    require_once "../config.php";

    // Define query based on search input and branch ID
    if (isset($_POST['search']) && !empty($_POST['search'])) {
        $search = mysqli_real_escape_string($link, $_POST['search']);
        $sql = "SELECT * FROM menu WHERE (item_type LIKE '%$search%' OR item_category LIKE '%$search%' 
                OR item_name LIKE '%$search%' OR item_id LIKE '%$search%')";

        $sql .= " ORDER BY id DESC;";
    } else {
        $sql = "SELECT * FROM menu";
        
        
        $sql .= " ORDER BY id DESC;";
    }

    // Execute query and display results
    if ($result = mysqli_query($link, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            echo '<div class="table-container">';
            echo '<table class="menu-table">';
            echo "<thead>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Item Details</th>";
            echo "<th>Dine-in Prices</th>";
            echo "<th>Takeaway Prices</th>";
            echo "<th>Delivery Service Prices</th>";
            echo "<th>Uber/PickMe Prices</th>";
            echo "<th>Actions</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                
                // Item ID
                echo "<td class='item-id'>" . htmlspecialchars($row['item_id']) . "</td>";
                
                // Item Details
                echo "<td>";
                echo "<div class='item-name'>" . htmlspecialchars($row['item_name']) . "</div>";
                echo "<div><span class='label-tag'>Category:</span> <span class='item-category'>" . htmlspecialchars($row['item_category']) . "</span></div>";
                echo "<div><span class='label-tag'>Menu Type:</span> <span class='item-type'>" . htmlspecialchars($row['item_type']) . "</span></div>";
                echo "<div><span class='label-tag'>Sub Menu Type:</span> <span class='item-type'>" . htmlspecialchars($row['sub_item_type']) . "</span></div>";

                echo "<div><span class='label-tag'>Description:</span> <span class='item-description'>" . htmlspecialchars($row['item_description']) . "</span></div>";
                echo "</td>";
                
                // Dine-in Prices
                echo "<td>";
                echo "<div class='service-type'><i class='fa fa-utensils'></i> Dine-in</div>";
                
                // Regular price
                echo "<div class='price-label'>Family</div>";
                echo "<div class='price-value" . (empty($row['regular_price']) ? " empty" : "") . "'>";
                echo !empty($row['regular_price']) ? htmlspecialchars($row['regular_price']) : "N/A";
                echo "</div>";
                
                // Medium price
                echo "<div class='price-label'>Medium</div>";
                echo "<div class='price-value" . (empty($row['medium_price']) ? " empty" : "") . "'>";
                echo !empty($row['medium_price']) ? htmlspecialchars($row['medium_price']) : "N/A";
                echo "</div>";
                
                // Large price
                echo "<div class='price-label'>Large</div>";
                echo "<div class='price-value" . (empty($row['large_price']) ? " empty" : "") . "'>";
                echo !empty($row['large_price']) ? htmlspecialchars($row['large_price']) : "N/A";
                echo "</div>";
                
                echo "</td>";
                
                // Takeaway Prices
                echo "<td>";
                echo "<div class='service-type'><i class='fa fa-shopping-bag'></i> Takeaway</div>";
                
                // Regular price
                echo "<div class='price-label'>Family</div>";
                echo "<div class='price-value" . (empty($row['takeaway_regular']) ? " empty" : "") . "'>";
                echo !empty($row['takeaway_regular']) ? htmlspecialchars($row['takeaway_regular']) : "N/A";
                echo "</div>";
                
                // Medium price
                echo "<div class='price-label'>Medium</div>";
                echo "<div class='price-value" . (empty($row['takeaway_medium']) ? " empty" : "") . "'>";
                echo !empty($row['takeaway_medium']) ? htmlspecialchars($row['takeaway_medium']) : "N/A";
                echo "</div>";
                
                // Large price
                echo "<div class='price-label'>Large</div>";
                echo "<div class='price-value" . (empty($row['takeaway_large']) ? " empty" : "") . "'>";
                echo !empty($row['takeaway_large']) ? htmlspecialchars($row['takeaway_large']) : "N/A";
                echo "</div>";
                
                echo "</td>";
                
                // Delivery Prices
                echo "<td>";
                echo "<div class='service-type'><i class='fa fa-truck'></i> Delivery Service</div>";
                
                // Regular price
                echo "<div class='price-label'>Family</div>";
                echo "<div class='price-value" . (empty($row['delivery_service_regular']) ? " empty" : "") . "'>";
                echo !empty($row['delivery_service_regular']) ? htmlspecialchars($row['delivery_service_regular']) : "N/A";
                echo "</div>";
                
                // Medium price
                echo "<div class='price-label'>Medium</div>";
                echo "<div class='price-value" . (empty($row['delivery_service_medium']) ? " empty" : "") . "'>";
                echo !empty($row['delivery_service_medium']) ? htmlspecialchars($row['delivery_service_medium']) : "N/A";
                echo "</div>";
                
                // Large price
                echo "<div class='price-label'>Large</div>";
                echo "<div class='price-value" . (empty($row['delivery_service_large']) ? " empty" : "") . "'>";
                echo !empty($row['delivery_service_large']) ? htmlspecialchars($row['delivery_service_large']) : "N/A";
                echo "</div>";
                
                echo "</td>";
                
                // Uber/PickMe Prices
                echo "<td>";
                echo "<div class='service-type'><i class='fa fa-car'></i> Uber/PickMe</div>";
                
                // Regular price
                echo "<div class='price-label'>Family</div>";
                echo "<div class='price-value" . (empty($row['uber_pickme_regular']) ? " empty" : "") . "'>";
                echo !empty($row['uber_pickme_regular']) ? htmlspecialchars($row['uber_pickme_regular']) : "N/A";
                echo "</div>";
                
                // Medium price
                echo "<div class='price-label'>Medium</div>";
                echo "<div class='price-value" . (empty($row['uber_pickme_medium']) ? " empty" : "") . "'>";
                echo !empty($row['uber_pickme_medium']) ? htmlspecialchars($row['uber_pickme_medium']) : "N/A";
                echo "</div>";
                
                // Large price
                echo "<div class='price-label'>Large</div>";
                echo "<div class='price-value" . (empty($row['uber_pickme_large']) ? " empty" : "") . "'>";
                echo !empty($row['uber_pickme_large']) ? htmlspecialchars($row['uber_pickme_large']) : "N/A";
                echo "</div>";
                
                echo "</td>";
                
                // Actions
                echo "<td class='actions-cell'>";
                echo '<a href="../menuCrud/updateItemVerify.php?id=' . $row['item_id'] . '" class="btn-action" title="Edit Item">';
                    echo '<i class="fa fa-pencil"></i>';
                echo '</a>';
                // for add ingredients
                echo '<a href="../menuCrud/setupMenuIngredients.php?id=' . $row['item_id'] . '" class="btn-action" title="Add Ingredients">';
                    echo '<i class="fa-solid fa-mortar-pestle"></i>';
                echo '</a>';
                // Add delete button
                echo '<button type="button" class="btn-action delete" title="Delete Item" onclick="confirmDelete(\'' . $row['item_id'] . '\')">';
                    echo '<i class="fa fa-trash"></i>';
                echo '</button>';
                echo "</td>";
                
                echo "</tr>";
            }
            
            echo "</tbody>";
            echo "</table>";
            echo "</div>"; // Close table-container
            
            // Free result set
            mysqli_free_result($result);
        } else {
            echo '<div class="empty-message">';
            echo '<i class="fa fa-info-circle fa-3x" style="color: #999; margin-bottom: 15px;"></i>';
            echo '<p>No menu items were found. Try a different search or add new items.</p>';
            echo '</div>';
        }
    } else {
        echo '<div class="empty-message">';
        echo '<i class="fa fa-exclamation-triangle fa-3x" style="color: #e74c3c; margin-bottom: 15px;"></i>';
        echo '<p>Oops! Something went wrong. Please try again later.</p>';
        echo '</div>';
    }

    // Close connection
    mysqli_close($link);
    ?>
</div>

<script>
    // JavaScript functions for the delete modal
    function confirmDelete(itemId) {
        document.getElementById('delete_item_id').value = itemId;
        document.getElementById('deleteModal').style.display = 'block';
    }
    
    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }
    
    // Close the modal if user clicks outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

<?php include '../inc/dashFooter.php'; ?>