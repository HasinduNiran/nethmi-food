<?php
session_start();
require_once "../config.php";

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $item_id = $_GET['id'];
} else {
    header("Location: ../panel/menu-panel.php");
    exit();
}

$menuItemQuery = "SELECT item_name FROM menu WHERE item_id = ?";
$stmt = $link->prepare($menuItemQuery);
$stmt->bind_param("s", $item_id);
$stmt->execute();
$menuItemResult = $stmt->get_result();
$menuItem = $menuItemResult->fetch_assoc();
$stmt->close();

// Handle delete ingredient
if (isset($_POST['delete_ingredient']) && isset($_POST['ingredient_id'])) {
    $ingredientId = $_POST['ingredient_id'];
    $deleteQuery = "DELETE FROM menu_ingredients WHERE id = ?";
    $stmt = $link->prepare($deleteQuery);
    $stmt->bind_param("i", $ingredientId);
    $stmt->execute();
    $stmt->close();
    
    header("Location: setupMenuIngredients.php?id=" . $item_id);
    exit();
}

// Handle delete entire recipe by portion size
if (isset($_POST['delete_recipe']) && isset($_POST['portion_size'])) {
    $portionSize = $_POST['portion_size'];
    $deleteQuery = "DELETE FROM menu_ingredients WHERE menu_item_id = ? AND portion_size = ?";
    $stmt = $link->prepare($deleteQuery);
    $stmt->bind_param("ss", $item_id, $portionSize);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['success_message'] = "Recipe for " . $portionSize . " portion successfully deleted!";
    header("Location: setupMenuIngredients.php?id=" . $item_id);
    exit();
}

// Handle finalize recipe submission (multiple ingredients)
if (isset($_POST['finalize_recipe']) && isset($_POST['ingredients'])) {
    $ingredients = $_POST['ingredients'];
    $portion_size = $_POST['recipe_portion_size'];
    $link->begin_transaction();
    try {
        foreach ($ingredients as $ingredient) {
            $ingredient_id = $ingredient['ingredient_id'];
            $quantity = $ingredient['quantity'];
            $measurement = $ingredient['measurement'];
            
            $insertQuery = "INSERT INTO menu_ingredients (menu_item_id, ingredient_id, quantity, measurement, portion_size, created_date) 
                        VALUES (?, ?, ?, ?, ?, CURDATE())";
            $stmt = $link->prepare($insertQuery);
            $stmt->bind_param("sidss", $item_id, $ingredient_id, $quantity, $measurement, $portion_size);
            $stmt->execute();
            $stmt->close();
        }
        $link->commit();
        
        $_SESSION['success_message'] = "Recipe successfully added!";
        header("Location: setupMenuIngredients.php?id=" . $item_id);
        exit();
    } catch (Exception $e) {
        $link->rollback();
        $_SESSION['error_message'] = "Error adding recipe: " . $e->getMessage();
        header("Location: setupMenuIngredients.php?id=" . $item_id);
        exit();
    }
}

// Get existing ingredients grouped by portion size
$ingredientsQuery = "SELECT mi.id, mi.quantity, mi.measurement, mi.portion_size, ii.itemname, ii.itemid 
                    FROM menu_ingredients mi
                    JOIN inventory_items ii ON mi.ingredient_id = ii.itemid
                    WHERE mi.menu_item_id = ?
                    ORDER BY mi.portion_size, ii.itemname";
$stmt = $link->prepare($ingredientsQuery);
$stmt->bind_param("s", $item_id);
$stmt->execute();
$ingredientsResult = $stmt->get_result();
$ingredientsByPortion = [];

while ($row = $ingredientsResult->fetch_assoc()) {
    $portionSize = $row['portion_size'] ?: 'Unspecified';
    if (!isset($ingredientsByPortion[$portionSize])) {
        $ingredientsByPortion[$portionSize] = [];
    }
    $ingredientsByPortion[$portionSize][] = $row;
}
$stmt->close();

