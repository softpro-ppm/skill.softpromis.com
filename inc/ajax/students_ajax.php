<?php
require_once '../../config.php';
require_once '../functions.php';

startSecureSession();
checkLogin();
checkPermission('admin');

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
error_log('Action received: ' . $action);

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
            $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : null;
            $batch_id = isset($_POST['batch_id']) ? (int)$_POST['batch_id'] : null;

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
            $stmt = $pdo->prepare("INSERT INTO students (enrollment_no, first_name, last_name, email, mobile, date_of_birth, gender, address, course_id, batch_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$enrollment_no, $first_name, $last_name, $email, $mobile, $date_of_birth, $gender, $address, $course_id, $batch_id]);
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
            $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : null;
            $batch_id = isset($_POST['batch_id']) ? (int)$_POST['batch_id'] : null;

            if (empty($student_id) || empty($first_name) || empty($last_name)) {
                sendJSONResponse(false, 'Required fields are missing');
            }
            if (!empty($email) && !validateEmail($email)) {
                sendJSONResponse(false, 'Invalid email format');
            }
            if (!empty($mobile) && !validatePhone($mobile)) {
                sendJSONResponse(false, 'Invalid mobile format');
            }
            $stmt = $pdo->prepare("UPDATE students SET first_name = ?, last_name = ?, email = ?, mobile = ?, date_of_birth = ?, gender = ?, address = ?, course_id = ?, batch_id = ?, updated_at = NOW() WHERE student_id = ?");
            $stmt->execute([$first_name, $last_name, $email, $mobile, $date_of_birth, $gender, $address, $course_id, $batch_id, $student_id]);
            sendJSONResponse(true, 'Student updated successfully');
            break;

        case 'delete':
            $student_id = (int)($_POST['student_id'] ?? 0);
            error_log('Delete Action: student_id=' . $student_id);
            if (empty($student_id)) {
                sendJSONResponse(false, 'Student ID is required');
            }
            try {
                // Delete from fees
                $stmt = $pdo->prepare('DELETE FROM fees WHERE student_id = ?');
                $stmt->execute([$student_id]);

                // Delete from student_batch_enrollment
                $stmt = $pdo->prepare('DELETE FROM student_batch_enrollment WHERE student_id = ?');
                $stmt->execute([$student_id]);

                // (Add more deletes for other related tables if needed)

                // Finally, delete the student
                $stmt = $pdo->prepare('DELETE FROM students WHERE student_id = ?');
                $stmt->execute([$student_id]);

                if ($stmt->rowCount() > 0) {
                    sendJSONResponse(true, 'Student deleted successfully');
                } else {
                    sendJSONResponse(false, 'No student deleted. The student may not exist.');
                }
            } catch (PDOException $e) {
                error_log('Delete Error: ' . $e->getMessage());
                sendJSONResponse(false, 'Failed to delete student: ' . $e->getMessage());
            }
            break;

        case 'get':
            $student_id = (int)($_POST['student_id'] ?? 0);
            if (empty($student_id)) {
                sendJSONResponse(false, 'Student ID is required');
            }
            $stmt = $pdo->prepare('SELECT s.*, c.course_name, b.batch_code FROM students s LEFT JOIN courses c ON s.course_id = c.course_id LEFT JOIN batches b ON s.batch_id = b.batch_id WHERE s.student_id = ?');
            $stmt->execute([$student_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($student) {
                sendJSONResponse(true, 'Student retrieved successfully', $student);
            } else {
                sendJSONResponse(false, 'Student not found');
            }
            break;

        case 'list':
            $stmt = $pdo->query('SELECT s.student_id, s.enrollment_no, s.first_name, s.last_name, s.gender, s.mobile, s.email, s.date_of_birth, s.address, 
                c.course_name, b.batch_code
                FROM students s
                LEFT JOIN courses c ON s.course_id = c.course_id
                LEFT JOIN batches b ON s.batch_id = b.batch_id
                ORDER BY s.created_at DESC');
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $students]);
            exit;

        case 'getEnrollments':
            try {
                $stmt = $pdo->query('SELECT student_id AS enrollment_id, enrollment_no, CONCAT(first_name, " ", last_name) AS student_name FROM students ORDER BY first_name ASC');
                $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log('Enrollments fetched: ' . json_encode($enrollments));

                echo json_encode([
                    'success' => true,
                    'data' => $enrollments
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to fetch enrollments: ' . $e->getMessage()
                ]);
            }
            exit;

        case 'get_enrollments_by_student':
            $student_id = (int)($_POST['student_id'] ?? 0);
            if (empty($student_id)) {
                sendJSONResponse(false, 'Student ID is required');
            }
            $stmt = $pdo->prepare('SELECT enrollment_id FROM student_batch_enrollment WHERE student_id = ?');
            $stmt->execute([$student_id]);
            $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendJSONResponse(true, 'Enrollments fetched', $enrollments);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError('Students error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'data' => [], 'error' => 'An error occurred. Please try again later.']);
    exit;
}