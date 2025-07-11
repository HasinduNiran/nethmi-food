<?php
session_start(); // Ensure session is started
?>
<?php include '../inc/dashHeader.php' ?>
<?php
// Include config file
require_once "../config.php";

$input_item_id = $item_id_err = $item_id = "";

// Processing form data when form is submitted
if (isset($_POST['submit'])) {
    if (empty($_POST['item_id'])) {
        $item_idErr = 'ID is required';
    } else {
        // $item_id = filter_var($_POST['item_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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
    <style>
        .wrapper {
            width: 1300px;
            padding-left: 200px;
            padding-top: 80px
        }
    </style>
</head>

<div class="wrapper" style="padding-right:50px; margin-top: -70px;">
    <h3>Create New Item</h1>
        <p>Please fill Items Information Properly </p>

        <form method="POST" action="success_create.php" class="ht-700 w-54">

            <div class="form-group">
                <label for="item_id" class="form-label">Item ID :</label>
                <input type="text" name="item_id" class="form-control <?php echo !$item_idErr ?:
                                                                            'is-invalid'; ?>" id="item_id" required item_id="item_id" placeholder="H88" value="<?php echo $item_id; ?>"><br>
                <div id="validationServerFeedback" class="invalid-feedback">
                    Please provide a valid item_id.
                </div>
            </div>

            <div class="form-group">
                <label for="item_name">Item Name :</label>
                <input type="text" name="item_name" id="item_name" placeholder="Spaghetti" required class="form-control <?php echo (!empty($itemname_err)) ? 'is-invalid' : ''; ?>"><br>
                <span class="invalid-feedback"></span>
            </div>

            <div class="form-group">
                <label for="item_category">Item Category:</label>
                <select name="item_category" id="item_category" class="form-control <?php echo (!empty($itemcategory_err)) ? 'is-invalid' : ''; ?>" required>
                    <option value="">Select Item Category</option>
                    <option value="Main Dishes">Main Dishes</option>
                    <option value="Side Snacks">Side Snacks</option>
                    <option value="Drinks">Drinks</option>
                </select>
                <span class="invalid-feedback"></span>
            </div><br>


            <div class="form-group">
                <label for="item_type">Item Type:</label>
                <select name="item_type" id="item_type" class="form-control placeholder=" Select Item Type" <?php echo (!empty($itemtype_err)) ? 'is-invalid' : ''; ?>" required>
                    <option value="">Select Item Type</option>
                    <option value="Steak & Ribs">Steak & Ribs</option>
                    <option value="Seafood">Seafood</option>
                    <option value="Pasta">Pasta</option>
                    <option value="Lamb">Lamb</option>
                    <option value="Chicken">Chicken</option>
                    <option value="Burgers & Sandwiches">Burgers & Sandwiches</option>
                    <option value="Bar Bites">Bar Bites</option>
                    <option value="House Dessert">House Dessert</option>
                    <option value="Salad">Salad</option>
                    <option value="Shoney Kid">Shoney Kid</option>
                    <option value="Side Dishes">Side Dishes</option>
                    <option value="Classic Cocktails">Classic Cocktails</option>
                    <option value="Cold Pressed Juice">Cold Pressed Juice</option>
                    <option value="House Cocktails">House Cocktails</option>
                    <option value="Mocktails">Mocktails</option>
                </select>
                <span class="invalid-feedback"></span>
            </div><br>


            <div class="form-group">
                <label for="item_price">Item Price :</label>
                <input min='0.01' type="number" name="item_price" id="item_price" placeholder="12.34" step="0.01" required class="form-control <?php echo (!empty($itemprice_err)) ? 'is-invalid' : ''; ?>"><br>
                <span class="invalid-feedback"></span>
            </div>

            <div class="form-group">
                <label for="item_description">Item Description :</label>
                <textarea name="item_description" id="item_description" rows="3" placeholder="The dish...." required class="form-control <?php echo (!empty($itemdescription_err)) ? 'is-invalid' : ''; ?>"></textarea><br>
                <span class="invalid-feedback"></span>
            </div>

            <div class="form-group">
                <input type="button" onclick="saveIteamData();" class="btn btn-dark" value="Create Item">
            </div>


        </form>
</div>

<div class="col-md-5 grid-margin stretch-card" style="padding-right: 30px;">

    <br>
    <br>
    <br>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add Ingredients</h4>
            <p id="cardDescriptioncurrency" class="text-danger"></p>

            <div class="table-responsive">
                <table class="table" id="currencyTable">
                    <thead>
                        <tr>
                            <th>Iteam Name</th>
                            <th>Mesuserment</th>
                            <th>QTY</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="currencyTableBody">
                        <td>
                            <div style="display: inline-block;">
                                <div class="dropdown">
                                    <input type="text" id="searchInputGuide0" placeholder="Search..." onclick="filterFunctionProductclick(0)" onkeyup="filterFunctionGuide(0);">
                                    <input name="vendorCommission" type="hidden" id="guidId0" value="0">
                                    <div id="dropdownListGuide0" class="dropdown-list form-control ">

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
                                                    $mersuer = $rowm['mesuer'];
                                                }

                                        ?>
                                                <div onclick="selectOptionGuide('<?php echo $rowin['iteamname']; ?>',0,'<?php echo $mersuer; ?>','<?php echo $rowin['id']; ?>','<?php echo $rowin['qty']; ?>');" value="<?php echo $rowin['id']; ?>"> <?php echo $rowin['iteamname']; ?></div>

                                        <?php
                                            }
                                        } else {
                                            echo "Error: " . $link->error;
                                        }

                                        ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><input type="text" name="mesuer" class="form-control text-end" id="exchangeRate0" disabled placeholder="KG"></td>
                        <td><input name="qty" width="70px" type="number" min="1" max="10" class="form-control text-end" id="amount0" required placeholder="00.00"></td>
                        <td><i class="fa fa-trash-o fs-5 text-danger" onclick="deleteRow(this)"></i></td>
                    </tbody>
                </table>
                <br>
                <div class="col-11">
                    <div class="row">
                        <div class="col-12 text-end">
                            <button onclick="addRow();" class="btn btn-primary">Add Row</button>
                        </div>
                        <!-- <div class="col-6 text-end">
                            <button onclick="saveTableDatacurrency();" class="btn btn-success">Save <i class="fa fa-save"></i></button>
                        </div> -->
                    </div>
                </div>
                <br>

            </div>
        </div>
    </div>
