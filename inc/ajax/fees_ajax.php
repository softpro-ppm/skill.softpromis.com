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
            $student_id = (int)($_POST['student_id'] ?? 0);
            $batch_id = (int)($_POST['batch_id'] ?? 0);
            $amount = (float)($_POST['amount'] ?? 0);
            $payment_date = sanitizeInput($_POST['payment_date'] ?? '');
            $payment_method = sanitizeInput($_POST['payment_method'] ?? '');
            $transaction_id = sanitizeInput($_POST['transaction_id'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'pending');
            $notes = sanitizeInput($_POST['notes'] ?? '');

            if (empty($student_id) || empty($batch_id) || $amount <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            // Validate student
            $stmt = $pdo->prepare("SELECT id FROM students WHERE id = ?");
            $stmt->execute([$student_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid student');
            }

            // Validate batch
            $stmt = $pdo->prepare("SELECT id FROM batches WHERE id = ?");
            $stmt->execute([$batch_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid batch');
            }

            // Check if student is enrolled in the batch
            $stmt = $pdo->prepare("
                SELECT id FROM batch_students 
                WHERE student_id = ? AND batch_id = ?
            ");
            $stmt->execute([$student_id, $batch_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Student is not enrolled in this batch');
            }

            $stmt = $pdo->prepare("
                INSERT INTO fees (
                    student_id, batch_id, amount, payment_date, payment_method,
                    transaction_id, status, notes, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $student_id, $batch_id, $amount, $payment_date, $payment_method,
                $transaction_id, $status, $notes
            ]);

            logAudit($_SESSION['user']['id'], 'create_fee', [
                'student_id' => $student_id,
                'batch_id' => $batch_id,
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
            $student_id = (int)($_POST['student_id'] ?? 0);
            $batch_id = (int)($_POST['batch_id'] ?? 0);

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

            if ($student_id > 0) {
                $where[] = "f.student_id = ?";
                $params[] = $student_id;
            }

            if ($batch_id > 0) {
                $where[] = "f.batch_id = ?";
                $params[] = $batch_id;
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
                SELECT f.*, s.name as student_name, b.name as batch_name,
                       c.name as course_name, tc.name as center_name
                FROM fees f
                JOIN students s ON f.student_id = s.id
                JOIN batches b ON f.batch_id = b.id
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
                SELECT f.amount, f.status, s.name as student_name, b.name as batch_name
                FROM fees f
                JOIN students s ON f.student_id = s.id
                JOIN batches b ON f.batch_id = b.id
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
                SELECT f.*, s.name as student_name, b.name as batch_name,
                       c.name as course_name, tc.name as center_name
                FROM fees f
                JOIN students s ON f.student_id = s.id
                JOIN batches b ON f.batch_id = b.id
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

        case 'get_student_fees':
            $student_id = (int)($_POST['student_id'] ?? 0);

            if (empty($student_id)) {
                sendJSONResponse(false, 'Student ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT f.*, b.name as batch_name, c.name as course_name,
                       tc.name as center_name
                FROM fees f
                JOIN batches b ON f.batch_id = b.id
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                WHERE f.student_id = ?
                ORDER BY f.payment_date DESC
            ");
            $stmt->execute([$student_id]);
            $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Student fee payments retrieved successfully', $fees);
            break;

        case 'get_batch_fees':
            $batch_id = (int)($_POST['batch_id'] ?? 0);

            if (empty($batch_id)) {
                sendJSONResponse(false, 'Batch ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT f.*, s.name as student_name
                FROM fees f
                JOIN students s ON f.student_id = s.id
                WHERE f.batch_id = ?
                ORDER BY f.payment_date DESC
            ");
            $stmt->execute([$batch_id]);
            $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Batch fee payments retrieved successfully', $fees);
            break;

        case 'list':
            $stmt = $pdo->query('
                SELECT f.id, f.receipt_no, s.first_name AS student_name, c.name AS course_name, f.amount, f.payment_date, f.payment_method, f.status
                FROM fees f
                JOIN students s ON f.student_id = s.student_id
                LEFT JOIN batches b ON f.batch_id = b.batch_id
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