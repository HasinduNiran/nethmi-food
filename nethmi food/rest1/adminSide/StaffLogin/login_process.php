<?php
session_start(); 

require_once "config.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $provided_account_id = $_POST['account_id'];
    $provided_password = $_POST['password'];
    
    $query = "SELECT * FROM accounts WHERE account_id = '$provided_account_id'";
    $result = $link->query($query);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        if ($provided_password === $stored_password) {
            
        $staff_query = "SELECT * FROM staffs WHERE account_id = '$provided_account_id'";
        $staff_result = $link->query($staff_query);

        if ($staff_result->num_rows === 1) {
            $staff_row = $staff_result->fetch_assoc();
            $logged_staff_name = $staff_row['staff_name']; 
            $logged_staff_roll = $staff_row['role']; 
     
            $_SESSION['logged_account_id'] = $provided_account_id;
            $_SESSION['logged_staff_name'] = $logged_staff_name;
            $_SESSION['roll'] = $logged_staff_roll;
            
            header("Location: ../panel/pos-panel.php");
            exit;
            
        } else {
            
            $message = "Staff ID not found.<br>Please try again to choose a correct Staff ID.";
            $iconClass = "fa-times-circle";
            $cardClass = "alert-danger";
            $bgColor = "#FFA7A7"; 
            $direction = "login.php"; 
            }      
            
        } else {
            $message = "Incorrect password.<br>Please try again to type your password.";
            $iconClass = "fa-times-circle";
            $cardClass = "alert-danger";
            $bgColor = "#FFA7A7"; 
            $direction = "login.php"; 
        }
    } else {
        $message = "Staff ID not found.<br>Please try again to choose a correct Staff ID.";
        $iconClass = "fa-times-circle";
        $cardClass = "alert-danger";
        $bgColor = "#FFA7A7";
        $direction = "login.php"; 
    }
}


$link->close();
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
    <style>
        
        body {
            text-align: center;
            padding: 40px 0;
            background: #EBF0F5;
        }
        h1 {
            color: #88B04B;
            font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
            font-weight: 900;
            font-size: 40px;
            margin-bottom: 10px;
        }
        p {
            color: #404F5E;
            font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
            font-size: 20px;
            margin: 0;
        }
        i.checkmark {
            color: #9ABC66;
            font-size: 100px;
            line-height: 200px;
            margin-left: -15px;
        }
        .card {
            background: white;
            padding: 60px;
            border-radius: 4px;
            box-shadow: 0 2px 3px #C8D0D8;
            display: inline-block;
            margin: 0 auto;
        }
        
        .alert-success {
            
            background-color: <?php echo $bgColor; ?>;
        }
        .alert-success i {
            color: #5DBE6F; 
        }
        .alert-danger {
            
            background-color: #FFA7A7; 
        }
        .alert-danger i {
            color: #F25454; 
        }
        .custom-x {
            color: #F25454;
            font-size: 100px;
            line-height: 200px;
        }
            .alert-box {
            max-width: 300px;
            margin: 0 auto;
        }

        .alert-icon {
            padding-bottom: 20px;
        }
    
    </style>
</head>
<body>
    <div class="card <?php echo $cardClass; ?>" style="display: none;">
        <div style="border-radius: 200px; height: 200px; width: 200px; background: #F8FAF5; margin: 0 auto;">
            <?php if ($iconClass === 'fa-check-circle'): ?>
                <i class="checkmark">✓</i>
            <?php else: ?>
                <i class="custom-x" style="font-size: 100px; line-height: 200px;">✘</i>
            <?php endif; ?>
        </div>
        <h1><?php echo ($cardClass === 'alert-success') ? 'Success' : 'Error'; ?></h1>
        <p><?php echo $message; ?></p>
    </div>

    <div style="text-align: center; margin-top: 20px;">Redirecting back in <span id="countdown">3</span></div>

    <script>
        
        var direction = "<?php echo $direction; ?>";
        
        function showPopup() {
            var messageCard = document.querySelector(".card");
            messageCard.style.display = "block";

            var i = 3;
            var countdownElement = document.getElementById("countdown");
            var countdownInterval = setInterval(function() {
                i--;
                countdownElement.textContent = i;
                if (i <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = direction;
                }
            }, 1000); // 1000 milliseconds = 1 second
        }

        // Show the message card and start the countdown when the page is loaded
        window.onload = showPopup;

        // Function to hide the message card after a delay
        function hidePopup() {
            var messageCard = document.querySelector(".card");
            messageCard.style.display = "none";
            // Redirect to another page after hiding the pop-up (adjust the delay as needed)
            setTimeout(function () {
                window.location.href = direction; // Replace with your desired URL
            }, 3000); // 3000 milliseconds = 3 seconds
        }

        // Hide the message card after 3 seconds (adjust the delay as needed)
        setTimeout(hidePopup, 3000);
    </script>
</body>
</html>