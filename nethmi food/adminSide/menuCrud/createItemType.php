<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vintage | Menu Types Management</title>
    <link rel="stylesheet" href="./createItemType.styles.css">
</head>
<body>
    <div class="item-type-adder-container">
        <!-- Left column for forms -->
        <div class="forms-column">
            <!-- form area for adding new menu types -->
            <div class="type-adder-form-area">
                <div class="headliner-item-type" style="margin-bottom: 30px;">Create Menu Type</div>
                <label class="item-type-label">Name of Menu Type:</label>
                <input type="text" id="menu-type-val" placeholder="eg: Chinese">
                <button class="add-type-btn" onclick="addMenuItemType()">Add Type</button>

                <div class="empty-type-alert">
                    <div class="qmark">
                        ?
                    </div>
                    Menu type can not be empty!
                </div>
            </div>

            <!-- form area for adding new sub menu types -->
            <div class="type-adder-form-area">
                <div class="headliner-item-type" style="margin-bottom: 30px;">Create Sub Menu Type</div>
                <label class="item-type-label">Parent Menu Type:</label>
                <select id="parent-menu-type" class="menu-type-select">
                    <option value="">Select Parent Menu Type</option>
                </select>
                
                <label class="item-type-label" style="margin-top: 15px;">Name of Sub Menu Type:</label>
                <input type="text" id="sub-menu-type-val" placeholder="eg: Appetizers">
                <button class="add-type-btn" onclick="addSubMenuType()">Add Sub Type</button>

                <div class="empty-type-alert sub-alert">
                    <div class="qmark">
                        ?
                    </div>
                    Fields can not be empty!
                </div>
            </div>
        </div>
        
        <!-- Right column for display -->
        <div class="display-column">
            <!-- Filter indicator -->
            <div id="filter-indicator" class="filter-indicator">
                <span id="filter-text">Showing all sub menu types</span>
                <button id="clear-filter" class="clear-filter-btn" onclick="clearFilter()" style="display:none;">Clear Filter</button>
            </div>
            
            <!-- Container for horizontal display of available types -->
            <div class="horizontal-displays">
                <!-- the window to toggle the availability of the menu types -->
                <div class="type-adder-availability-cont">
                    <div class="headliner-item-type">Available Menu Types</div>
                    <div class="av-type-link-cont" id="av-type-link-cont"></div>
                </div>
                
                <!-- the window to show sub menu types -->
                <div class="type-adder-availability-cont">
                    <div class="headliner-item-type">Available Sub Menu Types</div>
                    <div class="av-type-link-cont" id="sub-type-link-cont"></div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
function addMenuItemType() {
    let itemType = document.getElementById("menu-type-val").value;
    if (itemType.trim() === "") {
        document.querySelector(".empty-type-alert").style.display = "flex";
        setTimeout(()=>{
            document.querySelector(".empty-type-alert").style.display = "none";
        },5000)
        return;
    }
    let formData = new FormData();
    formData.append("item_type_name", itemType);
    fetch("./createItemTypeLogic.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert("New Menu Type Created!");
        document.getElementById("menu-type-val").value = ""; 
        fetchMenuItems();
        populateParentMenuTypes();
    })
    .catch(error => {
       console.log("Error " + error);
    });
}

function addSubMenuType() {
    let parentTypeId = document.getElementById("parent-menu-type").value;
    let subTypeName = document.getElementById("sub-menu-type-val").value;
    
    if (parentTypeId.trim() === "" || subTypeName.trim() === "") {
        document.querySelector(".sub-alert").style.display = "flex";
        setTimeout(()=>{
            document.querySelector(".sub-alert").style.display = "none";
        },5000)
        return;
    }
    
    let formData = new FormData();
    formData.append("parent_type_id", parentTypeId);
    formData.append("sub_type_name", subTypeName);
    
    fetch("./createSubMenuTypeLogic.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert("New Sub Menu Type Created!");
        document.getElementById("sub-menu-type-val").value = "";
        fetchSubMenuTypes();
        
        // If we have an active filter, refresh the filtered results
        const activeItem = document.querySelector('.item-type-holder.active');
        if (activeItem) {
            const typeId = activeItem.id.split('-')[1];
            const typeName = activeItem.querySelector('span').textContent;
            filterSubMenuTypes(typeId, typeName);
        }
    })
    .catch(error => {
       console.log("Error " + error);
    });
}

