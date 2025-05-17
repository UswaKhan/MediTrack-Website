<?php
require_once 'security.php';
require_once 'Database.php';
require_once 'SessionManager.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize database and session
$db = Database::getInstance();
$session = SessionManager::getInstance();

// Debug session state
error_log("Session status before login: " . session_status());
error_log("Session ID: " . session_id());

// Only verify CSRF token if it's a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("Login attempt started");
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        error_log("CSRF token verification failed");
        die("Invalid CSRF token");
    }
    error_log("CSRF token verified successfully");

    // Get and sanitize input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    error_log("Attempting login for username: " . $username);

    // Check for brute force attempts
    if (checkBruteForce($username, $db->getConnection())) {
        error_log("Brute force attempt detected for user: " . $username);
        die("Too many failed login attempts. Please try again in 15 minutes.");
    }

    // Prepare statement to prevent SQL injection
    $result = $db->select("SELECT * FROM admin WHERE username = ?", "s", [$username]);

    if (!empty($result)) {
        $user = $result[0];
        $stored_password = $user['password'];
        
        error_log("User found in database. Password hash length: " . strlen($stored_password));
        error_log("User role: " . $user['role']);
        
        // Check if the stored password is in old MD5 format
        if (strlen($stored_password) === 32) { // MD5 hashes are 32 characters long
            error_log("Checking MD5 password format");
            // Verify against MD5 hash
            if (md5($password) === $stored_password) {
                error_log("MD5 password verification successful");
                // Password is correct, update to new BCRYPT hash
                $new_hash = hashPassword($password);
                $db->update('admin', ['password' => $new_hash], 'id = ?', "i", [$user['id']]);
                
                // Log successful attempt
                logLoginAttempt($username, 1, $db->getConnection());
                
                // Set session variables using secure session manager
                $session->set('admin', $username);
                $session->set('role', $user['role']);
                
                error_log("Session variables set - admin: " . $username . ", role: " . $user['role']);
                error_log("Session data after login: " . print_r($_SESSION, true));
                
                // Redirect based on role
                if ($user['role'] == 'cashier') {
                    error_log("Redirecting to cashier dashboard");
                    header("Location: cashier_dashboard.php");
                } else {
                    error_log("Redirecting to admin dashboard");
                    header("Location: admin_dashboard.php");
                }
                exit();
            } else {
                error_log("MD5 password verification failed");
            }
        }
        
        // Verify against BCRYPT hash
        error_log("Checking BCRYPT password format");
        if (verifyPassword($password, $stored_password)) {
            error_log("BCRYPT password verification successful");
            // Log successful attempt
            logLoginAttempt($username, 1, $db->getConnection());
            
            // Set session variables using secure session manager
            $session->set('admin', $username);
            $session->set('role', $user['role']);
            
            error_log("Session variables set - admin: " . $username . ", role: " . $user['role']);
            error_log("Session data after login: " . print_r($_SESSION, true));
            
            // Redirect based on role
            if ($user['role'] == 'cashier') {
                error_log("Redirecting to cashier dashboard");
                header("Location: cashier_dashboard.php");
            } else {
                error_log("Redirecting to admin dashboard");
                header("Location: admin_dashboard.php");
            }
            exit();
        } else {
            error_log("BCRYPT password verification failed");
        }
    } else {
        error_log("User not found in database: " . $username);
    }

    // Log failed attempt
    logLoginAttempt($username, 0, $db->getConnection());
    echo "<script>alert('Invalid username or password'); window.location='index.php';</script>";
}
?>