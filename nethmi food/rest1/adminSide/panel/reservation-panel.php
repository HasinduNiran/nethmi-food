<?php
session_start();
require_once '../posBackend/checkIfLoggedIn.php';
?>
<?php include '../inc/dashHeader.php'; ?>
    <style>
        .wrapper { width: 1300px; padding-left: 200px; padding-top: 20px; }
    </style>
<div class="wrapper">
    <div class="container-fluid pt-5 pl-600">
        <div class="row">
            <div class="m-50">
                <!-- <div class="mt-5 mb-3">
                    <h2 class="pull-left">Reservation Details</h2>
                    <a href="../reservationsCrud/createReservation.php" class="btn btn-outline-dark"><i class="fa fa-plus"></i> Add Reservation</a>
                </div> -->
                <div class="mb-3">
                    <form method="POST" action="#">
                        <div class="row">
                            <div class="col-md-6">
                                <input required type="text" id="search" name="search" class="form-control" placeholder="Enter Reservation ID, Customer Name, Reservation Date (YYYY-MM-DD)">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-dark">Search</button>
                            </div>
                            <div class="col" style="text-align: right;">
                                <a href="reservation-panel.php" class="btn btn-light">Show All</a>
                            </div>
                        </div>
                    </form>
                </div>

                <?php
                require_once "../config.php";
                $sql = "SELECT * FROM reservations_tb ORDER BY reservation_date DESC, reservation_time DESC;";

                if (isset($_POST['search'])) {
                    if (!empty($_POST['search'])) {
                        $search = $_POST['search'];
                        $sql = "SELECT * FROM reservations_tb WHERE reservation_date LIKE '%$search%' OR id LIKE '%$search%' OR name LIKE '%$search%'";
                    }
                }

                if ($result = mysqli_query($link, $sql)) {
                    if (mysqli_num_rows($result) > 0) {
                        echo '<table class="table table-bordered table-striped">';
                        echo "<thead>";
                        echo "<tr>";
                        echo "<th>Reservation ID</th>";
                        echo "<th>Customer Name</th>";
                        echo "<th>Phone</th>";
                        echo "<th>Number of Persons</th>";
                        echo "<th>Reservation Date</th>";
                        echo "<th>Reservation Time</th>";
                        echo "<th>Message</th>";
                        echo "<th>Delete</th>";
                        echo "<th>Receipt</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['phone'] . "</td>";
                            echo "<td>" . $row['num_persons'] . "</td>";
                            echo "<td>" . $row['reservation_date'] . "</td>";
                            echo "<td>" . $row['reservation_time'] . "</td>";
                            echo "<td>" . $row['message'] . "</td>";
                            echo "<td>";
                            echo '<a href="../reservationsCrud/deleteReservationVerify.php?id='. $row['id'] .'" title="Delete Record" data-toggle="tooltip" onclick="return confirm(\'Admin permission required!\n\nAre you sure you want to delete this reservation?\n\nThis will affect related modules!\')"><span class="fa fa-trash text-black"></span></a>';
                            echo "</td>";
                            echo "<td>";
                            echo '<a href="../reservationsCrud/reservationReceipt.php?reservation_id='. $row['id'] .'" title="Receipt" data-toggle="tooltip"><span class="fa fa-receipt text-black"></span></a>';
                            echo "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";
                        echo "</table>";
                    } else {
                        echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                mysqli_close($link);
                ?>
            </div>
        </div>
    </div>
</div>

<?php include '../inc/dashFooter.php'; ?>
