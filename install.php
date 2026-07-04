<?php
/**
 * Installation Script
 * Sets up the database and creates initial configuration
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if already installed
if (file_exists('includes/config/installed.php')) {
    die('System is already installed. Please delete includes/config/installed.php to reinstall.');
}

// Load database schema
$schema = file_get_contents('database.sql');

// Create database connection
try {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'lira_university_repository';
    
    // Connect without database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");
    
    // Execute schema
    $pdo->exec($schema);
    
    // Create config file
    $configContent = "<?php\n";
    $configContent .= "define('DB_HOST', '$host');\n";
    $configContent .= "define('DB_NAME', '$dbname');\n";
    $configContent .= "define('DB_USER', '$username');\n";
    $configContent .= "define('DB_PASS', '$password');\n";
    $configContent .= "define('ENVIRONMENT', 'production');\n";
    $configContent .= "?>";
    
    file_put_contents('includes/config/config.php', $configContent);
    
    // Create installed flag
    file_put_contents('includes/config/installed.php', '<?php // System installed on ' . date('Y-m-d H:i:s'));
    
    // Create upload directories
    $directories = [
        'uploads',
        'uploads/papers',
        'uploads/research',
        'uploads/profile',
        'logs',
        'backups'
    ];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }
    
    echo "Installation completed successfully!<br>";
    echo "You can now <a href='index.php'>visit the site</a> or <a href='auth/login.php'>login</a>.<br>";
    echo "Default admin: admin@lirauniversity.ac.ug / Admin@2024<br>";
    echo "Default student: student@example.com / Student@2024";
    
} catch (PDOException $e) {
    die("Installation failed: " . $e->getMessage());
}
