<?php

include '../inc/dashHeader.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Date for Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 80px;
        }

        form {
            display: inline-block;
            text-align: left;
            background-color: #f9f9f9;
            padding: 50px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        label {
            font-weight: bold;
        }

        input[type="date"] {
            padding: 5px;
            margin: 10px 0;
            width: 100%;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="row">
        <div class="col-12">
            <h3 style="margin-top: 80px; margin-bottom: 30px;">Generate Kitchen Sales Report</h3>
            <form action="daysellreport.php" method="get">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" required>
                <button type="submit">Generate Kitchen Report</button>
            </form>
        </div>
        <div class="col-12 mt-5">
            <h3>Generate Bar Sales Report</h3>
            <form action="daysellreportbar.php" method="get">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" required>
                <button type="submit">Generate Bar Report</button>
            </form>
        </div>
    </div>

</body>

</html>