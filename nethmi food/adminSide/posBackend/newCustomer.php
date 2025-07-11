<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Seating</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5 text-center">
        <?php
        require_once '../config.php';

        if (isset($_GET['new_customer']) && $_GET['new_customer'] === 'true' && isset($_GET['id'])) {
            $table_id = $_GET['table_id'];
            $id = $_GET['id'];

            $bill_time = date('Y-m-d H:i:s');
            $insertQuery = "INSERT INTO bills (table_id, bill_time) VALUES ('$table_id', '$bill_time')";

            if ($link->query($insertQuery) === TRUE) {
                $bill_id = $link->insert_id;

                echo "<h2>Havok Foods </h2>";
                echo "<p>You're now seated at Table ID: $table_id</p>";
                echo "<p>Your bill has been created with bill ID: $bill_id</p>";
                echo '<a href="orderItem.php?bill_id=' . $bill_id . '&table_id=' . $table_id . '&id=' . $id . '" class="btn btn-primary">Back</a>';
            } else {
                echo "<div class='alert alert-danger'>Error inserting data into bills table: " . $link->error . "</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>Invalid request. Please provide all required details.</div>";
        }
        ?>
    </div>

    <!-- Add Bootstrap JS and jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
