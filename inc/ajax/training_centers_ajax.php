<?php
// DEBUG: Show all errors (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
            $center_name = sanitizeInput($_POST['center_name'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            $city = sanitizeInput($_POST['city'] ?? '');
            $state = sanitizeInput($_POST['state'] ?? '');
            $pincode = sanitizeInput($_POST['pincode'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? 'active');

            if (empty($center_name) || empty($address) || empty($city) || empty($state) || empty($pincode)) {
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
                    partner_id, center_name, address, city, state, pincode, phone, email, capacity, status, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $partner_id, $center_name, $address, $city, $state, $pincode, $phone, $email, $capacity, $status
            ]);

            logAudit($_SESSION['user']['id'], 'create_center', [
                'center_name' => $center_name,
                'city' => $city,
                'state' => $state
            ]);

            sendJSONResponse(true, 'Training center created successfully', [
                'center_id' => $pdo->lastInsertId()
            ]);
            break;

        case 'read':
            $page = (int)($_POST['page'] ?? 1);
            $perPage = (int)($_POST['per_page'] ?? 10);
            $partner_id = (int)($_POST['partner_id'] ?? 0);

            $where = [];
            $params = [];

            if ($partner_id > 0) {
                $where[] = "tc.partner_id = ?";
                $params[] = $partner_id;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            $stmt = $pdo->prepare("
                SELECT tc.center_id, tc.center_name
                FROM training_centers tc
                $whereClause
                ORDER BY tc.center_id DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $perPage, ($page-1)*$perPage]);
            $centers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Training centers retrieved successfully', $centers);
            break;

        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            $partner_id = (int)($_POST['partner_id'] ?? 0);
            $center_name = sanitizeInput($_POST['center_name'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            $city = sanitizeInput($_POST['city'] ?? '');
            $state = sanitizeInput($_POST['state'] ?? '');
            $pincode = sanitizeInput($_POST['pincode'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? 'active');

            if (empty($id) || empty($center_name) || empty($address) || empty($city) || empty($state) || empty($pincode)) {
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
                SET partner_id = ?, center_name = ?, address = ?, city = ?, state = ?, pincode = ?,
                    phone = ?, email = ?, capacity = ?, status = ?, updated_at = NOW()
                WHERE center_id = ?
            ");
            $stmt->execute([
                $partner_id, $center_name, $address, $city, $state, $pincode, $phone, $email, $capacity, $status, $id
            ]);

            logAudit($_SESSION['user']['id'], 'update_center', [
                'id' => $id,
                'center_name' => $center_name,
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
            $stmt = $pdo->prepare("SELECT center_name, city, state FROM training_centers WHERE center_id = ?");
            $stmt->execute([$id]);
            $center = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$center) {
                sendJSONResponse(false, 'Training center not found');
            }

            $stmt = $pdo->prepare("DELETE FROM training_centers WHERE center_id = ?");
            $stmt->execute([$id]);

            logAudit($_SESSION['user']['id'], 'delete_center', [
                'id' => $id,
                'name' => $center['center_name'],
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
                SELECT tc.center_id, tc.center_name, tc.partner_id, 
                       tc.contact_person, tc.email, tc.phone, tc.address, 
                       tc.city, tc.state, tc.pincode, tc.status,
                       tp.partner_name
                FROM training_centers tc
                LEFT JOIN training_partners tp ON tc.partner_id = tp.partner_id
                WHERE tc.center_id = ?
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
    echo $e->getMessage(); // DEBUG: Show the PDO error in the response for troubleshooting
    logError("Training centers error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}