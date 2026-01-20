<?php
// Test file to verify system configuration
require_once 'config.php';

echo "<h1>EduLink System Test</h1>";

// Test 1: Database Connection
echo "<h2>1. Database Connection Test</h2>";
try {
    $conn = getDBConnection();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test if tables exist
    $tables = ['users', 'profiles', 'recommendations'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Table '$table' exists</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' missing</p>";
        }
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

// Test 2: Configuration Check
echo "<h2>2. Configuration Check</h2>";

if (defined('OPENAI_API_KEY') && OPENAI_API_KEY !== 'YOUR_OPENAI_API_KEY') {
    echo "<p style='color: green;'>✅ OpenAI API key is configured</p>";
} else {
    echo "<p style='color: orange;'>⚠️ OpenAI API key needs to be set in config.php</p>";
}

if (defined('DB_NAME') && DB_NAME !== '') {
    echo "<p style='color: green;'>✅ Database name is configured</p>";
} else {
    echo "<p style='color: red;'>❌ Database name not configured</p>";
}

// Test 3: PHP Extensions
echo "<h2>3. PHP Extensions Check</h2>";

$required_extensions = ['mysqli', 'curl', 'json'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✅ $ext extension loaded</p>";
    } else {
        echo "<p style='color: red;'>❌ $ext extension missing</p>";
    }
}

// Test 4: File Permissions
echo "<h2>4. File Structure Check</h2>";

$required_files = [
    'index.php',
    'register.php', 
    'login.php',
    'dashboard.php',
    'process.php',
    'results.php',
    'css/style.css',
    'js/main.js'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file exists</p>";
    } else {
        echo "<p style='color: red;'>❌ $file missing</p>";
    }
}

// Test 5: Session Test
echo "<h2>5. Session Test</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p style='color: green;'>✅ Sessions are working</p>";
} else {
    echo "<p style='color: red;'>❌ Session not started</p>";
}

echo "<h2>Test Complete</h2>";
echo "<p>If all tests pass, your EduLink system is ready to use!</p>";
echo "<p><a href='index.php'>Go to EduLink Homepage</a></p>";

// Clean up
if (file_exists(__FILE__)) {
    echo "<p style='color: orange;'>⚠️ Remember to delete this test file (test_connection.php) after testing for security.</p>";
}
?>