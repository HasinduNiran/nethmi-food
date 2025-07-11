<?php
session_start();
include 'config.php'; // <-- adjust path as needed

if (!isset($conn)) {
    die("Database connection not established.");
}

// Fetch all users
$users = $conn->query("SELECT staff_id AS id, staff_name AS username FROM staffs");


// If a user is selected
$selected_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Sidebar items as an array (menu_key => menu_label)
$sidebar_items = [
    'dashboard' => 'Dashboard',
    'pos' => 'POS',
    'bills' => 'Bills',
    'pos_android' => 'POS Android',
    'table' => 'Table',
    'grn' => 'GRN',
    'menu' => 'Menu',
    'members' => 'Members',
    'staff' => 'Staff',
    'suppliers' => 'Suppliers',
    'accounts' => 'View All Accounts',
    'inventory' => 'Inventory',
    'assets' => 'Assets',
    'statistics' => 'Revenue Statistics',
    'reports' => 'Reports',
    'profiles' => 'Customer Profiles',
    'void_orders' => 'Void Orders',
    'cash_disbursements' => 'Cash Disbursements',
    'cash_receipts' => 'Cash Receipts',
];

// Fetch existing permissions for selected user
$user_permissions = [];
if ($selected_user_id) {
    $res = $conn->query("SELECT menu_key, is_allowed FROM user_permissions WHERE user_id = $selected_user_id");
    while ($row = $res->fetch_assoc()) {
        $user_permissions[$row['menu_key']] = $row['is_allowed'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($sidebar_items as $key => $label) {
        $is_allowed = isset($_POST['permissions'][$key]) ? 1 : 0;
        $check = $conn->query("SELECT * FROM user_permissions WHERE user_id = $selected_user_id AND menu_key = '$key'");
        if ($check->num_rows) {
            $conn->query("UPDATE user_permissions SET is_allowed = $is_allowed WHERE user_id = $selected_user_id AND menu_key = '$key'");
        } else {
            $conn->query("INSERT INTO user_permissions (user_id, menu_key, is_allowed) VALUES ($selected_user_id, '$key', $is_allowed)");
        }
    }
    header("Location: user_permissions.php?user_id=$selected_user_id&success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Permissions</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h2>User Permissions</h2>

        <form method="get" action="">
            <label for="user_id">Select User:</label>
            <select name="user_id" id="user_id" onchange="this.form.submit()">
                <option value="">-- Select User --</option>
                <?php while ($user = $users->fetch_assoc()) { ?>
                    <option value="<?= $user['id'] ?>" <?= $user['id'] == $selected_user_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['username']) ?>
                    </option>
                <?php } ?>
            </select>
        </form>

        <?php if ($selected_user_id): ?>
        <form method="post">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Sidebar Item</th>
                        <th>Allow</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sidebar_items as $key => $label): ?>
                    <tr>
                        <td><?= htmlspecialchars($label) ?></td>
                        <td>
                            <input type="checkbox" name="permissions[<?= $key ?>]" value="1"
                                <?= isset($user_permissions[$key]) && $user_permissions[$key] ? 'checked' : '' ?>>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Save Permissions</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
