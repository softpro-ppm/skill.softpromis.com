<?php
require_once '../../config.php';
require_once '../functions.php';
require_once '../../crud_functions.php';

$pdo = getDBConnection();

startSecureSession();
checkLogin();
checkPermission('admin');

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'list':
            $stmt = $pdo->query("SELECT b.batch_id, b.batch_name, b.batch_code, b.start_date, b.end_date, b.capacity, b.status, c.course_name
                FROM batches b
                LEFT JOIN courses c ON b.course_id = c.course_id
                ORDER BY b.batch_id DESC");
            $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Debugging log to inspect the data being returned
            error_log('Batches List Response: ' . json_encode($batches));

            echo json_encode(['status' => 'success', 'data' => $batches]);
            exit;
        case 'add':
            $batch_name = sanitizeInput($_POST['batch_name'] ?? '');
            $batch_code = isset($_POST['batch_code']) ? sanitizeInput($_POST['batch_code']) : null;
            $course_id = (int)($_POST['course_id'] ?? 0);
            $start_date = sanitizeInput($_POST['start_date'] ?? '');
            $end_date = sanitizeInput($_POST['end_date'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? 'upcoming');

            if (empty($batch_name) || empty($course_id) || empty($start_date) || empty($end_date) || $capacity <= 0) {
                error_log('Validation failed: ' . json_encode([
                    'batch_name' => $batch_name,
                    'batch_code' => $batch_code,
                    'course_id' => $course_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'capacity' => $capacity
                ]));
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (strtotime($end_date) < strtotime($start_date)) {
                sendJSONResponse(false, 'End date cannot be before start date');
            }

            $stmt = $pdo->prepare("INSERT INTO batches (batch_name, course_id, batch_code, start_date, end_date, capacity, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $result = $stmt->execute([$batch_name, $course_id, $batch_code, $start_date, $end_date, $capacity, $status]);

            error_log('Add Batch Response: ' . json_encode(['success' => $result, 'message' => $result ? 'Batch added successfully' : 'Failed to add batch']));

            if ($result) {
                sendJSONResponse(true, 'Batch added successfully');
            } else {
                sendJSONResponse(false, 'Failed to add batch');
            }
            break;
        case 'edit':
            $batch_id = (int)($_POST['batch_id'] ?? 0);
            $batch_name = sanitizeInput($_POST['batch_name'] ?? '');
            $batch_code = isset($_POST['batch_code']) ? sanitizeInput($_POST['batch_code']) : null;
            $course_id = (int)($_POST['course_id'] ?? 0);
            $start_date = sanitizeInput($_POST['start_date'] ?? '');
            $end_date = sanitizeInput($_POST['end_date'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? 'upcoming');

            if (empty($batch_id) || empty($batch_name) || empty($course_id) || empty($start_date) || empty($end_date) || $capacity <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (strtotime($end_date) < strtotime($start_date)) {
                sendJSONResponse(false, 'End date cannot be before start date');
            }

            $stmt = $pdo->prepare("UPDATE batches SET batch_name = ?, course_id = ?, batch_code = ?, start_date = ?, end_date = ?, capacity = ?, status = ?, updated_at = NOW() WHERE batch_id = ?");
            $result = $stmt->execute([$batch_name, $course_id, $batch_code, $start_date, $end_date, $capacity, $status, $batch_id]);

            if ($result) {
                sendJSONResponse(true, 'Batch updated successfully');
            } else {
                sendJSONResponse(false, 'Failed to update batch');
            }
            break;
        case 'delete':
            $batch_id = (int)($_POST['batch_id'] ?? 0);
            if (empty($batch_id)) {
                sendJSONResponse(false, 'Batch ID is required');
            }
            $stmt = $pdo->prepare("DELETE FROM batches WHERE batch_id = ?");
            $result = $stmt->execute([$batch_id]);
            if ($result) {
                sendJSONResponse(true, 'Batch deleted successfully');
            } else {
                sendJSONResponse(false, 'Failed to delete batch');
            }
            break;
        case 'get':
            $batch_id = (int)($_GET['batch_id'] ?? $_POST['batch_id'] ?? 0);
            if (empty($batch_id)) {
                sendJSONResponse(false, 'Batch ID is required');
            }
            $stmt = $pdo->prepare("SELECT b.*, c.course_name FROM batches b
                                   LEFT JOIN courses c ON b.course_id = c.course_id
                                   WHERE b.batch_id = ?");
            $stmt->execute([$batch_id]);
            $batch = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($batch) {
                sendJSONResponse(true, 'Batch retrieved successfully', $batch);
            } else {
                sendJSONResponse(false, 'Batch not found');
            }
            break;
        case 'get_centers_courses':
            // Fetch active courses
            $coursesStmt = $pdo->query("SELECT course_id, course_name FROM courses WHERE status = 'active' ORDER BY course_name ASC");
            $courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'success' => true,
                'courses' => $courses
            ]);
            exit;
        case 'get_available_students':
            $batch_id = (int)($_POST['batch_id'] ?? 0);
            $stmt = $pdo->prepare('SELECT s.student_id, s.first_name, s.last_name, s.enrollment_no FROM students s WHERE s.student_id NOT IN (SELECT student_id FROM student_batch_enrollment WHERE batch_id = ?)');
            $stmt->execute([$batch_id]);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendJSONResponse(true, 'Available students fetched', $students);
            break;
        case 'add_candidate_to_batch':
            $batch_id = (int)($_POST['batch_id'] ?? 0);
            $student_id = (int)($_POST['student_id'] ?? 0);
            $enrollment_date = $_POST['enrollment_date'] ?? date('Y-m-d');
            if (!$batch_id || !$student_id) {
                sendJSONResponse(false, 'Batch and student are required');
            }
            // Prevent duplicate
            $stmt = $pdo->prepare('SELECT enrollment_id FROM student_batch_enrollment WHERE batch_id = ? AND student_id = ?');
            $stmt->execute([$batch_id, $student_id]);
            if ($stmt->fetch()) {
                sendJSONResponse(false, 'Candidate already in batch');
            }
            $stmt = $pdo->prepare('INSERT INTO student_batch_enrollment (student_id, batch_id, enrollment_date, status, created_at, updated_at) VALUES (?, ?, ?, "active", NOW(), NOW())');
            $stmt->execute([$student_id, $batch_id, $enrollment_date]);
            sendJSONResponse(true, 'Candidate added to batch');
            break;
        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError('Batches error: ' . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}