<?php
require_once "config.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data if set, otherwise set to empty string or zero
    $name = isset($_POST['name']) ? $link->real_escape_string($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? $link->real_escape_string($_POST['phone']) : '';
    $num_persons = isset($_POST['num_persons']) ? (int)$_POST['num_persons'] : 0;
    $reservation_date = isset($_POST['reservation_date']) ? $link->real_escape_string($_POST['reservation_date']) : '';
    $reservation_time = isset($_POST['reservation_time']) ? $link->real_escape_string($_POST['reservation_time']) : '';
    $message = isset($_POST['message']) ? $link->real_escape_string($_POST['message']) : '';

    // Validate the required fields
    if (!empty($name) && !empty($phone) && $num_persons > 0 && !empty($reservation_date) && !empty($reservation_time)) {
        // Prepare an SQL statement to insert the data
        $sql = "INSERT INTO reservations_tb (name, phone, num_persons, reservation_date, reservation_time, message)
                VALUES ('$name', '$phone', $num_persons, '$reservation_date', '$reservation_time', '$message')";

        // Execute the query
        if ($link->query($sql) === TRUE) {
            echo "Reservation successfully created!";
        } else {
            echo "Error: " . $sql . "<br>" . $link->error;
        }
    } else {
        echo "Please fill in all required fields.";
    }
}

// Close the database connection
$link->close();
?>