// Variable to track current filter
let currentFilter = null;

function filterSubMenuTypes(typeId, typeName) {
    // Set the current filter
    currentFilter = typeId;
    
    // Update active state for menu items
    const allItems = document.querySelectorAll('.item-type-holder');
    allItems.forEach(item => {
        if (item.id === `item-${typeId}`) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
    
    // Update filter indicator
    document.getElementById('filter-text').textContent = `Showing sub menu types for: ${typeName}`;
    document.getElementById('clear-filter').style.display = 'block';
    
    // Fetch filtered sub menu types
    fetch(`fetchFilteredSubMenuTypes.php?parent_id=${typeId}`)
    .then(response => response.text())
    .then(data => {
        document.getElementById("sub-type-link-cont").innerHTML = data;
    })
    .catch(error => console.error("Error fetching filtered sub menu types:", error));
}

function clearFilter() {
    // Clear current filter
    currentFilter = null;
    
    // Remove active state from all menu items
    const allItems = document.querySelectorAll('.item-type-holder');
    allItems.forEach(item => {
        item.classList.remove('active');
    });
    
    // Reset filter indicator
    document.getElementById('filter-text').textContent = 'Showing all sub menu types';
    document.getElementById('clear-filter').style.display = 'none';
    
    // Fetch all sub menu types
    fetchSubMenuTypes();
}

document.addEventListener("DOMContentLoaded", function() {
    fetchMenuItems();
    fetchSubMenuTypes();
    populateParentMenuTypes();
});

function populateParentMenuTypes() {
    fetch("fetchAllItemTypesForSelect.php")
    .then(response => response.text())
    .then(data => {
        document.getElementById("parent-menu-type").innerHTML = 
            '<option value="">Select Parent Menu Type</option>' + data;
    })
    .catch(error => console.error("Error fetching menu types:", error));
}

function fetchMenuItems() {
    fetch("fetchAllItemTypes.php")
    .then(response => response.text())
    .then(data => {
        document.getElementById("av-type-link-cont").innerHTML = data;
    })
    .catch(error => console.error("Error fetching data:", error));
}

function fetchSubMenuTypes() {
    fetch("fetchAllSubMenuTypes.php")
    .then(response => response.text())
    .then(data => {
        document.getElementById("sub-type-link-cont").innerHTML = data;
    })
    .catch(error => console.error("Error fetching sub menu types:", error));
}

function deleteMenuItem(itemTypeId) {
    if (!confirm("Are you sure you want to delete this menu type? All associated sub menu types will also be deleted.")) return;

    let formData = new FormData();
    formData.append("item_type_id", itemTypeId);

    fetch("./deleteItemType.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data === "success") {
            document.getElementById("item-" + itemTypeId).remove();
            fetchSubMenuTypes(); // Refresh sub menu types as some might have been deleted
            populateParentMenuTypes(); // Refresh parent menu types dropdown
            
            // Clear filter if the deleted item was the active filter
            if (currentFilter == itemTypeId) {
                clearFilter();
            }
        } else {
            alert("Failed to delete item.");
        }
    })
    .catch(error => console.error("Error deleting item:", error));
}

function deleteSubMenuItem(subTypeId) {
    if (!confirm("Are you sure you want to delete this sub menu type?")) return;

    let formData = new FormData();
    formData.append("sub_type_id", subTypeId);

    fetch("./deleteSubMenuType.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data === "success") {
            document.getElementById("sub-item-" + subTypeId).remove();
        } else {
            alert("Failed to delete sub menu type.");
        }
    })
    .catch(error => console.error("Error deleting sub menu type:", error));
}
</script>
</html>