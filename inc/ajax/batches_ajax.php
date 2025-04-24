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
            $name = sanitizeInput($_POST['name'] ?? '');
            $course_id = (int)($_POST['course_id'] ?? 0);
            $center_id = (int)($_POST['center_id'] ?? 0);
            $start_date = sanitizeInput($_POST['start_date'] ?? '');
            $end_date = sanitizeInput($_POST['end_date'] ?? '');
            $schedule = sanitizeInput($_POST['schedule'] ?? '');
            $instructor = sanitizeInput($_POST['instructor'] ?? '');
            $max_students = (int)($_POST['max_students'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $notes = sanitizeInput($_POST['notes'] ?? '');

            if (empty($name) || empty($course_id) || empty($center_id) || empty($start_date) || empty($end_date)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (strtotime($end_date) < strtotime($start_date)) {
                sendJSONResponse(false, 'End date cannot be before start date');
            }

            // Validate course
            $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid course');
            }

            // Validate center
            $stmt = $pdo->prepare("SELECT id FROM training_centers WHERE id = ?");
            $stmt->execute([$center_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid training center');
            }

            $stmt = $pdo->prepare("
                INSERT INTO batches (
                    name, course_id, center_id, start_date, end_date,
                    schedule, instructor, max_students, status, notes,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $name, $course_id, $center_id, $start_date, $end_date,
                $schedule, $instructor, $max_students, $status, $notes
            ]);

            logAudit($_SESSION['user']['id'], 'create_batch', [
                'name' => $name,
                'course_id' => $course_id,
                'center_id' => $center_id
            ]);

            sendJSONResponse(true, 'Batch created successfully', [
                'id' => $pdo->lastInsertId()
            ]);
            break;

        case 'read':
            $page = (int)($_POST['page'] ?? 1);
            $perPage = (int)($_POST['per_page'] ?? 10);
            $search = sanitizeInput($_POST['search'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? '');
            $course_id = (int)($_POST['course_id'] ?? 0);
            $center_id = (int)($_POST['center_id'] ?? 0);

            $where = [];
            $params = [];

            if (!empty($search)) {
                $searchFields = ['b.name', 'b.schedule', 'b.instructor', 'b.notes'];
                $searchResult = buildSearchQuery($searchFields, $search);
                $where[] = "(" . $searchResult['conditions'] . ")";
                $params = array_merge($params, $searchResult['params']);
            }

            if (!empty($status)) {
                $where[] = "b.status = ?";
                $params[] = $status;
            }

            if ($course_id > 0) {
                $where[] = "b.course_id = ?";
                $params[] = $course_id;
            }

            if ($center_id > 0) {
                $where[] = "b.center_id = ?";
                $params[] = $center_id;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Get total count
            $countStmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM batches b
                $whereClause
            ");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            // Get pagination info
            $pagination = getPagination($page, $total, $perPage);

            // Get data with related info
            $stmt = $pdo->prepare("
                SELECT b.*, c.name as course_name, tc.name as center_name,
                       (SELECT COUNT(*) FROM batch_students WHERE batch_id = b.id) as student_count
                FROM batches b
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                $whereClause
                ORDER BY b.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $pagination['per_page'], $pagination['offset']]);
            $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Batches retrieved successfully', [
                'data' => $batches,
                'pagination' => $pagination
            ]);
            break;

        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            $name = sanitizeInput($_POST['name'] ?? '');
            $course_id = (int)($_POST['course_id'] ?? 0);
            $center_id = (int)($_POST['center_id'] ?? 0);
            $start_date = sanitizeInput($_POST['start_date'] ?? '');
            $end_date = sanitizeInput($_POST['end_date'] ?? '');
            $schedule = sanitizeInput($_POST['schedule'] ?? '');
            $instructor = sanitizeInput($_POST['instructor'] ?? '');
            $max_students = (int)($_POST['max_students'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $notes = sanitizeInput($_POST['notes'] ?? '');

            if (empty($id) || empty($name) || empty($course_id) || empty($center_id) || empty($start_date) || empty($end_date)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (strtotime($end_date) < strtotime($start_date)) {
                sendJSONResponse(false, 'End date cannot be before start date');
            }

            // Validate course
            $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid course');
            }

            // Validate center
            $stmt = $pdo->prepare("SELECT id FROM training_centers WHERE id = ?");
            $stmt->execute([$center_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid training center');
            }

            $stmt = $pdo->prepare("
                UPDATE batches 
                SET name = ?, course_id = ?, center_id = ?, start_date = ?,
                    end_date = ?, schedule = ?, instructor = ?, max_students = ?,
                    status = ?, notes = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $name, $course_id, $center_id, $start_date, $end_date,
                $schedule, $instructor, $max_students, $status, $notes, $id
            ]);

            logAudit($_SESSION['user']['id'], 'update_batch', [
                'id' => $id,
                'name' => $name,
                'course_id' => $course_id,
                'center_id' => $center_id
            ]);

            sendJSONResponse(true, 'Batch updated successfully');
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            // Check if batch has students
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM batch_students 
                WHERE batch_id = ?
            ");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                sendJSONResponse(false, 'Cannot delete batch with enrolled students');
            }

            // Get batch info for audit log
            $stmt = $pdo->prepare("
                SELECT b.name, c.name as course_name, tc.name as center_name
                FROM batches b
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                WHERE b.id = ?
            ");
            $stmt->execute([$id]);
            $batch = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$batch) {
                sendJSONResponse(false, 'Batch not found');
            }

            $stmt = $pdo->prepare("DELETE FROM batches WHERE id = ?");
            $stmt->execute([$id]);

            logAudit($_SESSION['user']['id'], 'delete_batch', [
                'id' => $id,
                'name' => $batch['name'],
                'course' => $batch['course_name'],
                'center' => $batch['center_name']
            ]);

            sendJSONResponse(true, 'Batch deleted successfully');
            break;

        case 'get':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT b.*, c.name as course_name, tc.name as center_name,
                       (SELECT COUNT(*) FROM batch_students WHERE batch_id = b.id) as student_count
                FROM batches b
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                WHERE b.id = ?
            ");
            $stmt->execute([$id]);
            $batch = $stmt->fetch(PDO::FETCH_ASSOC);

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
    logError("Batches error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 