<?php
require_once '../../config.php';
require_once '../functions.php';
require_once '../../crud_functions.php';

$pdo = getDBConnection();

startSecureSession();
checkLogin();
checkPermission('admin');

header('Content-Type: application/json');

// Helper function to determine status
function getBatchStatus($start_date, $end_date) {
    $today = date('Y-m-d');
    if ($today < $start_date) return 'upcoming';
    if ($today > $end_date) return 'completed';
    return 'ongoing';
}

$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'list':
            $stmt = $pdo->query("SELECT b.batch_id, b.batch_name, b.batch_code, b.start_date, b.end_date, b.capacity, b.status, c.course_name
                FROM batches b
                LEFT JOIN courses c ON b.course_id = c.course_id
                ORDER BY b.batch_id DESC");
            $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Add student count for each batch (count all enrollments for the batch)
            foreach ($batches as &$batch) {
                $batch_id = $batch['batch_id'];
                $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM student_batch_enrollment WHERE batch_id = ?");
                $stmt2->execute([$batch_id]);
                $batch['student_count'] = $stmt2->fetchColumn();
                // Always recalculate status for display
                $batch['status'] = getBatchStatus($batch['start_date'], $batch['end_date']);
            }

            // Debugging log to inspect the data being returned
            error_log('Batches List Response: ' . json_encode($batches));

            echo json_encode(['status' => 'success', 'data' => $batches]);
            exit;
        case 'add':
            $batch_name = sanitizeInput($_POST['batch_name'] ?? '');
            $course_id = (int)($_POST['course_id'] ?? 0);
            $start_date = sanitizeInput($_POST['start_date'] ?? '');
            $end_date = sanitizeInput($_POST['end_date'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $status = getBatchStatus($start_date, $end_date);

            if (empty($batch_name) || empty($course_id) || empty($start_date) || empty($end_date) || $capacity <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (strtotime($end_date) < strtotime($start_date)) {
                sendJSONResponse(false, 'End date cannot be before start date');
            }

            // Generate batch_code automatically: e.g., 'B' + (max_id+1) padded
            $maxIdStmt = $pdo->query("SELECT MAX(batch_id) as max_id FROM batches");
            $maxIdRow = $maxIdStmt->fetch(PDO::FETCH_ASSOC);
            $nextId = (int)($maxIdRow['max_id'] ?? 0) + 1;
            $batch_code = 'B' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO batches (batch_name, course_id, batch_code, start_date, end_date, capacity, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $result = $stmt->execute([$batch_name, $course_id, $batch_code, $start_date, $end_date, $capacity, $status]);

            if ($result) {
                sendJSONResponse(true, 'Batch added successfully');
            } else {
                sendJSONResponse(false, 'Failed to add batch. ' . implode(' | ', $stmt->errorInfo()));
            }
            break;
        case 'edit':
            $batch_id = (int)($_POST['batch_id'] ?? 0);
            $batch_name = sanitizeInput($_POST['batch_name'] ?? '');
            $course_id = (int)($_POST['course_id'] ?? 0);
            $start_date = sanitizeInput($_POST['start_date'] ?? '');
            $end_date = sanitizeInput($_POST['end_date'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $status = getBatchStatus($start_date, $end_date);
            $batch_code = null; // Do not update batch_code on edit

            if (empty($batch_id) || empty($batch_name) || empty($course_id) || empty($start_date) || empty($end_date) || $capacity <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (strtotime($end_date) < strtotime($start_date)) {
                sendJSONResponse(false, 'End date cannot be before start date');
            }

            $stmt = $pdo->prepare("UPDATE batches SET batch_name = ?, course_id = ?, start_date = ?, end_date = ?, capacity = ?, status = ?, updated_at = NOW() WHERE batch_id = ?");
            $result = $stmt->execute([$batch_name, $course_id, $start_date, $end_date, $capacity, $status, $batch_id]);

            if ($result) {
                sendJSONResponse(true, 'Batch updated successfully');
            } else {
                sendJSONResponse(false, 'Failed to update batch. ' . implode(' | ', $stmt->errorInfo()));
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
            // Removed Add Candidate to Batch feature
            sendJSONResponse(false, 'Not implemented');
            break;
        case 'add_candidate_to_batch':
            // Removed Add Candidate to Batch feature
            sendJSONResponse(false, 'Not implemented');
            break;
        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError('Batches error: ' . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}