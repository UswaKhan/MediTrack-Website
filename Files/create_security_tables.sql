CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    time DATETIME NOT NULL,
    success TINYINT(1) NOT NULL,
    INDEX (username, time)
); 

require_once 'AccessControl.php';
$access = AccessControl::getInstance(); 

if ($access->hasPermission('required_permission')) {
    // Allow action
} 

$access->enforceAccess('required_permission'); 

if ($access->isOwnData($userId)) {
    // Allow access to data
} 

require_once 'InputValidator.php';
$validator = InputValidator::getInstance();

// Define validation rules
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
    // Add more rules as needed
];

// Validate POST data
$validated = $validator->validatePOST($rules);

// Check for errors
if ($validator->hasErrors()) {
    $errors = $validator->getErrors();
    // Handle errors
} else {
    // Use validated data
    $username = $validated['username'];
    $email = $validated['email'];
} 