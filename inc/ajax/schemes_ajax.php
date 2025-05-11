<?php
require_once '../../config.php';
require_once '../functions.php';

startSecureSession();
checkLogin();
checkPermission('admin');

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

try {
    $pdo = getDBConnection();

    switch ($action) {
        case 'list':
            // Get all schemes for DataTables
            $stmt = $pdo->query("
                SELECT 
                    scheme_id,
                    scheme_name,
                    description,
                    status,
                    DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at,
                    DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at
                FROM schemes 
                ORDER BY created_at DESC
            ");
            $schemes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format for DataTables
            $response = [
                'status' => 'success',
                'message' => 'Schemes retrieved successfully',
                'data' => $schemes
            ];
            
            echo json_encode($response);
            exit;
            break;

        case 'add':
            $scheme_name = sanitizeInput($_POST['scheme_name'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');

            if (empty($scheme_name)) {
                sendJSONResponse(false, 'Scheme name is required');
            }

            $stmt = $pdo->prepare("
                INSERT INTO schemes (
                    scheme_name, description, status, created_at, updated_at
                ) VALUES (?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $scheme_name, $description, $status
            ]);

            logAudit($_SESSION['user']['user_id'], 'create_scheme', [
                'scheme_name' => $scheme_name
            ]);

            sendJSONResponse(true, 'Scheme added successfully');
            break;

        case 'edit':
            $scheme_id = (int)($_POST['scheme_id'] ?? 0);
            $scheme_name = sanitizeInput($_POST['scheme_name'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');

            if (empty($scheme_id) || empty($scheme_name)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            $stmt = $pdo->prepare("
                UPDATE schemes 
                SET scheme_name = ?, description = ?, status = ?, updated_at = NOW()
                WHERE scheme_id = ?
            ");
            $stmt->execute([
                $scheme_name, $description, $status, $scheme_id
            ]);

            logAudit($_SESSION['user']['user_id'], 'update_scheme', [
                'scheme_id' => $scheme_id,
                'scheme_name' => $scheme_name
            ]);

            sendJSONResponse(true, 'Scheme updated successfully');
            break;

        case 'delete':
            $scheme_id = (int)($_POST['scheme_id'] ?? 0);

            if (empty($scheme_id)) {
                sendJSONResponse(false, 'Scheme ID is required');
            }

            // Get scheme info for audit log
            $stmt = $pdo->prepare("SELECT scheme_name FROM schemes WHERE scheme_id = ?");
            $stmt->execute([$scheme_id]);
            $scheme = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$scheme) {
                sendJSONResponse(false, 'Scheme not found');
            }

            $stmt = $pdo->prepare("DELETE FROM schemes WHERE scheme_id = ?");
            $stmt->execute([$scheme_id]);

            logAudit($_SESSION['user']['user_id'], 'delete_scheme', [
                'scheme_id' => $scheme_id,
                'scheme_name' => $scheme['scheme_name']
            ]);

            sendJSONResponse(true, 'Scheme deleted successfully');
            break;

        case 'get':
            $scheme_id = (int)($_GET['scheme_id'] ?? 0);

            if (empty($scheme_id)) {
                sendJSONResponse(false, 'Scheme ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT 
                    scheme_id,
                    scheme_name,
                    description,
                    status,
                    DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at,
                    DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at
                FROM schemes 
                WHERE scheme_id = ?
            ");
            $stmt->execute([$scheme_id]);
            $scheme = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($scheme) {
                sendJSONResponse(true, 'Scheme retrieved successfully', $scheme);
            } else {
                sendJSONResponse(false, 'Scheme not found');
            }
            break;

        case 'assign_scheme':
            $scheme_id = (int)($_POST['scheme_id'] ?? 0);
            $center_id = (int)($_POST['center_id'] ?? 0);
            if (!$scheme_id || !$center_id) {
                sendJSONResponse(false, 'Scheme and Training Center are required');
            }
            // Check if already assigned
            $stmt = $pdo->prepare("SELECT id FROM assigned_schemes WHERE scheme_id = ? AND center_id = ?");
            $stmt->execute([$scheme_id, $center_id]);
            if ($stmt->fetch()) {
                sendJSONResponse(false, 'This scheme is already assigned to the selected center');
            }
            // Insert assignment
            $stmt = $pdo->prepare("INSERT INTO assigned_schemes (scheme_id, center_id, assigned_at) VALUES (?, ?, NOW())");
            $stmt->execute([$scheme_id, $center_id]);
            sendJSONResponse(true, 'Scheme assigned to training center successfully');
            break;

        case 'get_assigned_centers':
            $scheme_id = (int)($_GET['scheme_id'] ?? 0);
            if (!$scheme_id) {
                sendJSONResponse(false, 'Scheme ID is required');
            }
            $stmt = $pdo->prepare("SELECT ac.center_id, tc.center_name FROM assigned_schemes ac JOIN training_centers tc ON ac.center_id = tc.center_id WHERE ac.scheme_id = ?");
            $stmt->execute([$scheme_id]);
            $centers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendJSONResponse(true, 'Assigned centers fetched', $centers);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Schemes error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}