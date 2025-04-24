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
            $certificate_number = sanitizeInput($_POST['certificate_number'] ?? '');
            $issue_date = sanitizeInput($_POST['issue_date'] ?? '');
            $valid_until = sanitizeInput($_POST['valid_until'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $remarks = sanitizeInput($_POST['remarks'] ?? '');

            if (empty($student_id) || empty($batch_id) || empty($certificate_number) || empty($issue_date)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (!empty($valid_until) && strtotime($valid_until) < strtotime($issue_date)) {
                sendJSONResponse(false, 'Valid until date cannot be before issue date');
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

            // Check if certificate number is unique
            $stmt = $pdo->prepare("
                SELECT id FROM certificates 
                WHERE certificate_number = ?
            ");
            $stmt->execute([$certificate_number]);
            if ($stmt->fetch()) {
                sendJSONResponse(false, 'Certificate number already exists');
            }

            $stmt = $pdo->prepare("
                INSERT INTO certificates (
                    student_id, batch_id, certificate_number, issue_date,
                    valid_until, status, remarks, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $student_id, $batch_id, $certificate_number, $issue_date,
                $valid_until, $status, $remarks
            ]);

            logAudit($_SESSION['user']['id'], 'create_certificate', [
                'student_id' => $student_id,
                'batch_id' => $batch_id,
                'certificate_number' => $certificate_number
            ]);

            sendJSONResponse(true, 'Certificate created successfully', [
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
                $searchFields = ['c.certificate_number', 'c.remarks'];
                $searchResult = buildSearchQuery($searchFields, $search);
                $where[] = "(" . $searchResult['conditions'] . ")";
                $params = array_merge($params, $searchResult['params']);
            }

            if (!empty($status)) {
                $where[] = "c.status = ?";
                $params[] = $status;
            }

            if ($student_id > 0) {
                $where[] = "c.student_id = ?";
                $params[] = $student_id;
            }

            if ($batch_id > 0) {
                $where[] = "c.batch_id = ?";
                $params[] = $batch_id;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Get total count
            $countStmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM certificates c
                $whereClause
            ");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            // Get pagination info
            $pagination = getPagination($page, $total, $perPage);

            // Get data with related info
            $stmt = $pdo->prepare("
                SELECT c.*, s.name as student_name, b.name as batch_name,
                       co.name as course_name, tc.name as center_name
                FROM certificates c
                JOIN students s ON c.student_id = s.id
                JOIN batches b ON c.batch_id = b.id
                LEFT JOIN courses co ON b.course_id = co.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                $whereClause
                ORDER BY c.issue_date DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $pagination['per_page'], $pagination['offset']]);
            $certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Certificates retrieved successfully', [
                'data' => $certificates,
                'pagination' => $pagination
            ]);
            break;

        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            $certificate_number = sanitizeInput($_POST['certificate_number'] ?? '');
            $issue_date = sanitizeInput($_POST['issue_date'] ?? '');
            $valid_until = sanitizeInput($_POST['valid_until'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $remarks = sanitizeInput($_POST['remarks'] ?? '');

            if (empty($id) || empty($certificate_number) || empty($issue_date)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (!empty($valid_until) && strtotime($valid_until) < strtotime($issue_date)) {
                sendJSONResponse(false, 'Valid until date cannot be before issue date');
            }

            // Check if certificate number is unique (excluding current certificate)
            $stmt = $pdo->prepare("
                SELECT id FROM certificates 
                WHERE certificate_number = ? AND id != ?
            ");
            $stmt->execute([$certificate_number, $id]);
            if ($stmt->fetch()) {
                sendJSONResponse(false, 'Certificate number already exists');
            }

            $stmt = $pdo->prepare("
                UPDATE certificates 
                SET certificate_number = ?, issue_date = ?, valid_until = ?,
                    status = ?, remarks = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $certificate_number, $issue_date, $valid_until,
                $status, $remarks, $id
            ]);

            logAudit($_SESSION['user']['id'], 'update_certificate', [
                'id' => $id,
                'certificate_number' => $certificate_number,
                'issue_date' => $issue_date
            ]);

            sendJSONResponse(true, 'Certificate updated successfully');
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            // Get certificate info for audit log
            $stmt = $pdo->prepare("
                SELECT c.certificate_number, c.issue_date, s.name as student_name,
                       b.name as batch_name
                FROM certificates c
                JOIN students s ON c.student_id = s.id
                JOIN batches b ON c.batch_id = b.id
                WHERE c.id = ?
            ");
            $stmt->execute([$id]);
            $certificate = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$certificate) {
                sendJSONResponse(false, 'Certificate not found');
            }

            $stmt = $pdo->prepare("DELETE FROM certificates WHERE id = ?");
            $stmt->execute([$id]);

            logAudit($_SESSION['user']['id'], 'delete_certificate', [
                'id' => $id,
                'certificate_number' => $certificate['certificate_number'],
                'issue_date' => $certificate['issue_date'],
                'student' => $certificate['student_name'],
                'batch' => $certificate['batch_name']
            ]);

            sendJSONResponse(true, 'Certificate deleted successfully');
            break;

        case 'get':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT c.*, s.name as student_name, b.name as batch_name,
                       co.name as course_name, tc.name as center_name
                FROM certificates c
                JOIN students s ON c.student_id = s.id
                JOIN batches b ON c.batch_id = b.id
                LEFT JOIN courses co ON b.course_id = co.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                WHERE c.id = ?
            ");
            $stmt->execute([$id]);
            $certificate = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($certificate) {
                sendJSONResponse(true, 'Certificate retrieved successfully', $certificate);
            } else {
                sendJSONResponse(false, 'Certificate not found');
            }
            break;

        case 'get_student_certificates':
            $student_id = (int)($_POST['student_id'] ?? 0);

            if (empty($student_id)) {
                sendJSONResponse(false, 'Student ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT c.*, b.name as batch_name, co.name as course_name,
                       tc.name as center_name
                FROM certificates c
                JOIN batches b ON c.batch_id = b.id
                LEFT JOIN courses co ON b.course_id = co.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                WHERE c.student_id = ?
                ORDER BY c.issue_date DESC
            ");
            $stmt->execute([$student_id]);
            $certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Student certificates retrieved successfully', $certificates);
            break;

        case 'get_batch_certificates':
            $batch_id = (int)($_POST['batch_id'] ?? 0);

            if (empty($batch_id)) {
                sendJSONResponse(false, 'Batch ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT c.*, s.name as student_name
                FROM certificates c
                JOIN students s ON c.student_id = s.id
                WHERE c.batch_id = ?
                ORDER BY c.issue_date DESC
            ");
            $stmt->execute([$batch_id]);
            $certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Batch certificates retrieved successfully', $certificates);
            break;

        case 'verify':
            $certificate_number = sanitizeInput($_POST['certificate_number'] ?? '');

            if (empty($certificate_number)) {
                sendJSONResponse(false, 'Certificate number is required');
            }

            $stmt = $pdo->prepare("
                SELECT c.*, s.name as student_name, b.name as batch_name,
                       co.name as course_name, tc.name as center_name
                FROM certificates c
                JOIN students s ON c.student_id = s.id
                JOIN batches b ON c.batch_id = b.id
                LEFT JOIN courses co ON b.course_id = co.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                WHERE c.certificate_number = ?
            ");
            $stmt->execute([$certificate_number]);
            $certificate = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($certificate) {
                // Check if certificate is valid
                $isValid = true;
                $message = 'Certificate is valid';

                if ($certificate['status'] !== 'active') {
                    $isValid = false;
                    $message = 'Certificate is not active';
                } elseif (!empty($certificate['valid_until']) && strtotime($certificate['valid_until']) < time()) {
                    $isValid = false;
                    $message = 'Certificate has expired';
                }

                sendJSONResponse(true, $message, [
                    'certificate' => $certificate,
                    'is_valid' => $isValid
                ]);
            } else {
                sendJSONResponse(false, 'Certificate not found');
            }
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Certificates error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 