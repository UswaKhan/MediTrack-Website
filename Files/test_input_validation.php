<?php
require_once 'InputValidator.php';

$validator = InputValidator::getInstance();

// Test cases for parameter manipulation
$testCases = [
    // Numeric manipulation
    'numeric' => [
        'valid' => ['42', '3.14', '0'],
        'invalid' => ['abc', '12.34.56', '1e1000', 'NaN', 'Infinity']
    ],
    
    // Integer manipulation
    'integer' => [
        'valid' => ['42', '0', '-1'],
        'invalid' => ['3.14', 'abc', '1e1000', 'NaN', 'Infinity']
    ],
    
    // String manipulation
    'string' => [
        'valid' => ['Hello', '123', 'Special!@#$%'],
        'invalid' => [str_repeat('a', 1001), '', '   ']
    ],
    
    // Email manipulation
    'email' => [
        'valid' => ['user@example.com', 'user.name@domain.co.uk'],
        'invalid' => ['user@', '@domain.com', 'user@.com', 'user@domain.', 'user@domain..com']
    ],
    
    // URL manipulation
    'url' => [
        'valid' => ['https://example.com', 'http://sub.domain.co.uk/path?query=value'],
        'invalid' => ['not-a-url', 'http://', 'https://', 'ftp://invalid']
    ],
    
    // Date manipulation
    'date' => [
        'valid' => ['2024-03-14', '2024-02-29'],
        'invalid' => ['2024-13-01', '2024-02-30', '2024/03/14', '14-03-2024']
    ],
    
    // Boolean manipulation
    'boolean' => [
        'valid' => ['true', 'false', '1', '0', true, false],
        'invalid' => ['yes', 'no', 't', 'f', 'yes', 'no']
    ],
    
    // Array manipulation
    'array' => [
        'valid' => [['a', 'b', 'c'], [1, 2, 3]],
        'invalid' => ['not-an-array', 42, 'string']
    ],
    
    // SQL parameter manipulation
    'sql' => [
        'valid' => ['SELECT * FROM users', 'user123'],
        'invalid' => ["'; DROP TABLE users; --", "'; DELETE FROM users; --"]
    ]
];

echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px;'>";
echo "<h2>Input Validation Test Results</h2>";

foreach ($testCases as $type => $cases) {
    echo "<h3>" . ucfirst($type) . " Validation Test</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background-color: #f8f9fa;'><th>Input</th><th>Expected Result</th><th>Actual Result</th><th>Protected</th></tr>";
    
    // Test valid cases
    foreach ($cases['valid'] as $input) {
        $method = 'validate' . ucfirst($type);
        $result = $validator->$method($input, 'test_field');
        $isProtected = $result !== false;
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars(print_r($input, true)) . "</td>";
        echo "<td>Valid</td>";
        echo "<td>" . ($isProtected ? "Valid" : "Invalid") . "</td>";
        echo "<td>" . ($isProtected ? "✓" : "✗") . "</td>";
        echo "</tr>";
    }
    
    // Test invalid cases
    foreach ($cases['invalid'] as $input) {
        $method = 'validate' . ucfirst($type);
        $result = $validator->$method($input, 'test_field');
        $isProtected = $result === false;
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars(print_r($input, true)) . "</td>";
        echo "<td>Invalid</td>";
        echo "<td>" . ($isProtected ? "Invalid" : "Valid") . "</td>";
        echo "<td>" . ($isProtected ? "✓" : "✗") . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

// Test POST validation
echo "<h3>POST Data Validation Test</h3>";
$_POST = [
    'username' => 'user123',
    'email' => 'user@example.com',
    'age' => '25',
    'price' => '19.99',
    'date' => '2024-03-14',
    'is_active' => 'true',
    'tags' => ['tag1', 'tag2', 'tag3']
];

$rules = [
    'username' => [
        'type' => 'string',
        'required' => true,
        'min_length' => 3,
        'max_length' => 50,
        'pattern' => '/^[a-zA-Z0-9_]+$/'
    ],
    'email' => [
        'type' => 'email',
        'required' => true
    ],
    'age' => [
        'type' => 'integer',
        'required' => true,
        'min' => 18,
        'max' => 120
    ],
    'price' => [
        'type' => 'numeric',
        'required' => true,
        'min' => 0,
        'max' => 1000
    ],
    'date' => [
        'type' => 'date',
        'required' => true,
        'format' => 'Y-m-d'
    ],
    'is_active' => [
        'type' => 'boolean',
        'required' => true
    ],
    'tags' => [
        'type' => 'array',
        'required' => true,
        'allowed_values' => ['tag1', 'tag2', 'tag3', 'tag4']
    ]
];

$validated = $validator->validatePOST($rules);

echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
echo "<tr style='background-color: #f8f9fa;'><th>Field</th><th>Input</th><th>Validated</th><th>Protected</th></tr>";

foreach ($rules as $field => $rule) {
    $input = $_POST[$field] ?? 'not set';
    $validatedValue = $validated[$field] ?? 'invalid';
    $isProtected = isset($validated[$field]) && $validated[$field] !== false;
    
    echo "<tr>";
    echo "<td>$field</td>";
    echo "<td>" . htmlspecialchars(print_r($input, true)) . "</td>";
    echo "<td>" . htmlspecialchars(print_r($validatedValue, true)) . "</td>";
    echo "<td>" . ($isProtected ? "✓" : "✗") . "</td>";
    echo "</tr>";
}

echo "</table>";

// Display validation errors
if ($validator->hasErrors()) {
    echo "<h3>Validation Errors</h3>";
    echo "<ul style='color: red;'>";
    foreach ($validator->getErrors() as $field => $error) {
        echo "<li>$field: $error</li>";
    }
    echo "</ul>";
}

// Display security features
echo "<h3>Security Features Implemented</h3>";
echo "<ul style='list-style-type: none; padding: 0;'>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Input type validation</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Range validation</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Pattern matching</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Required field validation</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ SQL injection prevention</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ XSS prevention</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ File upload validation</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Array validation</li>";
echo "</ul>";

echo "</div>";
?> 