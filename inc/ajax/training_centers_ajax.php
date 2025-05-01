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
            $partner_id = (int)($_POST['partner_id'] ?? 0);
            $name = sanitizeInput($_POST['name'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            $city = sanitizeInput($_POST['city'] ?? '');
            $state = sanitizeInput($_POST['state'] ?? '');
            $pincode = sanitizeInput($_POST['pincode'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? 'active');

            if (empty($name) || empty($address) || empty($city) || empty($state) || empty($pincode)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (!empty($email) && !validateEmail($email)) {
                sendJSONResponse(false, 'Invalid email format');
            }

            if (!empty($phone) && !validatePhone($phone)) {
                sendJSONResponse(false, 'Invalid phone format');
            }

            $stmt = $pdo->prepare("
                INSERT INTO training_centers (
                    partner_id, name, address, city, state, pincode, phone, email, capacity, status, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $partner_id, $name, $address, $city, $state, $pincode, $phone, $email, $capacity, $status
            ]);

            logAudit($_SESSION['user']['id'], 'create_center', [
                'name' => $name,
                'city' => $city,
                'state' => $state
            ]);

            sendJSONResponse(true, 'Training center created successfully', [
                'id' => $pdo->lastInsertId()
            ]);
            break;

        case 'read':
            $page = (int)($_POST['page'] ?? 1);
            $perPage = (int)($_POST['per_page'] ?? 10);
            $search = sanitizeInput($_POST['search'] ?? '');
            $partner_id = (int)($_POST['partner_id'] ?? 0);

            $where = [];
            $params = [];

            if (!empty($search)) {
                $searchFields = ['name', 'address', 'city', 'state', 'pincode', 'phone', 'email'];
                $searchResult = buildSearchQuery($searchFields, $search);
                $where[] = "(" . $searchResult['conditions'] . ")";
                $params = array_merge($params, $searchResult['params']);
            }

            if ($partner_id > 0) {
                $where[] = "partner_id = ?";
                $params[] = $partner_id;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Get total count
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM training_centers $whereClause");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            // Get pagination info
            $pagination = getPagination($page, $total, $perPage);

            // Get data with partner info
            $stmt = $pdo->prepare("
                SELECT tc.*, tp.name as partner_name 
                FROM training_centers tc
                LEFT JOIN training_partners tp ON tc.partner_id = tp.id
                $whereClause
                ORDER BY tc.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $pagination['per_page'], $pagination['offset']]);
            $centers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Training centers retrieved successfully', [
                'data' => $centers,
                'pagination' => $pagination
            ]);
            break;

        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            $partner_id = (int)($_POST['partner_id'] ?? 0);
            $name = sanitizeInput($_POST['name'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            $city = sanitizeInput($_POST['city'] ?? '');
            $state = sanitizeInput($_POST['state'] ?? '');
            $pincode = sanitizeInput($_POST['pincode'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? 'active');

            if (empty($id) || empty($name) || empty($address) || empty($city) || empty($state) || empty($pincode)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (!empty($email) && !validateEmail($email)) {
                sendJSONResponse(false, 'Invalid email format');
            }

            if (!empty($phone) && !validatePhone($phone)) {
                sendJSONResponse(false, 'Invalid phone format');
            }

            $stmt = $pdo->prepare("
                UPDATE training_centers 
                SET partner_id = ?, name = ?, address = ?, city = ?, state = ?, pincode = ?,
                    phone = ?, email = ?, capacity = ?, status = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $partner_id, $name, $address, $city, $state, $pincode, $phone, $email, $capacity, $status, $id
            ]);

            logAudit($_SESSION['user']['id'], 'update_center', [
                'id' => $id,
                'name' => $name,
                'city' => $city,
                'state' => $state
            ]);

            sendJSONResponse(true, 'Training center updated successfully');
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            // Get center info for audit log
            $stmt = $pdo->prepare("SELECT name, city, state FROM training_centers WHERE id = ?");
            $stmt->execute([$id]);
            $center = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$center) {
                sendJSONResponse(false, 'Training center not found');
            }

            $stmt = $pdo->prepare("DELETE FROM training_centers WHERE id = ?");
            $stmt->execute([$id]);

            logAudit($_SESSION['user']['id'], 'delete_center', [
                'id' => $id,
                'name' => $center['name'],
                'city' => $center['city'],
                'state' => $center['state']
            ]);

            sendJSONResponse(true, 'Training center deleted successfully');
            break;

        case 'get':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT tc.*, tp.name as partner_name 
                FROM training_centers tc
                LEFT JOIN training_partners tp ON tc.partner_id = tp.id
                WHERE tc.id = ?
            ");
            $stmt->execute([$id]);
            $center = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($center) {
                sendJSONResponse(true, 'Training center retrieved successfully', $center);
            } else {
                sendJSONResponse(false, 'Training center not found');
            }
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Training centers error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}