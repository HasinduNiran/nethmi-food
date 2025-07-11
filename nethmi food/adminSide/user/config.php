<?php
$servername = "localhost";
$username = "nexaraso_nethmi";
$password = "nexaraso_nethmi";
$database = "nexaraso_nethmi";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
