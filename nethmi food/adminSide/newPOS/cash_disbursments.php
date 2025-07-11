<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Disbursements Management</title>
    <link rel="stylesheet" href="./cash_disbursments.styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cash Disbursements</h1>
            <p>Manage external payments and expenses</p>
        </div>

        <div class="main-content">
            <div class="card">
                <h2 class="card-title">Add New Disbursement</h2>
                
                <div id="message"></div>
                
                <form id="disbursementForm" method="POST">
                    <div class="form-group">
                        <label class="form-label" for="issued_amount">Amount (LKR)</label>
                        <input type="number" 
                               class="form-input" 
                               id="issued_amount" 
                               name="issued_amount" 
                               step="0.01" 
                               min="0.01" 
                               placeholder="0.00" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="issued_reason">Reason</label>
                        <input type="text" 
                               class="form-input" 
                               id="issued_reason" 
                               name="issued_reason" 
                               placeholder="Enter reason for payment" 
                               required>
                    </div>
                    
                    <button type="submit" class="btn btn-success" style="width: 100%;">
                        Add Disbursement
                    </button>
                </form>
            </div>

            <div class="card">
                <h2 class="card-title">Today's Summary</h2>
                <div style="text-align: center;">
                    <div style="margin-bottom: 15px;">
                        <div style="font-size: 2em; font-weight: bold; color: #e74c3c;">
                            LKR <span id="todayTotal">0.00</span>
                        </div>
                        <div style="color: #7f8c8d;">Total Disbursements</div>
                    </div>
                    <div>
                        <div style="font-size: 1.5em; font-weight: bold; color: #3498db;">
                            <span id="todayCount">0</span>
                        </div>
                        <div style="color: #7f8c8d;">Number of Records</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Records Section -->
        <div class="records-section">
            <h2 class="card-title">Export Records</h2>
            
            <div class="export-controls">
                <div class="form-group" style="margin-bottom: 0; flex: 1;">
                    <label class="form-label" for="export_date">Select Date</label>
                    <input type="date" class="form-input" id="export_date" name="export_date">
                </div>
                <button type="button" class="btn" onclick="loadRecords()">Load Records</button>
                <button type="button" class="btn" onclick="exportRecords()">Print/Export</button>
            </div>
            
            <div id="recordsContainer">
                <div class="no-records">Select a date to view records</div>
            </div>
        </div>
    </div>

    <script>
        function getCurrentDateSriLanka() {
            const now = new Date();
            const sriLankaTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Colombo"}));
            return sriLankaTime.toISOString().split('T')[0];
        }

        document.getElementById('export_date').value = getCurrentDateSriLanka();

        document.getElementById('disbursementForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('issued_amount', document.getElementById('issued_amount').value);
            formData.append('issued_reason', document.getElementById('issued_reason').value);
            
            fetch('./process_disbursment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showMessage(data.message, data.success ? 'success' : 'error');
                if (data.success) {
                    document.getElementById('disbursementForm').reset();
                    updateTodaySummary();
                }
            })
            .catch(error => {
                showMessage('Error processing request', 'error');
            });
        });

        function showMessage(message, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.innerHTML = `<div class="${type}-message">${message}</div>`;
            setTimeout(() => {
                messageDiv.innerHTML = '';
            }, 5000);
        }

        function loadRecords() {
            const selectedDate = document.getElementById('export_date').value;
            if (!selectedDate) {
                showMessage('Please select a date', 'error');
                return;
            }

            fetch(`./get_disbursment_records.php?date=${selectedDate}`)
            .then(response => response.json())
            .then(data => {
                displayRecords(data.records, selectedDate);
            })
            .catch(error => {
                document.getElementById('recordsContainer').innerHTML = 
                    '<div class="no-records">Error loading records</div>';
            });
        }

        function displayRecords(records, date) {
            const container = document.getElementById('recordsContainer');
            
            if (!records || records.length === 0) {
                container.innerHTML = `<div class="no-records">No records found for ${date}</div>`;
                return;
            }

            let total = 0;
            let tableHTML = `
                <table class="records-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Date</th>
                            <th>Issuer</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            records.forEach(record => {
                const cleanAmount = record.issued_amount.replace(/,/g, '');
                total += parseFloat(cleanAmount);
                
                tableHTML += `
                    <tr>
                        <td>${record.record_id}</td>
                        <td class="amount">LKR ${record.issued_amount}</td>
                        <td>${record.issued_reason}</td>
                        <td class="date">${record.issued_date}</td>
                        <td>${record.issuer_name}</td>
                    </tr>
                `;
            });

            console.log("total is " + total);
            

            tableHTML += `
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8f9fa; font-weight: bold;">
                            <td colspan="4" style="text-align: right; padding-right: 15px;">Total:</td>
                            <td class="amount">LKR ${parseFloat(total).toFixed(2)}</td>
                        </tr>
                    </tfoot>
                </table>
            `;

            container.innerHTML = tableHTML;
        }

        function exportRecords() {
            const selectedDate = document.getElementById('export_date').value;
            if (!selectedDate) {
                showMessage('Please select a date and load records first', 'error');
                return;
            }

            const originalTitle = document.title;
            document.title = `Cash Disbursements - ${selectedDate}`;
            window.print();
            document.title = originalTitle;
        }

        function updateTodaySummary() {
            const today = getCurrentDateSriLanka();
            fetch(`./get_disbursment_summary.php?date=${today}`)
            .then(response => response.json())
            .then(data => {
                const realTotal = data.total.replace(/,/g, '')
                document.getElementById('todayTotal').textContent = 
                    parseFloat(realTotal || 0).toFixed(2);
                document.getElementById('todayCount').textContent = data.count || 0;
            })
            .catch(error => {
                console.error('Error updating summary:', error);
            });
        }
        updateTodaySummary();
        loadRecords();
    </script>
</body>
</html>