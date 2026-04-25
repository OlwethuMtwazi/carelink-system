<?php
// Start session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
