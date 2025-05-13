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
            $stmt = $pdo->query("SELECT s.sector_id, s.sector_name, s.description, s.status, s.center_id, s.scheme_id, 
                tc.center_name, sch.scheme_name, 
                DATE_FORMAT(s.created_at, '%Y-%m-%d %H:%i:%s') as created_at, 
                DATE_FORMAT(s.updated_at, '%Y-%m-%d %H:%i:%s') as updated_at 
                FROM sectors s 
                LEFT JOIN training_centers tc ON s.center_id = tc.center_id 
                LEFT JOIN schemes sch ON s.scheme_id = sch.scheme_id 
                ORDER BY s.created_at DESC");
            $sectors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $sectors]);
            exit;
        case 'add':
            $sector_name = sanitizeInput($_POST['sector_name'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $center_id = (int)($_POST['center_id'] ?? 0);
            $scheme_id = (int)($_POST['scheme_id'] ?? 0);
            if (empty($sector_name) || !$center_id || !$scheme_id) {
                sendJSONResponse(false, 'Sector name, Training Center, and Scheme are required');
            }
            require_once '../../crud_functions.php';
            $result = Sector::create([
                'sector_name' => $sector_name,
                'description' => $description,
                'status' => $status,
                'center_id' => $center_id,
                'scheme_id' => $scheme_id
            ]);
            logAudit($_SESSION['user']['user_id'], 'create_sector', ['sector_name' => $sector_name]);
            sendJSONResponse(true, 'Sector added successfully');
            break;
        case 'edit':
            $sector_id = (int)($_POST['sector_id'] ?? 0);
            $sector_name = sanitizeInput($_POST['sector_name'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $center_id = (int)($_POST['center_id'] ?? 0);
            $scheme_id = (int)($_POST['scheme_id'] ?? 0);
            if (empty($sector_id) || empty($sector_name) || !$center_id || !$scheme_id) {
                sendJSONResponse(false, 'Required fields are missing');
            }
            require_once '../../crud_functions.php';
            $result = Sector::update($sector_id, [
                'sector_name' => $sector_name,
                'description' => $description,
                'status' => $status,
                'center_id' => $center_id,
                'scheme_id' => $scheme_id
            ]);
            logAudit($_SESSION['user']['user_id'], 'update_sector', ['sector_id' => $sector_id, 'sector_name' => $sector_name]);
            sendJSONResponse(true, 'Sector updated successfully');
            break;
        case 'delete':
            $sector_id = (int)($_POST['sector_id'] ?? 0);
            if (empty($sector_id)) {
                sendJSONResponse(false, 'Sector ID is required');
            }
            $stmt = $pdo->prepare("SELECT sector_name FROM sectors WHERE sector_id = ?");
            $stmt->execute([$sector_id]);
            $sector = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$sector) {
                sendJSONResponse(false, 'Sector not found');
            }
            $stmt = $pdo->prepare("DELETE FROM sectors WHERE sector_id = ?");
            $stmt->execute([$sector_id]);
            logAudit($_SESSION['user']['user_id'], 'delete_sector', ['sector_id' => $sector_id, 'sector_name' => $sector['sector_name']]);
            sendJSONResponse(true, 'Sector deleted successfully');
            break;
        case 'get':
            $sector_id = (int)($_GET['sector_id'] ?? $_POST['sector_id'] ?? 0);
            if (empty($sector_id)) {
                sendJSONResponse(false, 'Sector ID is required');
            }
            $stmt = $pdo->prepare("SELECT sector_id, sector_name, description, status, center_id, scheme_id, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at, DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at FROM sectors WHERE sector_id = ?");
            $stmt->execute([$sector_id]);
            $sector = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($sector) {
                sendJSONResponse(true, 'Sector retrieved successfully', $sector);
            } else {
                sendJSONResponse(false, 'Sector not found');
            }
            break;
        case 'assign_sector':
            $sector_id = (int)($_POST['sector_id'] ?? 0);
            $scheme_id = (int)($_POST['scheme_id'] ?? 0);
            $center_id = (int)($_POST['center_id'] ?? 0);
            if (!$sector_id || !$scheme_id || !$center_id) {
                sendJSONResponse(false, 'Sector, Scheme, and Training Center are required');
            }
            // Check if already assigned
            $stmt = $pdo->prepare("SELECT id FROM assigned_sectors WHERE sector_id = ? AND scheme_id = ? AND center_id = ?");
            $stmt->execute([$sector_id, $scheme_id, $center_id]);
            if ($stmt->fetch()) {
                sendJSONResponse(false, 'This sector is already assigned to the selected scheme and center');
            }
            // Insert assignment
            $stmt = $pdo->prepare("INSERT INTO assigned_sectors (sector_id, scheme_id, center_id, assigned_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$sector_id, $scheme_id, $center_id]);
            sendJSONResponse(true, 'Sector assigned to scheme and training center successfully');
            break;
        case 'get_assigned_schemes_centers':
            $sector_id = (int)($_GET['sector_id'] ?? 0);
            if (!$sector_id) {
                sendJSONResponse(false, 'Sector ID is required');
            }
            $stmt = $pdo->prepare("SELECT a.scheme_id, a.center_id, s.scheme_name, c.center_name FROM assigned_sectors a JOIN schemes s ON a.scheme_id = s.scheme_id JOIN training_centers c ON a.center_id = c.center_id WHERE a.sector_id = ?");
            $stmt->execute([$sector_id]);
            $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendJSONResponse(true, 'Assignments fetched', $assignments);
            break;
        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Sectors error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}