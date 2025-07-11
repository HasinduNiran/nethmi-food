<?php
// availability.php
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Sanitize and validate inputs
    $selectedDate = mysqli_real_escape_string($link, $_GET["reservation_date"]);
    $head_count = (int) $_GET["head_count"];
    $selectedTime = date("H:i:s", strtotime($_GET["reservation_time"]));

    // Prepare statement to get all reservations for the selected date and time
    $reservedQuery = "SELECT reservation_id, table_id, reservation_time, reservation_date, head_count FROM reservations WHERE reservation_date = ? AND reservation_time = ?";
    $stmt = mysqli_prepare($link, $reservedQuery);
    mysqli_stmt_bind_param($stmt, "ss", $selectedDate, $selectedTime);
    mysqli_stmt_execute($stmt);
    $reservedResult = mysqli_stmt_get_result($stmt);

    // Initialize an array to store reserved table IDs
    $reservedTableIDs = array();

    // Collect reserved table IDs
    if ($reservedResult) {
        while ($row = mysqli_fetch_assoc($reservedResult)) {
            $reservedTableIDs[] = $row["table_id"];

            // Display reservation details for debugging
            echo "Reservation Time: " . htmlspecialchars($row["reservation_time"]) . "<br>";
            echo "Reservation ID: " . htmlspecialchars($row["reservation_id"]) . "<br>";
            echo "Table ID: " . htmlspecialchars($row["table_id"]) . "<br>";
            echo "Reservation Date: " . htmlspecialchars($row["reservation_date"]) . "<br>";
            echo "Head Count: " . htmlspecialchars($row["head_count"]) . "<br>";
            echo "<br>"; // Add spacing between rows
        }
    } else {
        echo "Query failed: " . mysqli_error($link);
    }

    // Check for available tables
    $reservedTableIDsString = implode(",", array_map('intval', $reservedTableIDs));
    if (!empty($reservedTableIDs)) {
        $availableTablesQuery = "SELECT table_id, capacity FROM restaurant_tables WHERE capacity >= ? AND table_id NOT IN ($reservedTableIDsString)";
    } else {
        $availableTablesQuery = "SELECT table_id, capacity FROM restaurant_tables WHERE capacity >= ?";
    }

    $stmtAvailable = mysqli_prepare($link, $availableTablesQuery);
    mysqli_stmt_bind_param($stmtAvailable, "i", $head_count);
    mysqli_stmt_execute($stmtAvailable);
    $availableResult = mysqli_stmt_get_result($stmtAvailable);

    if ($availableResult) {
        while ($row = mysqli_fetch_assoc($availableResult)) {
            echo "Available Table ID: " . htmlspecialchars($row["table_id"]) . "<br>";
            echo "Capacity: " . htmlspecialchars($row["capacity"]) . "<br>";
        }
    } else {
        echo "Available tables query failed: " . mysqli_error($link);
    }

    // Construct the reservation link with the reserved table IDs
    $reservationLink = "createReservation.php?reservation_date=$selectedDate&head_count=$head_count&reservation_time=$selectedTime&reserved_table_id=" . urlencode($reservedTableIDsString ?: '0');

    // Redirect to the reservation page
    header("Location: $reservationLink");
    exit();
}
?>
