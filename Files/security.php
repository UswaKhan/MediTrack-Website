<?php
// Security functions for the application

// Function to check for brute force attempts
function checkBruteForce($username, $conn) {
    // Delete attempts older than 15 minutes
    $sql = "DELETE FROM login_attempts WHERE time < DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
    $conn->query($sql);
    
    // Count recent failed attempts
    $sql = "SELECT COUNT(*) as attempts FROM login_attempts WHERE username = ? AND success = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // If more than 5 failed attempts in 15 minutes, block access
    if ($row['attempts'] >= 5) {
        return true; // Block access
    }
    return false; // Allow access
}

// Function to log login attempts
function logLoginAttempt($username, $success, $conn) {
    $sql = "INSERT INTO login_attempts (username, time, success) VALUES (?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $username, $success);
    $stmt->execute();
}

// Function to hash password securely
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Function to verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Function to generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Function to verify CSRF token
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}
?> 