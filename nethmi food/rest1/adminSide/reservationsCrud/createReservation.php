<?php
session_start(); // Ensure session is started
?>
<?php include '../inc/dashHeader.php' ?>

<?php
require_once '../config.php';

// Process reservation status
$reservationStatus = $_GET['reservation'] ?? null;
$message = '';
if ($reservationStatus === 'success') {
    $message = "Reservation successful";
}

// Default values
$head_count = $_GET['head_count'] ?? 1;
$defaultReservationDate = $_GET['reservation_date'] ?? date("Y-m-d");
$defaultReservationTime = $_GET['reservation_time'] ?? "13:00:00";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Reservation</title>    
    <style>
        .wrapper { width: 1300px; padding-left: 200px; padding-top: 80px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h3>Search for Available Time</h3>
        
        <div id="Search Table">
            <form id="reservation-form" method="GET" action="availability.php" class="ht-600 w-50">
                <div class="form-group">
                    <label for="reservation_date">Select Date</label><br>
                    <input class="form-control" type="date" id="reservation_date" name="reservation_date" required><br>
                </div>
                
                <div class="form-group"> 
                    <label for="reservation_time">Available Reservation Times</label>
                    <div id="availability-table">
                        <?php
                        $availableTimes = [];
                        for ($hour = 10; $hour <= 20; $hour++) {
                            $time = sprintf('%02d:00:00', $hour);
                            $availableTimes[] = $time;
                        }
                        echo '<select name="reservation_time" id="reservation_time" class="form-control" required>';
                        echo '<option value="" selected disabled>Select a Time</option>';
                        foreach ($availableTimes as $time) {
                            echo "<option value='$time'>$time</option>";
                        }
                        echo '</select>';
                        
                        if (isset($_GET['message'])) {
                            echo "<p>" . htmlspecialchars($_GET['message']) . "</p>";
                        }
                        ?>
                    </div>
                </div>

                <input type="hidden" id="head_count" name="head_count" value="<?= htmlspecialchars($head_count) ?>" required>

                <div class="form-group mt-2">
                    <input type="submit" name="submit" class="btn btn-dark" value="Search Available">
                </div> 
            </form>
        </div>

        <!-- AFTER SEARCH -->
        <div id="insert-reservation-into-table"><br>
            <h3>Make the Reservation</h3>
            
            <form id="make-reservation-form" method="POST" action="insertReservation.php" class="ht-600 w-50">
                <div class="form-group">
                    <label for="customer_name">Customer Name</label><br>
                    <input type="text" id="customer_name" name="customer_name" class="form-control" required placeholder="Johnny Hatsoff"><br>
                </div>
                
                <div class="form-group">
                    <label for="reservation_date">Reservation Date</label><br>
                    <input type="date" id="reservation_date_view" name="reservation_date" value="<?= htmlspecialchars($defaultReservationDate) ?>" readonly required>
                    <input type="time" id="reservation_time_view" name="reservation_time" value="<?= htmlspecialchars($defaultReservationTime) ?>" readonly required>
                </div><br>
                
                <div class="form-group">
                    <label for="table_id_reserve">Pick a Table</label>
                    <select class="form-control" name="table_id" id="table_id_reserve" required>
                        <option value="" selected disabled>Select a Table</option>
                        <?php
                        // Generate table options based on availability
                        $reserved_table_ids = explode(',', $_GET['reserved_table_id'] ?? '');
                        $reserved_table_ids_string = implode(',', array_map('intval', $reserved_table_ids));
                        
                        $select_query_tables = "SELECT * FROM restaurant_tables WHERE capacity >= ?"
                                             . (!empty($reserved_table_ids) ? " AND table_id NOT IN ($reserved_table_ids_string)" : "");

                        if ($stmt = mysqli_prepare($link, $select_query_tables)) {
                            mysqli_stmt_bind_param($stmt, "i", $head_count);
                            mysqli_stmt_execute($stmt);
                            $result_tables = mysqli_stmt_get_result($stmt);

                            if (mysqli_num_rows($result_tables) > 0) {
                                while ($row = mysqli_fetch_assoc($result_tables)) {
                                    echo '<option value="' . htmlspecialchars($row['table_id']) . '">For ' . htmlspecialchars($row['capacity']) . ' people (Table Id: ' . htmlspecialchars($row['table_id']) . ')</option>';
                                }
                            } else {
                                echo '<option disabled>No tables available, please choose another time.</option>';
                                echo '<script>alert("No reservation tables found for the selected time. Please choose another time.");</script>';
                            }
                        }
                        ?>
                    </select>
                    <input type="hidden" id="head_count" name="head_count" value="<?= htmlspecialchars($head_count) ?>" required>
                </div><br>
                
                <div class="form-group">
                    <label for="special_request">Special request:</label><br>
                    <input type="text" id="special_request" name="special_request" class="ht-600 w-50" placeholder="One baby chair"><br>
                </div>
                
                <div class="form-group mt-2">
                    <input type="submit" name="submit" class="btn btn-dark" value="Make Reservation">
                </div>                        
            </form>
        </div>
    </div>

    <script>
        document.querySelector("#reservation_date").addEventListener("change", function() {
            document.querySelector("#reservation_date_view").value = this.value;
        });
    </script>
</body>
</html>
