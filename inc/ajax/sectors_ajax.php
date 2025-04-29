<?php
require_once '../../config.php';
require_once '../functions.php';

startSecureSession();
checkLogin();
checkPermission('admin');

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

function handleFileUpload($fileKey, $uploadDir = '../../uploads/sectors/') {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $filename = uniqid() . '_' . basename($_FILES[$fileKey]['name']);
    $targetPath = $uploadDir . $filename;
    if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath)) {
        return 'uploads/sectors/' . $filename;
    }
    return null;
}

try {
    $pdo = getDBConnection();

    switch ($action) {
        case 'create':
            $sector_name = sanitizeInput($_POST['sector_name'] ?? '');
            $sector_code = sanitizeInput($_POST['sector_code'] ?? '');
            $sector_type = sanitizeInput($_POST['sector_type'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $job_roles = sanitizeInput($_POST['job_roles'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $sector_document = handleFileUpload('sector_document');
            $curriculum_document = handleFileUpload('curriculum_document');

            if (empty($sector_name) || empty($sector_code)) {
                sendJSONResponse(false, 'Sector Name and Code are required');
            }

            $stmt = $pdo->prepare("INSERT INTO sectors (sector_name, sector_code, sector_type, description, job_roles, sector_document, curriculum_document, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $sector_name, $sector_code, $sector_type, $description, $job_roles, $sector_document, $curriculum_document, $status
            ]);

            logAudit($_SESSION['user']['user_id'], 'create_sector', [
                'sector_name' => $sector_name,
                'sector_code' => $sector_code
            ]);

            sendJSONResponse(true, 'Sector created successfully', [
                'sector_id' => $pdo->lastInsertId()
            ]);
            break;

        case 'update':
            $sector_id = (int)($_POST['sector_id'] ?? 0);
            $sector_name = sanitizeInput($_POST['sector_name'] ?? '');
            $sector_code = sanitizeInput($_POST['sector_code'] ?? '');
            $sector_type = sanitizeInput($_POST['sector_type'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $job_roles = sanitizeInput($_POST['job_roles'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');

            // File uploads (optional, only update if new file is uploaded)
            $sector_document = handleFileUpload('sector_document');
            $curriculum_document = handleFileUpload('curriculum_document');

            if (empty($sector_id) || empty($sector_name) || empty($sector_code)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            // Build dynamic SQL for optional file updates
            $fields = [
                'sector_name = ?',
                'sector_code = ?',
                'sector_type = ?',
                'description = ?',
                'job_roles = ?',
                'status = ?',
                'updated_at = NOW()'
            ];
            $params = [
                $sector_name, $sector_code, $sector_type, $description, $job_roles, $status
            ];
            if ($sector_document) {
                $fields[] = 'sector_document = ?';
                $params[] = $sector_document;
            }
            if ($curriculum_document) {
                $fields[] = 'curriculum_document = ?';
                $params[] = $curriculum_document;
            }
            $params[] = $sector_id;

            $sql = "UPDATE sectors SET " . implode(', ', $fields) . " WHERE sector_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            logAudit($_SESSION['user']['user_id'], 'update_sector', [
                'sector_id' => $sector_id,
                'sector_name' => $sector_name
            ]);

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
            logAudit($_SESSION['user']['user_id'], 'delete_sector', [
                'sector_id' => $sector_id,
                'sector_name' => $sector['sector_name']
            ]);
            sendJSONResponse(true, 'Sector deleted successfully');
            break;

        case 'get':
            $sector_id = (int)($_POST['sector_id'] ?? 0);
            if (empty($sector_id)) {
                sendJSONResponse(false, 'Sector ID is required');
            }
            $stmt = $pdo->prepare("SELECT * FROM sectors WHERE sector_id = ?");
            $stmt->execute([$sector_id]);
            $sector = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($sector) {
                sendJSONResponse(true, 'Sector retrieved successfully', $sector);
            } else {
                sendJSONResponse(false, 'Sector not found');
            }
            break;

        case 'check_code':
            $sector_code = sanitizeInput($_POST['sector_code'] ?? '');
            if (empty($sector_code)) {
                sendJSONResponse(false, 'Sector code is required');
            }
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM sectors WHERE sector_code = ?");
            $stmt->execute([$sector_code]);
            $exists = $stmt->fetchColumn() > 0;
            sendJSONResponse(true, 'Check complete', ['exists' => $exists]);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Sectors error: " . $e->getMessage());
    if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
        sendJSONResponse(false, 'Sector Code already exists. Please use a unique code.');
    } else {
        sendJSONResponse(false, 'An error occurred. Please try again later.');
    }
} 