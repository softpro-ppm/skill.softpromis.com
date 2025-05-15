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

    if ($action === 'get_enrollments') {
        // Return all enrollments with student names for the select
        $stmt = $pdo->prepare("SELECT e.enrollment_id, CONCAT(s.first_name, ' ', s.last_name) AS student_name FROM student_batch_enrollment e JOIN students s ON e.student_id = s.student_id");
        $stmt->execute();
        $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        sendJSONResponse(true, 'Enrollments fetched', $enrollments);
    }

    switch ($action) {
        case 'create':
            $enrollment_id = (int)($_POST['enrollment_id'] ?? 0);
            $certificate_number = sanitizeInput($_POST['certificate_number'] ?? '');
            $certificate_type = sanitizeInput($_POST['certificate_type'] ?? '');
            $issue_date = sanitizeInput($_POST['issue_date'] ?? '');
            $valid_until = sanitizeInput($_POST['valid_until'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'issued');
            $remarks = sanitizeInput($_POST['remarks'] ?? '');

            if (empty($enrollment_id) || empty($certificate_number) || empty($issue_date) || empty($certificate_type)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            // Look up student_id and batch_id from enrollment
            $stmt = $pdo->prepare("SELECT student_id, batch_id FROM student_batch_enrollment WHERE enrollment_id = ?");
            $stmt->execute([$enrollment_id]);
            $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$enrollment) {
                sendJSONResponse(false, 'Invalid enrollment ID');
            }
            $student_id = $enrollment['student_id'];
            $batch_id = $enrollment['batch_id'];

            // Check if certificate number is unique
            $stmt = $pdo->prepare("SELECT certificate_id FROM certificates WHERE certificate_number = ?");
            $stmt->execute([$certificate_number]);
            if ($stmt->fetch()) {
                sendJSONResponse(false, 'Certificate number already exists');
            }

            $stmt = $pdo->prepare("INSERT INTO certificates (enrollment_id, student_id, batch_id, certificate_number, certificate_type, issue_date, valid_until, status, remarks, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$enrollment_id, $student_id, $batch_id, $certificate_number, $certificate_type, $issue_date, $valid_until, $status, $remarks]);
            sendJSONResponse(true, 'Certificate created successfully');
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

            $countStmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM certificates c
                $whereClause
            ");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            $pagination = getPagination($page, $total, $perPage);

            $stmt = $pdo->prepare("
                SELECT c.*, CONCAT(s.first_name, ' ', s.last_name) AS student_name, b.batch_code, co.course_name
                FROM certificates c
                LEFT JOIN students s ON c.student_id = s.student_id
                LEFT JOIN batches b ON c.batch_id = b.batch_id
                LEFT JOIN courses co ON b.course_id = co.course_id
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
            $certificate_id = (int)($_POST['certificate_id'] ?? 0);
            $enrollment_id = (int)($_POST['enrollment_id'] ?? 0);
            $certificate_number = sanitizeInput($_POST['certificate_number'] ?? '');
            $certificate_type = sanitizeInput($_POST['certificate_type'] ?? '');
            $issue_date = sanitizeInput($_POST['issue_date'] ?? '');
            $valid_until = sanitizeInput($_POST['valid_until'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'issued');
            $remarks = sanitizeInput($_POST['remarks'] ?? '');

            if (empty($certificate_id) || empty($enrollment_id) || empty($certificate_number) || empty($issue_date) || empty($certificate_type)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            // Look up student_id and batch_id from enrollment
            $stmt = $pdo->prepare("SELECT student_id, batch_id FROM student_batch_enrollment WHERE enrollment_id = ?");
            $stmt->execute([$enrollment_id]);
            $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$enrollment) {
                sendJSONResponse(false, 'Invalid enrollment ID');
            }
            $student_id = $enrollment['student_id'];
            $batch_id = $enrollment['batch_id'];

            // Check if certificate number is unique (excluding current)
            $stmt = $pdo->prepare("SELECT certificate_id FROM certificates WHERE certificate_number = ? AND certificate_id != ?");
            $stmt->execute([$certificate_number, $certificate_id]);
            if ($stmt->fetch()) {
                sendJSONResponse(false, 'Certificate number already exists');
            }

            $stmt = $pdo->prepare("UPDATE certificates SET enrollment_id = ?, student_id = ?, batch_id = ?, certificate_number = ?, certificate_type = ?, issue_date = ?, valid_until = ?, status = ?, remarks = ?, updated_at = NOW() WHERE certificate_id = ?");
            $stmt->execute([$enrollment_id, $student_id, $batch_id, $certificate_number, $certificate_type, $issue_date, $valid_until, $status, $remarks, $certificate_id]);
            sendJSONResponse(true, 'Certificate updated successfully');
            break;

        case 'delete':
            $certificate_id = (int)($_POST['certificate_id'] ?? 0);
            if (empty($certificate_id)) {
                sendJSONResponse(false, 'ID is required');
            }
            $stmt = $pdo->prepare("DELETE FROM certificates WHERE certificate_id = ?");
            $stmt->execute([$certificate_id]);
            sendJSONResponse(true, 'Certificate deleted successfully');
            break;

        case 'get':
            $certificate_id = (int)($_POST['certificate_id'] ?? 0);
            if (empty($certificate_id)) {
                sendJSONResponse(false, 'ID is required');
            }
            $stmt = $pdo->prepare("SELECT c.*, CONCAT(s.first_name, ' ', s.last_name) AS student_name, b.batch_code, co.course_name FROM certificates c LEFT JOIN students s ON c.student_id = s.student_id LEFT JOIN batches b ON c.batch_id = b.batch_id LEFT JOIN courses co ON b.course_id = co.course_id WHERE c.certificate_id = ?");
            $stmt->execute([$certificate_id]);
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
                SELECT c.*, e.enrollment_id, b.batch_code, co.course_name
                FROM certificates c
                JOIN student_batch_enrollment e ON c.enrollment_id = e.enrollment_id
                JOIN batches b ON e.batch_id = b.batch_id
                JOIN courses co ON b.course_id = co.course_id
                WHERE e.student_id = ?
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
                SELECT c.*, e.enrollment_id, CONCAT(s.first_name, ' ', s.last_name) AS student_name
                FROM certificates c
                JOIN student_batch_enrollment e ON c.enrollment_id = e.enrollment_id
                JOIN students s ON e.student_id = s.student_id
                WHERE e.batch_id = ?
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
                SELECT c.*, e.enrollment_id, CONCAT(s.first_name, ' ', s.last_name) AS student_name, b.batch_code, co.course_name
                FROM certificates c
                JOIN student_batch_enrollment e ON c.enrollment_id = e.enrollment_id
                JOIN students s ON e.student_id = s.student_id
                JOIN batches b ON e.batch_id = b.batch_id
                JOIN courses co ON b.course_id = co.course_id
                WHERE c.certificate_number = ?
            ");
            $stmt->execute([$certificate_number]);
            $certificate = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($certificate) {
                // Check if certificate is valid
                $isValid = true;
                $message = 'Certificate is valid';

                if ($certificate['status'] !== 'issued') {
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

        case 'list':
            $stmt = $pdo->query("SELECT c.*, CONCAT(s.first_name, ' ', s.last_name) AS student_name, b.batch_code, co.course_name FROM certificates c LEFT JOIN students s ON c.student_id = s.student_id LEFT JOIN batches b ON c.batch_id = b.batch_id LEFT JOIN courses co ON b.course_id = co.course_id ORDER BY c.issue_date DESC");
            $certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $certificates]);
            exit;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Certificates error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred: ' . $e->getMessage());
} 