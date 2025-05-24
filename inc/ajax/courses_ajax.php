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
            $course_name = sanitizeInput($_POST['course_name'] ?? '');
            $course_code = sanitizeInput($_POST['course_code'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $duration_hours = (int)($_POST['duration_hours'] ?? 0);
            $fee = (float)($_POST['fee'] ?? 0);
            $prerequisites = sanitizeInput($_POST['prerequisites'] ?? '');
            $syllabus = sanitizeInput($_POST['syllabus'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $sector_id = (int)($_POST['sector_id'] ?? 0);
            $scheme_id = isset($_POST['scheme_id']) && $_POST['scheme_id'] !== '' ? (int)$_POST['scheme_id'] : null;
            $center_id = isset($_POST['center_id']) && $_POST['center_id'] !== '' ? (int)$_POST['center_id'] : null;

            if (empty($course_name) || empty($course_code) || $duration_hours <= 0 || $sector_id <= 0 || empty($center_id)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            // Validate sector
            $stmt = $pdo->prepare("SELECT sector_id FROM sectors WHERE sector_id = ?");
            $stmt->execute([$sector_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid sector');
            }

            // Validate center
            $stmt = $pdo->prepare("SELECT center_id FROM training_centers WHERE center_id = ?");
            $stmt->execute([$center_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid training center');
            }

            // Validate scheme if provided
            if ($scheme_id !== null) {
                $stmt = $pdo->prepare("SELECT scheme_id FROM schemes WHERE scheme_id = ?");
                $stmt->execute([$scheme_id]);
                if (!$stmt->fetch()) {
                    sendJSONResponse(false, 'Invalid scheme');
                }
            }

            $stmt = $pdo->prepare("
                INSERT INTO courses (
                    sector_id, scheme_id, center_id, course_code, course_name, duration_hours, fee, description, prerequisites, syllabus, status, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $sector_id, $scheme_id, $center_id, $course_code, $course_name, $duration_hours, $fee, $description, $prerequisites, $syllabus, $status
            ]);

            logAudit($_SESSION['user']['user_id'], 'create_course', [
                'course_name' => $course_name,
                'sector_id' => $sector_id,
                'scheme_id' => $scheme_id,
                'center_id' => $center_id
            ]);

            sendJSONResponse(true, 'Course created successfully', [
                'course_id' => $pdo->lastInsertId()
            ]);
            break;

        case 'read':
            $where = [];
            $params = [];
            $status = sanitizeInput($_POST['status'] ?? '');
            $sector_id = (int)($_POST['sector_id'] ?? 0);
            $scheme_id = (int)($_POST['scheme_id'] ?? 0);
            if (!empty($status)) {
                $where[] = "c.status = ?";
                $params[] = $status;
            }
            if ($sector_id > 0) {
                $where[] = "c.sector_id = ?";
                $params[] = $sector_id;
            }
            if ($scheme_id > 0) {
                $where[] = "c.scheme_id = ?";
                $params[] = $scheme_id;
            }
            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            $stmt = $pdo->prepare("
                SELECT c.*, s.sector_name, sc.scheme_name, tc.center_name
                FROM courses c
                LEFT JOIN sectors s ON c.sector_id = s.sector_id
                LEFT JOIN schemes sc ON c.scheme_id = sc.scheme_id
                LEFT JOIN training_centers tc ON c.center_id = tc.center_id
                $whereClause
                ORDER BY c.course_id DESC
            ");
            $stmt->execute($params);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendJSONResponse(true, 'Courses retrieved successfully', $courses);
            break;

        case 'update':
            $course_id = (int)($_POST['course_id'] ?? 0);
            $course_name = sanitizeInput($_POST['course_name'] ?? '');
            $course_code = sanitizeInput($_POST['course_code'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $duration_hours = (int)($_POST['duration_hours'] ?? 0);
            $fee = (float)($_POST['fee'] ?? 0);
            $prerequisites = sanitizeInput($_POST['prerequisites'] ?? '');
            $syllabus = sanitizeInput($_POST['syllabus'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $sector_id = (int)($_POST['sector_id'] ?? 0);
            $scheme_id = isset($_POST['scheme_id']) && $_POST['scheme_id'] !== '' ? (int)$_POST['scheme_id'] : null;
            $center_id = isset($_POST['center_id']) && $_POST['center_id'] !== '' ? (int)$_POST['center_id'] : null;

            if (empty($course_id) || empty($course_name) || empty($course_code) || $duration_hours <= 0 || $sector_id <= 0 || empty($center_id)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            // Validate sector
            $stmt = $pdo->prepare("SELECT sector_id FROM sectors WHERE sector_id = ?");
            $stmt->execute([$sector_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid sector');
            }
            // Validate center
            $stmt = $pdo->prepare("SELECT center_id FROM training_centers WHERE center_id = ?");
            $stmt->execute([$center_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid training center');
            }
            // Validate scheme if provided
            if ($scheme_id !== null) {
                $stmt = $pdo->prepare("SELECT scheme_id FROM schemes WHERE scheme_id = ?");
                $stmt->execute([$scheme_id]);
                if (!$stmt->fetch()) {
                    sendJSONResponse(false, 'Invalid scheme');
                }
            }
            $stmt = $pdo->prepare("
                UPDATE courses SET sector_id = ?, scheme_id = ?, center_id = ?, course_code = ?, course_name = ?, duration_hours = ?, fee = ?, description = ?, prerequisites = ?, syllabus = ?, status = ?, updated_at = NOW()
                WHERE course_id = ?
            ");
            $stmt->execute([
                $sector_id, $scheme_id, $center_id, $course_code, $course_name, $duration_hours, $fee, $description, $prerequisites, $syllabus, $status, $course_id
            ]);
            logAudit($_SESSION['user']['user_id'], 'update_course', [
                'course_id' => $course_id,
                'course_name' => $course_name,
                'sector_id' => $sector_id,
                'scheme_id' => $scheme_id,
                'center_id' => $center_id
            ]);
            sendJSONResponse(true, 'Course updated successfully');
            break;

        case 'delete':
            $course_id = (int)($_POST['course_id'] ?? 0);
            if (empty($course_id)) {
                sendJSONResponse(false, 'Course ID is required');
            }

            try {
                // Check if the course exists
                $stmt = $pdo->prepare("SELECT course_id FROM courses WHERE course_id = ?");
                $stmt->execute([$course_id]);
                if (!$stmt->fetch()) {
                    sendJSONResponse(false, 'Course not found');
                }

                // Check for dependencies in the batches table
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM batches WHERE course_id = ?");
                $stmt->execute([$course_id]);
                $batchCount = $stmt->fetchColumn();
                if ($batchCount > 0) {
                    sendJSONResponse(false, 'Cannot delete course. It is associated with existing batches.');
                }

                // Attempt to delete the course
                $stmt = $pdo->prepare("DELETE FROM courses WHERE course_id = ?");
                $stmt->execute([$course_id]);

                logAudit($_SESSION['user']['user_id'], 'delete_course', [
                    'course_id' => $course_id
                ]);

                sendJSONResponse(true, 'Course deleted successfully');
            } catch (PDOException $e) {
                logError("Delete course error: " . $e->getMessage());
                sendJSONResponse(false, 'An error occurred while deleting the course. Please try again later.');
            }
            break;

        case 'get':
            $course_id = (int)($_POST['course_id'] ?? 0);
            if (empty($course_id)) {
                sendJSONResponse(false, 'Course ID is required');
            }
            $stmt = $pdo->prepare("
                SELECT c.*, s.sector_name, sc.scheme_name, tc.center_name, tc.partner_id as partner_id
                FROM courses c
                LEFT JOIN sectors s ON c.sector_id = s.sector_id
                LEFT JOIN schemes sc ON c.scheme_id = sc.scheme_id
                LEFT JOIN training_centers tc ON c.center_id = tc.center_id
                WHERE c.course_id = ?
            ");
            $stmt->execute([$course_id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($course) {
                sendJSONResponse(true, 'Course retrieved successfully', $course);
            } else {
                sendJSONResponse(false, 'Course not found');
            }
            break;

        case 'assign_course':
            $course_id = (int)($_POST['course_id'] ?? 0);
            $sector_id = (int)($_POST['sector_id'] ?? 0);
            $scheme_id = (int)($_POST['scheme_id'] ?? 0);
            $center_id = (int)($_POST['center_id'] ?? 0);
            if (!$course_id || !$sector_id || !$scheme_id || !$center_id) {
                sendJSONResponse(false, 'All fields are required');
            }
            $stmt = $pdo->prepare("SELECT id FROM assigned_courses WHERE course_id = ? AND sector_id = ? AND scheme_id = ? AND center_id = ?");
            $stmt->execute([$course_id, $sector_id, $scheme_id, $center_id]);
            if ($stmt->fetch()) {
                sendJSONResponse(false, 'This course is already assigned to the selected sector, scheme, and center');
            }
            $stmt = $pdo->prepare("INSERT INTO assigned_courses (course_id, sector_id, scheme_id, center_id, assigned_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$course_id, $sector_id, $scheme_id, $center_id]);
            sendJSONResponse(true, 'Course assigned successfully');
            break;

        case 'get_assigned_courses':
            $course_id = (int)($_POST['course_id'] ?? 0);
            if (!$course_id) {
                sendJSONResponse(false, 'Course ID is required');
            }
            $stmt = $pdo->prepare("SELECT ac.sector_id, ac.scheme_id, ac.center_id, s.sector_name, sc.scheme_name, tc.center_name FROM assigned_courses ac JOIN sectors s ON ac.sector_id = s.sector_id JOIN schemes sc ON ac.scheme_id = sc.scheme_id JOIN training_centers tc ON ac.center_id = tc.center_id WHERE ac.course_id = ?");
            $stmt->execute([$course_id]);
            $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendJSONResponse(true, 'Assignments fetched', $assignments);
            break;

        case 'list':
            $scheme_id = isset($_POST['scheme_id']) && $_POST['scheme_id'] !== '' ? (int)$_POST['scheme_id'] : 0;
            $sector_id = isset($_POST['sector_id']) && $_POST['sector_id'] !== '' ? (int)$_POST['sector_id'] : 0;
            $where = ['status = "active"'];
            $params = [];
            if ($scheme_id > 0) {
                $where[] = 'scheme_id = ?';
                $params[] = $scheme_id;
            }
            if ($sector_id > 0) {
                $where[] = 'sector_id = ?';
                $params[] = $sector_id;
            }
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
            $stmt = $pdo->prepare("SELECT course_id, course_name FROM courses $whereClause ORDER BY course_name ASC");
            $stmt->execute($params);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendJSONResponse(true, 'Courses fetched', $courses);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Courses error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}