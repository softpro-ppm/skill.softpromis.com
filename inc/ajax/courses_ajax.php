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
            $scheme_id = (int)($_POST['scheme_id'] ?? 0);

            if (empty($course_name) || empty($course_code) || $duration_hours <= 0 || $sector_id <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            // Validate sector
            $stmt = $pdo->prepare("SELECT sector_id FROM sectors WHERE sector_id = ?");
            $stmt->execute([$sector_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid sector');
            }

            // Validate scheme if provided
            if ($scheme_id > 0) {
                $stmt = $pdo->prepare("SELECT scheme_id FROM schemes WHERE scheme_id = ?");
                $stmt->execute([$scheme_id]);
                if (!$stmt->fetch()) {
                    sendJSONResponse(false, 'Invalid scheme');
                }
            }

            $stmt = $pdo->prepare("
                INSERT INTO courses (
                    sector_id, scheme_id, course_code, course_name, duration_hours, fee, description, prerequisites, syllabus, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $sector_id, $scheme_id, $course_code, $course_name, $duration_hours, $fee, $description, $prerequisites, $syllabus, $status
            ]);

            logAudit($_SESSION['user']['user_id'], 'create_course', [
                'course_name' => $course_name,
                'sector_id' => $sector_id,
                'scheme_id' => $scheme_id
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
                SELECT c.*, s.sector_name, sc.scheme_name
                FROM courses c
                LEFT JOIN sectors s ON c.sector_id = s.sector_id
                LEFT JOIN schemes sc ON c.scheme_id = sc.scheme_id
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
            $scheme_id = (int)($_POST['scheme_id'] ?? 0);

            if (empty($course_id) || empty($course_name) || empty($course_code) || $duration_hours <= 0 || $sector_id <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            // Validate sector
            $stmt = $pdo->prepare("SELECT sector_id FROM sectors WHERE sector_id = ?");
            $stmt->execute([$sector_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid sector');
            }
            // Validate scheme if provided
            if ($scheme_id > 0) {
                $stmt = $pdo->prepare("SELECT scheme_id FROM schemes WHERE scheme_id = ?");
                $stmt->execute([$scheme_id]);
                if (!$stmt->fetch()) {
                    sendJSONResponse(false, 'Invalid scheme');
                }
            }
            $stmt = $pdo->prepare("
                UPDATE courses SET sector_id = ?, scheme_id = ?, course_code = ?, course_name = ?, duration_hours = ?, fee = ?, description = ?, prerequisites = ?, syllabus = ?, status = ?, updated_at = NOW()
                WHERE course_id = ?
            ");
            $stmt->execute([
                $sector_id, $scheme_id, $course_code, $course_name, $duration_hours, $fee, $description, $prerequisites, $syllabus, $status, $course_id
            ]);
            logAudit($_SESSION['user']['user_id'], 'update_course', [
                'course_id' => $course_id,
                'course_name' => $course_name,
                'sector_id' => $sector_id,
                'scheme_id' => $scheme_id
            ]);
            sendJSONResponse(true, 'Course updated successfully');
            break;

        case 'delete':
            $course_id = (int)($_POST['course_id'] ?? 0);
            if (empty($course_id)) {
                sendJSONResponse(false, 'Course ID is required');
            }
            $stmt = $pdo->prepare("DELETE FROM courses WHERE course_id = ?");
            $stmt->execute([$course_id]);
            logAudit($_SESSION['user']['user_id'], 'delete_course', [
                'course_id' => $course_id
            ]);
            sendJSONResponse(true, 'Course deleted successfully');
            break;

        case 'get':
            $course_id = (int)($_POST['course_id'] ?? 0);
            if (empty($course_id)) {
                sendJSONResponse(false, 'Course ID is required');
            }
            $stmt = $pdo->prepare("
                SELECT c.*, s.sector_name, sc.scheme_name
                FROM courses c
                LEFT JOIN sectors s ON c.sector_id = s.sector_id
                LEFT JOIN schemes sc ON c.scheme_id = sc.scheme_id
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

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Courses error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 