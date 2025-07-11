<?php
session_start(); // Ensure session is started
require_once '../posBackend/checkIfLoggedIn.php';
ob_start(); // Start output buffering

include '../inc/dashHeader.php'; 
require_once '../config.php';

// Initialize default date and time range to today
$startDateTime = isset($_GET['start_datetime']) ? $_GET['start_datetime'] : date('Y-m-d\TH:i');
$endDateTime = isset($_GET['end_datetime']) ? $_GET['end_datetime'] : date('Y-m-d\TH:i', strtotime('+1 day'));

// Handle Excel export
if (isset($_GET['export_excel'])) {
    // Clear the buffer
    if (ob_get_length()) {
        ob_end_clean();
    }

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=most_purchased_items_{$startDateTime}_to_{$endDateTime}.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    $menuItemSalesQuery = "
        SELECT 
            menu.item_name, 
            menu.item_price AS unit_price, 
            SUM(bill_items.quantity) AS total_quantity, 
            (menu.item_price * SUM(bill_items.quantity)) AS total, 
            DATE_FORMAT(bills.bill_time, '%Y-%m-%d %H:%i:%s') AS purchased_time
        FROM 
            bill_items
        INNER JOIN 
            menu ON bill_items.item_id = menu.item_id
        INNER JOIN 
            bills ON bill_items.bill_id = bills.bill_id
        WHERE 
            bills.bill_time BETWEEN '$startDateTime' AND '$endDateTime'
        GROUP BY 
            menu.item_name, menu.item_price, purchased_time
        ORDER BY 
            purchased_time ASC";

    $menuItemSalesResult = mysqli_query($link, $menuItemSalesQuery);

    if (!$menuItemSalesResult) {
        die("Query failed: " . mysqli_error($link) . " - SQL: " . $menuItemSalesQuery);
    }

    // Create Excel table
    echo "<table border='1'>";
    echo "<tr><th>Item Name</th><th>Unit Price</th><th>Units</th><th>Total</th><th>Purchased Time</th></tr>";
    while ($row = mysqli_fetch_assoc($menuItemSalesResult)) {
        echo "<tr><td>{$row['item_name']}</td><td>{$row['unit_price']}</td><td>{$row['total_quantity']}</td><td>{$row['total']}</td><td>{$row['purchased_time']}</td></tr>";
    }
    echo "</table>";
    exit;
}

?>

<div class="row">
    <div class="col-md-10 order-md-2" style="margin-top: 3rem; margin-left: 14rem;">
        <div class="container-fluid pt-5 row">
            <h3>Most Purchased Items</h3>
            <h3>(<?php echo "$startDateTime to $endDateTime"; ?>)</h3>

            <!-- Date and time range form -->
            <form method="GET" class="row g-3 mb-3">
                <div class="col-auto">
                    <label for="start_datetime" class="form-label">Start Date and Time:</label>
                    <input type="datetime-local" id="start_datetime" name="start_datetime" class="form-control" value="<?php echo $startDateTime; ?>">
                </div>
                <div class="col-auto">
                    <label for="end_datetime" class="form-label">End Date and Time:</label>
                    <input type="datetime-local" id="end_datetime" name="end_datetime" class="form-control" value="<?php echo $endDateTime; ?>">
                </div>
                <div class="col-auto align-self-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
                <div class="col-auto align-self-end">
                    <button type="submit" name="export_excel" class="btn btn-success">Export to Excel</button>
                </div>
            </form>

            <!-- Sorting form and button -->
            <div class="col d-flex justify-content-end"></div>
            <div>
                <?php
                // Get the sorting order from the form or use default (descending)
                $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'desc';

                // SQL query for data between selected date and time
                $menuItemSalesQuery = "
                    SELECT 
                        menu.item_name, 
                        menu.item_price AS unit_price, 
                        SUM(bill_items.quantity) AS total_quantity, 
                        (menu.item_price * SUM(bill_items.quantity)) AS total, 
                        DATE_FORMAT(bills.bill_time, '%Y-%m-%d %H:%i:%s') AS purchased_time
                    FROM 
                        bill_items
                    INNER JOIN 
                        menu ON bill_items.item_id = menu.item_id
                    INNER JOIN 
                        bills ON bill_items.bill_id = bills.bill_id
                    WHERE 
                        bills.bill_time BETWEEN '$startDateTime' AND '$endDateTime'
                    GROUP BY 
                        menu.item_name, menu.item_price, purchased_time
                    ORDER BY 
                        purchased_time ASC";

                $menuItemSalesResult = mysqli_query($link, $menuItemSalesQuery);

                $grandTotal = 0;

                echo '<table class="table">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Item Name</th>';
                echo '<th>Unit Price</th>';
                echo '<th>Units</th>';
                echo '<th>Total</th>';
                echo '<th>Purchased Time</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($row = mysqli_fetch_assoc($menuItemSalesResult)) {
                    $grandTotal += $row['total'];
                    echo '<tr>';
                    echo '<td>' . $row['item_name'] . '</td>';
                    echo '<td>' . $row['unit_price'] . '</td>';
                    echo '<td>' . $row['total_quantity'] . '</td>';
                    echo '<td>' . $row['total'] . '</td>';
                    echo '<td>' . $row['purchased_time'] . '</td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                ?>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 order-md-1 col" style="margin-top: 3rem; margin-left: 5rem;">
            <div class="container pt-3 row">
                <!-- Add a div for Google Charts -->
                <div id="mostPurchased" style="width: 113%; max-width: 1000px; height: 500px;"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 order-md-1 col text-end" style="margin-top: 1rem; margin-left: 5rem;">
            <h4><strong>Grand Total: <?php echo number_format($grandTotal, 2); ?></strong></h4>
        </div>
    </div>
</div>

<!-- Load Google Charts library -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(mostPurchasedChart);

    function mostPurchasedChart() {
        const data = google.visualization.arrayToDataTable([
            ['Item Name', 'Total Quantity'],
            <?php
            $topPurchasedItemsQuery = "
                SELECT 
                    menu.item_name, 
                    SUM(bill_items.quantity) AS total_quantity
                FROM 
                    bill_items
                INNER JOIN 
                    menu ON bill_items.item_id = menu.item_id
                INNER JOIN 
                    bills ON bill_items.bill_id = bills.bill_id
                WHERE 
                    bills.bill_time BETWEEN '$startDateTime' AND '$endDateTime'
                GROUP BY 
                    menu.item_name
                ORDER BY 
                    total_quantity DESC
                LIMIT 10";
            $topPurchasedItemsResult = mysqli_query($link, $topPurchasedItemsQuery);

            while ($row = mysqli_fetch_assoc($topPurchasedItemsResult)) {
                echo "['{$row['item_name']}', {$row['total_quantity']}],";
            }
            ?>
        ]);

        const options = {
            titleTextStyle: {
                fontSize: 20,
                bold: true,
            },
            title: 'Top Purchased Items (<?php echo "$startDateTime to $endDateTime"; ?>)',
            is3D: true
        };

        const chart = new google.visualization.PieChart(document.getElementById('mostPurchased'));
        chart.draw(data, options);
    }
</script>
