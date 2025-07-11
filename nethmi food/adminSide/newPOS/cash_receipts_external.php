<?php
session_start();

date_default_timezone_set('Asia/Colombo');
require_once '../config.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_receipt') {
    $received_amount = $_POST['received_amount'];
    $description = $_POST['description'];
    $received_date = date('Y-m-d');
    $receiver_account_id = $_SESSION['logged_account_id'] ?? null;
    $receiver_name = $_SESSION['logged_staff_name'] ?? 'Unknown';
    
    if (!empty($received_amount) && is_numeric($received_amount)) {
        $stmt = $link->prepare("INSERT INTO cash_receipts (received_amount, description, received_date, receiver_account_id, receiver_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("dssds", $received_amount, $description, $received_date, $receiver_account_id, $receiver_name);
        
        if ($stmt->execute()) {
            $message = 'Cash receipt recorded successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error recording receipt: ' . $stmt->error;
            $messageType = 'error';
        }
        $stmt->close();
    } else {
        $message = 'Please enter a valid amount.';
        $messageType = 'error';
    }
}

// Handle export request
$exportData = [];
if (isset($_GET['export_date']) && !empty($_GET['export_date'])) {
    $export_date = $_GET['export_date'];
    $stmt = $link->prepare("SELECT * FROM cash_receipts WHERE received_date = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $export_date);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $exportData = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $message = 'Error fetching export data: ' . $stmt->error;
        $messageType = 'error';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Receipts Management</title>
    <link rel="stylesheet" href="./cash_receipts_external.styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cash Receipts Management</h1>
            <p>Record and Export Daily Cash Income</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="main-content">
            <!-- Add New Receipt Form -->
            <div class="card">
                <div class="card-header">
                    <h2>Add New Cash Receipt</h2>
                </div>
                
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_receipt">
                    
                    <div class="form-group">
                        <label class="form-label" for="received_amount">Received Amount (LKR)</label>
                        <input type="number" 
                               class="form-input" 
                               id="received_amount" 
                               name="received_amount" 
                               step="0.01" 
                               min="0" 
                               required
                               placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <input type="text" 
                               class="form-input" 
                               id="description" 
                               name="description" 
                               placeholder="Enter description...">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="text" 
                               class="form-input" 
                               value="<?php echo date('Y-m-d (l)'); ?>" 
                               readonly>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Receiver</label>
                        <input type="text" 
                               class="form-input" 
                               value="<?php echo htmlspecialchars($_SESSION['logged_staff_name'] ?? 'Not logged in'); ?>" 
                               readonly>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Record Receipt</button>
                </form>
            </div>

            <!-- Export Section -->
            <div class="card">
                <div class="card-header">
                    <h2>Export Daily Report</h2>
                </div>
                
                <form method="GET" action="">
                    <div class="export-section">
                        <div class="form-group">
                            <label class="form-label" for="export_date">Select Date</label>
                            <input type="date" 
                                   class="form-input" 
                                   id="export_date" 
                                   name="export_date" 
                                   value="<?php echo date('Y-m-d'); ?>"
                                   required>
                        </div>
                        <button type="submit" class="btn btn-secondary">Load Data</button>
                    </div>
                </form>
                
                <?php if (!empty($exportData)): ?>
                    <button type="button" class="btn btn-primary" onclick="printReport()" style="margin-top: 20px;">
                        Print Report
                    </button>
                    
                    <div class="export-table-container">
                        <table class="export-table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Amount (LKR)</th>
                                    <th>Description</th>
                                    <th>Receiver</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                foreach ($exportData as $row): 
                                    $total += $row['received_amount'];
                                ?>
                                    <tr>
                                        <td><?php echo date('H:i:s', strtotime($row['created_at'])); ?></td>
                                        <td><?php echo number_format($row['received_amount'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['description'] ?: '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['receiver_name']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="total-row">
                                    <td><strong>TOTAL</strong></td>
                                    <td><strong><?php echo number_format($total, 2); ?></strong></td>
                                    <td colspan="2"><strong><?php echo count($exportData); ?> transactions</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php elseif (isset($_GET['export_date'])): ?>
                    <p style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border: 2px solid #2c3e50;">
                        No cash receipts found for <?php echo htmlspecialchars($_GET['export_date']); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Print Content -->
    <div class="print-content">
        <?php if (!empty($exportData)): ?>
            <div style="text-align: center; margin-bottom: 30px;">
                <h1>Cash Receipts Report</h1>
                <h2>Date: <?php echo date('F j, Y', strtotime($_GET['export_date'])); ?></h2>
                <p>Generated on: <?php echo date('F j, Y \a\t g:i A'); ?></p>
            </div>
            
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="border: 2px solid #2c3e50; padding: 12px; background-color: #ff5252; color: white;">Time</th>
                        <th style="border: 2px solid #2c3e50; padding: 12px; background-color: #ff5252; color: white;">Amount (LKR)</th>
                        <th style="border: 2px solid #2c3e50; padding: 12px; background-color: #ff5252; color: white;">Description</th>
                        <th style="border: 2px solid #2c3e50; padding: 12px; background-color: #ff5252; color: white;">Receiver</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($exportData as $row): 
                        $total += $row['received_amount'];
                    ?>
                        <tr>
                            <td style="border: 2px solid #2c3e50; padding: 12px;"><?php echo date('H:i:s', strtotime($row['created_at'])); ?></td>
                            <td style="border: 2px solid #2c3e50; padding: 12px;"><?php echo number_format($row['received_amount'], 2); ?></td>
                            <td style="border: 2px solid #2c3e50; padding: 12px;"><?php echo htmlspecialchars($row['description'] ?: '-'); ?></td>
                            <td style="border: 2px solid #2c3e50; padding: 12px;"><?php echo htmlspecialchars($row['receiver_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td style="border: 2px solid #2c3e50; padding: 12px; background-color: #2c3e50; color: white; font-weight: bold;">TOTAL</td>
                        <td style="border: 2px solid #2c3e50; padding: 12px; background-color: #2c3e50; color: white; font-weight: bold;"><?php echo number_format($total, 2); ?></td>
                        <td colspan="2" style="border: 2px solid #2c3e50; padding: 12px; background-color: #2c3e50; color: white; font-weight: bold;"><?php echo count($exportData); ?> transactions</td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script>
        function printReport() {
            window.print();
        }

        // Auto-focus on amount field
        document.addEventListener('DOMContentLoaded', function() {
            const amountField = document.getElementById('received_amount');
            if (amountField) {
                amountField.focus();
            }
        });

        // Form validation
        document.querySelector('form[method="POST"]').addEventListener('submit', function(e) {
            const amount = document.getElementById('received_amount').value;
            if (!amount || parseFloat(amount) <= 0) {
                e.preventDefault();
                alert('Please enter a valid amount greater than 0.');
                return false;
            }
        });
    </script>
</body>
</html>
