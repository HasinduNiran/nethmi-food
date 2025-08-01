<?php
session_start(); // Ensure session is started
require_once '../posBackend/checkIfLoggedIn.php';
require_once '../config.php';
include '../inc/dashHeader.php';
// Fetch all records where time_ended is NULL
$query = "SELECT * FROM kitchen WHERE time_ended IS NULL";
$result = mysqli_query($link, $query);

// // Undo functionality: Fetch the last updated record with time_ended
// if (isset($_GET['UndoUnshow']) && $_GET['UndoUnshow'] == 'true') {
//     $undoQuery = "SELECT kitchen_id FROM kitchen WHERE time_ended IS NOT NULL ORDER BY time_ended DESC LIMIT 1";
//     $undoResult = mysqli_query($link, $undoQuery);
//     if ($undoResult && mysqli_num_rows($undoResult) > 0) {
//         $undoRow = mysqli_fetch_assoc($undoResult);
//         $undoKitchenId = $undoRow['kitchen_id'];
//         $undoUpdateQuery = "UPDATE kitchen SET time_ended = NULL WHERE kitchen_id = $undoKitchenId";
//         mysqli_query($link, $undoUpdateQuery);
//         header("Location: kitchen-panel.php");
//         exit;
//     }
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="../css/pos.css" rel="stylesheet" />
    <meta http-equiv="refresh" content="5">
    <style>
        /* Custom Styles */
        .btn-success-slate {
            background-color: #6eff81;
            border-color: #6eff81;
            color: black;
        }

        .btn-warning-slate {
            background-color: #f9ff8f;
            border-color: #f9ff8f;
            color: black;
        }

        .btn-danger-slate {
            background-color: #ff9a8f;
            border-color: #ff9a8f;
            color: white;
        }
    </style>
</head>

<body>
    <div class="wrapper" style="width: 1300px; padding-left: 230px; padding-top: 20px">
        <div class="container-fluid pt-5 pl-600 mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Kitchen Orders</h2>
                 <a href="../posBackend/kitchenBackend/undo.php?UndoUnshow=true" class="btn btn-warning mb-2">Undo</a>
            </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kitchen ID</th>
                        <th>Table ID</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Time Submitted</th>
                        <th>Time Ended</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $kitchen_id = $row['kitchen_id'];
                            $table_id = $row['table_id'];
                            $item_id = $row['item_id'];
                            $quantity = $row['quantity'];
                            $time_submitted = $row['time_submitted'];
                            $time_ended = $row['time_ended'];
                            $date_confirm = $row['date_confirm'];
                            $date_processing = $row['date_processing'];

                            // Get item name from Menu table
                            $itemQuery = "SELECT item_name FROM menu WHERE item_id = '$item_id'";
                            $itemResult = mysqli_query($link, $itemQuery);
                            $itemRow = mysqli_fetch_assoc($itemResult);
                            $item_name = $itemRow['item_name'] ?? "Deleted";

                            echo '<tr>';
                            echo '<td>' . $kitchen_id . '</td>';
                            echo '<td>' . $table_id . '</td>';
                            echo '<td>' . $item_name . '</td>';
                            echo '<td>' . $quantity . '</td>';
                            echo '<td>' . $time_submitted . '</td>';
                            echo '<td>' . ($time_ended ?: 'Not Ended') . '</td>';
                            echo '<td>';

                            if (!$time_ended) {
                                // Check statuses to set button styles
                                $confirmClass = $date_confirm ? 'btn-success' : 'btn-success-slate';
                                $processClass = $date_processing ? 'btn-warning' : 'btn-warning-slate';
                                $doneClass = $time_ended ? 'btn-danger' : 'btn-danger-slate';

                                // Confirm Button (also sets Processing automatically)
                                echo '<a href="../posBackend/kitchenBackend/kitchen-panel-back.php?action=set_time_confimed&kitchen_id=' . $kitchen_id . '" class="btn ' . $confirmClass . ' ms-2">Confirm</a>';
                                // Processing Button
                                echo '<a href="../posBackend/kitchenBackend/kitchen-panel-back.php?action=set_time_processing&kitchen_id=' . $kitchen_id . '" class="btn ' . $processClass . ' ms-2">Processing</a>';
                                // Done Button
                                echo '<a href="../posBackend/kitchenBackend/kitchen-panel-back.php?action=set_time_ended&kitchen_id=' . $kitchen_id . '" class="btn ' . $doneClass . ' ms-2">Done</a>';
                            }

                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7">No records in the Kitchen table.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
