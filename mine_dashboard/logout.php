<?php
session_start();

// Destroy all session data
$_SESSION = [];
session_destroy();

// Redirect to login page after logout
header("Location: ../index.php");
exit;
?>