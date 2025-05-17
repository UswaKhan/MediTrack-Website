<?php
require_once 'SessionManager.php';

$session = SessionManager::getInstance();

echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background-color: #f8f9fa; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
echo "<h2>Session Test Results</h2>";

// Test session status
echo "<h3>Session Status</h3>";
echo "<ul>";
echo "<li>Session ID: " . session_id() . "</li>";
echo "<li>Session Name: " . session_name() . "</li>";
echo "<li>Session Status: " . session_status() . "</li>";
echo "</ul>";

// Test session variables
echo "<h3>Session Variables</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Test login status
echo "<h3>Login Status</h3>";
echo "<p>Is logged in: " . ($session->isLoggedIn() ? "Yes" : "No") . "</p>";
echo "<p>Username: " . ($session->getUsername() ?? "Not set") . "</p>";
echo "<p>Role: " . ($session->getRole() ?? "Not set") . "</p>";

// Test session fingerprint
echo "<h3>Session Fingerprint</h3>";
echo "<p>Fingerprint: " . ($session->get('fingerprint') ?? "Not set") . "</p>";

// Test session regeneration
echo "<h3>Session Regeneration</h3>";
echo "<p>Last regeneration: " . ($session->get('last_regeneration') ? date('Y-m-d H:i:s', $session->get('last_regeneration')) : "Not set") . "</p>";

echo "</div>";
?> 