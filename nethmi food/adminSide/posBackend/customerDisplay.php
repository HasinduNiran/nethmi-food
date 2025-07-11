<?php
session_start();
$display_amount = isset($_SESSION['display_amount']) ? $_SESSION['display_amount'] : 0.00;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Display</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: black;
            color: white;
            font-size: 72px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div>
        <p>Rs. <?php echo number_format($display_amount, 2); ?></p>
    </div>
</body>
<script>
    setTimeout(() => {
        window.location.reload();
    }, 1000); // Refresh every second
</script>
</html>
