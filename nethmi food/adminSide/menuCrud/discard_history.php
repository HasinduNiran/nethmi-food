<?php
session_start();
include '../inc/dashHeader.php';
require_once "../config.php";

// Query to get discarded items
$conn = $link;
$query = "SELECT d.*, u.username 
          FROM discarded_items d 
          LEFT JOIN users u ON d.user_id = u.id 
          ORDER BY d.discard_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Discard History</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            padding: 20px;
            margin-left: 220px;
        }
        
        .wrapper {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
        }
        
        .items-table-wrapper {
            max-height: 600px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .items-table th {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .items-table tr:hover {
            background-color: #e9ecef;
        }
        
        .search-container {
            margin-bottom: 20px;
        }
        
        .search-input {
            max-width: 300px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="wrapper">
            <h3>Discard History</h3>
            <div class="search-container">
                <input type="text" id="searchInput" class="form-control search-input" placeholder="Search discarded items...">
            </div>
            <div class="items-table-wrapper">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item ID</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Reason</th>
                            <th>Date</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['item_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                echo "<td>" . number_format($row['quantity_discarded'], 2) . "</td>";
                                echo "<td>" . htmlspecialchars($row['discard_reason']) . "</td>";
                                echo "<td>" . date('Y-m-d H:i', strtotime($row['discard_date'])) . "</td>";
                                echo "<td>" . htmlspecialchars($row['username'] ?? 'Unknown') . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No discard history found</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        let timeout = null;
        
        searchInput.addEventListener('input', function(e) {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                const searchValue = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('#itemsTableBody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if(text.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }, 300);
        });
    });
    </script>
</body>
</html>
