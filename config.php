<?php
// Database configuration
define('DB_HOST', 'YOUR_DBHOST');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'edulink_db');
define('DB_PORT', 3306);

// OpenAI API configuration
define('OPENAI_API_KEY', 'YOUR_API_KEY_HERE'); // Replace with your actual API key
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');

// Session configuration
session_start();

// Database connection function
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Security functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Set timezone
date_default_timezone_set('Asia/Kuala_Lumpur');
?>