// Get all available ingredients for dropdown
$allIngredientsQuery = "SELECT itemid, itemname FROM inventory_items ORDER BY itemname ASC";
$allIngredientsResult = $link->query($allIngredientsQuery);
$allIngredients = [];
while ($row = $allIngredientsResult->fetch_assoc()) {
    $allIngredients[] = $row;
}

$measurementUnits = ['g', 'ml'];
$portionSizes = ['Regular', 'Medium', 'Large'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Menu Ingredients</title>
    <link rel="stylesheet" href="./setupMenuIngredients.styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="menu-recipe-container">
        <div class="row">
            <div class="col-12">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success_message'] ?>
                        <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error_message'] ?>
                        <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Setup Ingredients for: <?= htmlspecialchars($menuItem['item_name']) ?></h5>
                        <a href="../panel/menu-panel.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Menu</a>
                    </div>
                    <div class="card-body">
                        
                        <!-- Previous Recipes -->
                        <h6 class="card-title">Previous Recipes</h6>
                        <?php if (empty($ingredientsByPortion)): ?>
                            <div class="alert alert-info">No ingredients have been added for this menu item yet.</div>
                        <?php else: ?>
                            <!-- Tabs for portion sizes -->
                            <div class="tab-buttons">
                                <?php $isFirst = true; ?>
                                <?php foreach ($ingredientsByPortion as $portionSize => $ingredients): ?>
                                    <button class="tab-button <?= $isFirst ? 'active' : '' ?>" 
                                            onclick="openTab('<?= strtolower(str_replace(' ', '-', $portionSize)) ?>')">
                                        <?= htmlspecialchars($portionSize) ?> Portion
                                    </button>
                                    <?php $isFirst = false; ?>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php $isFirst = true; ?>
                            <?php foreach ($ingredientsByPortion as $portionSize => $ingredients): ?>
                                <div id="<?= strtolower(str_replace(' ', '-', $portionSize)) ?>" 
                                     class="tab-content <?= $isFirst ? 'active' : '' ?>">
                                    
                                    <div class="recipe-cards">
                                        <div class="recipe-card">
                                            <div class="recipe-card-header">
                                                <?= htmlspecialchars($portionSize) ?> Portion
                                                <form method="post" style="display: inline;" 
                                                      onsubmit="return confirm('Are you sure you want to delete the entire <?= htmlspecialchars($portionSize) ?> recipe?');">
                                                    <input type="hidden" name="portion_size" value="<?= $portionSize ?>">
                                                    <button type="submit" name="delete_recipe" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Delete Recipe
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="recipe-card-body">
                                                <ul class="ingredient-list">
                                                    <?php foreach($ingredients as $ingredient): ?>
                                                        <li>
                                                            <span><?= htmlspecialchars($ingredient['itemname']) ?>: 
                                                                 <?= htmlspecialchars($ingredient['quantity']) ?> <?= htmlspecialchars($ingredient['measurement']) ?>
                                                            </span>
                                                            <form method="post" style="display: inline;" 
                                                                  onsubmit="return confirm('Are you sure you want to delete this ingredient?');">
                                                                <input type="hidden" name="ingredient_id" value="<?= $ingredient['id'] ?>">
                                                                <button type="submit" name="delete_ingredient" class="btn btn-sm btn-danger">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                            <div class="recipe-card-footer">
                                                <span><strong>Total Ingredients:</strong> <?= count($ingredients) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php $isFirst = false; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <!-- Add New Recipe -->
                        <h6 class="card-title mt-4">Add New Recipe</h6>
                        <form method="post" id="addRecipeForm">
                            <div class="form-group">
                                <label for="recipe_portion_size">Portion Size for this Recipe</label>
                                <select class="form-control" name="recipe_portion_size" id="recipe_portion_size" required>
                                    <option value="">Select Portion Size</option>
                                    <?php foreach($portionSizes as $size): ?>
                                        <option value="<?= $size ?>"><?= htmlspecialchars($size) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div id="ingredientsContainer">
                                <!-- Initial ingredient row -->
                                <div class="ingredient-row mb-3 border p-3 rounded">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>Ingredient</label>
                                            <select class="form-control" name="ingredients[0][ingredient_id]" required>
                                                <option value="">Select Ingredient</option>
                                                <?php foreach($allIngredients as $ingredient): ?>
                                                    <option value="<?= $ingredient['itemid'] ?>"><?= htmlspecialchars($ingredient['itemname']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Quantity</label>
                                            <input type="number" class="form-control" name="ingredients[0][quantity]" step="0.01" min="0.01" required>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Measurement</label>
                                            <select class="form-control" name="ingredients[0][measurement]" required>
                                                <option value="">Select Unit</option>
                                                <?php foreach($measurementUnits as $unit): ?>
                                                    <option value="<?= $unit ?>"><?= htmlspecialchars($unit) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger remove-ingredient" style="display: none;">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="button" id="addIngredientBtn" class="btn btn-info">
                                    <i class="fas fa-plus"></i> Add Another Ingredient
                                </button>
                            </div>
                            
                            <div class="form-group mt-4 d-flex justify-content-between">
                                <a href="../panel/menu-panel.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" name="finalize_recipe" class="btn recipe-final-btn">
                                    <i class="fas fa-check"></i> Finalize Recipe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        let ingredientCount = 1;
        
        // Add new ingredient row
        $('#addIngredientBtn').click(function() {
            const newRow = `
                <div class="ingredient-row mb-3 border p-3 rounded">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Ingredient</label>
                            <select class="form-control" name="ingredients[${ingredientCount}][ingredient_id]" required>
                                <option value="">Select Ingredient</option>
                                <?php foreach($allIngredients as $ingredient): ?>
                                    <option value="<?= $ingredient['itemid'] ?>"><?= htmlspecialchars($ingredient['itemname']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Quantity</label>
                            <input type="number" class="form-control" name="ingredients[${ingredientCount}][quantity]" step="0.01" min="0.01" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Measurement</label>
                            <select class="form-control" name="ingredients[${ingredientCount}][measurement]" required>
                                <option value="">Select Unit</option>
                                <?php foreach($measurementUnits as $unit): ?>
                                    <option value="<?= $unit ?>"><?= htmlspecialchars($unit) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-ingredient">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            $('#ingredientsContainer').append(newRow);
            ingredientCount++;
            
            // Show the remove button if we have more than one ingredient
            if ($('.ingredient-row').length > 1) {
                $('.remove-ingredient').show();
            }
        });
        
        // Remove ingredient row (using event delegation)
        $(document).on('click', '.remove-ingredient', function() {
            $(this).closest('.ingredient-row').remove();
            
            // Hide remove buttons if only one ingredient left
            if ($('.ingredient-row').length <= 1) {
                $('.remove-ingredient').hide();
            }
        });
        
        // Form validation before submission
        $('#addRecipeForm').on('submit', function(e) {
            if ($('.ingredient-row').length < 1) {
                e.preventDefault();
                alert('Please add at least one ingredient to the recipe');
                return false;
            }
            
            // Check if portion size is selected
            if (!$('#recipe_portion_size').val()) {
                e.preventDefault();
                alert('Please select a portion size for this recipe');
                return false;
            }
            
            return true;
        });
    });
    
    // Tab functionality
    function openTab(tabId) {
        // Hide all tab contents
        var tabContents = document.getElementsByClassName('tab-content');
        for (var i = 0; i < tabContents.length; i++) {
            tabContents[i].classList.remove('active');
        }
        
        // Deactivate all tab buttons
        var tabButtons = document.getElementsByClassName('tab-button');
        for (var i = 0; i < tabButtons.length; i++) {
            tabButtons[i].classList.remove('active');
        }
        
        // Show the selected tab content and activate the button
        document.getElementById(tabId).classList.add('active');
        document.querySelector(`button[onclick="openTab('${tabId}')"]`).classList.add('active');
    }
    </script>
</body>
</html>