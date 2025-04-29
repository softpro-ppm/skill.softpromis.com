<?php
require_once '../../config.php';
require_once '../functions.php';

startSecureSession();
checkLogin();
checkPermission('admin');

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

try {
    $pdo = getDBConnection();

    switch ($action) {
        case 'create':
            $scheme_name = sanitizeInput($_POST['scheme_name'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');

            if (empty($scheme_name)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            $stmt = $pdo->prepare("
                INSERT INTO schemes (
                    scheme_name, description, status, created_at
                ) VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([
                $scheme_name, $description, $status
            ]);

            logAudit($_SESSION['user']['user_id'], 'create_scheme', [
                'scheme_name' => $scheme_name
            ]);

            sendJSONResponse(true, 'Scheme created successfully', [
                'scheme_id' => $pdo->lastInsertId()
            ]);
            break;

        case 'read':
            $page = (int)($_POST['page'] ?? 1);
            $perPage = (int)($_POST['per_page'] ?? 10);
            $search = sanitizeInput($_POST['search'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? '');

            $where = [];
            $params = [];

            if (!empty($search)) {
                $searchFields = ['scheme_name', 'description'];
                $searchResult = buildSearchQuery($searchFields, $search);
                $where[] = "(" . $searchResult['conditions'] . ")";
                $params = array_merge($params, $searchResult['params']);
            }

            if (!empty($status)) {
                $where[] = "status = ?";
                $params[] = $status;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Get total count
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM schemes $whereClause");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            // Get pagination info
            $pagination = getPagination($page, $total, $perPage);

            // Get data
            $stmt = $pdo->prepare("
                SELECT * FROM schemes 
                $whereClause
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $pagination['per_page'], $pagination['offset']]);
            $schemes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Schemes retrieved successfully', [
                'data' => $schemes,
                'pagination' => $pagination
            ]);
            break;

        case 'update':
            $scheme_id = (int)($_POST['id'] ?? 0);
            $scheme_name = sanitizeInput($_POST['name'] ?? '');
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
            $scheme_id = (int)($_POST['id'] ?? 0);

            if (empty($scheme_id)) {
                sendJSONResponse(false, 'ID is required');
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
            $scheme_id = (int)($_POST['id'] ?? 0);

            if (empty($scheme_id)) {
                sendJSONResponse(false, 'ID is required');
            }

            $stmt = $pdo->prepare("SELECT * FROM schemes WHERE scheme_id = ?");
            $stmt->execute([$scheme_id]);
            $scheme = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($scheme) {
                sendJSONResponse(true, 'Scheme retrieved successfully', $scheme);
            } else {
                sendJSONResponse(false, 'Scheme not found');
            }
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Schemes error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 