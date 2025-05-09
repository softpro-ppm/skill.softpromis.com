<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

// Function to check user role
function hasRole($requiredRole) {
    if (!isset($_SESSION['user']['role'])) {
        return false;
    }
    
    $userRole = strtolower($_SESSION['user']['role']);
    $requiredRole = strtolower($requiredRole);
    
    // Check for admin roles
    if ($requiredRole === 'admin') {
        return in_array($userRole, ['admin', 'administrator']);
    }
    
    return $userRole === $requiredRole;
}

// Check if user is logged in, if not redirect to login
if (!isLoggedIn()) {
    // Store the current page URL in session for redirect after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    
    // Redirect to login
    header('Location: ' . SITE_URL . '/login_fix.php');
    exit;
}
?> 