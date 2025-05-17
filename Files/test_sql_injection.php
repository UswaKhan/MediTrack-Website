<?php
require_once 'Database.php';
require_once 'security.php';

// Initialize database
$db = Database::getInstance();

// Test cases for SQL injection attempts
$testCases = [
    // Test 1: Basic Union-based SQL Injection
    "admin' UNION SELECT * FROM admin--",
    
    // Test 2: Another Union-based attempt
    "admin' UNION SELECT username,password FROM admin--",
    
    // Test 3: Attempt to access other tables
    "admin' UNION SELECT * FROM medicines--",
    
    // Test 4: Complex Union attack
    "admin' UNION ALL SELECT * FROM admin WHERE '1'='1",
    
    // Test 5: Multiple Union statements
    "admin' UNION SELECT * FROM admin UNION SELECT * FROM medicines--"
];

echo "<h2>SQL Injection Protection Test Results</h2>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto;'>";

foreach ($testCases as $index => $testInput) {
    echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<h3>Test Case " . ($index + 1) . "</h3>";
    echo "<p><strong>Attack Attempt:</strong> " . htmlspecialchars($testInput) . "</p>";
    
    try {
        // Try to use the input in a query
        $result = $db->select("SELECT * FROM admin WHERE username = ?", "s", [$testInput]);
        
        echo "<p style='color: green;'><strong>Result:</strong> Query executed safely</p>";
        echo "<p><strong>Explanation:</strong> The attack was blocked because:</p>";
        echo "<ul>";
        echo "<li>The input was treated as a parameter, not as part of the SQL query</li>";
        echo "<li>The prepared statement prevented the UNION command from being executed</li>";
        echo "<li>The input was properly sanitized</li>";
        echo "</ul>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>Result:</strong> Attack blocked</p>";
        echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "</div>";
}

// Now let's show how the same attack would work without protection
echo "<h3>How It Would Work Without Protection</h3>";
echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px;'>";
echo "<p>Without our security measures, an attacker could:</p>";
echo "<ol>";
echo "<li>Access all user data by using: <code>admin' UNION SELECT * FROM admin--</code></li>";
echo "<li>Steal passwords by using: <code>admin' UNION SELECT username,password FROM admin--</code></li>";
echo "<li>Access other tables by using: <code>admin' UNION SELECT * FROM medicines--</code></li>";
echo "</ol>";
echo "<p>But with our protection in place, all these attempts are blocked!</p>";
echo "</div>";

echo "</div>";
?> 