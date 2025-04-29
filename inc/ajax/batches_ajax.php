<?php
require_once '../../config.php';
require_once '../functions.php';
require_once '../../crud_functions.php';

$pdo = getDBConnection();

startSecureSession();
checkLogin();
checkPermission('admin');

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'create':
            $batch_code = sanitizeInput($_POST['batch_code'] ?? '');
            $course_id = (int)($_POST['course_id'] ?? 0);
            $center_id = (int)($_POST['center_id'] ?? 0);
            $trainer_id = (int)($_POST['trainer_id'] ?? 0);
            $start_date = sanitizeInput($_POST['start_date'] ?? '');
            $end_date = sanitizeInput($_POST['end_date'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $schedule = sanitizeInput($_POST['schedule'] ?? '');
            $remarks = sanitizeInput($_POST['remarks'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'upcoming');

            if (empty($batch_code) || empty($course_id) || empty($center_id) || empty($start_date) || empty($end_date) || $capacity <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }
            if (strtotime($end_date) < strtotime($start_date)) {
                sendJSONResponse(false, 'End date cannot be before start date');
            }
            $data = [
                'batch_code' => $batch_code,
                'course_id' => $course_id,
                'center_id' => $center_id,
                'trainer_id' => $trainer_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'capacity' => $capacity,
                'schedule' => $schedule,
                'remarks' => $remarks,
                'status' => $status
            ];
            $result = Batch::create($data);
            if ($result) {
                sendJSONResponse(true, 'Batch created successfully');
            } else {
                sendJSONResponse(false, 'Failed to create batch');
            }
            break;

        case 'read':
            $batches = Batch::getAll();
            sendJSONResponse(true, 'Batches retrieved successfully', $batches);
            break;

        case 'update':
            $batch_id = (int)($_POST['batch_id'] ?? 0);
            $batch_code = sanitizeInput($_POST['batch_code'] ?? '');
            $course_id = (int)($_POST['course_id'] ?? 0);
            $center_id = (int)($_POST['center_id'] ?? 0);
            $trainer_id = (int)($_POST['trainer_id'] ?? 0);
            $start_date = sanitizeInput($_POST['start_date'] ?? '');
            $end_date = sanitizeInput($_POST['end_date'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $schedule = sanitizeInput($_POST['schedule'] ?? '');
            $remarks = sanitizeInput($_POST['remarks'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'upcoming');

            if (empty($batch_id) || empty($batch_code) || empty($course_id) || empty($center_id) || empty($start_date) || empty($end_date) || $capacity <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }
            if (strtotime($end_date) < strtotime($start_date)) {
                sendJSONResponse(false, 'End date cannot be before start date');
            }
            $data = [
                'batch_code' => $batch_code,
                'course_id' => $course_id,
                'center_id' => $center_id,
                'trainer_id' => $trainer_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'capacity' => $capacity,
                'schedule' => $schedule,
                'remarks' => $remarks,
                'status' => $status
            ];
            $result = Batch::update($batch_id, $data);
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
            $result = Batch::delete($batch_id);
            if ($result) {
                sendJSONResponse(true, 'Batch deleted successfully');
            } else {
                sendJSONResponse(false, 'Failed to delete batch');
            }
            break;

        case 'get':
            $batch_id = (int)($_POST['batch_id'] ?? 0);
            if (empty($batch_id)) {
                sendJSONResponse(false, 'Batch ID is required');
            }
            $batch = Batch::getById($batch_id);
            if ($batch) {
                sendJSONResponse(true, 'Batch retrieved successfully', $batch);
            } else {
                sendJSONResponse(false, 'Batch not found');
            }
            break;

        case 'get_students':
            $batch_id = (int)($_POST['batch_id'] ?? 0);

            if (empty($batch_id)) {
                sendJSONResponse(false, 'Batch ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT s.*, bs.status as enrollment_status, bs.enrolled_at
                FROM students s
                JOIN batch_students bs ON s.id = bs.student_id
                WHERE bs.batch_id = ?
                ORDER BY s.name
            ");
            $stmt->execute([$batch_id]);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Batch students retrieved successfully', $students);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError('Batches error: ' . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 