<?php
class AccessControl {
    private static $instance = null;
    private $session;
    private $db;
    
    // Define roles and their permissions
    private $rolePermissions = [
        'admin' => [
            'view_dashboard' => true,
            'add_medicine' => true,
            'edit_medicine' => true,
            'delete_medicine' => true,
            'view_medicines' => true,
            'add_user' => true,
            'edit_user' => true,
            'delete_user' => true,
            'view_users' => true,
            'view_sales' => true,
            'view_customers' => true,
            'manage_inventory' => true
        ],
        'cashier' => [
            'view_dashboard' => true,
            'view_medicines' => true,
            'sell_medicine' => true,
            'view_sales' => true,
            'view_customers' => true
        ]
    ];
    
    private function __construct() {
        $this->session = SessionManager::getInstance();
        $this->db = Database::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Check if user has permission for a specific action
    public function hasPermission($permission) {
        if (!$this->session->isLoggedIn()) {
            return false;
        }
        
        $role = $this->session->getRole();
        if (!isset($this->rolePermissions[$role])) {
            return false;
        }
        
        return $this->rolePermissions[$role][$permission] ?? false;
    }
    
    // Verify user's role matches the required role
    public function verifyRole($requiredRole) {
        if (!$this->session->isLoggedIn()) {
            return false;
        }
        
        return $this->session->getRole() === $requiredRole;
    }
    
    // Check if user is trying to access their own data
    public function isOwnData($userId) {
        if (!$this->session->isLoggedIn()) {
            return false;
        }
        
        $username = $this->session->getUsername();
        $result = $this->db->select("SELECT id FROM admin WHERE username = ?", "s", [$username]);
        
        return !empty($result) && $result[0]['id'] == $userId;
    }
    
    // Enforce access control for a page
    public function enforceAccess($requiredPermission, $redirectUrl = 'index.php') {
        if (!$this->hasPermission($requiredPermission)) {
            // Log the unauthorized access attempt
            $this->logUnauthorizedAccess();
            
            // Redirect to appropriate page
            header("Location: $redirectUrl");
            exit();
        }
    }
    
    // Log unauthorized access attempts
    private function logUnauthorizedAccess() {
        $username = $this->session->getUsername() ?? 'Not logged in';
        $role = $this->session->getRole() ?? 'No role';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown IP';
        $page = $_SERVER['REQUEST_URI'] ?? 'Unknown page';
        $timestamp = date('Y-m-d H:i:s');
        
        // Create logs table if it doesn't exist
        $this->createLogsTable();
        
        // Log the attempt
        $logData = [
            'username' => $username,
            'role' => $role,
            'ip_address' => $ip,
            'page_accessed' => $page,
            'timestamp' => $timestamp
        ];
        
        $this->db->insert('access_logs', $logData);
    }
    
    // Create logs table if it doesn't exist
    private function createLogsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS access_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            page_accessed VARCHAR(255) NOT NULL,
            timestamp DATETIME NOT NULL,
            INDEX (username, timestamp)
        )";
        
        $this->db->getConnection()->query($sql);
    }
    
    // Get all permissions for current user
    public function getUserPermissions() {
        if (!$this->session->isLoggedIn()) {
            return [];
        }
        
        $role = $this->session->getRole();
        return $this->rolePermissions[$role] ?? [];
    }
    
    // Check if user can perform multiple actions
    public function hasPermissions($permissions) {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }
}
?> 