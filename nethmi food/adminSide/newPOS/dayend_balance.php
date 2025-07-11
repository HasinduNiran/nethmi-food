<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../stafflogin/login.php"); // Redirect to login if not logged in
    exit();
}

require_once 'db_config.php'; // Include your database configuration

// Ensure database connection is established
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if the user has already submitted a day end balance today
$userId = $_SESSION['username'];
$today = date("Y-m-d"); // Get today's date

$query = "SELECT * FROM day_end_balance WHERE username = ? AND date = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $userId, $today); // Fix: "ss" for string parameters
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Already Submitted!",
                text: "You have already submitted a day end balance today.",
                icon: "warning",
                confirmButtonText: "OK"
            }).then(() => {
                window.location.href = "https://nethmi.rest.nexarasolutions.site/day_end_report.php";
            });
        });
    </script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day End Balance</title>
    <style>
        /* Professional Color Scheme */
        :root {
            --primary-color: #1a5f7a;
            /* Deep Teal */
            --secondary-color: #2c7da0;
            /* Muted Blue */
            --accent-color: #e76f51;
            /* Warm Terra Cotta */
            --background-light: #f8f9fa;
            /* Soft Off-White */
            --text-primary: #2d3748;
            /* Dark Charcoal */
            --text-secondary: #4a5568;
            /* Softer Charcoal */
            --border-color: #e2e8f0;
            /* Light Gray */
            --gradient-primary: linear-gradient(135deg, #1a5f7a 0%, #2c7da0 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-light);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 1400px;
            background-color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            overflow: hidden;
            padding: 20px;
        }

        /* Main Content Styles */
        .main-content {
            width: 100%;
            margin: 0 auto;
            background-color: white;
        }

        /* Title Styles */
        .title {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 15px;
            font-size: 24px;
            font-weight: 600;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 8px;
        }

        /* Balance Display Styles */
        .balance-display {
            background: var(--gradient-primary);
            color: white;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .totals-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 5px 0;
        }

        .total-item {
            text-align: center;
            padding: 0 15px;
        }

        .total-item h3 {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 5px;
            color: white;
        }

        .total-value {
            font-size: 24px;
            font-weight: 700;
            display: block;
        }

        /* Two-column layout container */
        .content-layout {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            margin-bottom:60px
        }

        /* Left column - Cash denominations */
        .cash-section {
            flex: 1;
            min-width: 0; /* Prevents flex items from overflowing */
        }

        /* Right column - Card machines */
        .card-machines-section {
            flex: 1;
            min-width: 0; /* Prevents flex items from overflowing */
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .balance-display2 {
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            overflow-y: auto;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            height: 100%;
            padding-bottom:0px;
        }

        /* Denomination Row Styles */
        .denominations {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom:-20px;
        }

        .denomination-row {
            display: grid;
            grid-template-columns: 70px 100px 1fr;
            align-items: center;
            padding: 8px;
            border-radius: 6px;
            background-color: var(--background-light);
            transition: background-color 0.2s ease;
        }

        .denomination-row:hover {
            background-color: rgba(44, 125, 160, 0.1);
        }

        .denomination-row span {
            font-size: 15px;
            color: var(--text-secondary);
        }

        .denomination-row input {
            width: 100%;
            padding: 6px 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 15px;
            color: var(--text-primary);
            text-align: center;
        }

        .denomination-row input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(26, 95, 122, 0.1);
        }

        .calculated {
            font-weight: 600;
            color: var(--primary-color) !important;
        }

        /* Card machine styles */
        .card-machine-entry {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        .card-machine-fields {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .form-group {
            margin-bottom: -2px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
        }

        h3, h4 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        #add-machine-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        #add-machine-btn:hover {
            background-color: var(--primary-color);
        }

        .card-total {
            margin-top: 15px;
            text-align: right;
            padding: 10px;
            background-color: rgba(44, 125, 160, 0.1);
            border-radius: 5px;
        }

        /* Grand total */
        .grand-total {
            margin-top: 20px;
            text-align: center;
            padding: 15px;
            background-color: var(--gradient-primary);
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Button Styles */
        .go-invoice {
            margin-top: 50px;
            padding: 12px 24px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .go-invoice:hover {
            background-color: #d6604a;
            transform: translateY(-2px);
        }

        .go-invoice:active {
            transform: translateY(1px);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .content-layout {
                flex-direction: column;
            }
            
            .card-machines-section {
                margin-top: 20px;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <!-- Main Content -->
        <div class="main-content">
            <h1 class="title">Day End Balance</h1>
            <div class="balance-display">
                <div class="totals-container">
                    <div class="total-item">
                        <h3>Total Cash</h3>
                        <span class="total-value" id="cash-total">0.00</span>
                    </div>
                    <div class="total-item">
                        <h3>Total Card</h3>
                        <span class="total-value" id="card-total">0.00</span>
                    </div>
                </div>
            </div>
            
            <!-- Two-column layout -->
            <div class="content-layout">
                <!-- Left Column - Cash denominations -->
                <div class="cash-section">
                    <h3>Cash Denominations</h3>
                    <div class="balance-display2">
                        <div class="denominations">
                            <?php
                            $denominations = [5000, 1000, 500, 100, 50, 20, 10, 5, 2, 1];
                            foreach ($denominations as $denom) {
                                echo "<div class='denomination-row' data-value='$denom'>
                                        <span>$denom x </span>
                                        <input type='number' min='0' value='' onchange='updateBalance(this)'>
                                        <span>= <span class='calculated'>0</span></span>
                                      </div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Card machines -->
                <div class="card-machines-section">
                    <h3>Card Machine Details</h3>
                    <div id="card-machines-container">
                        <!-- First card machine entry -->
                        <div class="card-machine-entry" id="card-machine-1">
                            <h4>Card Machine 1</h4>
                            <div class="card-machine-fields">
                                <div class="form-group">
                                    <label for="terminal-id-1">Terminal ID:</label>
                                    <input type="text" id="terminal-id-1" class="card-field terminal-id" data-field="terminal_id_1">
                                </div>
                                <div class="form-group">
                                    <label for="batch-number-1">Batch Number:</label>
                                    <input type="text" id="batch-number-1" class="card-field batch-number" data-field="batch_number_1">
                                </div>
                                <div class="form-group">
                                    <label for="bank-1">Bank:</label>
                                    <input type="text" id="bank-1" class="card-field bank" data-field="bank_1">
                                </div>
                                <div class="form-group">
                                    <label for="amount-1">Amount:</label>
                                    <input type="number" id="amount-1" class="card-field card-amount" data-field="card_amount_1" min="0" step="0.01" onchange="updateCardTotal()">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" id="add-machine-btn" onclick="addCardMachine()">Add Another Card Machine</button>
                    
                    <div class="card-total">
                        <h4>Total Card Amount: <span id="card-total-amount">0.00</span></h4>
                    </div>
                </div>
            </div>
            
            <!-- Removed grand total section -->
            
            <button class="go-invoice" id="go-invoice-btn" onclick="saveBalanceToDB()">Go To Invoice</button>
        </div>
    </div>
</body>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const inputs = document.querySelectorAll('.denomination-row input');
        const invoiceButton = document.getElementById('go-invoice-btn');

        if (inputs.length > 0) inputs[0].focus();

        inputs.forEach((input, index) => {
            input.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowDown' && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                    event.preventDefault();
                } else if (event.key === 'ArrowUp' && index > 0) {
                    inputs[index - 1].focus();
                    event.preventDefault();
                } else if (event.key === 'Enter' && index === inputs.length - 1) {
                    invoiceButton.click();
                }
            });
        });

        invoiceButton.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                invoiceButton.click();
            }
        });
    });
       
    // function saveBalanceToDB() {
    //     const totalBalance = document.getElementById('total-balance').textContent;
    //     const denominations = {};

    //     document.querySelectorAll('.denomination-row').forEach(row => {
    //         const value = row.dataset.value;
    //         const quantity = row.querySelector('input').value || 0;
    //         denominations[`denomination_${value}`] = parseInt(quantity);
    //     });

    //     const data = {
    //         total_balance: parseFloat(totalBalance),
    //         ...denominations
    //     };

    //     fetch('insert_dayend_balance.php', {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json'
    //             },
    //             body: JSON.stringify(data)
    //         })
    //         .then(response => response.json())
    //         .then(result => {
    //             Swal.fire({
    //                 title: result.success ? "Success" : "Error!",
    //                 text: result.message,
    //                 icon: result.success ? 'success' : 'error',
    //                 confirmButtonText: 'OK'
    //             }).then((swalResult) => {
    //                 if (swalResult.isConfirmed && result.success) {
    //                     window.location.href = "dayend_form.php";
    //                 }
    //             });
    //         })
    //         .catch(error => console.error('Error:', error));            
    // }


