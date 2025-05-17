<?php
require_once 'SessionManager.php';

// Initialize session manager
$session = SessionManager::getInstance();

// Get session information
$session_id = session_id();
$session_name = session_name();
$session_status = session_status();

// Get session data
$session_data = $_SESSION;

// Format the output
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; background-color: #f8f9fa; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
echo "<h2>Session Information</h2>";

echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Basic Session Info</h3>";
echo "<ul>";
echo "<li>Session ID: " . htmlspecialchars($session_id) . "</li>";
echo "<li>Session Name: " . htmlspecialchars($session_name) . "</li>";
echo "<li>Session Status: " . $session_status . "</li>";
echo "</ul>";
echo "</div>";

echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Session Data</h3>";
echo "<pre>";
print_r($session_data);
echo "</pre>";
echo "</div>";

echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Cookie Information</h3>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";
echo "</div>";

echo "</div>";
?> 