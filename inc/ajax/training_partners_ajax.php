<?php
require_once '../../config.php';
require_once '../functions.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON header 
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if user has admin privileges
if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    // Get database connection
    $pdo = getDBConnection();

    switch ($action) {
        case 'create':
            $name = sanitizeInput($_POST['name'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');

            if (empty($name) || empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Name and email are required']);
                exit;
            }

            if (!validateEmail($email)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                exit;
            }

            if (!empty($phone) && !validatePhone($phone)) {
                echo json_encode(['success' => false, 'message' => 'Invalid phone format']);
                exit;
            }

            $stmt = $pdo->prepare("
                INSERT INTO training_partners (name, email, phone, address, status, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $email, $phone, $address, $status]);

            logAudit($_SESSION['user']['id'], 'create_partner', [
                'name' => $name,
                'email' => $email
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Training partner created successfully',
                'id' => $pdo->lastInsertId()
            ]);
            exit;
            break;

        case 'read':
            $stmt = $pdo->query("SELECT * FROM training_partners ORDER BY created_at DESC");
            $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $partners
            ]);
            exit;
            break;

        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            $name = sanitizeInput($_POST['name'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');

            if (empty($id) || empty($name) || empty($email)) {
                echo json_encode(['success' => false, 'message' => 'ID, name and email are required']);
                exit;
            }

            if (!validateEmail($email)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                exit;
            }

            if (!empty($phone) && !validatePhone($phone)) {
                echo json_encode(['success' => false, 'message' => 'Invalid phone format']);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE training_partners 
                SET name = ?, email = ?, phone = ?, address = ?, status = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$name, $email, $phone, $address, $status, $id]);

            logAudit($_SESSION['user']['id'], 'update_partner', [
                'id' => $id,
                'name' => $name,
                'email' => $email
            ]);

            echo json_encode(['success' => true, 'message' => 'Training partner updated successfully']);
            exit;
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID is required']);
                exit;
            }

            // Get partner info for audit log
            $stmt = $pdo->prepare("SELECT name, email FROM training_partners WHERE id = ?");
            $stmt->execute([$id]);
            $partner = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$partner) {
                echo json_encode(['success' => false, 'message' => 'Training partner not found']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM training_partners WHERE id = ?");
            $stmt->execute([$id]);

            logAudit($_SESSION['user']['id'], 'delete_partner', [
                'id' => $id,
                'name' => $partner['name'],
                'email' => $partner['email']
            ]);

            echo json_encode(['success' => true, 'message' => 'Training partner deleted successfully']);
            exit;
            break;

        case 'get':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID is required']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM training_partners WHERE id = ?");
            $stmt->execute([$id]);
            $partner = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($partner) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Training partner retrieved successfully',
                    'data' => $partner
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Training partner not found']);
            }
            exit;
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit;
    }
} catch (PDOException $e) {
    error_log("Training partners error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
} 