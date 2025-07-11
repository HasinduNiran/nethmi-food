<?php
session_start();

// Serve the display amount stored in session
echo isset($_SESSION['display_amount']) ? number_format($_SESSION['display_amount'], 2) : '0.00';
?>
