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
            $name = sanitizeInput($_POST['name'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $start_date = sanitizeInput($_POST['start_date'] ?? '');
            $end_date = sanitizeInput($_POST['end_date'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $funding_amount = (float)($_POST['funding_amount'] ?? 0);
            $eligibility_criteria = sanitizeInput($_POST['eligibility_criteria'] ?? '');

            if (empty($name) || empty($start_date) || empty($end_date)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (strtotime($end_date) < strtotime($start_date)) {
                sendJSONResponse(false, 'End date cannot be before start date');
            }

            $stmt = $pdo->prepare("
                INSERT INTO schemes (
                    name, description, start_date, end_date, status,
                    funding_amount, eligibility_criteria, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $name, $description, $start_date, $end_date, $status,
                $funding_amount, $eligibility_criteria
            ]);

            logAudit($_SESSION['user']['id'], 'create_scheme', [
                'name' => $name,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);

            sendJSONResponse(true, 'Scheme created successfully', [
                'id' => $pdo->lastInsertId()
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
                $searchFields = ['name', 'description', 'eligibility_criteria'];
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
            $id = (int)($_POST['id'] ?? 0);
            $name = sanitizeInput($_POST['name'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $start_date = sanitizeInput($_POST['start_date'] ?? '');
            $end_date = sanitizeInput($_POST['end_date'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $funding_amount = (float)($_POST['funding_amount'] ?? 0);
            $eligibility_criteria = sanitizeInput($_POST['eligibility_criteria'] ?? '');

            if (empty($id) || empty($name) || empty($start_date) || empty($end_date)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (strtotime($end_date) < strtotime($start_date)) {
                sendJSONResponse(false, 'End date cannot be before start date');
            }

            $stmt = $pdo->prepare("
                UPDATE schemes 
                SET name = ?, description = ?, start_date = ?, end_date = ?,
                    status = ?, funding_amount = ?, eligibility_criteria = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $name, $description, $start_date, $end_date, $status,
                $funding_amount, $eligibility_criteria, $id
            ]);

            logAudit($_SESSION['user']['id'], 'update_scheme', [
                'id' => $id,
                'name' => $name,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);

            sendJSONResponse(true, 'Scheme updated successfully');
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            // Get scheme info for audit log
            $stmt = $pdo->prepare("SELECT name, start_date, end_date FROM schemes WHERE id = ?");
            $stmt->execute([$id]);
            $scheme = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$scheme) {
                sendJSONResponse(false, 'Scheme not found');
            }

            $stmt = $pdo->prepare("DELETE FROM schemes WHERE id = ?");
            $stmt->execute([$id]);

            logAudit($_SESSION['user']['id'], 'delete_scheme', [
                'id' => $id,
                'name' => $scheme['name'],
                'start_date' => $scheme['start_date'],
                'end_date' => $scheme['end_date']
            ]);

            sendJSONResponse(true, 'Scheme deleted successfully');
            break;

        case 'get':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            $stmt = $pdo->prepare("SELECT * FROM schemes WHERE id = ?");
            $stmt->execute([$id]);
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