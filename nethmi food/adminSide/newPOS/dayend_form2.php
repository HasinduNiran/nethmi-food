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
    #backBtn { background-color: rgb(119, 77, 255); color: white; }
    #printBtn { background-color: rgb(13, 92, 171); color: white; }
    #saveBtn { background-color: rgb(18, 139, 58); color: white; }
    .btn-container { display: flex; gap: 10px; }
    .action-btn { padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px; font-size: 16px; }
    
    /* New section styling */
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
    
    #card-machines-container {
        margin-bottom: 10px;
    }
    
    #card-machines-container .data-row {
        padding: 5px 8px;
        border-bottom: 1px solid #e2e8f0;
    }
    
    #card-machines-container .data-row:last-child {
        border-bottom: none;
    }
</style>
<body>
    <div class="day-end-form">
        <div class="form-title">Day End Shift Report</div>
        <div class="data-row"><span><i class="fas fa-user"></i> Username:</span> <span id="username">-</span></div>
        <div class="data-row"><span><i class="fas fa-wallet"></i> Opening Balance:</span> <span id="opening_balance">-</span></div>
        <div class="data-row"><span><i class="fas fa-money-bill-wave"></i> Total Gross Amount:</span> <span id="total_gross">-</span></div>
        <div class="data-row"><span><i class="fas fa-chart-bar"></i> Total Income:</span> <span id="total_income_new">-</span></div>
        <div class="data-row"><span><i class="fas fa-chart-bar"></i> Total Income From Service Charges:</span> <span id="total_income_service_charge">-</span></div>
        <div class="data-row" style="display: none;"><span><i class="fas fa-chart-bar"></i> Total Dispatched Balance:</span> <span id="total_dispatched_balance">-</span></div>
        <!-- <div class="data-row"><span><i class="fas fa-chart-bar"></i> Total Net Amount:</span> <span id="total_net">-</span></div> -->
        <div class="data-row"><span><i class="fas fa-gift"></i> Total Discount:</span> <span id="total_discount">-</span></div>
        <div class="data-row"><span><i class="fas fa-file-invoice"></i> Total Bills:</span> <span id="total_bills">-</span></div>
        
        <!-- Payment Methods Section -->
        <div class="section-title"><i class="fas fa-money-bill-alt"></i> Payment Methods</div>
        <div class="payment-section">
            <div class="data-row"><span><i class="fas fa-cash-register"></i> Total Cash Payments:</span> <span id="total_cash">-</span></div>
            <div class="data-row"><span><i class="fas fa-cash-register"></i> Total Bank Payments:</span> <span id="total_bank">-</span></div>
            <!-- <div class="data-row"><span><i class="fas fa-cash-register"></i> Total Card Payments:</span> <span id="total_card">-</span></div> -->
            <div class="data-row"><span><i class="fas fa-credit-card"></i> Total Credit Card Payments:</span> <span id="total_credit_card">-</span></div>
            <div class="data-row"><span><i class="fas fa-gift"></i> Total Debit Card Payments:</span> <span id="total_debit_card">-</span></div>
            <div class="data-row"><span><i class="fas fa-donate"></i> Total Credit Payments</span> <span id="total_credit">-</span></div>
            <div class="data-row"><span><i class="fas fa-car"></i> Uber Payments:</span> <span id="uber_payment">-</span></div>
            <div class="data-row"><span><i class="fas fa-car"></i> PickMe Payments:</span> <span id="pickme_payment">-</span></div>
            <div class="data-row"><span><i class="fas fa-file-invoice"></i> Total Bill Payments:</span> <span id="bill_payment">-</span></div>
            <div class="data-row"><span><i class="fas fa-cash-register"></i> Today Cash Drawer Payment:</span> <span id="cash_drawer">-</span></div>
        </div>
        
        <!-- Card Machines Section -->
        <div class="section-title"><i class="fas fa-credit-card"></i> Card Machine Totals</div>
        <div class="payment-section">
            <div id="card-machines-container">
                <!-- Individual card machine entries will be inserted here -->
            </div>
            <div class="data-row highlight-row"><span><i class="fas fa-credit-card"></i> Total Card Machine Payments:</span> <span id="total_card_machine">-</span></div>
        </div>
        
        <!-- Delivery Apps Section -->
        <!-- <div class="section-title"><i class="fas fa-motorcycle"></i> Delivery App Totals</div>
        <div class="payment-section">
        </div> -->
        
        <!-- Totals and Balance Section -->
        <div class="section-title"><i class="fas fa-calculator"></i> Totals & Balance</div>
        <div class="data-row"><span><i class="fas fa-balance-scale"></i> Total Balance:</span> <span id="total_balance">-</span></div>
        <div class="data-row"><span><i class="fas fa-balance-scale"></i> External Cash Incomes:</span> <span id="total_external_income">-</span></div>
        <div class="data-row"><span><i class="fas fa-money-bill-trend-up"></i> Petty Cash(Expenses)</span> <span id="petty_cash">-</span></div>
        <div class="data-row highlight-row"><span><i class="fas fa-hand-holding-usd"></i> Total Full Balance:</span> <span id="cash_balance" class="bold-text">-</span></div>
        <div class="data-row"><span><i class="fas fa-balance-scale"></i> Total Non-Cash Transactions:</span> <span id="non_cash_total">-</span></div>
        <div class="data-row"><span><i class="fas fa-balance-scale"></i> Cash In Hand:</span> <span id="cash_in_hand">-</span></div>
        <div class="data-row"><span><i class="fas fa-balance-scale"></i> Day End Hand Balance:</span> <span id="day_end_hand_balance">-</span></div>
        <div class="data-row" style="display: none;"><span><i class="fas fa-exchange-alt"></i> To Day Balance:</span> <span id="difference">-</span></div>
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
            fetchDayEndCashDisbursments();
            fetchTotalExternalCashIncomes();
            fetchUsername();
            fetchDayEndData();
            fetchPaymentMethodTotals(); // New function call
            fetchTodayCashDrawerPayment();
            fetchDayEndHandBalance();
            // fetchPettyCash();
            fetchCardMachineTotal();
            fetchDeliveryAppPayments();
        });

        function fetchUsername() {
            fetch('fetch_dayend_data2.php', {
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

        function fetchDayEndCashDisbursments() {
            fetch('fetch_dayend_data2.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'getTotalCashDisbursements=true'
            })
            .then(response => response.json())
            .then(data => {
                const realDisbursmentValue = data.total_disbursements.replace(/,/g, '')
                document.getElementById('petty_cash').textContent = parseFloat(realDisbursmentValue).toFixed(2);
                window.pettyCash = parseFloat(realDisbursmentValue)
                calculateCashBalanceAndDifference()
            })
            .catch(error => console.error('Error:', error));
        }

        function fetchTotalExternalCashIncomes() {
            fetch('fetch_dayend_data2.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'getTotalExternalCashIncomes=true'
            })
            .then(response => response.json())
            .then(data => {
                const realExternalIncomeTotal = data.total_external_income.replace(/,/g, '')
                document.getElementById('total_external_income').textContent = parseFloat(realExternalIncomeTotal).toFixed(2);
                window.externalCashIncome = parseFloat(realExternalIncomeTotal)
                calculateCashBalanceAndDifference()
            })
            .catch(error => console.error('Error:', error));
        }

        function fetchOpeningBalance() {
            fetch('fetch_dayend_data2.php', {
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
            fetch('fetch_dayend_data2.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'getDayEndData=true'
            })
            .then(response => response.json())
            .then(data => {
                console.log("Fetched Data:", data);
                const totalNet = parseFloat(data.total_net|| '0.00');
                // document.getElementById('total_net').textContent = totalNet.toFixed(2);
                document.getElementById('total_dispatched_balance').textContent = parseFloat(data.total_balance).toFixed(2);
                const totalIncome = parseFloat(data.total_net)
                const totalServiceCharge = parseFloat(data.total_service_charge).toFixed(2)
                document.getElementById('total_income_new').textContent = totalIncome.toFixed(2);
                document.getElementById('total_balance').textContent = totalIncome.toFixed(2);
                document.getElementById('total_income_service_charge').textContent = totalServiceCharge;
                document.getElementById('total_discount').textContent = data.total_discount || '0.00';
                document.getElementById('total_bills').textContent = data.total_bills || '0';
                document.getElementById('bill_payment').textContent = parseFloat(data.total_bill_payment || '0.00').toFixed(2);
                window.totalNet = totalNet;
                window.totalBalance = data.total_balance
                calculateCashBalanceAndDifference();
            })
            .catch(error => console.error('Error:', error));
        }

        function fetchDayEndHandBalance() {
            fetch('fetch_dayend_data2.php', {
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
            fetch('fetch_dayend_data2.php', {
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

        // function fetchPettyCash() {
        //     fetch('fetch_dayend_data2.php', {
        //         method: 'POST',
        //         headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        //         body: 'getPettyCashExpenses=true'
        //     })
        //     .then(response => response.json())
        //     .then(data => {
        //         const pettyCash = parseFloat(data.petty_cash || '0.00');
        //         document.getElementById('petty_cash').textContent = pettyCash.toFixed(2);
        //         window.pettyCash = pettyCash;
        //         calculateCashBalanceAndDifference();
        //     })
        //     .catch(error => console.error('Error:', error));
        // }

        // New function to fetch card machine details from the day_end_balance table
        function fetchCardMachineTotal() {
            fetch('fetch_dayend_data2.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'getCardMachineTotal=true'
            })
            .then(response => response.json())
            .then(data => {
                const cardMachineTotal = parseFloat(data.card_machine_total || '0.00');
                document.getElementById('total_card_machine').textContent = cardMachineTotal.toFixed(2);
                window.cardMachineTotal = cardMachineTotal;
                
                // Display individual card machine entries
                const cardMachinesContainer = document.getElementById('card-machines-container');
                cardMachinesContainer.innerHTML = ''; // Clear previous content
                
                if (data.card_machines && data.card_machines.length > 0) {
                    // Create entries for each card machine
                    data.card_machines.forEach((machine, index) => {
                        const machineRow = document.createElement('div');
                        machineRow.className = 'data-row';
                        machineRow.innerHTML = `
                            <span><i class="fas fa-university"></i> ${machine.bank} (${machine.terminal_id}):</span>
                            <span>${machine.amount}</span>
                        `;
                        cardMachinesContainer.appendChild(machineRow);
                    });
                } else {
                    // No card machines found
                    const noDataRow = document.createElement('div');
                    noDataRow.className = 'data-row';
                    noDataRow.innerHTML = '<span><i class="fas fa-info-circle"></i> No card machine data available</span>';
                    cardMachinesContainer.appendChild(noDataRow);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // New function to fetch Uber and PickMe payments from the bills table
        function fetchDeliveryAppPayments() {
            fetch('fetch_dayend_data2.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'getDeliveryAppPayments=true'
            })
            .then(response => response.json())
            .then(data => {
                const uberPayment = parseFloat(data.uber_payment || '0.00');
                const pickmePayment = parseFloat(data.pickme_payment || '0.00');
                
                document.getElementById('uber_payment').textContent = uberPayment.toFixed(2);
                document.getElementById('pickme_payment').textContent = pickmePayment.toFixed(2);
                
                window.uberPayment = uberPayment;
                window.pickmePayment = pickmePayment;
                calculateCashBalanceAndDifference();
            })
            .catch(error => console.error('Error:', error));
        }

        // New function to fetch payment method totals
        function fetchPaymentMethodTotals() {
            fetch('fetch_dayend_data2.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'getPaymentMethodTotals=true'
            })
            .then(response => response.json())
            .then(data => {
                console.log("Payment Method Totals:", data);
                document.getElementById('total_cash').textContent = parseFloat((data.cash - parseFloat(document.getElementById('total_dispatched_balance').textContent)) || '0.00').toFixed(2);
                document.getElementById('total_bank').textContent = parseFloat(data.bank || '0.00').toFixed(2);
                // document.getElementById('total_card').textContent = parseFloat(data.card || '0.00').toFixed(2);
                document.getElementById('total_credit_card').textContent = parseFloat(data.credit || '0.00').toFixed(2);
                document.getElementById('total_debit_card').textContent = parseFloat(data.debit || '0.00').toFixed(2);
                document.getElementById('total_credit').textContent = parseFloat(data.cre || '0.00').toFixed(2);
                
                window.cashPayments = parseFloat(data.cash || '0.00')
                window.bankPayments = parseFloat(data.bank || '0.00')
                window.creditCardPayments = parseFloat(data.credit || '0.00')
                window.debitCardPayments = parseFloat(data.debit || '0.00')
                window.creditPayments = parseFloat(data.cre || '0.00')
                calculateCashBalanceAndDifference();
                // Keep bill_payment update in fetchDayEndData to maintain existing logic
            })
            .catch(error => console.error('Error:', error));
        }

        function calculateCashBalanceAndDifference() {
            const requiredFields = [
                'openingBalance',
                'totalNet',
                'cashDrawer',
                'dayEndHandBalance',
                'pettyCash',
                'externalCashIncome',
                'uberPayment',
                'pickmePayment',
                'bankPayments',
                'creditCardPayments',
                'debitCardPayments',
                'creditPayments',
                'cashPayments',
                'totalBalance'
            ];

            const allFieldsAvailable = requiredFields.every(field => window[field] !== undefined);
            if (allFieldsAvailable) {
                const totalBankTransfers = parseFloat(window.bankPayments)
                const totalCreditCardTransfers = parseFloat(window.creditCardPayments)
                const totalDebitCardTransfers = parseFloat(window.debitCardPayments)
                const totalCreditTransfers = parseFloat(window.creditPayments)
                const totalUberTransfers = parseFloat(window.uberPayment)
                const totalPickmeTransfers = parseFloat(window.pickmePayment)
                const totalCashTransfers = parseFloat(window.cashPayments)
                const totalCashBalance = parseFloat(window.totalBalance)

                const totalCashIncome = totalCashTransfers - totalCashBalance

                document.getElementById('total_cash').textContent = totalCashIncome.toFixed(2)

                const nonCashTotal = totalBankTransfers + totalCreditCardTransfers + totalDebitCardTransfers + totalCreditTransfers + totalUberTransfers + totalPickmeTransfers;
                document.getElementById('non_cash_total').textContent = nonCashTotal.toFixed(2);
                
                const totalCashInHand = parseFloat(window.openingBalance + window.externalCashIncome + totalCashIncome) -  window.pettyCash
                document.getElementById('cash_in_hand').textContent = totalCashInHand.toFixed(2);
                
                // Calculate Total Full Balance (Cash Balance)
                let cashBalance = (window.openingBalance + window.totalNet + window.externalCashIncome) -  window.pettyCash;
                // cashBalance = cashBalance - parseFloat(document.getElementById('petty_cash').textContent)
                document.getElementById('cash_balance').textContent = cashBalance.toFixed(2);

                // Calculate To Day Balance (Difference: Cash Balance - Cash Drawer - Petty Cash)
                const difference = cashBalance - window.cashDrawer - window.pettyCash;
                document.getElementById('difference').textContent = difference.toFixed(2);

                // Calculate Difference (Day End Hand Balance - Difference)
                const differenceHand = totalCashInHand - window.dayEndHandBalance;
                document.getElementById('differencehand').textContent = differenceHand.toFixed(2);

                console.log(cashBalance, window.dayEndHandBalance , difference, differenceHand);
                
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

        // Save button logic with added fields
        document.getElementById('saveBtn').addEventListener('click', function() {
            const getElementValue = (id) => {
                const element = document.getElementById(id);
                if (element) {
                    return parseFloat(element.textContent) || 0.00;
                }
                return 0.00;
            };
            
            const getElementText = (id) => {
                const element = document.getElementById(id);
                return element ? element.textContent.trim() : 'N/A';
            };
            
            const getElementIntValue = (id) => {
                const element = document.getElementById(id);
                if (element) {
                    return parseInt(element.textContent) || 0;
                }
                return 0;
            };
            
            const reportData = {
                username: getElementText('username'),
                opening_balance: getElementValue('opening_balance'),
                total_gross: getElementValue('total_gross'),
                total_net: getElementValue('total_income_new'),
                total_dispatched_balance: 0,
                total_discount: getElementValue('total_discount'),
                total_bills: getElementIntValue('total_bills'),
                total_cash: getElementValue('total_cash'),
                total_bank: getElementValue('total_bank'),
                total_card: getElementValue('total_card') || 0,
                total_credit_card: getElementValue('total_credit_card'),
                total_debit_card: getElementValue('total_debit_card'),
                total_credit: getElementValue('total_credit'),
                bill_payment: getElementValue('bill_payment'),
                cash_drawer: getElementValue('cash_drawer'),
                voucher_payment: 0.00, // Default value since element might not exist
                free_payment: 0.00, // Default value since element might not exist
                total_balance: getElementValue('total_balance'),
                petty_cash: getElementValue('petty_cash'),
                day_end_hand_balance: getElementValue('day_end_hand_balance'),
                cash_balance: getElementValue('cash_balance'),
                today_balance: getElementValue('difference'),
                difference_hand: getElementValue('differencehand'),
                // Add new fields
                card_machine_total: getElementValue('total_card_machine'),
                uber_payment: getElementValue('uber_payment'),
                pickme_payment: getElementValue('pickme_payment'),
                service_charge_income: getElementValue('total_income_service_charge'),
                total_external_income: getElementValue('total_external_income'),
                non_cash_total: getElementIntValue('non_cash_total'),
                cash_in_hand: getElementIntValue('cash_in_hand')
            };

            fetch('save_dayend_report.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ saveDayEndReport: true, ...reportData })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error("Server response:", text);
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    });
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error("Invalid JSON response:", text);
                        throw new Error("Server returned invalid JSON");
                    }
                });
            })
            .then(data => {
                resetBillCount();
                if (data.success) {
                    alert("Report saved successfully!");
                } else {
                    alert("Error saving report: " + (data.message || "Unknown error"));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Failed to save report. Please check the console for details.");
            });
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