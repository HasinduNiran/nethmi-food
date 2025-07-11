<?php
// reservation.php
require_once '../config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST["customer_name"];
    $table_id = intval($_POST["table_id"]);
    $reservation_time = $_POST["reservation_time"];
    $reservation_date = $_POST["reservation_date"];
    $special_request = $_POST["special_request"];

    $select_query_capacity = "SELECT capacity FROM restaurant_tables WHERE table_id='$table_id';";
    $results_capacity = mysqli_query($link, $select_query_capacity);

    if ($results_capacity && $row = mysqli_fetch_assoc($results_capacity)) {
        $head_count = $row['capacity'];
        $reservation_id = intval($reservation_time) . intval($reservation_date) . $table_id;

        $insert_query1 = "INSERT INTO Reservations (reservation_id, customer_name, table_id, reservation_time, reservation_date, head_count, special_request) 
                          VALUES ('$reservation_id', '$customer_name', '$table_id', '$reservation_time', '$reservation_date', '$head_count', '$special_request');";
        $insert_query2 = "INSERT INTO Table_Availability (availability_id, table_id, reservation_date, reservation_time, status) 
                          VALUES ('$reservation_id', '$table_id', '$reservation_date', '$reservation_time', 'no');";

        if (mysqli_query($link, $insert_query1) && mysqli_query($link, $insert_query2)) {
            $_SESSION['customer_name'] = $customer_name;
            $reservationStatus = "success";
        } else {
            $reservationStatus = "error";
        }
    } else {
        $reservationStatus = "error";
    }
}

$iconClass = $reservationStatus === "success" ? "fa-check-circle" : "custom-x";
$cardClass = $reservationStatus === "success" ? "alert-success" : "alert-danger";
$bgColor = $reservationStatus === "success" ? "#D4F4DD" : "#FFA7A7";
$message = $reservationStatus === "success" ? "Reservation Created Successfully!" : "Failed to create reservation.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Status</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
    <style>
        body {
            text-align: center;
            padding: 40px 0;
            background: #EBF0F5;
        }
        h1 {
            font-family: "Nunito Sans", sans-serif;
            font-weight: 900;
            font-size: 40px;
            margin-bottom: 10px;
        }
        p {
            font-family: "Nunito Sans", sans-serif;
            font-size: 20px;
            margin: 0;
        }
        .card {
            background: white;
            padding: 60px;
            border-radius: 4px;
            box-shadow: 0 2px 3px #C8D0D8;
            display: inline-block;
            margin: 0 auto;
        }
        .alert-success {
            background-color: <?php echo $bgColor; ?>;
            color: #5DBE6F;
        }
        .alert-danger {
            background-color: <?php echo $bgColor; ?>;
            color: #F25454;
        }
        .icon {
            font-size: 100px;
            line-height: 200px;
        }
    </style>
</head>
<body>
    <div class="card <?php echo $cardClass; ?>">
        <div style="border-radius: 200px; height: 200px; width: 200px; background: #F8FAF5; margin: 0 auto;">
            <i class="icon"><?php echo $reservationStatus === "success" ? "✓" : "✘"; ?></i>
        </div>
        <h1><?php echo $reservationStatus === "success" ? "Success" : "Error"; ?></h1>
        <p><?php echo $message; ?></p>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        Redirecting back in <span id="countdown">3</span> seconds
    </div>

    <script>
        function startCountdown() {
            let countdown = 3;
            const countdownElement = document.getElementById("countdown");

            const countdownInterval = setInterval(function() {
                countdown--;
                countdownElement.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = "../panel/reservation-panel.php";
                }
            }, 1000);
        }

        window.onload = startCountdown;
    </script>
</body>
</html>
