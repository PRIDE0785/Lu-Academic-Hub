<?php
/**
 * Security Functions
 * Comprehensive security utilities
 */

/**
 * Sanitize input to prevent XSS attacks
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF token input field
 */
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

/**
 * Hash password securely
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_ROUNDS]);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate secure random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Validate file upload
 */
function validateFileUpload($file, $allowedExtensions = null) {
    $allowedExtensions = $allowedExtensions ?? ALLOWED_EXTENSIONS;
    
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['valid' => false, 'message' => 'File upload failed'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['valid' => false, 'message' => 'File size exceeds maximum limit'];
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        return ['valid' => false, 'message' => 'File type not allowed'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedMimeTypes = ALLOWED_MIME_TYPES;
    if (!in_array($mimeType, $allowedMimeTypes)) {
        return ['valid' => false, 'message' => 'Invalid file type'];
    }
    
    return ['valid' => true, 'message' => 'File is valid'];
}

/**
 * Rate limiting
 */
function checkRateLimit($key, $limit = 100, $timeWindow = 3600) {
    $sessionKey = 'rate_limit_' . $key;
    
    if (!isset($_SESSION[$sessionKey])) {
        $_SESSION[$sessionKey] = ['count' => 1, 'reset_at' => time() + $timeWindow];
        return true;
    }
    
    $data = $_SESSION[$sessionKey];
    
    if (time() > $data['reset_at']) {
        $_SESSION[$sessionKey] = ['count' => 1, 'reset_at' => time() + $timeWindow];
        return true;
    }
    
    if ($data['count'] >= $limit) {
        return false;
    }
    
    $_SESSION[$sessionKey]['count']++;
    return true;
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    
    if (!filter_var($ipaddress, FILTER_VALIDATE_IP)) {
        return '0.0.0.0';
    }
    return $ipaddress;
}

/**
 * Audit logging
 */
function auditLog($userId, $action, $module, $description = '') {
    try {
        $db = Database::getInstance();
        $sql = "INSERT INTO audit_logs (user_id, action, module, description, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $db->query($sql, [
            $userId,
            $action,
            $module,
            $description,
            getClientIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        return true;
    } catch (Exception $e) {
        error_log("Audit log error: " . $e->getMessage());
        return false;
    }
}

/**
 * Log security events
 */
function logSecurityEvent($event, $details = '') {
    $logMessage = date('Y-m-d H:i:s') . " | IP: " . getClientIP() . " | " . $event . " | " . $details . "\n";
    file_put_contents(LOG_PATH . 'security.log', $logMessage, FILE_APPEND);
}

/**
 * Check password strength
 */
function checkPasswordStrength($password) {
    $score = 0;
    
    if (strlen($password) >= 8) $score++;
    if (strlen($password) >= 12) $score++;
    if (preg_match('/[a-z]/', $password)) $score++;
    if (preg_match('/[A-Z]/', $password)) $score++;
    if (preg_match('/[0-9]/', $password)) $score++;
    if (preg_match('/[^a-zA-Z0-9]/', $password)) $score++;
    
    return $score;
}

/**
 * Generate and send email verification
 */
function sendVerificationEmail($userId, $email) {
    try {
        $token = generateToken();
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $db = Database::getInstance();
        $sql = "INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)";
        $db->query($sql, [$userId, $token, $expires]);
        
        $verificationLink = APP_URL . "/auth/verify-email.php?token=" . $token;
        
        // In production, send actual email
        // For now, log it
        error_log("Verification link for $email: $verificationLink");
        
        return true;
    } catch (Exception $e) {
        error_log("Error sending verification email: " . $e->getMessage());
        return false;
    }
}

/**
 * Generate and send password reset email
 */
function sendPasswordResetEmail($email) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $db->query($sql, [$email]);
        $user = $stmt->fetch();
        
        if (!$user) return false;
        
        $token = generateToken();
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $sql = "INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)";
        $db->query($sql, [$user['id'], $token, $expires]);
        
        $resetLink = APP_URL . "/auth/reset-password.php?token=" . $token;
        
        // In production, send actual email
        error_log("Password reset link for $email: $resetLink");
        
        return true;
    } catch (Exception $e) {
        error_log("Error sending password reset email: " . $e->getMessage());
        return false;
    }
}
