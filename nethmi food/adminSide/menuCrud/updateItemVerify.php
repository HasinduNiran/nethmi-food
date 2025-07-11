<?php
require_once "../config.php";

// Check if 'id' is set and valid
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../panel/menu-panel.php");
    exit();
}

$menu_id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Replace these credentials with your actual admin credentials
    $correct_admin_id = "99999"; // Example Admin ID
    $correct_password = "12345"; // Example Password

    // Fetch user input
    $provided_account_id = $_POST['admin_id'];
    $provided_password = $_POST['password'];

    // Validate admin credentials
    if ($provided_account_id === $correct_admin_id && $provided_password === $correct_password) {
        // Successful authentication - Redirect to update page
        header("Location: ../menuCrud/updateItem.php?id=" . $menu_id);
        exit();
    } else {
        $error_message = "Incorrect Admin ID or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../css/verifyAdmin.css" rel="stylesheet" />
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #000000;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background: #DE3163;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <h5 class="text-center">Admin Credentials Required to Edit Item</h5>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <div class="form-group">
                <label>Admin ID</label>
                <input type="text" name="admin_id" class="form-control" placeholder="Enter Admin ID" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter Admin Password" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Login</button>
                <a href="../panel/menu-panel.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
