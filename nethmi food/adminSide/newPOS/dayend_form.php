<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day End Shift Report</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./create-invoice.styles.css">
    <link rel="stylesheet" href="day_end.css">
</head>
<style>
    /* Highlight row styling */.highlight-row {
            background-color: #ffffe0;
            border: 2px solid #ffd700;
            border-radius: 5px;
            padding: 10px;
            margin: 5px 0;
            width: 720px;
        }
        .bold-text {
            font-weight: bold;
        }
        #backBtn { background-color: rgb(119, 77, 255); color: white; }
        #printBtn { background-color: rgb(13, 92, 171); color: white; }
        #saveBtn { background-color: rgb(18, 139, 58); color: white; }
        .btn-container { display: flex; gap: 10px; }
        .action-btn { padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px; font-size: 16px; }
</style>
<body>
    <div class="day-end-form">
        <div class="form-title">Day End Shift Report</div>
        <div class="data-row"><span><i class="fas fa-user"></i> Username:</span> <span id="username">-</span></div>
        <div class="data-row"><span><i class="fas fa-wallet"></i> Opening Balance:</span> <span id="opening_balance">-</span></div>
        <div class="data-row"><span><i class="fas fa-money-bill-wave"></i> Total Gross Amount:</span> <span id="total_gross">-</span></div>
        <div class="data-row"><span><i class="fas fa-chart-bar"></i> Total Net Amount:</span> <span id="total_net">-</span></div>
        <div class="data-row"><span><i class="fas fa-gift"></i> Total Discount:</span> <span id="total_discount">-</span></div>
        <div class="data-row"><span><i class="fas fa-file-invoice"></i> Total Bills:</span> <span id="total_bills">-</span></div>
        <div class="data-row"><span><i class="fas fa-cash-register"></i> Total Cash Payments:</span> <span id="total_cash">-</span></div>
        <div class="data-row"><span><i class="fas fa-credit-card"></i> Total Credit Payments:</span> <span id="total_credit">-</span></div>
        <div class="data-row"><span><i class="fas fa-file-invoice"></i> Total Bill Payments:</span> <span id="bill_payment">-</span></div>
        <div class="data-row"><span><i class="fas fa-cash-register"></i> Today Cash Drawer Payment:</span> <span id="cash_drawer">-</span></div>
        <div class="data-row"><span><i class="fas fa-gift"></i> Total Voucher Payments:</span> <span id="voucher_payment">-</span></div>
        <div class="data-row"><span><i class="fas fa-donate"></i> Total Free Payments:</span> <span id="free_payment">-</span></div>
        <div class="data-row"><span><i class="fas fa-balance-scale"></i> Total Balance:</span> <span id="total_balance">-</span></div>
        <div class="data-row"><span><i class="fas fa-money-bill-trend-up"></i> Petty cash(expenses)</span> <span id="petty_cash">-</span></div>
        <div class="data-row"><span><i class="fas fa-balance-scale"></i> Day End Hand Balance:</span> <span id="day_end_hand_balance">-</span></div>
        <div class="data-row"><span><i class="fas fa-hand-holding-usd"></i> Total Full Balance:</span> <span id="cash_balance" class="bold-text">-</span></div>
        <div class="data-row"><span><i class="fas fa-exchange-alt"></i> To Day Balance:</span> <span id="difference">-</span></div>
        <div class="data-row"><span><i class="fas fa-exchange-alt"></i> Difference</span> <span id="differencehand">-</span></div>
        <div class="data-row">
            <div class="btn-container">
                <button class="action-btn" id="backBtn"><i class="fas fa-arrow-left"></i> Back</button>
                <button class="action-btn" id="printBtn"><i class="fas fa-print"></i> Print</button>
                <button class="action-btn" id="saveBtn"><i class="fas fa-save"></i> Save Report</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchOpeningBalance();
            fetchUsername();
            fetchDayEndData();
            fetchTodayCashDrawerPayment();
            fetchDayEndHandBalance();
            fetchPettyCash();
        });

        function fetchUsername() {
            fetch('fetch_dayend_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'getUsername=true'
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('username').textContent = data.username || 'N/A';
                window.username = data.username;
            })
            .catch(error => console.error('Error:', error));
        }

        function fetchOpeningBalance() {
            fetch('fetch_dayend_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'getOpeningBalance=true'
            })
            .then(response => response.json())
            .then(data => {
                const openingBalance = parseFloat(data.total_balance || '0.00');
                document.getElementById('opening_balance').textContent = openingBalance.toFixed(2);
                window.openingBalance = openingBalance;
                calculateCashBalanceAndDifference();
            })
            .catch(error => console.error('Error:', error));
        }

        function fetchDayEndData() {
            fetch('fetch_dayend_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'getDayEndData=true'
            })
            .then(response => response.json())
            .then(data => {
                console.log("Fetched Data:", data);
                const totalNet = parseFloat(data.total_net || '0.00');
                document.getElementById('total_net').textContent = totalNet.toFixed(2);
                document.getElementById('total_discount').textContent = data.total_discount || '0.00';
                document.getElementById('total_bills').textContent = data.total_bills || '0';
                document.getElementById('bill_payment').textContent = data.total_bill_payment || '0.00';
                window.totalNet = totalNet;
                calculateCashBalanceAndDifference();
            })
            .catch(error => console.error('Error:', error));
        }

        function fetchDayEndHandBalance() {
            fetch('fetch_dayend_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'getDayEndHandBalance=true'
            })
            .then(response => response.json())
            .then(data => {
                const dayEndHandBalance = parseFloat(data.total_balance || '0.00');
                document.getElementById('day_end_hand_balance').textContent = dayEndHandBalance.toFixed(2);
                window.dayEndHandBalance = dayEndHandBalance;
                calculateCashBalanceAndDifference();
            })
            .catch(error => console.error('Error:', error));
        }

        function fetchTodayCashDrawerPayment() {
            fetch('fetch_dayend_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'getTodayCashDrawerPayment=true'
            })
            .then(response => response.json())
            .then(data => {
                const cashDrawer = parseFloat(data.total_cash_drawer || '0.00');
                document.getElementById('cash_drawer').textContent = cashDrawer.toFixed(2);
                window.cashDrawer = cashDrawer;
                calculateCashBalanceAndDifference();
            })
            .catch(error => console.error('Error:', error));
        }

        function fetchPettyCash() {
            fetch('fetch_dayend_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'getPettyCashExpenses=true'
            })
            .then(response => response.json())
            .then(data => {
                const pettyCash = parseFloat(data.petty_cash || '0.00');
                document.getElementById('petty_cash').textContent = pettyCash.toFixed(2);
                window.pettyCash = pettyCash;
                calculateCashBalanceAndDifference();
            })
            .catch(error => console.error('Error:', error));
        }

        function calculateCashBalanceAndDifference() {
            if (
                window.openingBalance !== undefined &&
                window.totalNet !== undefined &&
                window.cashDrawer !== undefined &&
                window.dayEndHandBalance !== undefined &&
                window.pettyCash !== undefined
            ) {
                // Calculate Total Full Balance (Cash Balance)
                const cashBalance = window.openingBalance + window.totalNet;
                document.getElementById('cash_balance').textContent = cashBalance.toFixed(2);

                // Calculate To Day Balance (Difference: Cash Balance - Cash Drawer - Petty Cash)
                const difference = cashBalance - window.cashDrawer - window.pettyCash;
                document.getElementById('difference').textContent = difference.toFixed(2);

                // Calculate Difference (Day End Hand Balance - Difference)
                const differenceHand = window.dayEndHandBalance - difference;
                document.getElementById('differencehand').textContent = differenceHand.toFixed(2);
            }
        }

        document.getElementById('backBtn').addEventListener('click', function() {
            window.history.back();
        });

        document.getElementById('printBtn').addEventListener('click', function() {
            window.print();
        });
        function resetBillCount() {
            localStorage.setItem("todayBillCount", 0);
        }

        // Save button logic (unchanged)
        document.getElementById('saveBtn').addEventListener('click', function() {
            const reportData = {
                username: document.getElementById('username').textContent.trim(),
                opening_balance: parseFloat(document.getElementById('opening_balance').textContent) || 0.00,
                total_gross: parseFloat(document.getElementById('total_gross').textContent) || 0.00,
                total_net: parseFloat(document.getElementById('total_net').textContent) || 0.00,
                total_discount: parseFloat(document.getElementById('total_discount').textContent) || 0.00,
                total_bills: parseInt(document.getElementById('total_bills').textContent) || 0,
                total_cash: parseFloat(document.getElementById('total_cash').textContent) || 0.00,
                total_credit: parseFloat(document.getElementById('total_credit').textContent) || 0.00,
                bill_payment: parseFloat(document.getElementById('bill_payment').textContent) || 0.00,
                cash_drawer: parseFloat(document.getElementById('cash_drawer').textContent) || 0.00,
                voucher_payment: parseFloat(document.getElementById('voucher_payment').textContent) || 0.00,
                free_payment: parseFloat(document.getElementById('free_payment').textContent) || 0.00,
                total_balance: parseFloat(document.getElementById('total_balance').textContent) || 0.00,
                petty_cash: parseFloat(document.getElementById('petty_cash').textContent) || 0.00,
                day_end_hand_balance: parseFloat(document.getElementById('day_end_hand_balance').textContent) || 0.00,
                cash_balance: parseFloat(document.getElementById('cash_balance').textContent) || 0.00,
                today_balance: parseFloat(document.getElementById('difference').textContent) || 0.00,
                difference_hand: parseFloat(document.getElementById('differencehand').textContent) || 0.00
            };

            fetch('save_dayend_report.php', { // Replace with actual save endpoint
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ saveDayEndReport: true, ...reportData })
            })
            .then(response => response.json())
            .then(data => {
                resetBillCount()
                if (data.success) {
                    alert("Report saved successfully!");
                } else {
                    alert("Error saving report: " + data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        document.addEventListener('DOMContentLoaded', () => {
            updateDateTime();
            setInterval(updateDateTime, 1000);
        });

        function updateDateTime() {
            const now = new Date();
            const timeElement = document.getElementById('current-time') || { textContent: '' };
            timeElement.textContent = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true });
            const dateElement = document.getElementById('current-date') || { textContent: '' };
            dateElement.textContent = now.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        }

        document.addEventListener("keydown", function(event) {
            if (event.code === "Home") {
                window.location.href = "../dashboard/index.php";
            }
        });
    </script>
</body>
</html>