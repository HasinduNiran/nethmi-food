<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day End Shift Report Viewer</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="day_end.css">
    <!-- Date picker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
    <style>
        /* Report viewer specific styles */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px 0;
            border-bottom: 2px solid #e2e8f0;
        }

        .date-picker-container {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
        }

        #date-picker {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            min-width: 200px;
        }

        .search-btn {
            background-color: #4a5568;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .search-btn:hover {
            background-color: #2d3748;
        }

        /* Highlight row styling */
        .highlight-row {
            background-color: #ffffe0;
            border: 2px solid #ffd700;
            border-radius: 5px;
            padding: 10px;
            margin: 5px 0;
        }

        .bold-text {
            font-weight: bold;
        }

        #backBtn {
            background-color: rgb(119, 77, 255);
            color: white;
        }

        #printBtn {
            background-color: rgb(13, 92, 171);
            color: white;
        }

        .btn-container {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Section styling */
        .section-title {
            font-weight: bold;
            font-size: 18px;
            margin-top: 15px;
            margin-bottom: 10px;
            color: #1a5f7a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }

        .payment-section {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .no-data-message {
            text-align: center;
            padding: 40px;
            color: #718096;
            font-size: 18px;
            background-color: #f7fafc;
            border-radius: 5px;
            margin: 20px 0;
        }

        /* User info */
        .user-info {
            margin-bottom: 10px;
            font-style: italic;
            color: #4a5568;
        }

        .timestamp {
            font-weight: normal;
            font-size: 14px;
            color: #718096;
        }

        /* Card machines container */
        #card-machines-container .data-row {
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        #card-machines-container .data-row:last-child {
            border-bottom: none;
        }

        /* Added loading indicator */
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        /* Multiple reports styling */
        .report-selector {
            margin-bottom: 20px;
            display: none;
        }

        .report-selector select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            min-width: 250px;
        }
    </style>
</head>

<body>
    <div class="day-end-form">
        <div class="report-header">
            <div class="form-title">Day End Shift Report Viewer</div>
        </div>

        <div class="date-picker-container">
            <label for="date-picker"><i class="fas fa-calendar-alt"></i> Select Date:</label>
            <input type="text" id="date-picker" placeholder="Select a date">
            <button id="search-btn" class="search-btn"><i class="fas fa-search"></i> Search</button>
        </div>

        <div id="report-selector" class="report-selector">
            <label for="report-select"><i class="fas fa-file-alt"></i> Select Report:</label>
            <select id="report-select">
                <option value="">Select a report</option>
            </select>
        </div>

        <div id="loading" class="loading">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p>Loading report data...</p>
        </div>

        <div id="no-data" class="no-data-message">
            <i class="fas fa-info-circle fa-2x"></i>
            <p>Please select a date to view reports</p>
        </div>

        <div id="report-content" style="display: none;">
            <div class="user-info">
                <span id="report-username">-</span>
                <span class="timestamp" id="report-timestamp">-</span>
            </div>

            <div class="data-row"><span><i class="fas fa-wallet"></i> Opening Balance:</span> <span id="opening_balance">-</span></div>
            <div class="data-row"><span><i class="fas fa-money-bill-wave"></i> Total Gross Amount:</span> <span id="total_gross">-</span></div>
            <div class="data-row"><span><i class="fas fa-chart-bar"></i> Total Income:</span> <span id="total_income_new">-</span></div>
            <div class="data-row"><span><i class="fas fa-chart-bar"></i> Total Income From Service Charges:</span> <span id="total_income_service_charge">-</span></div>
            <div class="data-row" style="display: none;"><span><i class="fas fa-chart-bar"></i> Total Dispatched Balance:</span> <span id="total_dispatched_balance">-</span></div>
            <div class="data-row" style="display: none;"><span><i class="fas fa-chart-bar"></i> Total Net Amount:</span> <span id="total_net">-</span></div>
            <div class="data-row"><span><i class="fas fa-gift"></i> Total Discount:</span> <span id="total_discount">-</span></div>
            <div class="data-row"><span><i class="fas fa-file-invoice"></i> Total Bills:</span> <span id="total_bills">-</span></div>

            <!-- Payment Methods Section -->
            <div class="section-title"><i class="fas fa-money-bill-alt"></i> Payment Methods</div>
            <div class="payment-section">
                <div class="data-row"><span><i class="fas fa-cash-register"></i> Total Cash Payments:</span> <span id="total_cash">-</span></div>
                <div class="data-row"><span><i class="fas fa-cash-register"></i> Total Bank Payments:</span> <span id="total_bank">-</span></div>
                <div class="data-row"><span><i class="fas fa-credit-card"></i> Total Credit Card Payments:</span> <span id="total_credit_card">-</span></div>
                <div class="data-row"><span><i class="fas fa-gift"></i> Total Debit Card Payments:</span> <span id="total_debit_card">-</span></div>
                <div class="data-row"><span><i class="fas fa-donate"></i> Total Credit Payments</span> <span id="total_credit">-</span></div>
                <div class="data-row"><span><i class="fas fa-car"></i> Uber Payments:</span> <span id="uber_payment">-</span></div>
                <div class="data-row"><span><i class="fas fa-car"></i> PickMe Payments:</span> <span id="pickme_payment">-</span></div>
                <div class="data-row"><span><i class="fas fa-file-invoice"></i> Total Bill Payments:</span> <span id="bill_payment">-</span></div>
                <div class="data-row"><span><i class="fas fa-cash-register"></i> Cash Drawer Payment:</span> <span id="cash_drawer">-</span></div>
                <div class="data-row"><span><i class="fas fa-ticket-alt"></i> Voucher Payment:</span> <span id="voucher_payment">-</span></div>
                <div class="data-row"><span><i class="fas fa-hand-holding-heart"></i> Free Payment:</span> <span id="free_payment">-</span></div>
            </div>

            <!-- Card Machines Section -->
            <div class="section-title"><i class="fas fa-credit-card"></i> Card Machine Totals</div>
            <div class="payment-section">
                <div class="data-row highlight-row"><span><i class="fas fa-credit-card"></i> Total Card Machine Payments:</span> <span id="card_machine_total">-</span></div>
            </div>

            <!-- Delivery Apps Section -->
            <!-- <div class="section-title"><i class="fas fa-motorcycle"></i> Delivery App Totals</div>
            <div class="payment-section">
                
            </div> -->

            <!-- Totals and Balance Section -->
            <div class="section-title"><i class="fas fa-calculator"></i> Totals & Balance</div>
            <div class="data-row"><span><i class="fas fa-balance-scale"></i> Total Balance:</span> <span id="total_balance">-</span></div>
            <div class="data-row"><span><i class="fas fa-balance-scale"></i> External Cash Incomes:</span> <span id="total_external_income">-</span></div> 
            <div class="data-row"><span><i class="fas fa-money-bill-trend-up"></i> Petty cash(expenses)</span> <span id="petty_cash">-</span></div>
            <div class="data-row highlight-row"><span><i class="fas fa-hand-holding-usd"></i> Total Full Balance:</span> <span id="cash_balance" class="bold-text">-</span></div>
            <div class="data-row"><span><i class="fas fa-balance-scale"></i> Total Non-Cash Transactions:</span> <span id="non_cash_total">-</span></div>
        <div class="data-row"><span><i class="fas fa-balance-scale"></i> Cash In Hand:</span> <span id="cash_in_hand">-</span></div>
            <div class="data-row"><span><i class="fas fa-balance-scale"></i> Day End Hand Balance:</span> <span id="day_end_hand_balance">-</span></div>
            <div class="data-row" style="display: none;"><span><i class="fas fa-exchange-alt"></i> To Day Balance:</span> <span id="today_balance">-</span></div>
            <div class="data-row"><span><i class="fas fa-exchange-alt"></i> Difference</span> <span id="difference_hand">-</span></div>

            <div class="data-row">
                <div class="btn-container">
                    <button class="action-btn" id="backBtn"><i class="fas fa-arrow-left"></i> Back</button>
                    <button class="action-btn" id="printBtn"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include flatpickr for date picking -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date picker
            const datePicker = flatpickr("#date-picker", {
                dateFormat: "Y-m-d",
                maxDate: "today",
                defaultDate: "today"
            });

            // Get DOM elements
            const searchBtn = document.getElementById('search-btn');
            const reportSelector = document.getElementById('report-selector');
            const reportSelect = document.getElementById('report-select');
            const loadingIndicator = document.getElementById('loading');
            const noDataMessage = document.getElementById('no-data');
            const reportContent = document.getElementById('report-content');
            const backBtn = document.getElementById('backBtn');
            const printBtn = document.getElementById('printBtn');

            // Search button event handler
            searchBtn.addEventListener('click', function() {
                const selectedDate = datePicker.input.value;
                if (!selectedDate) {
                    alert('Please select a date first');
                    return;
                }
                
                fetchReportsByDate(selectedDate);
            });

            // Report selector change event
            reportSelect.addEventListener('change', function() {
                const reportId = this.value;
                if (reportId) {
                    fetchReportDetails(reportId);
                }
            });

            // Back button event handler
            backBtn.addEventListener('click', function() {
                window.history.back();
            });

            // Print button event handler
            printBtn.addEventListener('click', function() {
                window.print();
            });

            // Function to fetch reports by date
            function fetchReportsByDate(date) {
                // Show loading, hide content and no data message
                loadingIndicator.style.display = 'block';
                reportContent.style.display = 'none';
                noDataMessage.style.display = 'none';
                reportSelector.style.display = 'none';

                // Fetch reports for the selected date
                fetch('./custom_day_end_fetch.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=getReportsByDate&date=${date}`
                })
                .then(response => response.json())
                .then(data => {
                    loadingIndicator.style.display = 'none';
                    
                    if (data.success) {
                        if (data.reports && data.reports.length > 0) {
                            if (data.reports.length === 1) {
                                // If only one report, display it directly
                                displayReportData(data.reports[0]);
                                reportContent.style.display = 'block';
                            } else {
                                // If multiple reports, show selector
                                populateReportSelector(data.reports);
                                reportSelector.style.display = 'block';
                                noDataMessage.style.display = 'block';
                                noDataMessage.innerHTML = '<p>Multiple reports found. Please select one from the dropdown.</p>';
                            }
                        } else {
                            // No reports found
                            noDataMessage.style.display = 'block';
                            noDataMessage.innerHTML = '<i class="fas fa-exclamation-triangle"></i><p>No reports found for the selected date.</p>';
                        }
                    } else {
                        // Error fetching reports
                        noDataMessage.style.display = 'block';
                        noDataMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i><p>Error: ${data.message || 'Failed to fetch reports'}</p>`;
                    }
                })
                .catch(error => {
                    loadingIndicator.style.display = 'none';
                    noDataMessage.style.display = 'block';
                    noDataMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i><p>Error: ${error.message || 'An unexpected error occurred'}</p>`;
                    console.error('Error:', error);
                });
            }

            // Function to populate report selector dropdown
            function populateReportSelector(reports) {
                // Clear existing options
                reportSelect.innerHTML = '<option value="">Select a report</option>';
                
                // Add options for each report
                reports.forEach(report => {
                    const option = document.createElement('option');
                    option.value = report.id;
                    const timestamp = new Date(report.created_at);
                    option.textContent = `${report.username} - ${formatTime(timestamp)}`;
                    reportSelect.appendChild(option);
                });
            }

            // Function to fetch report details by ID
            function fetchReportDetails(reportId) {
                // Show loading, hide content
                loadingIndicator.style.display = 'block';
                reportContent.style.display = 'none';
                noDataMessage.style.display = 'none';

                // Fetch report details
                fetch('./custom_day_end_fetch.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=getReportById&id=${reportId}`
                })
                .then(response => response.json())
                .then(data => {
                    loadingIndicator.style.display = 'none';
                    
                    if (data.success && data.report) {
                        displayReportData(data.report);
                        reportContent.style.display = 'block';
                    } else {
                        noDataMessage.style.display = 'block';
                        noDataMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i><p>Error: ${data.message || 'Failed to fetch report details'}</p>`;
                    }
                })
                .catch(error => {
                    loadingIndicator.style.display = 'none';
                    noDataMessage.style.display = 'block';
                    noDataMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i><p>Error: ${error.message || 'An unexpected error occurred'}</p>`;
                    console.error('Error:', error);
                });
            }

            // Function to display report data in the form
            function displayReportData(report) {
                // Format timestamp
                const timestamp = new Date(report.created_at);
                
                // Set user info
                document.getElementById('report-username').textContent = `Report by: ${report.username}`;
                document.getElementById('report-timestamp').textContent = `Created on: ${formatDate(timestamp)} at ${formatTime(timestamp)}`;
                
                // Set values in the form
                setElementValue('opening_balance', report.opening_balance);
                setElementValue('total_gross', report.total_gross);
                setElementValue('total_net', report.total_net);
                setElementValue('total_dispatched_balance', report.total_dispatched_balance);
                setElementValue('total_income_new', report.total_net);
                setElementValue('total_discount', report.total_discount);
                setElementValue('total_bills', report.total_bills);
                setElementValue('total_cash', report.total_cash);
                setElementValue('total_bank', report.total_bank);
                setElementValue('total_credit_card', report.total_credit_card);
                setElementValue('total_debit_card', report.total_debit_card);
                setElementValue('total_credit', report.total_credit);
                setElementValue('bill_payment', report.bill_payment);
                setElementValue('cash_drawer', report.cash_drawer);
                setElementValue('voucher_payment', report.voucher_payment);
                setElementValue('free_payment', report.free_payment);
                setElementValue('total_balance', report.total_balance);
                setElementValue('petty_cash', report.petty_cash);
                setElementValue('day_end_hand_balance', report.day_end_hand_balance);
                setElementValue('cash_balance', report.cash_balance);
                setElementValue('today_balance', report.today_balance);
                setElementValue('difference_hand', report.difference_hand);
                setElementValue('card_machine_total', report.card_machine_total);
                setElementValue('uber_payment', report.uber_payment);
                setElementValue('pickme_payment', report.pickme_payment);
                setElementValue('total_income_service_charge', report.service_charge_income);
                setElementValue('total_external_income', report.total_external_income);
                setElementValue('non_cash_total', report.non_cash_total);
                setElementValue('cash_in_hand', report.cash_in_hand);
            }

            // Helper function to set element value
            function setElementValue(id, value) {
                const element = document.getElementById(id);
                if (element) {
                    if (id === 'total_bills') {
                        element.textContent = value || '0';
                    } else {
                        const numValue = parseFloat(value) || 0;
                        element.textContent = numValue.toFixed(2);
                    }
                }
            }

            // Helper function to format date
            function formatDate(date) {
                return date.toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            }

            // Helper function to format time
            function formatTime(date) {
                return date.toLocaleTimeString('en-US', { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit',
                    hour12: true 
                });
            }

            // Add keyboard shortcut
            document.addEventListener("keydown", function(event) {
                if (event.code === "Home") {
                    window.location.href = "../dashboard/index.php";
                }
            });
        });
    </script>
</body>
</html>