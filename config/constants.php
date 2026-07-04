<?php
/**
 * Application Constants
 * Central configuration for the entire application
 */

// Application Configuration
define('APP_NAME', 'LU Academic Hub');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/lira-university-past-papers');
define('APP_EMAIL', 'support@lirauniversity.ac.ug');

// Directory Paths
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
define('PAPER_UPLOAD_PATH', UPLOAD_PATH . 'papers/');
define('RESEARCH_UPLOAD_PATH', UPLOAD_PATH . 'research/');
define('PROFILE_UPLOAD_PATH', UPLOAD_PATH . 'profile/');
define('LOG_PATH', ROOT_PATH . '/logs/');

// Session Configuration
define('SESSION_LIFETIME', 86400); // 24 hours
define('SESSION_NAME', 'lu_academic_session');

// Security Configuration
define('BCRYPT_ROUNDS', 12);
define('CSRF_TOKEN_LENGTH', 32);

// File Upload Limits
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'jpg', 'jpeg', 'png']);
define('ALLOWED_MIME_TYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'application/zip',
    'image/jpeg',
    'image/png'
]);

// Pagination
define('ITEMS_PER_PAGE', 20);
define('ITEMS_PER_PAGE_ADMIN', 50);

// Timezone
date_default_timezone_set('Africa/Kampala');

// Error Reporting
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
