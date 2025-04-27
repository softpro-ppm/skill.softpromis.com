<?php
require_once '../../inc/auth_check.php';
require_once '../../config.php';
require_once '../functions.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if user has admin privileges
if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'Administrator') {
    error_log("Access denied: User role is " . ($_SESSION['user']['role'] ?? 'not set'));
    echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
    exit;
}

// Get the action
$action = $_POST['action'] ?? '';
error_log("Training partners action: " . $action);

try {
    $pdo = getDBConnection();

    // First, ensure the table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS training_partners (
        partner_id INT AUTO_INCREMENT PRIMARY KEY,
        partner_name VARCHAR(100) NOT NULL,
        contact_person VARCHAR(100),
        email VARCHAR(100),
        phone VARCHAR(15),
        address TEXT,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    switch ($action) {
        case 'list':
            // Get all training partners
            $stmt = $pdo->prepare("
                SELECT * FROM training_partners 
                ORDER BY partner_name ASC
            ");
            $stmt->execute();
            $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Found " . count($partners) . " partners");
            echo json_encode([
                'success' => true,
                'data' => $partners
            ]);
            break;

        case 'add':
            // Validate input
            $partner_name = trim($_POST['partner_name'] ?? '');
            $contact_person = trim($_POST['contact_person'] ?? '');
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $status = $_POST['status'] ?? 'active';

            if (empty($partner_name)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Partner name is required'
                ]);
                exit;
            }

            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please enter a valid email address'
                ]);
                exit;
            }

            // Insert new partner
            $stmt = $pdo->prepare("
                INSERT INTO training_partners (partner_name, contact_person, email, phone, address, status)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$partner_name, $contact_person, $email, $phone, $address, $status]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Training partner added successfully'
            ]);
            break;

        case 'get':
            // Get partner details
            $partner_id = (int)$_POST['partner_id'];
            
            $stmt = $pdo->prepare("
                SELECT * FROM training_partners 
                WHERE partner_id = ?
            ");
            $stmt->execute([$partner_id]);
            $partner = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($partner) {
                echo json_encode([
                    'success' => true,
                    'data' => $partner
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Partner not found'
                ]);
            }
            break;

        case 'update':
            // Validate input
            $partner_id = (int)$_POST['partner_id'];
            $partner_name = trim($_POST['partner_name'] ?? '');
            $contact_person = trim($_POST['contact_person'] ?? '');
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $status = $_POST['status'] ?? 'active';

            if (empty($partner_name)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Partner name is required'
                ]);
                exit;
            }

            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please enter a valid email address'
                ]);
                exit;
            }

            // Update partner
            $stmt = $pdo->prepare("
                UPDATE training_partners 
                SET partner_name = ?, contact_person = ?, email = ?, phone = ?, 
                    address = ?, status = ?
                WHERE partner_id = ?
            ");
            $stmt->execute([$partner_name, $contact_person, $email, $phone, $address, $status, $partner_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Training partner updated successfully'
            ]);
            break;

        case 'delete':
            // Delete partner
            $partner_id = (int)$_POST['partner_id'];
            
            $stmt = $pdo->prepare("
                DELETE FROM training_partners 
                WHERE partner_id = ?
            ");
            $stmt->execute([$partner_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Training partner deleted successfully'
            ]);
            break;

        case 'list_all':
            // Fetch only active partners for dropdowns
            $stmt = $pdo->prepare("SELECT partner_id, partner_name FROM training_partners WHERE status = 'active' ORDER BY partner_name ASC");
            $stmt->execute();
            $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'success' => true,
                'message' => 'Partners list retrieved successfully',
                'data' => ['partners' => $partners]
            ]);
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            break;
    }
} catch (PDOException $e) {
    error_log("Training partners error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Training partners error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred'
    ]);
} 