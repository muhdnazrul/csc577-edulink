<?php
require_once 'config.php';

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home page
header('Location: index.php?message=logged_out');
exit();
?>