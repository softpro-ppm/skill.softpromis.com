<?php
session_start();
require_once '../../config.php';

echo "test"; die;
// Set headers for JSON response
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get and sanitize input
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']) ? (bool)$_POST['remember'] : false;

// Validate input
if (empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please fill in all required fields'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid email address'
    ]);
    exit;
}

try {
    // Get database connection using the improved function
    $pdo = getDBConnection();

    // Prepare and execute query with explicit column selection
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id,
            u.username,
            u.full_name,
            u.email,
            u.password,
            u.status,
            u.role_id
        FROM users u 
        WHERE u.email = ? AND u.status = 'active'
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debug log
    error_log("User data: " . print_r($user, true));

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        // Generate token
        $token = bin2hex(random_bytes(32));
        
        // Update user's token and last_login
        $updateStmt = $pdo->prepare("UPDATE users SET token = ?, last_login = NOW() WHERE user_id = ?");
        $updateStmt->execute([$token, $user['user_id']]);

        // Prepare user data for response
        $userData = [
            'user_id' => $user['user_id'],
            'email' => $user['email'],
            'name' => $user['full_name'] ?? $user['username'] ?? 'Administrator', // Fallback chain
            'role' => 'Administrator', // Since role is in users table
            'token' => $token
        ];

        // Set session
        $_SESSION['user'] = $userData;

        echo json_encode([
            'success' => true,
            'user' => $userData
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or password'
        ]);
    }
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Unable to connect to the database. Please try again later.',
        'debug_info' => $e->getMessage()
    ]);
    exit;
} 