// Global variable to track number of card machines
let cardMachineCount = 1;
const MAX_CARD_MACHINES = 3; // Maximum number of card machines allowed

// Initialize totals when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize both totals to zero
    updateCashTotal();
    updateCardTotal();
    
    // Add CSS for the remove button
    const style = document.createElement('style');
    style.textContent = `
        .remove-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        
        .remove-btn:hover {
            background-color: #c0392b;
        }
    `;
    document.head.appendChild(style);
});

// Function to update balance based on denomination inputs
function updateBalance(input) {
    const row = input.closest('.denomination-row');
    const value = parseInt(row.dataset.value);
    const quantity = parseInt(input.value) || 0;
    const calculated = value * quantity;
    
    row.querySelector('.calculated').textContent = calculated;
    
    // Update cash total
    updateCashTotal();
}

// Function to calculate and update cash total
function updateCashTotal() {
    let cashTotal = 0;
    
    // Sum up all denomination values
    document.querySelectorAll('.denomination-row').forEach(row => {
        const calculatedElement = row.querySelector('.calculated');
        cashTotal += parseInt(calculatedElement.textContent) || 0;
    });
    
    // Update cash total display
    document.getElementById('cash-total').textContent = cashTotal.toFixed(2);
}

// Function to add new card machine entry
function addCardMachine() {
    if (cardMachineCount >= MAX_CARD_MACHINES) {
        Swal.fire({
            title: 'Maximum Limit Reached',
            text: 'You can add up to ' + MAX_CARD_MACHINES + ' card machines.',
            icon: 'info',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    cardMachineCount++;
    const container = document.getElementById('card-machines-container');
    
    const newMachineHtml = `
        <div class="card-machine-entry" id="card-machine-${cardMachineCount}">
            <h4>Card Machine ${cardMachineCount}</h4>
            <div class="card-machine-fields">
                <div class="form-group">
                    <label for="terminal-id-${cardMachineCount}">Terminal ID:</label>
                    <input type="text" id="terminal-id-${cardMachineCount}" class="card-field terminal-id" data-field="terminal_id_${cardMachineCount}">
                </div>
                <div class="form-group">
                    <label for="batch-number-${cardMachineCount}">Batch Number:</label>
                    <input type="text" id="batch-number-${cardMachineCount}" class="card-field batch-number" data-field="batch_number_${cardMachineCount}">
                </div>
                <div class="form-group">
                    <label for="bank-${cardMachineCount}">Bank:</label>
                    <input type="text" id="bank-${cardMachineCount}" class="card-field bank" data-field="bank_${cardMachineCount}">
                </div>
                <div class="form-group">
                    <label for="amount-${cardMachineCount}">Amount:</label>
                    <input type="number" id="amount-${cardMachineCount}" class="card-field card-amount" data-field="card_amount_${cardMachineCount}" min="0" step="0.01" onchange="updateCardTotal()">
                </div>
            </div>
            <button type="button" class="remove-btn" onclick="removeCardMachine(${cardMachineCount})">Remove</button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', newMachineHtml);
    
    // Update the add button visibility
    if (cardMachineCount >= MAX_CARD_MACHINES) {
        document.getElementById('add-machine-btn').style.display = 'none';
    }
}

// Function to remove a card machine entry
function removeCardMachine(machineId) {
    const machineElement = document.getElementById(`card-machine-${machineId}`);
    if (machineElement) {
        machineElement.remove();
        cardMachineCount--;
        
        // Show the add button if we're under the limit
        if (cardMachineCount < MAX_CARD_MACHINES) {
            document.getElementById('add-machine-btn').style.display = 'block';
        }
        
        // Update card total
        updateCardTotal();
    }
}

// Function to calculate and update card total amount
function updateCardTotal() {
    let cardTotal = 0;
    
    // Sum all card machine amounts
    document.querySelectorAll('.card-amount').forEach(input => {
        cardTotal += parseFloat(input.value) || 0;
    });
    
    // Update card total displays
    document.getElementById('card-total-amount').textContent = cardTotal.toFixed(2);
    document.getElementById('card-total').textContent = cardTotal.toFixed(2);
    
    return cardTotal;
}

// Function to save all data to the database
function saveBalanceToDB() {
    // Get cash denomination data
    const denominations = {};
    document.querySelectorAll('.denomination-row').forEach(row => {
        const value = row.dataset.value;
        const quantity = row.querySelector('input').value || 0;
        denominations[`denomination_${value}`] = parseInt(quantity);
    });
    
    // Get card machine data - keep the same field names for compatibility
    const cardFields = {};
    document.querySelectorAll('.card-field').forEach(field => {
        const dataField = field.dataset.field;
        if (dataField) {
            let value = field.value;
            // Convert amount fields to float, others remain as strings
            if (dataField.includes('amount')) {
                value = parseFloat(value) || 0;
            }
            cardFields[dataField] = value;
        }
    });
    
    // Calculate total balance (sum of cash and card totals)
    const cashTotal = parseFloat(document.getElementById('cash-total').textContent) || 0;
    const cardTotal = parseFloat(document.getElementById('card-total').textContent) || 0;
    const totalBalance = cashTotal;
    
    // Prepare the data object
    const data = {
        total_balance: totalBalance,
        ...denominations,
        ...cardFields
    };
    
    // Send data to server
    fetch('insert_dayend_balance.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        Swal.fire({
            title: result.success ? "Success" : "Error!",
            text: result.message,
            icon: result.success ? 'success' : 'error',
            confirmButtonText: 'OK'
        }).then((swalResult) => {
            if (swalResult.isConfirmed && result.success) {
                window.location.href = "https://nethmi.rest.nexarasolutions.site/day_end_report.php";
            }
        });
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: "Error!",
            text: "There was a problem saving the data. Please try again.",
            icon: "error",
            confirmButtonText: "OK"
        });
    });
}

    // function updateBalance(inputElement) {
    //     const denominationRow = inputElement.closest('.denomination-row');
    //     const denominationValue = parseInt(denominationRow.dataset.value);
    //     const quantity = parseInt(inputElement.value) || 0;
    //     const calculatedAmount = denominationValue * quantity;

    //     denominationRow.querySelector('.calculated').textContent = calculatedAmount;
    //     updateTotalBalance();
    // }

    // function updateTotalBalance() {
    //     let total = 0;
    //     document.querySelectorAll('.denomination-row').forEach(row => {
    //         total += parseInt(row.querySelector('.calculated').textContent) || 0;
    //     });
    //     document.getElementById('total-balance').textContent = total;
    // }
</script>

</html>