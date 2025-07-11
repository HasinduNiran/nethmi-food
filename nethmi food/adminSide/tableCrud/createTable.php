<?php
session_start(); // Ensure session is started
?>
<?php include '../inc/dashHeader.php'; ?>
<?php
// Include config file
require_once "../config.php";

$conn = $link; // Use the connection from config.php

$next_table_id = $table_id_err = $capacity_err = $hoteltype_err = "";

// Function to get the next available table ID
function getNextAvailableTableID($conn) {
    $sql = "SELECT MAX(table_id) as max_table_id FROM restaurant_tables";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['max_table_id'] + 1;
    } else {
        return 1; // Start with 1 if no tables exist
    }
}

// Get the next available table ID
$next_table_id = getNextAvailableTableID($conn);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Table</title>
    <style>
        .wrapper { width: 70%; padding-left: 280px; padding-top: 80px; margin:0 auto; margin-top:20px;}
    </style>
</head>
<body>
<div class="wrapper">
    <div>
        <h3>Create New Table</h3>
        <p>Please fill in the Table Information.</p>

        <form method="POST" action="succ_create_table.php" class="ht-600 w-50" >
            <div class="form-group">
                <label for="table_id" class="form-label">Table ID:</label>
                <input type="number" name="table_id" class="form-control" id="next_table_id" value="<?php echo $next_table_id; ?>" readonly>
            </div>

            <div class="form-group">
                <label for="capacity">Capacity:</label>
                <input type="number" name="capacity" id="capacity" class="form-control" min="1" placeholder="Enter capacity (e.g., 8)" required>
            </div>

            <div class="form-group" style="display:none; margin-bottom:10px;">
                <label for="hoteltype">Hotel Area:</label>
                <select name="hoteltype" id="hoteltype" class="form-select" >
                    <option value="">Select</option>
                    <?php
                    $queryCategory = "SELECT * FROM holetype";
                    $resultCategory = $conn->query($queryCategory);
                    if ($resultCategory) {
                        while ($row = $resultCategory->fetch_assoc()) {
                            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                        }
                    } else {
                        echo '<option value="">Error loading hotel areas</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group" style="margin-top:15px;">
                <input type="submit" class="btn btn-dark" value="Create Table">
            </div>
        </form>
    </div>
</div>
</body>
</html>
