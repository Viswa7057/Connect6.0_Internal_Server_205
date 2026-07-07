<?php
// api/auth.php - Authentication Endpoint
require_once __DIR__ . '/../db.php';

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if ($action === 'login') {
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');
        
        if (empty($username) || empty($password)) {
            respondError('Username and password are required.');
        }
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            
            respondSuccess([
                'message' => 'Logged in successfully',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'name' => $user['name']
                ]
            ]);
        } else {
            respondError('Invalid username or password.');
        }
    }
    
    if ($action === 'logout') {
        session_destroy();
        respondSuccess(['message' => 'Logged out successfully']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'status') {
        if (isset($_SESSION['user_id'])) {
            respondSuccess([
                'logged_in' => true,
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username'],
                    'name' => $_SESSION['name']
                ]
            ]);
        } else {
            respondSuccess(['logged_in' => false]);
        }
    }
}

respondError('Invalid request method or action.');
