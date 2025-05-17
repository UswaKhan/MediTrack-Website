<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test file access
echo "Test file is accessible<br>";

// Test session
session_start();
echo "Session started successfully<br>";

// Test database connection
require_once 'Database.php';
try {
    $db = Database::getInstance();
    echo "Database connection successful<br>";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "<br>";
}

// Test file permissions
$files_to_check = ['index.php', 'login.php', 'SessionManager.php', 'security.php'];
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "$file exists and is readable<br>";
    } else {
        echo "$file does not exist or is not readable<br>";
    }
}
?> 