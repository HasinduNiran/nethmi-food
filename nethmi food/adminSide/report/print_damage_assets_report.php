<?php
// print_damage_assets_report.php
require_once "../config.php"; 

try {
    if (!$link) {
        throw new Exception("Database connection failed.");
    }

    $search = $_GET['search'] ?? '';
    $startDate = $_GET['start_date'] ?? '';
    $endDate = $_GET['end_date'] ?? '';

    $query = "SELECT 
                a.asset_name, 
                d.damage_qty, 
                d.damage_description, 
                d.damage_date, 
                d.created_at
              FROM damages d
              INNER JOIN assets a ON d.asset_id = a.id
              WHERE 1=1";

    $params = [];
    $types = '';

    if (!empty($search)) {
        $query .= " AND a.asset_name LIKE ?";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $types .= 's';
    }
    if (!empty($startDate) && !empty($endDate)) {
        $query .= " AND d.damage_date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
        $types .= 'ss';
    }

    $stmt = $link->prepare($query);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $link->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $link->close();
} catch (Exception $e) {
    $link->close();
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Damage Assets Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .no-data {
            text-align: center;
            padding: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <h1>Damage Assets Report</h1>
    
    <?php if (!empty($search) || (!empty($startDate) && !empty($endDate))): ?>
        <p>
            <?php
            $filters = [];
            if (!empty($search)) $filters[] = "Search: $search";
            if (!empty($startDate) && !empty($endDate)) $filters[] = "Date Range: $startDate to $endDate";
            echo "Filters: " . implode(", ", $filters);
            ?>
        </p>
    <?php endif; ?>

    <?php if (empty($data)): ?>
        <p class="no-data">No damaged assets found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Asset Name</th>
                    <th>Damage Quantity</th>
                    <th>Damage Description</th>
                    <th>Damage Date</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['asset_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['damage_qty']); ?></td>
                        <td><?php echo htmlspecialchars($item['damage_description'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($item['damage_date']); ?></td>
                        <td><?php echo htmlspecialchars($item['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <button class="no-print" onclick="window.close()">Close</button>
</body>
</html>