<?php
session_start();
require_once '../config.php';
require_once '../posBackend/checkIfLoggedIn.php';
include '../inc/dashHeader.php';

// Get company and branch name for the title
$company_name = isset($_SESSION['company_name']) ? $_SESSION['company_name'] : "Ministry Of Cakes & Bakes";
$branch_name = isset($_SESSION['branch_name']) ? " - " . $_SESSION['branch_name'] : "";
$title = $company_name . $branch_name;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard - <?php echo htmlspecialchars($title); ?></title>
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
        /* Modern Card Styles */
        .card {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #a8e6cf, #dcedc1);
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #64b5f6, #90caf9);
        }

        .bg-gradient-warning {
            background: linear-gradient(45deg, #ffd54f, #ffb74d);
        }

        .bg-gradient-danger {
            background: linear-gradient(45deg, #ff8a65, #ff7043);
        }

        .stat-icon-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
        }

        /* Add responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }

            .stat-icon-container {
                width: 50px;
                height: 50px;
            }

            .stat-icon-container i {
                font-size: 1.5rem;
            }
        }

        /* Loading spinner */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Period tabs */
        .period-tabs {
            display: flex;
            margin-bottom: 20px;
            gap: 10px;
        }
        
        .period-tab {
            padding: 8px 16px;
            background: #f5f5f5;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .period-tab.active {
            background: #4e73df;
            color: white;
        }
        
        /* Recent transactions table */
        .recent-transactions {
            margin-top: 30px;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .transaction-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .transaction-table th {
            background-color: #f8f9fc;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .transaction-table th, .transaction-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .transaction-table tr:last-child td {
            border-bottom: none;
        }
        
        .transaction-table tbody tr:hover {
            background-color: #f8f9fc;
        }

        /* Service type cards in a row */
        .service-cards-container {
            display: flex;
            overflow-x: auto;
            gap: 15px;
            padding: 10px 0;
            -webkit-overflow-scrolling: touch;
        }
        
        .service-card {
            flex: 0 0 280px;
            max-width: 280px;
        }
        
        /* Hide scrollbar but allow scrolling */
        .service-cards-container::-webkit-scrollbar {
            height: 6px;
        }
        
        .service-cards-container::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }
        
        .service-cards-container::-webkit-scrollbar-track {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Timer styles */
        .refresh-timer {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 14px;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Last update time display */
        .last-update {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 14px;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Digital clock styles */
        .digital-clock-container {
            display: inline-flex;
            align-items: center;
            margin-left: 20px;
        }
        
        .clock-wrapper {
            display: flex;
            flex-direction: column;
            margin-left: 10px;
        }
        
        .digital-date {
            font-size: 0.85rem;
            font-weight: 600;
            color: #4e73df;
            margin-bottom: 4px;
        }
        
        .digital-clock {
            font-size: 1.2rem;
            font-weight: bold;
            background: linear-gradient(45deg, #4e73df, #224abe);
            color: white;
            padding: 8px 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            letter-spacing: 1px;
        }
        
        .clock-label {
            font-size: 0.9rem;
            color: #555;
        }

        .unauthorized_banner{
            width: 100%;
            height: 200px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .unauth_placeholder{
            width: 150px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>
    
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <!-- Refresh timer display -->
                <div class="refresh-timer" id="refresh-timer">
                    Auto refresh in: <span id="countdown">120</span>s
                </div>
                
                <!-- Last update time display -->
                <div class="last-update" id="last-update">
                    Last updated: <span id="update-time">Just now</span>
                </div>
                
                <div class="d-flex align-items-center mb-4">
                    <h1 class="mt-4 mb-0 text-dark fw-bold">Dashboard</h1>
                    <div class="digital-clock-container">
                        <span class="clock-label">Sri Lanka Time:</span>
                        <div class="clock-wrapper">
                            <div class="digital-date" id="sri-lanka-date">Loading date...</div>
                            <div class="digital-clock" id="sri-lanka-clock">00:00:00</div>
                        </div>
                    </div>
                </div>
                
                <!-- Date Filter and Time Period Tabs -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="period-tabs">
                            <div class="period-tab active" data-period="today">Today</div>
                            <div class="period-tab" data-period="week">This Week</div>
                            <div class="period-tab" data-period="month">This Month</div>
                            <div class="period-tab" data-period="year">This Year</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="dateFilter" class="form-label">Custom Date:</label>
                        <input type="date" class="form-control" id="dateFilter">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button id="refreshData" class="btn btn-outline-primary w-100">
                            <i class="fas fa-sync-alt me-2"></i> Refresh
                        </button>
                    </div>
                </div>
                
                <div class="row g-4">
                    <!-- Total Bills Card -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 bg-gradient-success h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="text-dark mb-1 fw-bold" id="total-bills">0</h3>
                                        <p class="text-dark-50 mb-0">Total Bills</p>
                                    </div>
                                    <div class="stat-icon-container rounded-circle bg-white bg-opacity-25 p-3">
                                        <i class="fas fa-receipt fa-2x text-dark"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-0 bg-transparent py-3">
                                <div class="d-flex align-items-center text-dark-50">
                                    <i id="bills-icon" class="fas fa-arrow-up me-2 small"></i>
                                    <span class="small" id="bills-percentage">0% from last month</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Customers Card -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 bg-gradient-primary h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="text-dark mb-1 fw-bold" id="total-customers">0</h3>
                                        <p class="text-dark-50 mb-0">Total Customers</p>
                                    </div>
                                    <div class="stat-icon-container rounded-circle bg-white bg-opacity-25 p-3">
                                        <i class="fas fa-users fa-2x text-dark"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-0 bg-transparent py-3">
                                <div class="d-flex align-items-center text-dark-50">
                                    <i id="customers-icon" class="fas fa-arrow-up me-2 small"></i>
                                    <span class="small" id="customers-percentage">0% from last month</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Balance Card -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 bg-gradient-warning h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="text-dark mb-1 fw-bold" id="cash-balance">0</h3>
                                        <p class="text-dark-50 mb-0">Cash Balance</p>
                                    </div>
                                    <div class="stat-icon-container rounded-circle bg-white bg-opacity-25 p-3">
                                        <i class="fas fa-money-bill fa-2x text-dark"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-0 bg-transparent py-3">
                                <div class="d-flex align-items-center text-dark-50">
                                    <i id="cash-icon" class="fas fa-arrow-up me-2 small"></i>
                                    <span class="small" id="cash-percentage">0% from last month</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Sold Card -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 bg-gradient-danger h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="text-dark mb-1 fw-bold" id="items-sold">0</h3>
                                        <p class="text-dark-50 mb-0">Items Sold</p>
                                    </div>
                                    <div class="stat-icon-container rounded-circle bg-white bg-opacity-25 p-3">
                                        <i class="fas fa-utensils fa-2x text-dark"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-0 bg-transparent py-3">
                                <div class="d-flex align-items-center text-dark-50">
                                    <i id="items-icon" class="fas fa-arrow-up me-2 small"></i>
                                    <span class="small" id="items-percentage">0% from last month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Type Sales Cards -->
                <div class="mt-5">
                    <h3>Sales by Service Type</h3>
                    <div class="service-cards-container mt-2" id="serviceTypeSales">
                        <!-- Service type cards will be loaded here -->
                    </div>
                </div>

                <!-- Sales Overview Section -->
                <div class="mt-5">
                    <h3>Sales Overview</h3>
                    <div id="barChart" style="height: 400px;"></div>
                </div>

                <!-- Sales Distribution Section -->
                <div class="mt-5">
                    <h3>Sales Distribution</h3>
                    <div id="pieChart" style="height: 400px;"></div>
                </div>

                <!-- Recent Transactions Section -->
                <div class="recent-transactions mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3>Recent Transactions</h3>
                        <a href="../panel/transactions.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="transaction-table">
                            <thead>
                                <tr>
                                    <th>Bill #</th>
                                    <th>Date & Time</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentTransactions">
                                <!-- Transactions will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Load Google Charts properly
        google.charts.load('current', {
            packages: ['corechart']
        });
        
        // Set a callback for when Google Charts is loaded
        google.charts.setOnLoadCallback(initDashboard);
        
        // Variable to store auto-refresh timer
        let refreshTimer;
        let countdownInterval;
        let countdown = 120; // 2 minutes in seconds
        let clockInterval; // Sri Lanka clock timer
        
        // Function to update Sri Lanka clock and date
        function updateSriLankaClock() {
            const now = new Date();
            
            // Time options
            const timeOptions = { 
                timeZone: 'Asia/Colombo',
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            
            // Date options with day of week
            const dateOptions = {
                timeZone: 'Asia/Colombo',
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            
            const sriLankaTime = now.toLocaleTimeString('en-US', timeOptions);
            const sriLankaDate = now.toLocaleDateString('en-US', dateOptions);
            
            document.getElementById('sri-lanka-clock').textContent = sriLankaTime;
            document.getElementById('sri-lanka-date').textContent = sriLankaDate;
        }
        
        // Function to start Sri Lanka clock
        function startClock() {
            // Clear any existing clock interval
            if (clockInterval) {
                clearInterval(clockInterval);
            }
            
            // Update immediately
            updateSriLankaClock();
            
            // Update every second
            clockInterval = setInterval(updateSriLankaClock, 1000);
        }
        
        // Function to update the last update time
        function updateLastUpdateTime() {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds}`;
            document.getElementById('update-time').textContent = timeString;
        }
        
        // Function to start auto-refresh timer
        function startAutoRefreshTimer() {
            // Clear any existing timers
            clearTimeout(refreshTimer);
            clearInterval(countdownInterval);
            
            // Reset countdown
            countdown = 120;
            updateCountdownDisplay();
            
            // Start countdown display
            countdownInterval = setInterval(function() {
                countdown--;
                updateCountdownDisplay();
                
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                }
            }, 1000);
            
            // Set timer for auto-refresh
            refreshTimer = setTimeout(function() {
                const date = document.getElementById('dateFilter').value;
                const period = $('.period-tab.active').data('period') || 'custom';
                fetchDashboardData(date, period);
                
                // Restart timer after refresh
                startAutoRefreshTimer();
            }, 120000); // 2 minutes
        }
        
        // Function to update countdown display
        function updateCountdownDisplay() {
            document.getElementById('countdown').textContent = countdown;
        }
        
        function initDashboard() {
            // Fetch data for the dashboard with today's date by default
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('dateFilter').value = today;
            fetchDashboardData(today);
            
            // Start auto-refresh timer
            startAutoRefreshTimer();
            
            // Start the Sri Lanka clock
            startClock();
        }

        // Declare global functions for charts to avoid scope issues
        function drawBarChart(salesData) {
            const data = new google.visualization.DataTable();
            data.addColumn('string', 'Date');
            data.addColumn('number', 'Sales');
            data.addColumn({type: 'string', role: 'tooltip'});

            if (salesData.labels && salesData.values) {
                salesData.labels.forEach((label, index) => {
                    const value = salesData.values[index];
                    const tooltip = `Date: ${label}\nSales: Rs. ${value.toFixed(2)}`;
                    data.addRow([label, value, tooltip]);
                });
            } else {
                // Add a placeholder row if no data
                data.addRow(['No Data', 0, 'No sales data available']);
            }

            const options = {
                title: 'Sales Overview',
                titleTextStyle: {
                    fontSize: 18,
                    bold: true
                },
                legend: {
                    position: 'none'
                },
                height: 400,
                bars: 'vertical',
                colors: ['#4e73df'],
                hAxis: {
                    title: 'Date',
                    slantedText: true,
                    slantedTextAngle: 45
                },
                vAxis: {
                    title: 'Sales (Rs.)'
                },
                chartArea: {
                    width: '80%',
                    height: '70%'
                },
                animation: {
                    startup: true,
                    duration: 1000,
                    easing: 'out'
                },
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        color: '#000',
                        auraColor: 'none'
                    }
                }
            };

            const chart = new google.visualization.BarChart(document.getElementById('barChart'));
            chart.draw(data, options);
        }

        function drawPieChart(salesDistribution) {
            const data = new google.visualization.DataTable();
            data.addColumn('string', 'Category');
            data.addColumn('number', 'Sales');

            if (salesDistribution && salesDistribution.length > 0) {
                salesDistribution.forEach((item) => {
                    data.addRow([item.categoryName || item.category, item.sales]);
                });
            } else {
                // Add a placeholder row if no data
                data.addRow(['No Data', 1]);
            }

            const options = {
                title: 'Sales Distribution by Category',
                titleTextStyle: {
                    fontSize: 18,
                    bold: true
                },
                pieHole: 0.4,
                height: 400,
                is3D: false,
                colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                chartArea: {
                    width: '80%',
                    height: '80%'
                },
                legend: {
                    position: 'right',
                    alignment: 'center',
                    textStyle: {
                        fontSize: 12
                    }
                },
                tooltip: { 
                    showColorCode: true,
                    textStyle: {
                        fontSize: 13
                    }
                },
                animation: {
                    startup: true,
                    duration: 1000,
                    easing: 'out'
                }
            };

            const chart = new google.visualization.PieChart(document.getElementById('pieChart'));
            chart.draw(data, options);
        }

        // Function to update percentage displays with appropriate styling
        function updatePercentageDisplay(elementPrefix, percentChange) {
            // Default to 0 if the value is undefined
            percentChange = percentChange || 0;
            
            // Get the icon and percentage elements
            const iconElement = $(`#${elementPrefix}-icon`);
            const percentElement = $(`#${elementPrefix}-percentage`);
            
            // Update the icon and class based on positive/negative change
            if (percentChange >= 0) {
                iconElement.removeClass('fa-arrow-down text-danger').addClass('fa-arrow-up text-success');
                percentElement.removeClass('text-danger').addClass('text-success');
                percentElement.text(`+${percentChange}% from last month`);
            } else {
                iconElement.removeClass('fa-arrow-up text-success').addClass('fa-arrow-down text-danger');
                percentElement.removeClass('text-danger').addClass('text-danger');
                percentElement.text(`${percentChange}% from last month`);
            }
        }
        
        // Function to update recent transactions table
        function updateRecentTransactions(transactions) {
            const tbody = $('#recentTransactions');
            tbody.empty();
            
            if (transactions.length === 0) {
                tbody.html('<tr><td colspan="6" class="text-center">No recent transactions found</td></tr>');
                return;
            }
            
            transactions.forEach(transaction => {
                const statusClass = transaction.status === 'Completed' ? 'text-success' : 
                                   (transaction.status === 'Pending' ? 'text-warning' : 'text-danger');
                
                tbody.append(`
                    <tr>
                        <td>${transaction.billId}</td>
                        <td>${transaction.dateTime}</td>
                        <td>${transaction.customer || 'Walk-in'}</td>
                        <td>${transaction.itemCount}</td>
                        <td>Rs. ${parseFloat(transaction.amount).toFixed(2)}</td>
                        <td><span class="${statusClass}">${transaction.status}</span></td>
                    </tr>
                `);
            });
        }
        
        // Function to update service type sales cards
        function updateServiceTypeSales(hotelTypeSales, hotelTypeNames, hotelTypePercentChanges) {
            const container = $('#serviceTypeSales');
            container.empty();
            
            // Define colors for each service type
            const colors = {
                1: 'bg-gradient-success', // Dine Station
                7: 'bg-gradient-primary', // Takeaway
                11: 'bg-gradient-warning', // Delivery Service
                6: 'bg-gradient-info',    // Pick Me
                4: 'bg-gradient-danger'   // Uber
            };
            
            // Define icons for each service type
            const icons = {
                1: 'fas fa-utensils',      // Dine Station
                7: 'fas fa-shopping-bag',  // Takeaway
                11: 'fas fa-truck',        // Delivery Service
                6: 'fas fa-car',           // Pick Me
                4: 'fab fa-uber'           // Uber
            };
            
            // Loop through hotel types and create cards
            Object.keys(hotelTypeNames).forEach(typeId => {
                const typeName = hotelTypeNames[typeId];
                const sales = parseFloat(hotelTypeSales[typeId] || 0);
                const percentChange = hotelTypePercentChanges[typeId] || 0;
                const color = colors[typeId] || 'bg-gradient-secondary';
                const icon = icons[typeId] || 'fas fa-store';
                
                // Create card HTML - using service-card class instead of col-* classes
                const card = `
                    <div class="service-card">
                        <div class="card border-0 ${color} h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="text-dark mb-1 fw-bold">Rs. ${sales.toFixed(2)}</h3>
                                        <p class="text-dark-50 mb-0">${typeName} Sales</p>
                                    </div>
                                    <div class="stat-icon-container rounded-circle bg-white bg-opacity-25 p-3">
                                        <i class="${icon} fa-2x text-dark"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-0 bg-transparent py-3">
                                <div class="d-flex align-items-center ${percentChange >= 0 ? 'text-success' : 'text-danger'}">
                                    <i class="fas ${percentChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'} me-2 small"></i>
                                    <span class="small">${percentChange >= 0 ? '+' : ''}${percentChange}% from last month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                container.append(card);
            });
        }
        
        // Function to fetch dashboard data - moved outside document.ready to fix reference error
        function fetchDashboardData(date, period = 'custom') {
            // Show loading overlay
            $('#loadingOverlay').css('display', 'flex');
            
            $.ajax({
                url: 'fetchDashboardData.php',
                method: 'GET',
                data: {
                    date: date,
                    period: period
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data); // Log the data to the console

                    // Update card values
                    $('#total-bills').text(data.totalBills);
                    $('#total-customers').text(data.totalCustomers);

                    // Ensure cashBalance is a number
                    const cashBalance = parseFloat(data.cashBalance);
                    $('#cash-balance').text('Rs. ' + (isNaN(cashBalance) ? '0.00' : cashBalance.toFixed(2)));

                    // Ensure itemsSold is a valid value
                    const itemsSold = data.itemsSold || '0';
                    $('#items-sold').text(itemsSold);
                    
                    // Update percentage changes with dynamic values
                    updatePercentageDisplay('bills', data.billsPercentChange);
                    updatePercentageDisplay('customers', data.customersPercentChange);
                    updatePercentageDisplay('cash', data.cashPercentChange);
                    updatePercentageDisplay('items', data.itemsPercentChange);

                    // Update service type sales
                    if (data.hotelTypeSales && data.hotelTypeNames && window.currentUserRole !== 5) {
                        updateServiceTypeSales(data.hotelTypeSales, data.hotelTypeNames, data.hotelTypePercentChanges);
                    }

                    // Render Charts - make sure Google Charts is loaded
                    if (google.visualization && google.visualization.BarChart) {
                        drawBarChart(data.salesData || {labels: [], values: []});
                        drawPieChart(data.salesDistribution || []);
                    } else {
                        console.error('Google Charts not fully loaded yet');
                        google.charts.setOnLoadCallback(function() {
                            drawBarChart(data.salesData || {labels: [], values: []});
                            drawPieChart(data.salesDistribution || []);
                        });
                    }
                    
                    // Update recent transactions
                    updateRecentTransactions(data.recentTransactions || []);
                    
                    // Update last update time
                    updateLastUpdateTime();
                    
                    // Hide loading overlay
                    $('#loadingOverlay').hide();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching dashboard data:', error);
                    // Hide loading overlay
                    $('#loadingOverlay').hide();
                    
                    // Show error message
                    alert('Failed to load dashboard data. Please try again later.');
                }
            });
        }

        $(document).ready(function() {
            // Initialize last update time on page load
            updateLastUpdateTime();
            
            // Start the Sri Lanka clock
            startClock();
            
            // Date filter event listener
            document.getElementById('dateFilter').addEventListener('change', function() {
                const date = this.value;
                fetchDashboardData(date);
                // Clear active state from period tabs
                $('.period-tab').removeClass('active');
                // Restart auto-refresh timer after manual refresh
                startAutoRefreshTimer();
            });
            
            // Period tabs click handler
            $('.period-tab').on('click', function() {
                $('.period-tab').removeClass('active');
                $(this).addClass('active');
                
                const period = $(this).data('period');
                let date = new Date();
                
                switch(period) {
                    case 'today':
                        date = new Date();
                        break;
                    case 'week':
                        // Set to beginning of current week
                        date.setDate(date.getDate() - date.getDay());
                        break;
                    case 'month':
                        // Set to beginning of current month
                        date.setDate(1);
                        break;
                    case 'year':
                        // Set to beginning of current year
                        date.setMonth(0);
                        date.setDate(1);
                        break;
                }
                
                const formattedDate = date.toISOString().split('T')[0];
                document.getElementById('dateFilter').value = formattedDate;
                fetchDashboardData(formattedDate, period);
                // Restart auto-refresh timer after changing period
                startAutoRefreshTimer();
            });
            
            // Refresh button click handler
            $('#refreshData').on('click', function() {
                const date = document.getElementById('dateFilter').value;
                const period = $('.period-tab.active').data('period') || 'custom';
                fetchDashboardData(date, period);
                // Restart auto-refresh timer after manual refresh
                startAutoRefreshTimer();
            });
        });

function fetchUserRole() {
    return fetch('./fetch_user_permission_level.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error('Error fetching role:', data.error);
                return null;
            }
            console.log('User role:', data.role);
            return data.role;
        })
        .catch(error => {
            console.error('Error:', error);
            return null;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    fetchUserRole()
        .then(userRole => {
            if (userRole) {
                window.currentUserRole = userRole;

                if (parseInt(userRole) === 5 || parseInt(userRole) === 1) {
                    const targetDiv1 = document.querySelector('.row.g-4')
                    targetDiv1.innerHTML = ''

                    const targetDiv2 = document.getElementById('serviceTypeSales')
                    targetDiv2.innerHTML = ''
                    
                    // First banner for targetDiv1
                    const unauthorizedBanner = document.createElement('div')
                    unauthorizedBanner.classList.add('unauthorized_banner')
                    const placeHolderPic = document.createElement('img')
                    placeHolderPic.setAttribute('src', '../../images/unauthorized_placeholder.png')
                    placeHolderPic.setAttribute('class', 'unauth_placeholder')
                    const placeHolderText = document.createElement('span')
                    placeHolderText.innerText = 'Not Authorized To See The Insights'
                    
                    unauthorizedBanner.appendChild(placeHolderPic)
                    unauthorizedBanner.appendChild(placeHolderText)
                    
                    // Second banner for targetDiv2
                    const unauthorizedBanner2 = document.createElement('div')
                    unauthorizedBanner2.classList.add('unauthorized_banner')
                    const placeHolderPic2 = document.createElement('img')
                    placeHolderPic2.setAttribute('src', '../../images/unauthorized_placeholder.png')
                    placeHolderPic2.setAttribute('class', 'unauth_placeholder')
                    const placeHolderText2 = document.createElement('span')
                    placeHolderText2.innerText = 'Not Authorized To See The Insights'

                    unauthorizedBanner2.appendChild(placeHolderPic2)
                    unauthorizedBanner2.appendChild(placeHolderText2)

                    targetDiv1.appendChild(unauthorizedBanner)
                    targetDiv2.appendChild(unauthorizedBanner2)
                }
            }
        });
});
    </script>
</body>

</html>