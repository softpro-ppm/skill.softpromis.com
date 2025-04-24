<?php
session_start();
require_once '../../config.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'valid' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get token from request
$token = $_POST['token'] ?? '';

if (empty($token)) {
    echo json_encode([
        'valid' => false,
        'message' => 'Token is required'
    ]);
    exit;
}

try {
    // Connect to database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Prepare and execute query
    $stmt = $pdo->prepare("
        SELECT u.*, r.role_name 
        FROM users u 
        JOIN roles r ON u.role_id = r.id 
        WHERE u.token = ? AND u.status = 'active'
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if token is expired (24 hours)
        $lastLogin = strtotime($user['last_login']);
        $now = time();
        $tokenAge = $now - $lastLogin;

        if ($tokenAge <= 86400) { // 24 hours in seconds
            echo json_encode([
                'valid' => true,
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'role' => $user['role_name']
                ]
            ]);
        } else {
            // Token expired, clear it
            $updateStmt = $pdo->prepare("UPDATE users SET token = NULL WHERE id = ?");
            $updateStmt->execute([$user['id']]);

            echo json_encode([
                'valid' => false,
                'message' => 'Session expired'
            ]);
        }
    } else {
        echo json_encode([
            'valid' => false,
            'message' => 'Invalid token'
        ]);
    }
} catch (PDOException $e) {
    error_log("Token verification error: " . $e->getMessage());
    echo json_encode([
        'valid' => false,
        'message' => 'An error occurred while verifying token'
    ]);
} 