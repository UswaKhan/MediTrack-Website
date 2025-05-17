<?php
class SessionManager {
    private static $instance = null;
    
    private function __construct() {
        // Set secure session parameters before any output
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.cookie_secure', 1); // Enable secure cookies
            ini_set('session.gc_maxlifetime', 1800); // 30 minutes
            
            // Set session name
            session_name('SECURE_SESSION');
            
            // Start session
            session_start();
        }
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['last_regeneration'])) {
            $this->regenerateSession();
        } else {
            $interval = 30 * 60; // 30 minutes
            if (time() - $_SESSION['last_regeneration'] > $interval) {
                $this->regenerateSession();
            }
        }
        
        // Validate session
        $this->validateSession();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function regenerateSession() {
        // Save old session data
        $old_session = $_SESSION;
        
        // Regenerate session ID
        session_regenerate_id(true);
        
        // Restore session data
        $_SESSION = $old_session;
        
        // Update last regeneration time
        $_SESSION['last_regeneration'] = time();
        
        // Set session fingerprint
        $this->setSessionFingerprint();
    }
    
    private function setSessionFingerprint() {
        // Create a unique fingerprint for this session
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $fingerprint = hash('sha256', $user_agent . $ip_address);
        
        $_SESSION['fingerprint'] = $fingerprint;
    }
    
    private function validateSession() {
        // Check if fingerprint exists
        if (!isset($_SESSION['fingerprint'])) {
            $this->setSessionFingerprint();
            return;
        }
        
        // Validate fingerprint
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $current_fingerprint = hash('sha256', $user_agent . $ip_address);
        
        if ($_SESSION['fingerprint'] !== $current_fingerprint) {
            // Session hijacking attempt detected
            $this->destroySession();
            header('Location: index.php?error=invalid_session');
            exit();
        }
    }
    
    public function destroySession() {
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy the session
        session_destroy();
    }
    
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public function remove($key) {
        unset($_SESSION[$key]);
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['admin']) && !empty($_SESSION['admin']);
    }
    
    public function getRole() {
        return $_SESSION['role'] ?? null;
    }
    
    public function getUsername() {
        return $_SESSION['admin'] ?? null;
    }
}
?> 