</div>

<script>
    function saveIteamData() {

        var item_id = document.getElementById('item_id').value;
        var item_name = document.getElementById('item_name').value;
        var item_category = document.getElementById('item_category').value;
        var item_type = document.getElementById('item_type').value;
        var item_price = document.getElementById('item_price').value;
        var item_description = document.getElementById('item_description').value;

        var f = new FormData();

        f.append("item_id", item_id);
        f.append("item_name", item_name);
        f.append("item_category", item_category);
        f.append("item_type", item_type);
        f.append("item_price", item_price);
        f.append("item_description", item_description);

        var x = new XMLHttpRequest();

        x.onreadystatechange = function() {
            if (x.readyState === 4 && x.status === 200) {                
                saveData(item_id);
            }
        };

        x.open("POST", "success_create.php", true);
        x.send(f);

    }



    function saveData(id) {
        var id = id;
        const rows = document.querySelectorAll("#currencyTableBody tr");
        let data = [];

        rows.forEach((row) => {
            const iteamId = row.querySelector('input[name="vendorCommission"]').value;
            const mesuer = row.querySelector('input[name="mesuer"]').value;
            const qty = row.querySelector('input[name="qty"]').value;

            if (iteamId && mesuer && id && qty) {
                data.push({
                    iteamId: iteamId,
                    mesuer: mesuer,
                    id: id,
                    qty: qty,
                });
            }
        });

        if (data.length > 0) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "saveproductsdata.php", true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert(xhr.responseText);
                    window.location.reload();
                }
            };
            xhr.send(JSON.stringify(data));
        } else {
            alert("Please fill out all fields before saving.");
        }
    }


    function deleteRow(element) {
        element.closest("tr").remove();
        countRows();
    }


    function filterFunctionProduct(id) {
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
            dropdownList.style.display = "block";
        } else {
            dropdownList.style.display = "block";
        }

    }

    function selectOptionGuide(option, id, mersuer, iteam, qty) {

        document.getElementById("searchInputGuide" + id).value = option;
        document.getElementById("guidId" + id).value = iteam;
        document.getElementById("exchangeRate" + id).value = mersuer;
        document.getElementById("amount" + id).max = qty;
        document.getElementById("dropdownListGuide" + id).style.display = "none";

    }

    let rowCounts = 0;

    function addRow() {
        rowCounts++;
        const newRow = `
    <tr id="row${rowCounts}">
        <td>
                            <div style="display: inline-block;">
                                <div class="dropdown">
                                    <input type="text" id="searchInputGuide${rowCounts}" placeholder="Search..." onclick="filterFunctionProductclick(${rowCounts})" onkeyup="filterFunctionGuide(${rowCounts});">
                                    <input name="vendorCommission" type="hidden" id="guidId${rowCounts}" value="0">
                                    <div id="dropdownListGuide${rowCounts}" class="dropdown-list form-control ">

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
                                                    $mersuer = $rowm['mesuer'];
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
                            </div>
                        </td>
                        <td><input type="text" name="mesuer" class="form-control text-end" id="exchangeRate${rowCounts}" disabled placeholder="KG"></td>
                        <td><input  name="qty" type="number" min="1" max="10" class="form-control text-end" id="amount${rowCounts}" placeholder="00.00"></td>
                        <td><i class="fa fa-trash-o fs-5 text-danger" onclick="deleteRow(this)"></i></td>
                    </tr>
    `;
        document.getElementById("currencyTableBody").insertAdjacentHTML("beforeend", newRow);
    }
</script>