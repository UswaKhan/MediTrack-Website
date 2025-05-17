<?php
class InputValidator {
    private static $instance = null;
    private $errors = [];
    private $xss;
    
    private function __construct() {
        $this->xss = XSSProtection::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Validate and sanitize numeric input
    public function validateNumeric($value, $fieldName, $min = null, $max = null) {
        if (!is_numeric($value)) {
            $this->errors[$fieldName] = "$fieldName must be a number";
            return false;
        }
        
        if ($min !== null && $value < $min) {
            $this->errors[$fieldName] = "$fieldName must be greater than or equal to $min";
            return false;
        }
        
        if ($max !== null && $value > $max) {
            $this->errors[$fieldName] = "$fieldName must be less than or equal to $max";
            return false;
        }
        
        return (float)$value;
    }
    
    // Validate and sanitize integer input
    public function validateInteger($value, $fieldName, $min = null, $max = null) {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $this->errors[$fieldName] = "$fieldName must be an integer";
            return false;
        }
        
        if ($min !== null && $value < $min) {
            $this->errors[$fieldName] = "$fieldName must be greater than or equal to $min";
            return false;
        }
        
        if ($max !== null && $value > $max) {
            $this->errors[$fieldName] = "$fieldName must be less than or equal to $max";
            return false;
        }
        
        return (int)$value;
    }
    
    // Validate and sanitize string input
    public function validateString($value, $fieldName, $minLength = null, $maxLength = null, $pattern = null) {
        $value = trim($value);
        
        if ($minLength !== null && strlen($value) < $minLength) {
            $this->errors[$fieldName] = "$fieldName must be at least $minLength characters long";
            return false;
        }
        
        if ($maxLength !== null && strlen($value) > $maxLength) {
            $this->errors[$fieldName] = "$fieldName must not exceed $maxLength characters";
            return false;
        }
        
        if ($pattern !== null && !preg_match($pattern, $value)) {
            $this->errors[$fieldName] = "$fieldName contains invalid characters";
            return false;
        }
        
        return $this->xss->sanitizeInput($value);
    }
    
