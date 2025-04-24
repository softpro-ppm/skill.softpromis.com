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

        // Clear user's token
        $stmt = $pdo->prepare("UPDATE users SET token = NULL WHERE token = ?");
        $stmt->execute([$token]);
    } catch (PDOException $e) {
        error_log("Logout error: " . $e->getMessage());
    }
}

// Clear session data
session_unset();
session_destroy();

// Clear cookies
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to login page
header('Location: index.php');
exit; 