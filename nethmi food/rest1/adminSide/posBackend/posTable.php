<?php
session_start(); // Ensure session is started
require_once 'checkIfLoggedIn.php';

if ($_SESSION['roll'] == 4) {
?>
    <script>
        window.location.href = '../panel/kitchen-panel.php';
    </script>
<?php
}
?>
<?php
include '../inc/dashHeader.php';
require_once '../config.php'; // Include your database configuration
?>

<!DOCTYPE html>
<html>

<head>
    <link href="../css/pos.css" rel="stylesheet" />
</head>

<body>

    <div class="container" style="text-align: center; width:100%; margin-top:3rem; margin-left: 2rem;  ">
        <div id="POS-Content" class="row">
            <div class="row center-middle">


                <div class="col-md-15" style="margin-left: 17rem; margin-top: 0rem;max-height: 700px; overflow-y: auto;">
                    <div class="row justify-content-center">
                        <?php
                        // Fetch all tables from the database
                        $query = "SELECT * FROM restaurant_tables ORDER BY table_id;";
                        $result = mysqli_query($link, $query);
                        $table = array("", "", "");
                        if ($result) {
                            $table_count = 0;
                            // ...
                            while ($row = mysqli_fetch_assoc($result)) {
                                if ($table_count % 5 == 0) {
                                    echo '</div><div class="row justify-content-center">';
                                }
                                $table_id = $row['table_id'];
                                $capacity = $row['capacity'];


                                $sqlBill = "SELECT bill_id FROM bills WHERE table_id = $table_id ORDER BY bill_time DESC LIMIT 1";
                                $result1 = $link->query($sqlBill);
                                $latestBillData = $result1->fetch_assoc();

                                // Check if the table is reserved for the selected time
                                date_default_timezone_set('Asia/Colombo'); // Set the time zone to Singapore

                                $selectedDate = date("Y-m-d"); // Get the current date, you can change this to your selected date
                                $endTime = date("H:i:s"); // Get the current time, you can change this to your selected time

                                // Calculate the end time of the 20-minute range
                                $startTime = date("H:i:s", strtotime($endTime) - (20 * 60));
                                // Check if there's a reservation within the 20-minute range
                                $reservationQuery = "SELECT * FROM reservations WHERE table_id = $table_id AND reservation_date = '$selectedDate' AND reservation_time BETWEEN '$startTime' AND '$endTime'";
                                $reservationResult = mysqli_query($link, $reservationQuery);

                                //Show all reservations

                                //

                                if ($latestBillData) {
                                    $latestBillID = $latestBillData['bill_id'];

                                    $sqlBillItems = "SELECT * FROM bill_items WHERE bill_id = $latestBillID";
                                    $result2 = $link->query($sqlBillItems);
                                    if ($result2 && mysqli_num_rows($result2) > 0) {
                                        $billItemColor = 'rgb(216, 0, 50)'; // Bill has associated bill items (red)
                                    } else {
                                        $billItemColor = 'rgb(23, 89, 74)'; // Bill has no associated bill items (rgb(23, 89, 74))
                                    }

                                    $paymentTimeQuery = "SELECT payment_time FROM bills WHERE bill_id = $latestBillID";
                                    $paymentTimeResult = $link->query($paymentTimeQuery);
                                    $hasPaymentTime = false;

                                    if ($paymentTimeResult && $paymentTimeResult->num_rows > 0) {
                                        $paymentTimeRow = $paymentTimeResult->fetch_assoc();
                                        if (!empty($paymentTimeRow['payment_time'])) {
                                            $hasPaymentTime = true;
                                        }
                                    }

                                    $box_color = $hasPaymentTime ? 'rgb(23, 89, 74)' : $billItemColor;
                                } else {
                                    $latestBillID = null;
                                    $box_color = 'gray'; // No bill for the table (gray)
                                }

                                // Fetch order status for the table from the kitchen table
                                $orderStatusQuery = "
SELECT item_id, quantity, time_submitted, date_confirm, date_processing, time_ended 
FROM kitchen 
WHERE table_id = $table_id 
ORDER BY time_submitted DESC 
LIMIT 1
";

$querys = "
    SELECT COUNT(item_id) AS total_items,
    SUM(CASE WHEN time_ended IS NOT NULL THEN 1 ELSE 0 END) AS done_items
    FROM kitchen 
    WHERE table_id = $table_id
";

$resultst = mysqli_query($link, $querys);
$rowst = mysqli_fetch_assoc($resultst);

$totalItems = $rowst['total_items'] ?? 0; // Total items
$doneItems = $rowst['done_items'] ?? 0; // Done items

                                $orderStatusResult = mysqli_query($link, $orderStatusQuery);
                                $orderStatus = mysqli_fetch_assoc($orderStatusResult);


                                $statusIcon = ''; // Default to no icon

                                if ($orderStatus) {
                                    if ($orderStatus['date_confirm'] && !$orderStatus['date_processing']) {
                                        $statusIcon = '<i class="fas fa-check-circle text-warning status-icon" title="Confirmed"></i>'; // Confirmed
                                    } elseif ($orderStatus['date_processing'] && !$orderStatus['time_ended']) {
                                        $statusIcon = '<i class="fas fa-hourglass-half text-info status-icon" title="Processing"></i>'; // Processing
                                    } elseif ($orderStatus['time_ended']) {
                                        $statusIcon = '<i class="fas fa-check-circle text-success status-icon" title="Done"></i>'; // Done
                                    }
                                }


                                // (Your existing PHP code to set up the $orderStatus and $statusIcon)
                                
                                // Output the HTML for each table button with the status icon and link
                                echo '<div class="col-md-2 mb-3">';
                                if ($reservationResult && mysqli_num_rows($reservationResult) > 0) {
                                    ?>
                                    <div class="text-center">
      hfgr
    </div>
    <a href="orderItem.php?bill_id=<?= $latestBillID; ?>&table_id=<?= $table_id; ?>"
       class="btn btn-primary btn-block btn-lg"
       style="color:black; background-color: rgb(248, 222, 34); 
       justify-content: center; align-items: center; display: flex; 
       width: 9rem; height: 9rem;"
       data-table-id="<?= $table_id; ?>">
        Table: <?= $table_id; ?><br>Capacity: <?= $capacity; ?>
    </a>

                                        <?php
                                } else {
                                    ?>
                                <div class="text-center" onclick="openOrderDetails('<?php echo $table_id; ?>');">
    <button type="button" class="btn btn-warning">
        Done <span class="badge badge-black fs-6 text-black bg-white"><?= $doneItems; ?> / <?= $totalItems; ?></span>
    </button>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBodyContent">
                <!-- Order details will be dynamically loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

                                <a href="orderItem.php?bill_id=<?= $latestBillID; ?>&table_id=<?= $table_id; ?>"
                                   class="btn btn-primary btn-block btn-lg"
                                   style="background-color: <?= $box_color; ?>; 
                                   justify-content: center; align-items: center; display: flex; 
                                   width: 9rem; height: 9rem;"
                                   data-table-id="<?= $table_id; ?>">
                                    Table: <?= $table_id; ?><br>Capacity: <?= $capacity; ?>
                                </a>
                                <?php
                                }
                                echo '</div>';
                              
                                
                                


                                $table_count++;

                        ?>
                               
                        <?php

                            }
                            // ...
                        } else {
                            echo "Error fetching tables: " . mysqli_error($link);
                        }
                        ?>
                    </div>

                    <div class="row d-flex justify-content-around" style="margin-top: 2rem;">
                        <div class="col-md-3">
                            <div class="alert alert-success" role="alert" style="color:white;background-color: rgb(23, 89, 74);" data-toggle="tooltip" data-placement="top" title="Tables That are Free">
                                Available
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-danger" role="alert" style="color:white;background-color: rgb(216, 0, 50);" data-toggle="tooltip" data-placement="top" title="Tables That are Used">
                                Occupied
                            </div>
                        </div>
                        <!--
                <div class="col-md-3">
                    <div class="alert alert-dark" role="alert">
                        No Bill Id
                    </div>
                </div>
                -->
                        <div class="col-md-3">
                            <div class="alert alert-warning" style="color:black;background-color: rgb(248, 222, 34);" role="alert" data-toggle="tooltip" data-placement="top" title="Tables That are Reserved">
                                Reserved
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
      function openOrderDetails(tableId) {
    // Use AJAX to load order details for the table
    $.ajax({
        url: 'fetchOrderDetails.php', // Update to your actual script to fetch order details
        method: 'POST',
        data: { table_id: tableId },
        success: function(response) {
            $('#modalBodyContent').html(response);
            $('#orderDetailsModal').modal('show'); // Show the modal with details
        },
        error: function() {
            alert('Failed to load order details.');
        }
    });

    // Add click event for confirm button
    $('#confirmButton').off('click').on('click', function() {
        confirmOrderProcessing(tableId);
    });
}

function confirmOrderProcessing(tableId) {
    // Add functionality for confirming and processing order
    $.ajax({
        url: 'confirmOrder.php', // Update to your actual script to confirm the order
        method: 'POST',
        data: { table_id: tableId },
        success: function(response) {
            alert('Order confirmed and processing started!');
            $('#orderDetailsModal').modal('hide'); // Hide the modal
            // Optionally, refresh the page or update the UI as needed
        },
        error: function() {
            alert('Failed to confirm the order.');
        }
    });
}



    </script>
    <?php include '../inc/dashFooter.php' ?>