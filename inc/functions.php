<?php
// Session management
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1);
        session_start();
    }
}

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

function checkLogin() {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit;
    }
}

function checkPermission($requiredRole) {
    if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== $requiredRole) {
        echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
        exit;
    }
}

// Database functions
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database connection error']);
        exit;
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