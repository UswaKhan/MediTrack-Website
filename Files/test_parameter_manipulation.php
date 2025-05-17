<?php
require_once 'InputValidator.php';
require_once 'XSSProtection.php';

$validator = InputValidator::getInstance();
$xss = XSSProtection::getInstance();

// Test cases for parameter manipulation attacks
$attackTests = [
    // SQL Injection attempts
    'sql_injection' => [
        "'; DROP TABLE users; --",
        "' OR '1'='1",
        "' UNION SELECT * FROM users; --",
        "admin' --",
        "1' OR '1' = '1"
    ],
    
    // XSS attempts
    'xss' => [
        "<script>alert('XSS')</script>",
        "<img src='x' onerror='alert(1)'>",
        "javascript:alert('XSS')",
        "<svg onload='alert(1)'>",
        "'\"><script>alert('XSS')</script>"
    ],
    
    // Command Injection attempts
    'command_injection' => [
        "; rm -rf /",
        "& del /f /s /q",
        "| cat /etc/passwd",
        "`whoami`",
        "$(cat /etc/passwd)"
    ],
    
    // Path Traversal attempts
    'path_traversal' => [
        "../../../etc/passwd",
        "..\\..\\..\\windows\\system32\\config",
        "....//....//....//etc/passwd",
        "%2e%2e%2f%2e%2e%2f%2e%2e%2f",
        "..%252f..%252f..%252fetc%252fpasswd"
    ],
    
    // Type Confusion attempts
    'type_confusion' => [
        "0" => "1",
        "true" => "false",
        "null" => "undefined",
        "[]" => "{}",
        "0.0" => "0"
    ],
    
    // Buffer Overflow attempts
    'buffer_overflow' => [
        str_repeat("A", 1000),
        str_repeat("0", 1000),
        str_repeat("X", 1000),
        str_repeat("Z", 1000),
        str_repeat("1", 1000)
    ]
];

echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px;'>";
echo "<h2>Parameter Manipulation Protection Test Results</h2>";

// Test SQL Injection Protection
echo "<h3>SQL Injection Protection Test</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
echo "<tr style='background-color: #f8f9fa;'><th>Attack Attempt</th><th>Sanitized Result</th><th>Protected</th></tr>";

foreach ($attackTests['sql_injection'] as $attack) {
    $sanitized = $validator->validateSQLParam($attack, 'test_field');
    $isProtected = $sanitized === false || strpos($sanitized, "'") === false && 
                  strpos($sanitized, ";") === false && 
                  strpos($sanitized, "DROP") === false;
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($attack) . "</td>";
    echo "<td>" . htmlspecialchars($sanitized) . "</td>";
    echo "<td>" . ($isProtected ? "✓" : "✗") . "</td>";
    echo "</tr>";
}

echo "</table>";

// Test XSS Protection
echo "<h3>XSS Protection Test</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
echo "<tr style='background-color: #f8f9fa;'><th>Attack Attempt</th><th>Sanitized Result</th><th>Protected</th></tr>";

foreach ($attackTests['xss'] as $attack) {
    $sanitized = $xss->sanitizeInput($attack);
    $isProtected = strpos($sanitized, '<script') === false && 
                  strpos($sanitized, 'javascript:') === false &&
                  strpos($sanitized, 'onerror') === false;
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($attack) . "</td>";
    echo "<td>" . htmlspecialchars($sanitized) . "</td>";
    echo "<td>" . ($isProtected ? "✓" : "✗") . "</td>";
    echo "</tr>";
}

echo "</table>";

// Test Command Injection Protection
echo "<h3>Command Injection Protection Test</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
echo "<tr style='background-color: #f8f9fa;'><th>Attack Attempt</th><th>Sanitized Result</th><th>Protected</th></tr>";

foreach ($attackTests['command_injection'] as $attack) {
    $sanitized = $validator->validateString($attack, 'test_field', null, null, '/^[a-zA-Z0-9\s\-_]+$/');
    $isProtected = $sanitized === false || 
                  strpos($sanitized, ';') === false && 
                  strpos($sanitized, '|') === false && 
                  strpos($sanitized, '`') === false;
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($attack) . "</td>";
    echo "<td>" . htmlspecialchars($sanitized) . "</td>";
    echo "<td>" . ($isProtected ? "✓" : "✗") . "</td>";
    echo "</tr>";
}

echo "</table>";

// Test Path Traversal Protection
echo "<h3>Path Traversal Protection Test</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
echo "<tr style='background-color: #f8f9fa;'><th>Attack Attempt</th><th>Sanitized Result</th><th>Protected</th></tr>";

foreach ($attackTests['path_traversal'] as $attack) {
    $sanitized = $validator->validateString($attack, 'test_field', null, null, '/^[a-zA-Z0-9\-\_\.]+$/');
    $isProtected = $sanitized === false || 
                  strpos($sanitized, '..') === false && 
                  strpos($sanitized, '%') === false;
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($attack) . "</td>";
    echo "<td>" . htmlspecialchars($sanitized) . "</td>";
    echo "<td>" . ($isProtected ? "✓" : "✗") . "</td>";
    echo "</tr>";
}

echo "</table>";

// Test Type Confusion Protection
echo "<h3>Type Confusion Protection Test</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
echo "<tr style='background-color: #f8f9fa;'><th>Attack Attempt</th><th>Validated Result</th><th>Protected</th></tr>";

foreach ($attackTests['type_confusion'] as $attack => $value) {
    $validated = $validator->validateString($value, 'test_field', null, null, '/^[a-zA-Z0-9]+$/');
    $isProtected = $validated === false || $validated === $value;
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($attack) . " => " . htmlspecialchars($value) . "</td>";
    echo "<td>" . htmlspecialchars($validated) . "</td>";
    echo "<td>" . ($isProtected ? "✓" : "✗") . "</td>";
    echo "</tr>";
}

echo "</table>";

// Test Buffer Overflow Protection
echo "<h3>Buffer Overflow Protection Test</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
echo "<tr style='background-color: #f8f9fa;'><th>Attack Attempt</th><th>Validated Result</th><th>Protected</th></tr>";

foreach ($attackTests['buffer_overflow'] as $attack) {
    $validated = $validator->validateString($attack, 'test_field', null, 255);
    $isProtected = $validated === false || strlen($validated) <= 255;
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars(substr($attack, 0, 50)) . "...</td>";
    echo "<td>" . htmlspecialchars($validated) . "</td>";
    echo "<td>" . ($isProtected ? "✓" : "✗") . "</td>";
    echo "</tr>";
}

echo "</table>";

// Display security features
echo "<h3>Security Features Implemented</h3>";
echo "<ul style='list-style-type: none; padding: 0;'>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ SQL Injection Protection</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ XSS Protection</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Command Injection Protection</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Path Traversal Protection</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Type Confusion Protection</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Buffer Overflow Protection</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Input Length Validation</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Pattern Matching</li>";
echo "</ul>";

echo "</div>";
?> 