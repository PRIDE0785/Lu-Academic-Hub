<?php
/**
 * Authentication Functions
 * Handles user authentication, roles, and permissions
 */

require_once dirname(__DIR__) . '/config/database.php';

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user has specific role
 */
function hasRole($role) {
    if (!isLoggedIn()) return false;
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Check if user has any of the specified roles
 */
function hasAnyRole($roles) {
    if (!isLoggedIn()) return false;
    return in_array($_SESSION['role'], (array)$roles);
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    try {
        $db = Database::getInstance();
        $sql = "SELECT u.*, r.name as role_name FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.id = ?";
        $stmt = $db->query($sql, [$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error fetching user: " . $e->getMessage());
        return null;
    }
}

/**
 * Redirect to appropriate dashboard based on role
 */
function redirectToDashboard() {
    if (!isLoggedIn()) {
        header('Location: /auth/login.php');
        exit;
    }
    
    $role = $_SESSION['role'] ?? 'student';
    
    switch ($role) {
        case 'super_admin':
        case 'admin':
            header('Location: /admin/dashboard.php');
            break;
        case 'lecturer':
            header('Location: /lecturer/dashboard.php');
            break;
        default:
            header('Location: /student/dashboard.php');
    }
    exit;
}

/**
 * Require authentication
 */
function requireAuth() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: /auth/login.php');
        exit;
    }
}

/**
 * Require specific role
 */
function requireRole($role) {
    requireAuth();
    if (!hasRole($role) && !hasRole('super_admin')) {
        header('Location: /unauthorized.php');
        exit;
    }
}

/**
 * Require admin access
 */
function requireAdmin() {
    requireAuth();
    if (!hasAnyRole(['admin', 'super_admin'])) {
        header('Location: /unauthorized.php');
        exit;
    }
}

/**
 * Get user by ID
 */
function getUserById($userId) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $db->query($sql, [$userId]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error fetching user: " . $e->getMessage());
        return null;
    }
}

/**
 * Create user session
 */
function createUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role_name'] ?? getRoleName($user['role_id']);
    $_SESSION['role_id'] = $user['role_id'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['is_verified'] = $user['is_verified'];
    
    // Update last login
    try {
        $db = Database::getInstance();
        $sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $db->query($sql, [$user['id']]);
    } catch (Exception $e) {
        error_log("Error updating last login: " . $e->getMessage());
    }
}

/**
 * Get role name by ID
 */
function getRoleName($roleId) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT name FROM roles WHERE id = ?";
        $stmt = $db->query($sql, [$roleId]);
        $result = $stmt->fetch();
        return $result ? $result['name'] : 'student';
    } catch (Exception $e) {
        return 'student';
    }
}

/**
 * Check if user has permission
 */
function hasPermission($permission) {
    if (!isLoggedIn()) return false;
    if (hasRole('super_admin')) return true;
    
    // Implement permission checking logic
    // This can be expanded with a permissions table
    return false;
}

/**
 * Check if email is verified
 */
function isEmailVerified() {
    if (!isLoggedIn()) return false;
    return isset($_SESSION['is_verified']) && $_SESSION['is_verified'] == 1;
}

/**
 * Generate remember me token
 */
function generateRememberToken($userId) {
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    try {
        $db = Database::getInstance();
        $sql = "INSERT INTO sessions (user_id, session_token, ip_address, user_agent, last_activity) 
                VALUES (?, ?, ?, ?, NOW())";
        $db->query($sql, [$userId, $token, getClientIP(), $_SERVER['HTTP_USER_AGENT'] ?? '']);
        return $token;
    } catch (Exception $e) {
        error_log("Error generating remember token: " . $e->getMessage());
        return null;
    }
}
