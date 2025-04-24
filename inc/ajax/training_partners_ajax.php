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
            $email = sanitizeInput($_POST['email'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');

            if (empty($name) || empty($email)) {
                sendJSONResponse(false, 'Name and email are required');
            }

            if (!validateEmail($email)) {
                sendJSONResponse(false, 'Invalid email format');
            }

            if (!empty($phone) && !validatePhone($phone)) {
                sendJSONResponse(false, 'Invalid phone format');
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

            sendJSONResponse(true, 'Training partner created successfully', [
                'id' => $pdo->lastInsertId()
            ]);
            break;

        case 'read':
            $page = (int)($_POST['page'] ?? 1);
            $perPage = (int)($_POST['per_page'] ?? 10);
            $search = sanitizeInput($_POST['search'] ?? '');

            $where = '';
            $params = [];

            if (!empty($search)) {
                $searchFields = ['name', 'email', 'phone', 'address'];
                $searchResult = buildSearchQuery($searchFields, $search);
                $where = "WHERE " . $searchResult['conditions'];
                $params = $searchResult['params'];
            }

            // Get total count
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM training_partners $where");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            // Get pagination info
            $pagination = getPagination($page, $total, $perPage);

            // Get data
            $stmt = $pdo->prepare("
                SELECT * FROM training_partners 
                $where
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $pagination['per_page'], $pagination['offset']]);
            $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Training partners retrieved successfully', [
                'data' => $partners,
                'pagination' => $pagination
            ]);
            break;

        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            $name = sanitizeInput($_POST['name'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');

            if (empty($id) || empty($name) || empty($email)) {
                sendJSONResponse(false, 'ID, name and email are required');
            }

            if (!validateEmail($email)) {
                sendJSONResponse(false, 'Invalid email format');
            }

            if (!empty($phone) && !validatePhone($phone)) {
                sendJSONResponse(false, 'Invalid phone format');
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

            sendJSONResponse(true, 'Training partner updated successfully');
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            // Get partner info for audit log
            $stmt = $pdo->prepare("SELECT name, email FROM training_partners WHERE id = ?");
            $stmt->execute([$id]);
            $partner = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$partner) {
                sendJSONResponse(false, 'Training partner not found');
            }

            $stmt = $pdo->prepare("DELETE FROM training_partners WHERE id = ?");
            $stmt->execute([$id]);

            logAudit($_SESSION['user']['id'], 'delete_partner', [
                'id' => $id,
                'name' => $partner['name'],
                'email' => $partner['email']
            ]);

            sendJSONResponse(true, 'Training partner deleted successfully');
            break;

        case 'get':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            $stmt = $pdo->prepare("SELECT * FROM training_partners WHERE id = ?");
            $stmt->execute([$id]);
            $partner = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($partner) {
                sendJSONResponse(true, 'Training partner retrieved successfully', $partner);
            } else {
                sendJSONResponse(false, 'Training partner not found');
            }
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Training partners error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 