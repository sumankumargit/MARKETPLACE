<?php


// session_start();

require_once '../../config/config.php';

// Destroy all sessions
session_unset();
session_destroy();

// Redirect to login page
header("Location: ".$base_url."auth/Login/");
exit;
?>
