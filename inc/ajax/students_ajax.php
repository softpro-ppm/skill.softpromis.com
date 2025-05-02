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
            $enrollment_no = sanitizeInput($_POST['enrollment_no'] ?? '');
            $first_name = sanitizeInput($_POST['first_name'] ?? '');
            $last_name = sanitizeInput($_POST['last_name'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $mobile = sanitizeInput($_POST['mobile'] ?? '');
            $date_of_birth = sanitizeInput($_POST['date_of_birth'] ?? '');
            $gender = sanitizeInput($_POST['gender'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');

            if (empty($enrollment_no) || empty($first_name) || empty($last_name)) {
                sendJSONResponse(false, 'Required fields are missing');
            }
            if (!empty($email) && !validateEmail($email)) {
                sendJSONResponse(false, 'Invalid email format');
            }
            if (!empty($mobile) && !validatePhone($mobile)) {
                sendJSONResponse(false, 'Invalid mobile format');
            }
            // Check for unique enrollment_no
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM students WHERE enrollment_no = ?');
            $stmt->execute([$enrollment_no]);
            if ($stmt->fetchColumn() > 0) {
                sendJSONResponse(false, 'Enrollment number already exists');
            }
            $stmt = $pdo->prepare("INSERT INTO students (enrollment_no, first_name, last_name, email, mobile, date_of_birth, gender, address, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$enrollment_no, $first_name, $last_name, $email, $mobile, $date_of_birth, $gender, $address]);
            sendJSONResponse(true, 'Student created successfully', ['student_id' => $pdo->lastInsertId()]);
            break;

        case 'update':
            $student_id = (int)($_POST['student_id'] ?? 0);
            $first_name = sanitizeInput($_POST['first_name'] ?? '');
            $last_name = sanitizeInput($_POST['last_name'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $mobile = sanitizeInput($_POST['mobile'] ?? '');
            $date_of_birth = sanitizeInput($_POST['date_of_birth'] ?? '');
            $gender = sanitizeInput($_POST['gender'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            if (empty($student_id) || empty($first_name) || empty($last_name)) {
                sendJSONResponse(false, 'Required fields are missing');
            }
            if (!empty($email) && !validateEmail($email)) {
                sendJSONResponse(false, 'Invalid email format');
            }
            if (!empty($mobile) && !validatePhone($mobile)) {
                sendJSONResponse(false, 'Invalid mobile format');
            }
            $stmt = $pdo->prepare("UPDATE students SET first_name = ?, last_name = ?, email = ?, mobile = ?, date_of_birth = ?, gender = ?, address = ?, updated_at = NOW() WHERE student_id = ?");
            $stmt->execute([$first_name, $last_name, $email, $mobile, $date_of_birth, $gender, $address, $student_id]);
            sendJSONResponse(true, 'Student updated successfully');
            break;

        case 'delete':
            $student_id = (int)($_POST['student_id'] ?? 0);
            if (empty($student_id)) {
                sendJSONResponse(false, 'Student ID is required');
            }
            $stmt = $pdo->prepare('DELETE FROM students WHERE student_id = ?');
            $stmt->execute([$student_id]);
            sendJSONResponse(true, 'Student deleted successfully');
            break;

        case 'get':
            $student_id = (int)($_POST['student_id'] ?? 0);
            if (empty($student_id)) {
                sendJSONResponse(false, 'Student ID is required');
            }
            $stmt = $pdo->prepare('SELECT * FROM students WHERE student_id = ?');
            $stmt->execute([$student_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($student) {
                sendJSONResponse(true, 'Student retrieved successfully', $student);
            } else {
                sendJSONResponse(false, 'Student not found');
            }
            break;

        case 'list':
            $stmt = $pdo->query('SELECT student_id, enrollment_no, first_name, last_name, gender, mobile, email, date_of_birth, address FROM students ORDER BY created_at DESC');
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $students]);
            exit;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError('Students error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'data' => [], 'error' => 'An error occurred. Please try again later.']);
    exit;
}