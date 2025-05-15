<?php
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

    if ($action === 'get_enrollments') {
        $stmt = $pdo->prepare("SELECT e.enrollment_id, CONCAT(s.first_name, ' ', s.last_name) AS student_name FROM student_batch_enrollment e JOIN students s ON e.student_id = s.student_id");
        $stmt->execute();
        $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $enrollments]);
        exit;
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

            if (!$enrollment_id || !$certificate_number || !$certificate_type || !$issue_date) {
                echo json_encode(['success' => false, 'message' => 'Required fields are missing']); exit;
            }

            $stmt = $pdo->prepare("SELECT student_id, batch_id FROM student_batch_enrollment WHERE enrollment_id = ?");
            $stmt->execute([$enrollment_id]);
            $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$enrollment) {
                echo json_encode(['success' => false, 'message' => 'Invalid enrollment ID']); exit;
            }
            $student_id = $enrollment['student_id'];
            $batch_id = $enrollment['batch_id'];

            $stmt = $pdo->prepare("SELECT certificate_id FROM certificates WHERE certificate_number = ?");
            $stmt->execute([$certificate_number]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Certificate number already exists']); exit;
            }

            $stmt = $pdo->prepare("INSERT INTO certificates (enrollment_id, student_id, batch_id, certificate_number, certificate_type, issue_date, valid_until, status, remarks, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$enrollment_id, $student_id, $batch_id, $certificate_number, $certificate_type, $issue_date, $valid_until, $status, $remarks]);
            echo json_encode(['success' => true, 'message' => 'Certificate created successfully']);
            exit;

        case 'update':
            $certificate_id = (int)($_POST['certificate_id'] ?? 0);
            $enrollment_id = (int)($_POST['enrollment_id'] ?? 0);
            $certificate_number = sanitizeInput($_POST['certificate_number'] ?? '');
            $certificate_type = sanitizeInput($_POST['certificate_type'] ?? '');
            $issue_date = sanitizeInput($_POST['issue_date'] ?? '');
            $valid_until = sanitizeInput($_POST['valid_until'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'issued');
            $remarks = sanitizeInput($_POST['remarks'] ?? '');

            if (!$certificate_id || !$enrollment_id || !$certificate_number || !$certificate_type || !$issue_date) {
                echo json_encode(['success' => false, 'message' => 'Required fields are missing']); exit;
            }

            $stmt = $pdo->prepare("SELECT student_id, batch_id FROM student_batch_enrollment WHERE enrollment_id = ?");
            $stmt->execute([$enrollment_id]);
            $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$enrollment) {
                echo json_encode(['success' => false, 'message' => 'Invalid enrollment ID']); exit;
            }
            $student_id = $enrollment['student_id'];
            $batch_id = $enrollment['batch_id'];

            $stmt = $pdo->prepare("SELECT certificate_id FROM certificates WHERE certificate_number = ? AND certificate_id != ?");
            $stmt->execute([$certificate_number, $certificate_id]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Certificate number already exists']); exit;
            }

            $stmt = $pdo->prepare("UPDATE certificates SET enrollment_id = ?, student_id = ?, batch_id = ?, certificate_number = ?, certificate_type = ?, issue_date = ?, valid_until = ?, status = ?, remarks = ?, updated_at = NOW() WHERE certificate_id = ?");
            $stmt->execute([$enrollment_id, $student_id, $batch_id, $certificate_number, $certificate_type, $issue_date, $valid_until, $status, $remarks, $certificate_id]);
            echo json_encode(['success' => true, 'message' => 'Certificate updated successfully']);
            exit;

        case 'delete':
            $certificate_id = (int)($_POST['certificate_id'] ?? 0);
            if (!$certificate_id) {
                echo json_encode(['success' => false, 'message' => 'ID is required']); exit;
            }
            $stmt = $pdo->prepare("DELETE FROM certificates WHERE certificate_id = ?");
            $stmt->execute([$certificate_id]);
            echo json_encode(['success' => true, 'message' => 'Certificate deleted successfully']);
            exit;

        case 'get':
            $certificate_id = (int)($_POST['certificate_id'] ?? 0);
            if (!$certificate_id) {
                echo json_encode(['success' => false, 'message' => 'ID is required']); exit;
            }
            $stmt = $pdo->prepare("SELECT c.*, CONCAT(s.first_name, ' ', s.last_name) AS student_name, b.batch_code, co.course_name FROM certificates c LEFT JOIN students s ON c.student_id = s.student_id LEFT JOIN batches b ON c.batch_id = b.batch_id LEFT JOIN courses co ON b.course_id = co.course_id WHERE c.certificate_id = ?");
            $stmt->execute([$certificate_id]);
            $certificate = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($certificate) {
                echo json_encode(['success' => true, 'data' => $certificate]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Certificate not found']);
            }
            exit;

        case 'list':
            $stmt = $pdo->query("SELECT c.*, CONCAT(s.first_name, ' ', s.last_name) AS student_name, b.batch_code, co.course_name FROM certificates c LEFT JOIN students s ON c.student_id = s.student_id LEFT JOIN batches b ON c.batch_id = b.batch_id LEFT JOIN courses co ON b.course_id = co.course_id ORDER BY c.issue_date DESC");
            $certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $certificates]);
            exit;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
} 