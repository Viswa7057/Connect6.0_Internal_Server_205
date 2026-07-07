<?php
// db.php - Database connection settings and auto-seeding

$host = 'localhost';
$db   = 'voiceblast_db';
$user = 'root';
$pass = 'Hal0o(0m@72427242'; // Set your database password here
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // If the database voiceblast_db does not exist, connect to MySQL directly and create it
    try {
        $tempDsn = "mysql:host=$host;charset=$charset";
        $tempPdo = new PDO($tempDsn, $user, $pass, $options);
        $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `voiceblast_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Now try connecting to the database again
        $pdo = new PDO($dsn, $user, $pass, $options);
        
        // Run schema.sql if it exists
        $schemaFile = __DIR__ . '/schema.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            $pdo->exec($sql);
        }
    } catch (\PDOException $ex) {
        // Output JSON error for API calls or a clean message
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database connection failed: ' . $ex->getMessage()]);
        exit;
    }
}

// Ensure ivr_ratings table exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS ivr_ratings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        calledid VARCHAR(50) NULL,
        callerid VARCHAR(50) NULL,
        ivr_name VARCHAR(100) NULL,
        uniq VARCHAR(50) NULL,
        channel VARCHAR(100) NULL,
        rating VARCHAR(10) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (\PDOException $e) {
    // Ignore error
}

// Ensure lists table has is_paused column
try {
    $pdo->exec("ALTER TABLE lists ADD COLUMN is_paused TINYINT(1) DEFAULT 0;");
} catch (\PDOException $e) {
    // Ignore if column already exists
}

// Auto-seed admin user if the users table is empty
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        $username = 'admin';
        $name = 'System Admin';
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        
        $insertStmt = $pdo->prepare("INSERT INTO users (username, name, password) VALUES (?, ?, ?)");
        $insertStmt->execute([$username, $name, $password]);
    }
} catch (\PDOException $e) {
    // Table might not be created yet, ignore error
}

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * API response helper functions
 */
function respondSuccess($data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['status' => 'success'], $data));
    exit;
}

function respondError($message, $code = 400) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit;
}

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        respondError('Unauthorized access. Please login.', 401);
    }
}
