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
            $enrollment_id = (int)($_POST['enrollment_id'] ?? 0);
            $amount = (float)($_POST['amount'] ?? 0);
            $payment_date = sanitizeInput($_POST['payment_date'] ?? '');
            $payment_method = sanitizeInput($_POST['payment_method'] ?? '');
            $transaction_id = sanitizeInput($_POST['transaction_id'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'pending');
            $notes = sanitizeInput($_POST['notes'] ?? '');

            if (empty($enrollment_id) || $amount <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            // Validate enrollment
            $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE id = ?");
            $stmt->execute([$enrollment_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid enrollment');
            }

            $stmt = $pdo->prepare("
                INSERT INTO fees (
                    enrollment_id, amount, payment_date, payment_method,
                    transaction_id, status, notes, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $enrollment_id, $amount, $payment_date, $payment_method,
                $transaction_id, $status, $notes
            ]);

            logAudit($_SESSION['user']['id'], 'create_fee', [
                'enrollment_id' => $enrollment_id,
                'amount' => $amount
            ]);

            sendJSONResponse(true, 'Fee payment recorded successfully', [
                'id' => $pdo->lastInsertId()
            ]);
            break;

        case 'read':
            $page = (int)($_POST['page'] ?? 1);
            $perPage = (int)($_POST['per_page'] ?? 10);
            $search = sanitizeInput($_POST['search'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? '');
            $enrollment_id = (int)($_POST['enrollment_id'] ?? 0);

            $where = [];
            $params = [];

            if (!empty($search)) {
                $searchFields = ['f.transaction_id', 'f.notes'];
                $searchResult = buildSearchQuery($searchFields, $search);
                $where[] = "(" . $searchResult['conditions'] . ")";
                $params = array_merge($params, $searchResult['params']);
            }

            if (!empty($status)) {
                $where[] = "f.status = ?";
                $params[] = $status;
            }

            if ($enrollment_id > 0) {
                $where[] = "f.enrollment_id = ?";
                $params[] = $enrollment_id;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Get total count
            $countStmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM fees f
                $whereClause
            ");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            // Get pagination info
            $pagination = getPagination($page, $total, $perPage);

            // Get data with related info
            $stmt = $pdo->prepare("
                SELECT f.*, e.student_id, e.batch_id, s.name as student_name, b.name as batch_name,
                       c.name as course_name, tc.name as center_name
                FROM fees f
                JOIN enrollments e ON f.enrollment_id = e.id
                JOIN students s ON e.student_id = s.id
                JOIN batches b ON e.batch_id = b.id
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                $whereClause
                ORDER BY f.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $pagination['per_page'], $pagination['offset']]);
            $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Fee payments retrieved successfully', [
                'data' => $fees,
                'pagination' => $pagination
            ]);
            break;

        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            $amount = (float)($_POST['amount'] ?? 0);
            $payment_date = sanitizeInput($_POST['payment_date'] ?? '');
            $payment_method = sanitizeInput($_POST['payment_method'] ?? '');
            $transaction_id = sanitizeInput($_POST['transaction_id'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'pending');
            $notes = sanitizeInput($_POST['notes'] ?? '');

            if (empty($id) || $amount <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            $stmt = $pdo->prepare("
                UPDATE fees 
                SET amount = ?, payment_date = ?, payment_method = ?,
                    transaction_id = ?, status = ?, notes = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $amount, $payment_date, $payment_method,
                $transaction_id, $status, $notes, $id
            ]);

            logAudit($_SESSION['user']['id'], 'update_fee', [
                'id' => $id,
                'amount' => $amount,
                'status' => $status
            ]);

            sendJSONResponse(true, 'Fee payment updated successfully');
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            // Get fee info for audit log
            $stmt = $pdo->prepare("
                SELECT f.amount, f.status, e.student_id, e.batch_id, s.name as student_name, b.name as batch_name
                FROM fees f
                JOIN enrollments e ON f.enrollment_id = e.id
                JOIN students s ON e.student_id = s.id
                JOIN batches b ON e.batch_id = b.id
                WHERE f.id = ?
            ");
            $stmt->execute([$id]);
            $fee = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$fee) {
                sendJSONResponse(false, 'Fee payment not found');
            }

            $stmt = $pdo->prepare("DELETE FROM fees WHERE id = ?");
            $stmt->execute([$id]);

            logAudit($_SESSION['user']['id'], 'delete_fee', [
                'id' => $id,
                'amount' => $fee['amount'],
                'status' => $fee['status'],
                'student' => $fee['student_name'],
                'batch' => $fee['batch_name']
            ]);

            sendJSONResponse(true, 'Fee payment deleted successfully');
            break;

        case 'get':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT f.*, e.student_id, e.batch_id, s.name as student_name, b.name as batch_name,
                       c.name as course_name, tc.name as center_name
                FROM fees f
                JOIN enrollments e ON f.enrollment_id = e.id
                JOIN students s ON e.student_id = s.id
                JOIN batches b ON e.batch_id = b.id
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                WHERE f.id = ?
            ");
            $stmt->execute([$id]);
            $fee = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($fee) {
                sendJSONResponse(true, 'Fee payment retrieved successfully', $fee);
            } else {
                sendJSONResponse(false, 'Fee payment not found');
            }
            break;

        case 'getDetails':
            $enrollment_id = (int)($_POST['enrollment_id'] ?? 0);

            if (empty($enrollment_id)) {
                sendJSONResponse(false, 'Enrollment ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT e.*, s.name as student_name, b.name as batch_name,
                       c.name as course_name, tc.name as center_name
                FROM enrollments e
                JOIN students s ON e.student_id = s.id
                JOIN batches b ON e.batch_id = b.id
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                WHERE e.id = ?
            ");
            $stmt->execute([$enrollment_id]);
            $details = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($details) {
                sendJSONResponse(true, 'Enrollment details retrieved successfully', $details);
            } else {
                sendJSONResponse(false, 'Enrollment not found');
            }
            break;

        case 'list':
            $stmt = $pdo->query('
                SELECT f.id, f.receipt_no, e.student_id, e.batch_id, s.first_name AS student_name, c.name AS course_name, f.amount, f.payment_date, f.payment_method, f.status
                FROM fees f
                JOIN enrollments e ON f.enrollment_id = e.id
                JOIN students s ON e.student_id = s.id
                LEFT JOIN batches b ON e.batch_id = b.id
                LEFT JOIN courses c ON b.course_id = c.course_id
                ORDER BY f.created_at DESC
            ');
            $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([ 'data' => $fees ]);
            exit;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Fees error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}