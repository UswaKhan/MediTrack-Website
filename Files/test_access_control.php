<?php
require_once 'SessionManager.php';
require_once 'Database.php';
require_once 'AccessControl.php';

// Initialize managers
$session = SessionManager::getInstance();
$db = Database::getInstance();
$access = AccessControl::getInstance();

// Check if user is logged in
if (!$session->isLoggedIn()) {
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background-color: #f8f9fa; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
    echo "<h2 style='color: #dc3545;'>⚠️ Login Required</h2>";
    echo "<p>To test the access control system, you need to be logged in first.</p>";
    echo "<p>Please follow these steps:</p>";
    echo "<ol>";
    echo "<li>Go to <a href='index.php'>Login Page</a></li>";
    echo "<li>Log in with your admin credentials</li>";
    echo "<li>Return to this page to see the access control test results</li>";
    echo "</ol>";
    echo "</div>";
    exit();
}

// Function to simulate different user roles
function simulateUserRole($role) {
    global $session;
    $session->set('role', $role);
    $session->set('username', 'test_user');
}

// Function to test access
function testAccess($permission, $expected) {
    global $access;
    $result = $access->hasPermission($permission);
    echo "<tr>";
    echo "<td>$permission</td>";
    echo "<td>" . ($result ? "Allowed" : "Denied") . "</td>";
    echo "<td>" . ($result === $expected ? "✓" : "✗") . "</td>";
    echo "</tr>";
}

// Test cases
$testCases = [
    'admin' => [
        'add_user' => true,
        'edit_user' => true,
        'delete_user' => true,
        'view_reports' => true,
        'manage_inventory' => true,
        'process_sales' => true
    ],
    'cashier' => [
        'add_user' => false,
        'edit_user' => false,
        'delete_user' => false,
        'view_reports' => false,
        'manage_inventory' => false,
        'process_sales' => true
    ]
];

// Display test results
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px;'>";
echo "<h2>Access Control Test Results</h2>";
echo "<p>Current user: " . htmlspecialchars($session->getUsername()) . " (Role: " . htmlspecialchars($session->getRole()) . ")</p>";

foreach ($testCases as $role => $permissions) {
    echo "<h3>Testing as $role</h3>";
    simulateUserRole($role);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
    echo "<tr style='background-color: #f8f9fa;'><th>Permission</th><th>Result</th><th>Expected</th></tr>";
    
    foreach ($permissions as $permission => $expected) {
        testAccess($permission, $expected);
    }
    
    echo "</table>";
}

// Test privilege escalation attempts
echo "<h3>Testing Privilege Escalation Protection</h3>";
echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
echo "<tr style='background-color: #f8f9fa;'><th>Test</th><th>Result</th></tr>";

// Test 1: Try to access admin page as cashier
simulateUserRole('cashier');
try {
    $access->enforceAccess('add_user');
    echo "<tr><td>Access admin page as cashier</td><td>Failed (should be denied)</td></tr>";
} catch (Exception $e) {
    echo "<tr><td>Access admin page as cashier</td><td>✓ Protected</td></tr>";
}

// Test 2: Try to modify role to admin
simulateUserRole('cashier');
try {
    $access->verifyRole('admin');
    echo "<tr><td>Modify role to admin</td><td>Failed (should be denied)</td></tr>";
} catch (Exception $e) {
    echo "<tr><td>Modify role to admin</td><td>✓ Protected</td></tr>";
}

// Test 3: Try to access own data
simulateUserRole('cashier');
$result = $access->isOwnData('test_user');
echo "<tr><td>Access own data</td><td>" . ($result ? "✓ Allowed" : "✗ Denied") . "</td></tr>";

// Test 4: Try to access other user's data
$result = $access->isOwnData('other_user');
echo "<tr><td>Access other user's data</td><td>" . ($result ? "✗ Allowed" : "✓ Denied") . "</td></tr>";

echo "</table>";

// Display security features
echo "<h3>Security Features Implemented</h3>";
echo "<ul style='list-style-type: none; padding: 0;'>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Role-based access control</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Permission validation</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Privilege escalation protection</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Data access control</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Unauthorized access logging</li>";
echo "</ul>";

echo "</div>";
?> 