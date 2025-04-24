<?php
session_start();
require_once 'config.php';

// Get user token from session
$token = $_SESSION['user']['token'] ?? null;

if ($token) {
    try {
        // Connect to database
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // Clear user's token and update last logout time
        $stmt = $pdo->prepare("
            UPDATE users 
            SET token = NULL, 
                last_logout = NOW() 
            WHERE token = ?
        ");
        $stmt->execute([$token]);
    } catch (PDOException $e) {
        error_log("Logout error: " . $e->getMessage());
    }
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Clear any other cookies
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Set a logout message in a temporary session
session_start();
$_SESSION['logout_message'] = 'You have been successfully logged out.';
session_write_close();

// Redirect to login page with a status parameter
header('Location: index.php?status=logged_out');
exit; 