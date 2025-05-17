<?php
require_once 'SessionManager.php';
require_once 'AccessControl.php';

// Initialize managers
$session = SessionManager::getInstance();
$access = AccessControl::getInstance();

// Get current user info
$username = $session->getUsername();
$role = $session->getRole();
$isLoggedIn = $session->isLoggedIn();

// Format the output
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; background-color: #f8f9fa; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
echo "<h2>Role-Based Access Control (RBAC) Information</h2>";

// Display user information
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>User Information</h3>";
echo "<ul>";
echo "<li><strong>Username:</strong> " . htmlspecialchars($username ?? 'Not logged in') . "</li>";
echo "<li><strong>Role:</strong> " . htmlspecialchars($role ?? 'No role') . "</li>";
echo "<li><strong>Login Status:</strong> " . ($isLoggedIn ? 'Logged In' : 'Not Logged In') . "</li>";
echo "</ul>";
echo "</div>";

// Display role permissions
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Current Role Permissions</h3>";
if ($isLoggedIn) {
    $permissions = $access->getUserPermissions();
    echo "<table style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background-color: #f8f9fa;'><th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Permission</th><th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Status</th></tr>";
    foreach ($permissions as $permission => $allowed) {
        echo "<tr>";
        echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($permission) . "</td>";
        echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . ($allowed ? "✓ Allowed" : "✗ Denied") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Please log in to view permissions.</p>";
}
echo "</div>";

// Test different permissions
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Permission Tests</h3>";
if ($isLoggedIn) {
    $testPermissions = [
        'view_dashboard',
        'add_medicine',
        'edit_medicine',
        'delete_medicine',
        'view_medicines',
        'add_user',
        'edit_user',
        'delete_user',
        'view_users',
        'view_sales',
        'view_customers',
        'manage_inventory'
    ];

    echo "<table style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background-color: #f8f9fa;'><th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Permission</th><th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Access</th></tr>";
    
    foreach ($testPermissions as $permission) {
        $hasAccess = $access->hasPermission($permission);
        echo "<tr>";
        echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($permission) . "</td>";
        echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . ($hasAccess ? "✓ Allowed" : "✗ Denied") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Please log in to test permissions.</p>";
}
echo "</div>";

// Role comparison
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>Role Comparison</h3>";
echo "<table style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background-color: #f8f9fa;'>";
echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Permission</th>";
echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Admin</th>";
echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Cashier</th>";
echo "</tr>";

$permissions = [
    'view_dashboard' => ['admin' => true, 'cashier' => true],
    'add_medicine' => ['admin' => true, 'cashier' => false],
    'edit_medicine' => ['admin' => true, 'cashier' => false],
    'delete_medicine' => ['admin' => true, 'cashier' => false],
    'view_medicines' => ['admin' => true, 'cashier' => true],
    'add_user' => ['admin' => true, 'cashier' => false],
    'edit_user' => ['admin' => true, 'cashier' => false],
    'delete_user' => ['admin' => true, 'cashier' => false],
    'view_users' => ['admin' => true, 'cashier' => false],
    'view_sales' => ['admin' => true, 'cashier' => true],
    'view_customers' => ['admin' => true, 'cashier' => true],
    'manage_inventory' => ['admin' => true, 'cashier' => false]
];

foreach ($permissions as $permission => $roles) {
    echo "<tr>";
    echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($permission) . "</td>";
    echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . ($roles['admin'] ? "✓" : "✗") . "</td>";
    echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . ($roles['cashier'] ? "✓" : "✗") . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// Test instructions
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>How to Test RBAC</h3>";
echo "<ol>";
echo "<li>Log in as an admin user</li>";
echo "<li>Check your permissions</li>";
echo "<li>Log out</li>";
echo "<li>Log in as a cashier</li>";
echo "<li>Compare the permissions</li>";
echo "<li>Try accessing restricted pages</li>";
echo "</ol>";
echo "</div>";

echo "</div>";
?> 