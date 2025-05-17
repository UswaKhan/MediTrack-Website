<?php
require_once 'XSSProtection.php';

$xss = XSSProtection::getInstance();

// Test cases for XSS attacks
$testCases = [
    // Basic XSS
    '<script>alert("XSS")</script>',
    
    // XSS with event handlers
    '<img src="x" onerror="alert(\'XSS\')">',
    
    // XSS with JavaScript protocol
    '<a href="javascript:alert(\'XSS\')">Click me</a>',
    
    // XSS with encoded characters
    '&#60;script&#62;alert("XSS")&#60;/script&#62;',
    
    // XSS with mixed case
    '<ScRiPt>alert("XSS")</ScRiPt>',
    
    // XSS with nested quotes
    '<img src="x" onerror="alert(\'XSS\')" onmouseover="alert(\'XSS\')">',
    
    // XSS with data URI
    '<img src="data:text/html;base64,PHNjcmlwdD5hbGVydCgnWFNTJyk8L3NjcmlwdD4=">',
    
    // XSS with CSS
    '<div style="background-image: url(\'javascript:alert(\'XSS\')\')">',
    
    // XSS with iframe
    '<iframe src="javascript:alert(\'XSS\')"></iframe>',
    
    // XSS with form
    '<form action="javascript:alert(\'XSS\')"><input type="submit"></form>'
];

echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px;'>";
echo "<h2>XSS Protection Test Results</h2>";

// Test input sanitization
echo "<h3>Input Sanitization Test</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
echo "<tr style='background-color: #f8f9fa;'><th>Original Input</th><th>Sanitized Output</th><th>Protected</th></tr>";

foreach ($testCases as $test) {
    $sanitized = $xss->sanitizeInput($test);
    $isProtected = strpos($sanitized, '<script') === false && 
                  strpos($sanitized, 'javascript:') === false &&
                  strpos($sanitized, 'onerror') === false;
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($test) . "</td>";
    echo "<td>" . htmlspecialchars($sanitized) . "</td>";
    echo "<td>" . ($isProtected ? "✓" : "✗") . "</td>";
    echo "</tr>";
}

echo "</table>";

// Test output sanitization
echo "<h3>Output Sanitization Test</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
echo "<tr style='background-color: #f8f9fa;'><th>Original Output</th><th>Sanitized Output</th><th>Protected</th></tr>";

foreach ($testCases as $test) {
    $sanitized = $xss->sanitizeOutput($test);
    $isProtected = strpos($sanitized, '<script') === false && 
                  strpos($sanitized, 'javascript:') === false &&
                  strpos($sanitized, 'onerror') === false;
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($test) . "</td>";
    echo "<td>" . htmlspecialchars($sanitized) . "</td>";
    echo "<td>" . ($isProtected ? "✓" : "✗") . "</td>";
    echo "</tr>";
}

echo "</table>";

// Test URL sanitization
echo "<h3>URL Sanitization Test</h3>";
$urlTests = [
    'https://example.com',
    'javascript:alert("XSS")',
    'http://example.com/<script>alert("XSS")</script>',
    'data:text/html,<script>alert("XSS")</script>'
];

echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
echo "<tr style='background-color: #f8f9fa;'><th>Original URL</th><th>Sanitized URL</th><th>Protected</th></tr>";

foreach ($urlTests as $url) {
    $sanitized = $xss->sanitizeURL($url);
    $isProtected = strpos($sanitized, 'javascript:') === false && 
                  strpos($sanitized, '<script') === false;
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($url) . "</td>";
    echo "<td>" . htmlspecialchars($sanitized) . "</td>";
    echo "<td>" . ($isProtected ? "✓" : "✗") . "</td>";
    echo "</tr>";
}

echo "</table>";

// Display security features
echo "<h3>Security Features Implemented</h3>";
echo "<ul style='list-style-type: none; padding: 0;'>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Content Security Policy (CSP)</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ X-XSS-Protection header</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Input sanitization</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ Output sanitization</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ URL sanitization</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ HTML content sanitization</li>";
echo "<li style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 4px;'>✓ JavaScript sanitization</li>";
echo "</ul>";

echo "</div>";
?> 