    // Validate and sanitize email
    public function validateEmail($value, $fieldName) {
        $value = trim($value);
        
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$fieldName] = "Invalid email format";
            return false;
        }
        
        return $this->xss->sanitizeEmail($value);
    }
    
    // Validate and sanitize URL
    public function validateURL($value, $fieldName) {
        $value = trim($value);
        
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->errors[$fieldName] = "Invalid URL format";
            return false;
        }
        
        return $this->xss->sanitizeURL($value);
    }
    
    // Validate and sanitize date
    public function validateDate($value, $fieldName, $format = 'Y-m-d') {
        $date = DateTime::createFromFormat($format, $value);
        
        if (!$date || $date->format($format) !== $value) {
            $this->errors[$fieldName] = "Invalid date format. Expected format: $format";
            return false;
        }
        
        return $value;
    }
    
    // Validate and sanitize boolean
    public function validateBoolean($value, $fieldName) {
        if (!is_bool($value) && !in_array($value, ['0', '1', 'true', 'false', true, false], true)) {
            $this->errors[$fieldName] = "$fieldName must be a boolean value";
            return false;
        }
        
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
    
    // Validate and sanitize array
    public function validateArray($value, $fieldName, $allowedValues = null) {
        if (!is_array($value)) {
            $this->errors[$fieldName] = "$fieldName must be an array";
            return false;
        }
        
        if ($allowedValues !== null) {
            foreach ($value as $item) {
                if (!in_array($item, $allowedValues, true)) {
                    $this->errors[$fieldName] = "$fieldName contains invalid values";
                    return false;
                }
            }
        }
        
        return array_map([$this->xss, 'sanitizeInput'], $value);
    }
    
    // Validate and sanitize file upload
    public function validateFile($file, $fieldName, $allowedTypes = null, $maxSize = null) {
        if (!isset($file['error']) || is_array($file['error'])) {
            $this->errors[$fieldName] = "Invalid file parameter";
            return false;
        }
        
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->errors[$fieldName] = "File size exceeds limit";
                return false;
            case UPLOAD_ERR_PARTIAL:
                $this->errors[$fieldName] = "File was only partially uploaded";
                return false;
            case UPLOAD_ERR_NO_FILE:
                $this->errors[$fieldName] = "No file was uploaded";
                return false;
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->errors[$fieldName] = "Missing a temporary folder";
                return false;
            case UPLOAD_ERR_CANT_WRITE:
                $this->errors[$fieldName] = "Failed to write file to disk";
                return false;
            case UPLOAD_ERR_EXTENSION:
                $this->errors[$fieldName] = "A PHP extension stopped the file upload";
                return false;
            default:
                $this->errors[$fieldName] = "Unknown upload error";
                return false;
        }
        
        if ($allowedTypes !== null) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            
            if (!in_array($mimeType, $allowedTypes, true)) {
                $this->errors[$fieldName] = "Invalid file type";
                return false;
            }
        }
        
        if ($maxSize !== null && $file['size'] > $maxSize) {
            $this->errors[$fieldName] = "File size exceeds limit";
            return false;
        }
        
        return $file;
    }
    
    // Validate and sanitize SQL query parameters
    public function validateSQLParam($value, $fieldName) {
        // Remove SQL injection patterns
        $value = preg_replace('/[\'";]/', '', $value);
        
        if (empty($value)) {
            $this->errors[$fieldName] = "$fieldName cannot be empty";
            return false;
        }
        
        return $this->xss->sanitizeInput($value);
    }
    
    // Get all validation errors
    public function getErrors() {
        return $this->errors;
    }
    
    // Check if there are any validation errors
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    // Clear all validation errors
    public function clearErrors() {
        $this->errors = [];
    }
    
    // Validate all POST data
    public function validatePOST($rules) {
        $validated = [];
        
        foreach ($rules as $field => $rule) {
            if (!isset($_POST[$field])) {
                if (isset($rule['required']) && $rule['required']) {
                    $this->errors[$field] = "$field is required";
                }
                continue;
            }
            
            $value = $_POST[$field];
            
            switch ($rule['type']) {
                case 'string':
                    $validated[$field] = $this->validateString(
                        $value,
                        $field,
                        $rule['min_length'] ?? null,
                        $rule['max_length'] ?? null,
                        $rule['pattern'] ?? null
                    );
                    break;
                    
                case 'email':
                    $validated[$field] = $this->validateEmail($value, $field);
                    break;
                    
                case 'url':
                    $validated[$field] = $this->validateURL($value, $field);
                    break;
                    
                case 'numeric':
                    $validated[$field] = $this->validateNumeric(
                        $value,
                        $field,
                        $rule['min'] ?? null,
                        $rule['max'] ?? null
                    );
                    break;
                    
                case 'integer':
                    $validated[$field] = $this->validateInteger(
                        $value,
                        $field,
                        $rule['min'] ?? null,
                        $rule['max'] ?? null
                    );
                    break;
                    
                case 'date':
                    $validated[$field] = $this->validateDate(
                        $value,
                        $field,
                        $rule['format'] ?? 'Y-m-d'
                    );
                    break;
                    
                case 'boolean':
                    $validated[$field] = $this->validateBoolean($value, $field);
                    break;
                    
                case 'array':
                    $validated[$field] = $this->validateArray(
                        $value,
                        $field,
                        $rule['allowed_values'] ?? null
                    );
                    break;
                    
                case 'file':
                    $validated[$field] = $this->validateFile(
                        $value,
                        $field,
                        $rule['allowed_types'] ?? null,
                        $rule['max_size'] ?? null
                    );
                    break;
                    
                case 'sql':
                    $validated[$field] = $this->validateSQLParam($value, $field);
                    break;
            }
        }
        
        return $validated;
    }
    
    // Validate all GET data
    public function validateGET($rules) {
        $validated = [];
        
        foreach ($rules as $field => $rule) {
            if (!isset($_GET[$field])) {
                if (isset($rule['required']) && $rule['required']) {
                    $this->errors[$field] = "$field is required";
                }
                continue;
            }
            
            $value = $_GET[$field];
            
            switch ($rule['type']) {
                case 'string':
                    $validated[$field] = $this->validateString(
                        $value,
                        $field,
                        $rule['min_length'] ?? null,
                        $rule['max_length'] ?? null,
                        $rule['pattern'] ?? null
                    );
                    break;
                    
                case 'email':
                    $validated[$field] = $this->validateEmail($value, $field);
                    break;
                    
                case 'url':
                    $validated[$field] = $this->validateURL($value, $field);
                    break;
                    
                case 'numeric':
                    $validated[$field] = $this->validateNumeric(
                        $value,
                        $field,
                        $rule['min'] ?? null,
                        $rule['max'] ?? null
                    );
                    break;
                    
                case 'integer':
                    $validated[$field] = $this->validateInteger(
                        $value,
                        $field,
                        $rule['min'] ?? null,
                        $rule['max'] ?? null
                    );
                    break;
                    
                case 'date':
                    $validated[$field] = $this->validateDate(
                        $value,
                        $field,
                        $rule['format'] ?? 'Y-m-d'
                    );
                    break;
                    
                case 'boolean':
                    $validated[$field] = $this->validateBoolean($value, $field);
                    break;
                    
                case 'array':
                    $validated[$field] = $this->validateArray(
                        $value,
                        $field,
                        $rule['allowed_values'] ?? null
                    );
                    break;
                    
                case 'sql':
                    $validated[$field] = $this->validateSQLParam($value, $field);
                    break;
            }
        }
        
        return $validated;
    }
}
?> 