<?php
session_start();
require_once 'config.php';

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
        WHERE u.token = ? 
        AND u.status = 'active'
        AND u.last_login >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Update last activity
        $updateStmt = $pdo->prepare("
            UPDATE users 
            SET last_activity = NOW() 
            WHERE id = ?
        ");
        $updateStmt->execute([$user['id']]);

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
        // Clear invalid token
        $clearStmt = $pdo->prepare("
            UPDATE users 
            SET token = NULL 
            WHERE token = ?
        ");
        $clearStmt->execute([$token]);

        echo json_encode([
            'valid' => false,
            'message' => 'Session expired or invalid token'
        ]);
    }
} catch (PDOException $e) {
    error_log("Token verification error: " . $e->getMessage());
    echo json_encode([
        'valid' => false,
        'message' => 'An error occurred. Please try again later.'
    ]);
} 