<?php
// Environment configuration
$is_local = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');

// Database configuration
if ($is_local) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'skill');
    define('SITE_URL', 'http://localhost/skill.softpromis.com');
} else {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'u820431346_skill');
    define('DB_PASS', 'c^79r]SE');
    define('DB_NAME', 'u820431346_skill');
    define('SITE_URL', 'https://skill.softpromis.com');
}

// Application configuration
define('SITE_NAME', 'Softpro Skill Solutions');
define('UPLOAD_PATH', __DIR__ . '/uploads');
define('DEFAULT_TIMEZONE', 'Asia/Kolkata');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1); // Enable error display for debugging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection function with better error handling
function getDBConnection() {
    try {
        static $conn = null;
        if ($conn !== null) {
            return $conn;
        }

        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];

        try {
            // First try connecting to the database
            $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
            return $conn;
        } catch (PDOException $e) {
            // If database doesn't exist and we're in local environment, try to create it
            if ($e->getCode() == 1049 && ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1')) {
                $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS, $options);
                $conn->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
                return $conn;
            }
            throw $e;
        }
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw new Exception("Database connection failed. Please check your database settings.");
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