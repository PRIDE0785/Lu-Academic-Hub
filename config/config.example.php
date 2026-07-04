<?php
// Copy to config/config.php and fill values.
// Use PDO with prepared statements (no mysqli) and environment-based configuration for production.

return [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=lu_academic_hub;charset=utf8mb4',
        'username' => 'db_user',
        'password' => 'db_pass',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ],
    'app' => [
        'base_url' => 'http://localhost/lu-academic-hub',
        'upload_path' => __DIR__ . '/../uploads',
        'max_upload_size' => 50 * 1024 * 1024, // 50MB
        'allowed_extensions' => ['pdf','docx','doc','pptx','ppt','zip','jpg','jpeg','png','gif'],
        'csrf_token_name' => 'lu_csrf_token',
    ],
    'mail' => [
         // SMTP config for email verification, notifications
    ],
];
