<?php
// Include database connection
require_once '../config.php';
// Use $conn instead of $db_conn for database operations
$db_conn = $link;

// Set SQL_BIG_SELECTS=1 to allow large joins - MUST BE FIRST
$db_conn->query("SET SQL_BIG_SELECTS=1");

// Initialize variables for filtering
$staff_id = isset($_GET['staff_id']) ? $_GET['staff_id'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-2 days')); 
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$paymentType = isset($_GET['payment_type']) ? $_GET['payment_type'] : '';
$reportType = isset($_GET['report_type']) ? $_GET['report_type'] : 'sales';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 100; // Limit number of items per page
$productSearch = isset($_GET['product_search']) ? $_GET['product_search'] : '';

// Calculate offset for pagination
$offset = ($page - 1) * $itemsPerPage;

// Fetch staff list from accounts table
$staffQuery = "SELECT account_id, email FROM accounts ORDER BY email ASC";
$staffResult = $db_conn->query($staffQuery);
$staffList = [];
if ($staffResult && $staffResult->num_rows > 0) {
    while ($row = $staffResult->fetch_assoc()) {
        $staffList[] = $row;
    }
}

// Calculate summary data with a separate optimized query
$summaryQuery = "
    SELECT 
        SUM(b.total_before_discount) AS total_sales,
        SUM(CASE WHEN bp.payment_method = 'card' THEN bp.amount ELSE 0 END) AS card_balance,
        SUM(CASE WHEN bp.payment_method = 'cash' THEN bp.amount ELSE 0 END) AS cash_balance
    FROM bills b
    LEFT JOIN bill_payments bp ON b.bill_id = bp.bill_id
    WHERE DATE(b.bill_time) BETWEEN ? AND ?
";

$params = ['ss', $startDate, $endDate];

// Apply summary filters
if (!empty($staff_id)) {
    $summaryQuery .= " AND b.staff_id = ?";
    $params[0] .= 's';
    $params[] = $staff_id;
}
if (!empty($paymentType)) {
    $summaryQuery .= " AND b.bill_id IN (SELECT bill_id FROM bill_payments WHERE payment_method = ?)";
    $params[0] .= 's';
    $params[] = $paymentType;
}
if (!empty($productSearch)) {
    $summaryQuery .= " AND b.bill_id IN (SELECT bill_id FROM bill_items WHERE product_name LIKE ?)";
    $params[0] .= 's';
    $params[] = "%{$productSearch}%";
}

try {
    $stmt = $db_conn->prepare($summaryQuery);
    if ($stmt) {
        $stmt->bind_param(...$params);
        $stmt->execute();
        $summaryResult = $stmt->get_result();
        $summaryData = $summaryResult->fetch_assoc();
        $stmt->close();
    }
    
    $totalSale = $summaryData['total_sales'] ?? 0;
    $cardBalance = $summaryData['card_balance'] ?? 0;
    $cashBalance = $summaryData['cash_balance'] ?? 0;
    
    // Separate query for total items to ensure it respects all filters
    $itemsQuery = "
        SELECT SUM(bi.quantity) AS total_items
        FROM bill_items bi
        JOIN bills b ON bi.bill_id = b.bill_id
        WHERE DATE(b.bill_time) BETWEEN ? AND ?
    ";
    
    $itemsParams = ['ss', $startDate, $endDate];

    if (!empty($staff_id)) {
        $itemsQuery .= " AND b.staff_id = ?";
        $itemsParams[0] .= 's';
        $itemsParams[] = $staff_id;
    }
    if (!empty($paymentType)) {
        $itemsQuery .= " AND b.bill_id IN (SELECT bill_id FROM bill_payments WHERE payment_method = ?)";
        $itemsParams[0] .= 's';
        $itemsParams[] = $paymentType;
    }
    if (!empty($productSearch)) {
        $itemsQuery .= " AND bi.product_name LIKE ?";
        $itemsParams[0] .= 's';
        $itemsParams[] = "%{$productSearch}%";
    }

    $stmt = $db_conn->prepare($itemsQuery);
    if ($stmt) {
        $stmt->bind_param(...$itemsParams);
        $stmt->execute();
        $itemsResult = $stmt->get_result();
        $totalItems = $itemsResult->fetch_assoc()['total_items'] ?? 0;
        $stmt->close();
    } else {
        $totalItems = 0;
    }

} catch (Exception $e) {
    echo '<div style="color:red; padding:20px; margin:20px; background:#ffe6e6; border:1px solid #ff9999;">';
    echo 'Error executing summary query: ' . $e->getMessage();
    echo '</div>';
    $totalSale = 0;
    $cardBalance = 0;
    $cashBalance = 0;
    $totalItems = 0;
}

// Get total count of records for pagination
$countQuery = "
    SELECT COUNT(DISTINCT bi.bill_item_id) as total_count
    FROM bill_items bi
    JOIN bills b ON bi.bill_id = b.bill_id
    WHERE DATE(b.bill_time) BETWEEN ? AND ?
";

$countParams = ['ss', $startDate, $endDate];

// Apply count filters
if (!empty($staff_id)) {
    $countQuery .= " AND b.staff_id = ?";
    $countParams[0] .= 's';
    $countParams[] = $staff_id;
}
if (!empty($paymentType)) {
     $countQuery .= " AND b.bill_id IN (SELECT bill_id FROM bill_payments WHERE payment_method = ?)";
     $countParams[0] .= 's';
     $countParams[] = $paymentType;
}
if (!empty($productSearch)) {
    $countQuery .= " AND bi.product_name LIKE ?";
    $countParams[0] .= 's';
    $countParams[] = "%{$productSearch}%";
}

try {
    $stmt = $db_conn->prepare($countQuery);
    if ($stmt) {
        $stmt->bind_param(...$countParams);
        $stmt->execute();
        $countResult = $stmt->get_result();
        $totalCount = $countResult->fetch_assoc()['total_count'] ?? 0;
        $totalPages = ceil($totalCount / $itemsPerPage);
        $stmt->close();
    } else {
        $totalCount = 0;
        $totalPages = 1;
    }
} catch (Exception $e) {
    echo '<div style="color:red; padding:20px; margin:20px; background:#ffe6e6; border:1px solid #ff9999;">';
    echo 'Error executing count query: ' . $e->getMessage();
    echo '</div>';
    $totalCount = 0;
    $totalPages = 1;
}

// We'll fetch data via AJAX instead of loading it all at once
$salesData = [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            padding: 30px;
            color: #333;
        }

        .report-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            max-width: 1700px;
            margin: 0 auto;
            overflow: hidden;
        }

        .header {
            background: #2c3e50;
            color: white;
            padding: 22px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header h1 {
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header h1::before {
            content: "📊";
            font-size: 24px;
        }
        
        .header-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .close-btn {
            background: rgba(231, 76, 60, 0.85);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.2s ease;
        }

        .close-btn:hover {
            background: #e74c3c;
            transform: scale(1.05);
        }

        .content {
            padding: 30px;
        }

        .filter-section {
            display: flex;
            gap: 20px;
            margin-bottom: 35px;
            align-items: flex-end; /* Align items at the bottom */
            flex-wrap: wrap;
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #eaedf0;
        }

        .filter-section form {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            width: 100%;
            align-items: flex-end; /* Align form items at the bottom */
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1 1 auto;
            min-width: 150px; /* Minimum width for each form group */
            max-width: 200px; /* Maximum width for each form group */
        }

        /* The submit button should not grow/shrink */
        .filter-section .btn-container {
            flex: 0 0 auto;
            margin-bottom: 1px; /* Align with the bottom of inputs */
        }

        .form-group label {
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
        }

        .form-group select,
        .form-group input {
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            min-width: 180px;
            background-color: white;
            color: #2c3e50;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            height: 40px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.25);
        }

        .report-options {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            align-items: center;
            border-bottom: 1px solid #eaedf0;
            padding-bottom: 20px;
        }

        .report-options h3 {
            font-size: 15px;
            color: #64748b;
            font-weight: 600;
            margin-right: 10px;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            cursor: pointer;
            font-weight: 500;
            color: #334155;
            padding: 6px 12px;
            border-radius: 30px;
            transition: background 0.2s ease;
        }

        .radio-group label:hover {
            background: #f1f5f9;
        }

        .radio-group input[type="radio"] {
            accent-color: #3b82f6;
            width: 16px;
            height: 16px;
        }

        .table-container {
            border: 1px solid #eaedf0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            background: #f8fafc;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }

        th:last-child {
            text-align: right;
        }

        td {
            padding: 14px 15px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        td:last-child {
            text-align: right;
            font-weight: 600;
            color: #0f766e;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background: #f8fafc;
        }

        .summary-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 35px;
        }

        .summary-card {
            background: white;
            color: #334155;
            padding: 24px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #eaedf0;
            transition: transform 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .summary-card:nth-child(1) {
            border-top: 4px solid #3b82f6;
        }

        .summary-card:nth-child(2) {
            border-top: 4px solid #ef4444;
        }

        .summary-card:nth-child(3) {
            border-top: 4px solid #8b5cf6;
        }

        .summary-card:nth-child(4) {
            border-top: 4px solid #10b981;
        }

        .summary-card h3 {
            font-size: 14px;
            margin-bottom: 12px;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-card .value {
            font-size: 28px;
            font-weight: 700;
            margin-top: 8px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 25px;
        }

        .pagination a, .pagination span {
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            color: #475569;
            background: #f1f5f9;
            transition: background 0.2s ease;
        }

        .pagination a:hover {
            background: #e2e8f0;
        }

        .pagination .active {
            background: #3b82f6;
            color: white;
            font-weight: 600;
        }

        .loader {
            text-align: center;
            padding: 40px;
            font-size: 16px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="header">
            <h1>Sales Report</h1>
            <div class="header-buttons">
                <button class="close-btn" onclick="window.close();">&times;</button>
            </div>
        </div>
        <div class="content">
            <div class="filter-section">
                <form action="" method="GET">
                    <div class="form-group">
                        <label for="start-date">Start Date</label>
                        <input type="date" id="start-date" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                    </div>
                    <div class="form-group">
                        <label for="end-date">End Date</label>
                        <input type="date" id="end-date" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
                    </div>
                    <div class="form-group">
                        <label for="staff-select">Staff</label>
                        <select id="staff-select" name="staff_id">
                            <option value="">All Staff</option>
                            <?php foreach ($staffList as $staff): ?>
                                <option value="<?= htmlspecialchars($staff['account_id']) ?>" <?= $staff['account_id'] == $staff_id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($staff['email']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payment-type">Payment Type</label>
                        <select id="payment-type" name="payment_type">
                            <option value="">All</option>
                            <option value="cash" <?= $paymentType == 'cash' ? 'selected' : '' ?>>Cash</option>
                            <option value="card" <?= $paymentType == 'card' ? 'selected' : '' ?>>Card</option>
                            <option value="credit" <?= $paymentType == 'credit' ? 'selected' : '' ?>>Credit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="product-search">Product Search</label>
                        <input type="text" id="product-search" name="product_search" placeholder="Product name..." value="<?= htmlspecialchars($productSearch) ?>">
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </form>
            </div>

            <div class="summary-section">
                <div class="summary-card">
                    <h3>Total Sales</h3>
                    <div class="value">Rs <?= number_format($totalSale, 2) ?></div>
                </div>
                <div class="summary-card">
                    <h3>Card Balance</h3>
                    <div class="value">Rs <?= number_format($cardBalance, 2) ?></div>
                </div>
                <div class="summary-card">
                    <h3>Cash Balance</h3>
                    <div class="value">Rs <?= number_format($cashBalance, 2) ?></div>
                </div>
                <div class="summary-card">
                    <h3>Total Items Sold</h3>
                    <div class="value"><?= number_format($totalItems) ?></div>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Bill ID</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th>Date</th>
                            <th>Staff</th>
                            <th>Payment Method</th>
                        </tr>
                    </thead>
                    <tbody id="sales-data-body">
                        <!-- Data will be loaded here by AJAX -->
                    </tbody>
                </table>
                <div id="loader" class="loader">Loading data...</div>
            </div>

            <div class="pagination" id="pagination-container">
                <!-- Pagination links will be generated here -->
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalPages = <?= $totalPages ?>;
            let currentPage = <?= $page ?>;
            const filterParams = new URLSearchParams(window.location.search);

            function loadSalesData(page) {
                const loader = document.getElementById('loader');
                const tableBody = document.getElementById('sales-data-body');
                
                loader.style.display = 'block';
                tableBody.innerHTML = '';

                // Update the page parameter for the fetch request
                filterParams.set('page', page);

                fetch(`fetch_sales_data.php?${filterParams.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        loader.style.display = 'none';
                        let rows = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                rows += `
                                    <tr>
                                        <td>${item.bill_id}</td>
                                        <td>${item.product_name}</td>
                                        <td>${item.quantity}</td>
                                        <td>Rs ${parseFloat(item.price).toFixed(2)}</td>
                                        <td>Rs ${parseFloat(item.subtotal).toFixed(2)}</td>
                                        <td>${new Date(item.bill_time).toLocaleString()}</td>
                                        <td>${item.staff_email || 'N/A'}</td>
                                        <td>${item.payment_method || 'N/A'}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            rows = '<tr><td colspan="8" style="text-align:center; padding: 20px;">No sales data found for this period.</td></tr>';
                        }
                        tableBody.innerHTML = rows;
                    })
                    .catch(error => {
                        loader.style.display = 'none';
                        tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:red;">Error loading data: ${error.message}</td></tr>`;
                    });
            }

            function setupPagination() {
                const paginationContainer = document.getElementById('pagination-container');
                paginationContainer.innerHTML = '';

                if (totalPages <= 1) return;

                for (let i = 1; i <= totalPages; i++) {
                    const link = document.createElement('a');
                    link.href = '#';
                    link.innerText = i;
                    if (i === currentPage) {
                        link.classList.add('active');
                    }
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        currentPage = i; // Update current page
                        loadSalesData(i);
                        
                        // Update URL without reloading
                        const newUrl = new URL(window.location);
                        newUrl.searchParams.set('page', i);
                        window.history.pushState({path: newUrl.href}, '', newUrl.href);

                        // Update active class
                        document.querySelector('.pagination .active').classList.remove('active');
                        this.classList.add('active');
                    });
                    paginationContainer.appendChild(link);
                }
            }

            loadSalesData(currentPage);
            setupPagination();
        });
    </script>
</body>
</html>