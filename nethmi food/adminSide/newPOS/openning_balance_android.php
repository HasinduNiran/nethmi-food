<?php
session_start(); // Start the session

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

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

// Check if the user has already submitted an opening balance today (Sri Lanka date)
$userId = $_SESSION['username'];
$today = date("Y-m-d"); // Get today's Sri Lanka date
$current_datetime = date("Y-m-d H:i:s"); // Get current Sri Lanka datetime

$query = "SELECT * FROM opening_balance WHERE username = ? AND date = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $userId, $today); // Fix: "ss" for string parameters
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "⚠️ Already Submitted!",
                html: "<p>You have already submitted an opening balance today</p><p><strong>Date: ' . $today . '</strong></p>",
                icon: "warning",
                confirmButtonText: "Go to POS",
                showClass: {
                    popup: "animate__animated animate__fadeInDown"
                },
                hideClass: {
                    popup: "animate__animated animate__fadeOutUp"
                },
                customClass: {
                    popup: "custom-swal-popup",
                    title: "custom-swal-title",
                    confirmButton: "custom-swal-confirm"
                },
                buttonsStyling: false,
                timer: 10000,
                timerProgressBar: true,
                footer: "<small>You will be redirected automatically in 10 seconds</small>"
            }).then(() => {
                window.location.href = "pos_main_android.php";
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
    <title>Balance Interface</title>
    <style>
        /* Professional Color Scheme */
        :root {
            --primary-color: #1a5f7a;     /* Deep Teal */
            --secondary-color: #2c7da0;   /* Muted Blue */
            --accent-color: #e76f51;      /* Warm Terra Cotta */
            --background-light: #f8f9fa;  /* Soft Off-White */
            --text-primary: #2d3748;      /* Dark Charcoal */
            --text-secondary: #4a5568;    /* Softer Charcoal */
            --border-color: #e2e8f0;      /* Light Gray */
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
            min-height: 100vh; /* Ensure body takes full height */
            display: flex; /* Use Flexbox to center content */
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
        }

        .container {
            max-width: 45%; /* Reduced from 97% */
            width: 45%; /* Ensure it scales */
            background-color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            overflow: hidden;
            padding: 20px; /* Add padding inside container */
        }

        /* Main Content Styles */
        .main-content {
            width: 100%; /* Full width within container */
            max-width: 600px; /* Optional: Limit max width for better readability */
            margin: 0 auto; /* Center within container */
            padding: 15px 20px;
            background-color: white;
            display: flex;
            flex-direction: column;
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

        /* Date Display Styles */
        .date-info {
            background-color: var(--background-light);
            color: var(--text-secondary);
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
            border: 1px solid var(--border-color);
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

        .balance-display h2 {
            font-size: 20px;
            font-weight: 500;
        }

        .total-balance {
            font-size: 24px;
            font-weight: 700;
        }

        .balance-display2 {
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            flex: 1;
            overflow-y: auto;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Denomination Row Styles */
        .denominations {
            display: flex;
            flex-direction: column;
            gap: 12px;
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

        /* Button Styles */
        .go-invoice {
            margin-top: 15px;
            padding: 10px 18px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: block; /* Ensure it's a block element */
            margin-left: auto; /* Center horizontally */
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
        @media (max-width: 1200px) {
            .container {
                max-width: 90%;
            }

            .main-content {
                max-width: 100%; /* Full width on smaller screens */
            }
        }

        /* Custom SweetAlert Styles */
        .custom-swal-popup {
            border-radius: 15px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
            border: 2px solid var(--primary-color) !important;
        }

        .custom-swal-title {
            color: var(--primary-color) !important;
            font-weight: 600 !important;
            font-size: 1.5rem !important;
        }

        .custom-swal-confirm {
            background-color: var(--primary-color) !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 25px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }

        .custom-swal-confirm:hover {
            background-color: var(--secondary-color) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 5px 15px rgba(26, 95, 122, 0.3) !important;
        }

        .custom-swal-success {
            background-color: #10b981 !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 25px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }

        .custom-swal-success:hover {
            background-color: #059669 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3) !important;
        }

        .custom-swal-error {
            background-color: var(--accent-color) !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 25px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }

        .custom-swal-error:hover {
            background-color: #d6604a !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 5px 15px rgba(231, 111, 81, 0.3) !important;
        }

        .custom-swal-cancel {
            background-color: #6b7280 !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 25px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }

        .custom-swal-cancel:hover {
            background-color: #4b5563 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 5px 15px rgba(107, 114, 128, 0.3) !important;
        }

        /* SweetAlert Timer Progress Bar */
        .swal2-timer-progress-bar {
            background-color: var(--primary-color) !important;
        }

        /* SweetAlert Icon Styling */
        .swal2-success .swal2-success-ring {
            border-color: #10b981 !important;
        }

        .swal2-success .swal2-success-fix {
            background-color: #10b981 !important;
        }

        .swal2-success [class^='swal2-success-line'] {
            background-color: #10b981 !important;
        }
    </style>

    <!-- Add Animate.css for SweetAlert animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <!-- Main Content -->
        <div class="main-content">
            <h1 class="title">Opening Balance</h1>
            
            <!-- Date Information -->
            <div class="date-info">
                <strong>Business Date:</strong> <?php echo $today; ?> | 
                <strong>Current Time:</strong> <?php echo date("H:i:s"); ?>
            </div>
            
            <div class="balance-display">
                <h2>Total Balance - <span class="total-balance" id="total-balance">0.00</span></h2>
            </div>
            <br><br>
            <!-- Balance Display -->
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
                <button class="go-invoice" id="go-invoice-btn" onclick="saveBalanceToDB()">Go To Invoice</button>
            </div>
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

    function saveBalanceToDB() {
        const totalBalance = document.getElementById('total-balance').textContent;
        
        // Check if total balance is zero
        if (parseFloat(totalBalance) === 0) {
            Swal.fire({
                title: "⚠️ Zero Balance",
                html: "<p>Are you sure you want to proceed with a zero opening balance?</p>",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Continue',
                cancelButtonText: 'No, Let me add amounts',
                customClass: {
                    popup: "custom-swal-popup",
                    title: "custom-swal-title",
                    confirmButton: "custom-swal-confirm",
                    cancelButton: "custom-swal-cancel"
                },
                buttonsStyling: false,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    proceedWithSave();
                }
            });
            return;
        }

        // Show confirmation dialog for non-zero balance
        Swal.fire({
            title: "💰 Confirm Opening Balance",
            html: `
                <div style="text-align: left; margin: 20px 0;">
                    <p><strong>Total Opening Balance: Rs. ${parseFloat(totalBalance).toLocaleString()}</strong></p>
                    <hr style="margin: 15px 0;">
                    <p>Please confirm that this amount is correct before proceeding.</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Confirm & Save',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: "custom-swal-popup",
                title: "custom-swal-title",
                confirmButton: "custom-swal-success",
                cancelButton: "custom-swal-cancel"
            },
            buttonsStyling: false,
            reverseButtons: true,
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                proceedWithSave();
            }
        });
    }

    function proceedWithSave() {
        const totalBalance = document.getElementById('total-balance').textContent;
        const denominations = {};

        document.querySelectorAll('.denomination-row').forEach(row => {
            const value = row.dataset.value;
            const quantity = row.querySelector('input').value || 0;
            denominations[`denomination_${value}`] = parseInt(quantity);
        });

        const data = {
            total_balance: parseFloat(totalBalance),
            ...denominations
        };

        // Show loading alert
        Swal.fire({
            title: '💾 Saving...',
            html: 'Please wait while we save your opening balance.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        fetch('insert_opening_balance.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            Swal.fire({
                title: result.success ? "✅ Success!" : "❌ Error!",
                html: result.success ? 
                    "<p><strong>Opening balance saved successfully!</strong></p><p>You can now proceed to the POS system.</p>" : 
                    "<p><strong>Failed to save opening balance</strong></p><p>" + result.message + "</p>",
                icon: result.success ? 'success' : 'error',
                confirmButtonText: result.success ? 'Go to POS' : 'Try Again',
                showClass: {
                    popup: "animate__animated animate__bounceIn"
                },
                hideClass: {
                    popup: "animate__animated animate__fadeOut"
                },
                customClass: {
                    popup: "custom-swal-popup",
                    title: "custom-swal-title",
                    confirmButton: result.success ? "custom-swal-success" : "custom-swal-error"
                },
                buttonsStyling: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                footer: result.success ? 
                    "<small>💰 Total Balance: Rs. " + parseFloat(document.getElementById('total-balance').textContent).toLocaleString() + "</small>" : 
                    "<small>⚠️ Please check your data and try again</small>"
            }).then((swalResult) => {
                if (swalResult.isConfirmed && result.success) {
                    window.location.href = "pos_main_android.php";
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: "🚨 Connection Error!",
                html: "<p><strong>Failed to save opening balance</strong></p><p>There was a problem connecting to the server.</p><p>Please check your internet connection and try again.</p>",
                icon: 'error',
                confirmButtonText: 'Retry',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                showClass: {
                    popup: "animate__animated animate__shakeX"
                },
                hideClass: {
                    popup: "animate__animated animate__fadeOut"
                },
                customClass: {
                    popup: "custom-swal-popup",
                    title: "custom-swal-title",
                    confirmButton: "custom-swal-error",
                    cancelButton: "custom-swal-cancel"
                },
                buttonsStyling: false,
                reverseButtons: true,
                footer: "<small>💡 Tip: Make sure you're connected to the internet</small>"
            }).then((result) => {
                if (result.isConfirmed) {
                    saveBalanceToDB(); // Retry the operation
                }
            });
        });
    }

    function updateBalance(inputElement) {
        const denominationRow = inputElement.closest('.denomination-row');
        const denominationValue = parseInt(denominationRow.dataset.value);
        const quantity = parseInt(inputElement.value) || 0;
        const calculatedAmount = denominationValue * quantity;

        denominationRow.querySelector('.calculated').textContent = calculatedAmount;
        updateTotalBalance();
    }

    function updateTotalBalance() {
        let total = 0;
        document.querySelectorAll('.denomination-row').forEach(row => {
            total += parseInt(row.querySelector('.calculated').textContent) || 0;
        });
        document.getElementById('total-balance').textContent = total;
    }
</script>

</html>