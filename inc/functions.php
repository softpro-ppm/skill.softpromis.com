<?php
// Session management
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.gc_maxlifetime', 3600); // 1 hour
        ini_set('session.use_strict_mode', 1);
        
        session_start();
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['last_regeneration']) || 
            time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
}

// Authentication functions
function checkLogin() {
    require_once __DIR__ . '/auth_check.php';
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit;
    }
}

function checkPermission($requiredRole) {
    require_once __DIR__ . '/auth_check.php';
    if (!hasRole($requiredRole)) {
        echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
        exit;
    }
}

// Database functions - Make sure we're not trying to use constants from config.php
if (!function_exists('getDBConnection')) {
    function getDBConnection() {
        static $pdo = null;
        
        if ($pdo !== null) {
            return $pdo; // Return existing connection if available
        }
        
        // Get DB parameters from config constants
        $host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $dbname = defined('DB_NAME') ? DB_NAME : 'u820431346_skill';
        $user = defined('DB_USER') ? DB_USER : 'u820431346_skill';
        $pass = defined('DB_PASS') ? DB_PASS : 'w:A85&!J5p';
        
        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            // Log the error
            error_log("Database connection error: " . $e->getMessage());
            
            // If this is an AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['success' => false, 'message' => 'Database connection error']);
                exit;
            }
            
            // Regular page request
            echo "Database connection error. Please try again later or contact support.";
            exit;
        }
    }
}

// Input validation functions
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}

// Response functions
function sendJSONResponse($success, $message, $data = null) {
    $response = ['success' => $success, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

// Logging functions
function logError($message, $context = []) {
    $logMessage = date('Y-m-d H:i:s') . " - " . $message;
    if (!empty($context)) {
        $logMessage .= " - Context: " . json_encode($context);
    }
    error_log($logMessage);
}

// Token management
function generateToken() {
    return bin2hex(random_bytes(32));
}

function validateToken($token) {
    return !empty($token) && strlen($token) === 64;
}

// File handling
function validateFileUpload($file, $allowedTypes, $maxSize) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return false;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($file['size'] > $maxSize) {
        return false;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    return in_array($mimeType, $allowedTypes);
}

// Date formatting
function formatDate($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

// Pagination
function getPagination($page, $total, $perPage) {
    $totalPages = ceil($total / $perPage);
    $page = max(1, min($page, $totalPages));
    
    return [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'per_page' => $perPage,
        'total' => $total,
        'offset' => ($page - 1) * $perPage
    ];
}

// Search and filter
function buildSearchQuery($searchFields, $searchTerm) {
    $conditions = [];
    $params = [];
    
    foreach ($searchFields as $field) {
        $conditions[] = "$field LIKE ?";
        $params[] = "%$searchTerm%";
    }
    
    return [
        'conditions' => implode(' OR ', $conditions),
        'params' => $params
    ];
}

// Export functions
function generateCSV($data, $filename) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
}

// Notification functions
function sendNotification($userId, $message, $type = 'info') {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, message, type, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute([$userId, $message, $type]);
    } catch (PDOException $e) {
        logError("Notification error: " . $e->getMessage());
        return false;
    }
}

// Audit logging
function logAudit($userId, $action, $details) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (user_id, action, details, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute([$userId, $action, json_encode($details)]);
    } catch (PDOException $e) {
        logError("Audit log error: " . $e->getMessage());
        return false;
    }
} 