<?php
require_once 'SessionManager.php';

// Initialize session manager
$session = SessionManager::getInstance();

echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; background-color: #f8f9fa; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
echo "<h2>Session Security Test Results</h2>";

// Test 1: Session Cookie Security
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Test 1: Session Cookie Security</h3>";
$cookie_params = session_get_cookie_params();
echo "<ul>";
echo "<li>HTTP-only: " . ($cookie_params['httponly'] ? "✓ Enabled" : "✗ Disabled") . "</li>";
echo "<li>Secure: " . ($cookie_params['secure'] ? "✓ Enabled" : "✗ Disabled") . "</li>";
echo "<li>SameSite: " . ($cookie_params['samesite'] === 'Strict' ? "✓ Strict" : "✗ Not Strict") . "</li>";
echo "<li>Session Name: " . session_name() . "</li>";
echo "</ul>";
echo "</div>";

// Test 2: Session Fingerprint
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Test 2: Session Fingerprint</h3>";
echo "<p>Your session fingerprint: " . $session->get('fingerprint') . "</p>";
echo "<p>Components used:</p>";
echo "<ul>";
echo "<li>User Agent: " . htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'Not set') . "</li>";
echo "<li>IP Address: " . htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'Not set') . "</li>";
echo "<li>Accept Language: " . htmlspecialchars($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'Not set') . "</li>";
echo "</ul>";
echo "</div>";

// Test 3: Session Regeneration
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Test 3: Session Regeneration</h3>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Last regeneration: " . ($session->get('last_regeneration') ? date('Y-m-d H:i:s', $session->get('last_regeneration')) : 'Not set') . "</p>";
echo "<p>Expires at: " . ($session->get('expires_at') ? date('Y-m-d H:i:s', $session->get('expires_at')) : 'Not set') . "</p>";
echo "</div>";

// Test 4: Session Hijacking Simulation
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Test 4: Session Hijacking Simulation</h3>";
echo "<p>To test session hijacking protection:</p>";
echo "<ol>";
echo "<li>Open this page in a different browser or incognito window</li>";
echo "<li>Try to copy the session cookie and use it in the other browser</li>";
echo "<li>The session should be invalidated due to different fingerprint</li>";
echo "</ol>";
echo "</div>";

// Test 5: Session Expiration
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Test 5: Session Expiration</h3>";
$time_left = $session->get('expires_at') - time();
echo "<p>Time until session expires: " . floor($time_left / 60) . " minutes and " . ($time_left % 60) . " seconds</p>";
echo "</div>";

// Test 6: Security Headers
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Test 6: Security Headers</h3>";
$headers = getallheaders();
echo "<ul>";
echo "<li>X-Frame-Options: " . ($headers['X-Frame-Options'] ?? 'Not set') . "</li>";
echo "<li>X-XSS-Protection: " . ($headers['X-XSS-Protection'] ?? 'Not set') . "</li>";
echo "<li>X-Content-Type-Options: " . ($headers['X-Content-Type-Options'] ?? 'Not set') . "</li>";
echo "</ul>";
echo "</div>";

// Instructions for Manual Testing
echo "<div style='margin-top: 30px; padding: 15px; background-color: #e9ecef; border-radius: 5px;'>";
echo "<h3>How to Test Session Security</h3>";
echo "<ol>";
echo "<li><strong>Test Session Cookie Security:</strong><br>";
echo "Open browser dev tools (F12) → Application → Cookies<br>";
echo "Verify that the session cookie has HttpOnly and Secure flags</li>";

echo "<li><strong>Test Session Hijacking Protection:</strong><br>";
echo "1. Log in to your application<br>";
echo "2. Open the same page in an incognito window<br>";
echo "3. Try to copy the session cookie to the incognito window<br>";
echo "4. The session should be invalidated</li>";

echo "<li><strong>Test Session Expiration:</strong><br>";
echo "1. Log in to your application<br>";
echo "2. Wait for 30 minutes without activity<br>";
echo "3. Try to access a protected page<br>";
echo "4. You should be redirected to login</li>";

echo "<li><strong>Test Session Regeneration:</strong><br>";
echo "1. Log in to your application<br>";
echo "2. Note the session ID<br>";
echo "3. Wait for 30 minutes<br>";
echo "4. Check if the session ID has changed</li>";
echo "</ol>";
echo "</div>";

echo "</div>";
?> 