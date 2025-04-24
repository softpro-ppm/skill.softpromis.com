<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'u820431346_skill');
define('DB_PASS', 'w:A85&!J5p');
define('DB_NAME', 'u820431346_skill');

// Application configuration
define('SITE_NAME', 'Softpro Skill Solutions');
define('SITE_URL', 'http://localhost/skill.softpromis.com');
define('UPLOAD_PATH', __DIR__ . '/uploads');
define('DEFAULT_TIMEZONE', 'Asia/Kolkata');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Database connection
function getDBConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Helper functions
function clean($string) {
    return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . SITE_URL . "/" . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function checkPermission($required_role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $required_role) {
        redirect('login.php');
    }
}

function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 
        ceil($length/strlen($x)))), 1, $length);
}

// Flash messages
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = array(
        'type' => $type,
        'message' => $message
    );
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// API Response helper
function jsonResponse($success, $data = null, $message = '') {
    header('Content-Type: application/json');
    echo json_encode(array(
        'success' => $success,
        'data' => $data,
        'message' => $message
    ));
    exit();
}
?> 