<?php
require_once 'SessionManager.php';

// Initialize session manager
$session = SessionManager::getInstance();

// Get current fingerprint components
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Not set';
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Not set';
$current_fingerprint = hash('sha256', $user_agent . $ip_address);

// Get stored fingerprint
$stored_fingerprint = $_SESSION['fingerprint'] ?? 'Not set';

// Format the output
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; background-color: #f8f9fa; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
echo "<h2>Session Fingerprint Information</h2>";

// Display fingerprint components
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Fingerprint Components</h3>";
echo "<ul>";
echo "<li><strong>User Agent:</strong> " . htmlspecialchars($user_agent) . "</li>";
echo "<li><strong>IP Address:</strong> " . htmlspecialchars($ip_address) . "</li>";
echo "</ul>";
echo "</div>";

// Display fingerprint hashes
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Fingerprint Hashes</h3>";
echo "<ul>";
echo "<li><strong>Current Fingerprint:</strong> " . htmlspecialchars($current_fingerprint) . "</li>";
echo "<li><strong>Stored Fingerprint:</strong> " . htmlspecialchars($stored_fingerprint) . "</li>";
echo "</ul>";
echo "</div>";

// Display validation status
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Fingerprint Validation</h3>";
if ($current_fingerprint === $stored_fingerprint) {
    echo "<p style='color: green;'>✓ Fingerprints match - Session is valid</p>";
} else {
    echo "<p style='color: red;'>✗ Fingerprints don't match - Session might be hijacked</p>";
}
echo "</div>";

// Test instructions
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>How to Test Fingerprinting</h3>";
echo "<ol>";
echo "<li>Open this page in your normal browser</li>";
echo "<li>Note the current fingerprint</li>";
echo "<li>Open the same page in an incognito window</li>";
echo "<li>Compare the fingerprints - they should be different</li>";
echo "<li>Try to copy the session cookie to the incognito window</li>";
echo "<li>The session should be invalidated due to different fingerprint</li>";
echo "</ol>";
echo "</div>";

// Session regeneration info
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Session Regeneration</h3>";
echo "<p>Last regeneration: " . (isset($_SESSION['last_regeneration']) ? date('Y-m-d H:i:s', $_SESSION['last_regeneration']) : 'Not set') . "</p>";
echo "<p>Next regeneration in: " . (isset($_SESSION['last_regeneration']) ? 
    ceil((30 * 60 - (time() - $_SESSION['last_regeneration'])) / 60) . " minutes" : 'Not set') . "</p>";
echo "</div>";

echo "</div>";
?> 