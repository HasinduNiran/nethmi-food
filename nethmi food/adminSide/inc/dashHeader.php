<?php

function hasPermission($key) {
    global $conn;
    if ($_SESSION['roll'] == 1) return true; // Admin sees everything
    $user_id = $_SESSION['logged_account_id'];
    $res = $conn->query("SELECT is_allowed FROM user_permissions WHERE user_id = $user_id AND menu_key = '$key' LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        return $row['is_allowed'] == 1;
    }
    return false;
}



        ?> 

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard - SB Admin</title>
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="sb-nav-fixed">
    <!-- Top Navigation -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <a class="navbar-brand ps-3" href="../panel/pos-panel.php">Food Yard By Nethmi</a>
    </nav>

    <!-- Layout Wrapper -->
    <div id="layoutSidenav">
        <!-- Side Navigation (Hidden by Default) -->
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <?php
                        if ($_SESSION['roll'] == 3 || $_SESSION['roll'] == 5) {
                        ?>
                            <div class="sb-sidenav-menu-heading">Main</div>
                            <a class="nav-link" href="../panel/pos-panel.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-cash-register"></i></div>
                                Dashboard
                            </a>

                        <?php
                        }
                        if ($_SESSION['roll'] == 3 || $_SESSION['roll'] == 5) {
                        ?>
                            <!-- <a class="nav-link" href="../panel/pos.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                                POS
                            </a> -->

                        <?php
                        }
                        if ($_SESSION['roll'] == 3 || $_SESSION['roll'] == 5) {
                        ?>
                            <a class="nav-link" href="../newPOS/openning_balance.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-cash-register"></i></div>
                                POS
                            </a>
                            



                        <?php
                        }
                        if ($_SESSION['roll'] == 3) {
                        ?>
                            <a class="nav-link" href="../panel/bill-panel.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                                Bills
                            </a>

                        <?php
                        }
                        if (in_array($_SESSION['roll'], [1,3])) { // 2 = Admin, 3 = Manager, 5 = Cashier
                        ?>
                        <a class="nav-link" href="../newPOS/openning_balance_android.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-cash-register"></i></div>
                                POS Android
                            </a>
                        <?php
                        }
                        
                        if ($_SESSION['roll'] == 3 || $_SESSION['roll'] == 5) {

                            ?>
                                <!-- <a class="nav-link" href="../user/user_permissions.php">-->
                                <!--    <div class="sb-nav-link-icon"><i class="fas fa-table-cells"></i></div>-->
                                <!--    User Permission-->
                                <!--</a>-->
                            <?php
                        }
                        if ($_SESSION['roll'] == 3) {

                            ?>
                                <a class="nav-link" href="../panel/table-panel.php">
                                    <div class="sb-nav-link-icon"><i class="fas fa-table-cells"></i></div>
                                    Table
                                </a>
                            <?php
                        }
                        if ($_SESSION['roll'] == 3) {

                        ?>
                            <a class="nav-link" href="../menuCrud/bakery_item.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-bread-slice"></i></div>
                                GRN
                            </a>
                        <?php
                        }
                        if ($_SESSION['roll'] == 3) {

                        ?>
                            <a class="nav-link" href="../panel/menu-panel.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-utensils"></i></div>
                                Menu
                            </a>
                        <?php
                        }
                        if ($_SESSION['roll'] == 3) {

                        ?>
                            <a class="nav-link" href="../panel/customer-panel.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-person-shelter"></i></div>
                                Members
                            </a>
                        <?php
                        }
                        if ($_SESSION['roll'] == 3) {

                        ?>
                            <a class="nav-link" href="../panel/staff-panel.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-people-group"></i></div>
                                Staff
                            </a>
                        <?php
                        }
                        if ($_SESSION['roll'] == 3) {

                        ?>

                            <a class="nav-link" href="../panel/suppliers.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-fire"></i></div>
                                Suppliers
                            </a>
                        <?php
                        }

                        if ($_SESSION['roll'] == 3) {

                        ?>
                            <a class="nav-link" href="../panel/account-panel.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-eye"></i></div>
                                View All Accounts
                            </a>
                        <?php
                        }
                        // if ($_SESSION['roll'] != 1) {

                        // ?>
                             <!-- <a class="nav-link" href="../panel/kitchen-panel.php">
                        //         <div class="sb-nav-link-icon"><i class="fas fa-kitchen-set"></i></div>
                        //         Kitchen
                        //     </a> -->
                         <?php
                        // }
                        if ($_SESSION['roll'] == 3) {

                        ?>
                            <a class="nav-link" href="../panel/inventory-table.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-eye"></i></div>
                                Inventory
                            </a>
                        <?php
                        }
                        if ($_SESSION['roll'] == 3) {

                        ?>
                            <a class="nav-link" href="../panel/Asset-table.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                                Assets
                            </a>
                        <?php
                        }
                        if ($_SESSION['roll'] == 3) {

                        ?>
                            <div class="sb-sidenav-menu-heading">Report & Analytics</div>
                            <!-- <a class="nav-link" href="../panel/sales-panel.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-fire"></i></div>
                                Items Sales
                            </a> -->
                        <?php
                        }

                        if ($_SESSION['roll'] == 3) {

                        ?>
                            <a class="nav-link" href="../panel/statistics-panel.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Revenue Statistics
                            </a>

                        <?php
                        }
                        if ($_SESSION['roll'] == 3) {

                        ?>
                            <a class="nav-link" href="../panel/Report_section.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                               Reports
                            </a>

                        <?php
                        }

                        if ($_SESSION['roll'] == 3) {

                        ?>
                            <a class="nav-link" href="../panel/profiles-panel.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Cutomer Profiles
                            </a>
                        <?php
                        }
                        if ($_SESSION['roll'] == 3) {
                        ?>
                            <a class="nav-link" href="../panel/voidorder.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Void Orders
                            </a>


                        <?php
                        }
                    if (in_array($_SESSION['roll'], [3,5])) { // 2 = Admin, 3 = Manager, 5 = Cashier {
                        ?>
                            <a class="nav-link" href="../newPOS/cash_disbursments.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Cash Disbursments
                            </a>
                        <?php
                        }
                        ?>

                        <?php
                        if (in_array($_SESSION['roll'], [3,5])) { // 2 = Admin, 3 = Manager, 5 = Cashier {
                        ?>
                            <a class="nav-link" href="../newPOS/cash_receipts_external.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Cash Receipts
                            </a>
                        <?php
                        }
                        ?>


                        <a class="nav-link" href="../StaffLogin/logout.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-key"></i></div>
                            Log out
                        </a>



                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php
                    // Check if the session variables are set
                    if (isset($_SESSION['logged_account_id']) && isset($_SESSION['logged_staff_name'])) {
                        // Display the logged-in staff ID and name
                        echo "Staff ID: " . $_SESSION['logged_account_id'] . "<br>";
                        echo "Staff Name: " . $_SESSION['logged_staff_name'];
                    } else {
                        // If session variables are not set, display a default message or handle as needed
                        echo "Not logged in";
                    }
                    ?>
                </div>
            </nav>
        </div>
        </<div>
        <div id="content-for-template"></div>
        <script>
            document.getElementById('sidebarToggle').addEventListener('click', function() {
                document.getElementById('layoutSidenav_nav').classList.toggle('active');
            });
        </script>
        <script src="../js/scripts.js" type="text/javascript"></script>