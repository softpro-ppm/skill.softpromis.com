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
            $stmt = $pdo->query("SELECT b.batch_id, b.batch_name, b.batch_code, b.start_date, b.end_date, b.capacity, b.status, c.course_name, tc.center_name
                FROM batches b
                LEFT JOIN courses c ON b.course_id = c.course_id
                LEFT JOIN training_centers tc ON b.center_id = tc.center_id
                ORDER BY b.batch_id DESC");
            $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Debugging log to inspect the data being returned
            error_log('Batches List Response: ' . json_encode($batches));

            echo json_encode(['status' => 'success', 'data' => $batches]);
            exit;
        case 'add':
            $batch_name = sanitizeInput($_POST['batch_name'] ?? '');
            $batch_code = sanitizeInput($_POST['batch_code'] ?? '');
            $course_id = (int)($_POST['course_id'] ?? 0);
            $center_id = (int)($_POST['center_id'] ?? 0);
            $start_date = sanitizeInput($_POST['start_date'] ?? '');
            $end_date = sanitizeInput($_POST['end_date'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? 'upcoming');
            $stable = sanitizeInput($_POST['stable'] ?? '');

            if (empty($batch_name) || empty($batch_code) || empty($course_id) || empty($center_id) || empty($start_date) || empty($end_date) || $capacity <= 0 || empty($stable)) {
                error_log('Validation failed: ' . json_encode([
                    'batch_name' => $batch_name,
                    'batch_code' => $batch_code,
                    'course_id' => $course_id,
                    'center_id' => $center_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'capacity' => $capacity,
                    'stable' => $stable
                ]));
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (strtotime($end_date) < strtotime($start_date)) {
                sendJSONResponse(false, 'End date cannot be before start date');
            }

            $stmt = $pdo->prepare("INSERT INTO batches (batch_name, center_id, course_id, batch_code, start_date, end_date, capacity, status, stable, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $result = $stmt->execute([$batch_name, $center_id, $course_id, $batch_code, $start_date, $end_date, $capacity, $status, $stable]);

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
            $course_id = (int)($_POST['course_id'] ?? 0);
            $start_date = sanitizeInput($_POST['start_date'] ?? '');
            $end_date = sanitizeInput($_POST['end_date'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? '');
            $stable = sanitizeInput($_POST['stable'] ?? '');

            if (empty($batch_id) || empty($batch_name) || empty($course_id) || empty($start_date) || empty($end_date) || empty($status) || empty($stable)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (strtotime($end_date) < strtotime($start_date)) {
                sendJSONResponse(false, 'End date cannot be before start date');
            }

            $stmt = $pdo->prepare("UPDATE batches SET batch_name = ?, course_id = ?, start_date = ?, end_date = ?, status = ?, stable = ?, updated_at = NOW() WHERE batch_id = ?");
            $result = $stmt->execute([$batch_name, $course_id, $start_date, $end_date, $status, $stable, $batch_id]);

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
            $stmt = $pdo->prepare("SELECT b.*, c.course_name, tc.center_name FROM batches b
                                   LEFT JOIN courses c ON b.course_id = c.course_id
                                   LEFT JOIN training_centers tc ON b.center_id = tc.center_id
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
            // Fetch active centers
            $centersStmt = $pdo->query("SELECT center_id, center_name FROM training_centers WHERE status = 'active' ORDER BY center_name ASC");
            $centers = $centersStmt->fetchAll(PDO::FETCH_ASSOC);
            // Fetch active courses
            $coursesStmt = $pdo->query("SELECT course_id, course_name FROM courses WHERE status = 'active' ORDER BY course_name ASC");
            $courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'success' => true,
                'centers' => $centers,
                'courses' => $courses
            ]);
            exit;
        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError('Batches error: ' . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}