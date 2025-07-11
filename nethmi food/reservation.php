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
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Reservation Successful</title>
                <style>
                    body {
                        margin: 0;
                        overflow: hidden;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        background-color: black;
                    }
                    video {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        z-index: -1;
                    }
                    .message {
                        color: white;
                        font-size: 2em;
                        text-align: center;
                    }
                </style>
                <script>
                    setTimeout(() => {
                        window.location.href = "https://havok.lk.nexarasolutions.site/";
                    }, 10000); // Redirect after 10 seconds
                </script>
            </head>
            <body>
                <video autoplay muted loop>
                    <source src="video/1.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div class="message">
                    Reservation successfully created!<br>
                    Redirecting in 10 seconds...
                </div>
            </body>
            </html>';
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
