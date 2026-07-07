<?php
// api/users.php - User Management Endpoint
require_once __DIR__ . '/../db.php';

// Ensure the caller is authenticated
checkAuth();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // List all users
    try {
        $stmt = $pdo->query("SELECT id, username, name, created_at FROM users ORDER BY id DESC");
        $users = $stmt->fetchAll();
        respondSuccess(['users' => $users]);
    } catch (\PDOException $e) {
        respondError('Database error: ' . $e->getMessage());
    }
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $_GET['action'] ?? '';
    
    if ($action === 'create') {
        $username = trim($input['username'] ?? '');
        $name = trim($input['name'] ?? '');
        $password = trim($input['password'] ?? '');
        
        if (empty($username) || empty($name) || empty($password)) {
            respondError('All fields (username, name, password) are required.');
        }
        
        if (strlen($username) < 3) {
            respondError('Username must be at least 3 characters long.');
        }
        
        if (strlen($password) < 6) {
            respondError('Password must be at least 6 characters long.');
        }
        
        try {
            // Check if username already exists
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $checkStmt->execute([$username]);
            if ($checkStmt->fetchColumn() > 0) {
                respondError('Username is already taken.');
            }
            
            // Create user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $pdo->prepare("INSERT INTO users (username, name, password) VALUES (?, ?, ?)");
            $insertStmt->execute([$username, $name, $hashedPassword]);
            
            respondSuccess(['message' => 'User created successfully']);
        } catch (\PDOException $e) {
            respondError('Database error: ' . $e->getMessage());
        }
    }
    
    if ($action === 'delete') {
        $userId = intval($input['id'] ?? 0);
        
        if ($userId <= 0) {
            respondError('Valid user ID is required.');
        }
        
        // Prevent deleting oneself
        if ($userId === intval($_SESSION['user_id'])) {
            respondError('You cannot delete your own account while logged in.');
        }
        
        try {
            // Check if the user is the primary admin (id = 1 or username = 'admin') as a safety measure
            $checkStmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
            $checkStmt->execute([$userId]);
            $usernameToDelete = $checkStmt->fetchColumn();
            
            if ($usernameToDelete === 'admin') {
                respondError('The primary admin account cannot be deleted.');
            }
            
            $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $deleteStmt->execute([$userId]);
            
            respondSuccess(['message' => 'User deleted successfully']);
        } catch (\PDOException $e) {
            respondError('Database error: ' . $e->getMessage());
        }
    }
}

respondError('Invalid request method or action.');
