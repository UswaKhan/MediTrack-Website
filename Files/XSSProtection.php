<?php
class XSSProtection {
    private static $instance = null;
    
    private function __construct() {
        // Set security headers
        $this->setSecurityHeaders();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Set security headers to prevent XSS
    private function setSecurityHeaders() {
        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://code.jquery.com https://stackpath.bootstrapcdn.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://stackpath.bootstrapcdn.com; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net;");
        
        // X-XSS-Protection
        header("X-XSS-Protection: 1; mode=block");
        
        // X-Content-Type-Options
        header("X-Content-Type-Options: nosniff");
        
        // X-Frame-Options
        header("X-Frame-Options: SAMEORIGIN");
        
        // Referrer-Policy
        header("Referrer-Policy: strict-origin-when-cross-origin");
    }
    
    // Sanitize output to prevent XSS
    public function sanitizeOutput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeOutput'], $data);
        }
        
        // Convert special characters to HTML entities
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    // Sanitize input to prevent XSS
    public function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        
        // Remove any HTML tags
        $data = strip_tags($data);
        
        // Convert special characters to HTML entities
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    // Validate and sanitize URLs
    public function sanitizeURL($url) {
        // Remove any HTML tags
        $url = strip_tags($url);
        
        // Validate URL
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        
        return '';
    }
    
    // Validate and sanitize email addresses
    public function sanitizeEmail($email) {
        // Remove any HTML tags
        $email = strip_tags($email);
        
        // Validate email
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }
        
        return '';
    }
    
    // Validate and sanitize file names
    public function sanitizeFileName($filename) {
        // Remove any HTML tags
        $filename = strip_tags($filename);
        
        // Remove any directory components
        $filename = basename($filename);
        
        // Remove any non-alphanumeric characters except for dots and underscores
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        return $filename;
    }
    
    // Validate and sanitize HTML content (for rich text editors)
    public function sanitizeHTML($html) {
        // Define allowed HTML tags and attributes
        $allowedTags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img>';
        $allowedAttributes = [
            'a' => ['href', 'title', 'target'],
            'img' => ['src', 'alt', 'title', 'width', 'height']
        ];
        
        // Remove any disallowed HTML tags
        $html = strip_tags($html, $allowedTags);
        
        // Remove any disallowed attributes
        foreach ($allowedAttributes as $tag => $attributes) {
            $pattern = '/<' . $tag . '[^>]*>/i';
            preg_match_all($pattern, $html, $matches);
            
            foreach ($matches[0] as $match) {
                $cleanTag = '<' . $tag;
                foreach ($attributes as $attr) {
                    if (preg_match('/' . $attr . '=["\']([^"\']*)["\']/i', $match, $attrMatch)) {
                        $cleanTag .= ' ' . $attr . '="' . $this->sanitizeOutput($attrMatch[1]) . '"';
                    }
                }
                $cleanTag .= '>';
                $html = str_replace($match, $cleanTag, $html);
            }
        }
        
        return $html;
    }
    
    // Validate and sanitize JavaScript content
    public function sanitizeJavaScript($js) {
        // Remove any HTML tags
        $js = strip_tags($js);
        
        // Remove any script tags
        $js = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $js);
        
        // Remove any event handlers
        $js = preg_replace('/on\w+="[^"]*"/', '', $js);
        
        return $js;
    }
}
?> 