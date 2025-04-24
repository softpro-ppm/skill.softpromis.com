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
            $description = sanitizeInput($_POST['description'] ?? '');
            $duration = (int)($_POST['duration'] ?? 0);
            $duration_unit = sanitizeInput($_POST['duration_unit'] ?? 'hours');
            $fee = (float)($_POST['fee'] ?? 0);
            $max_students = (int)($_POST['max_students'] ?? 0);
            $prerequisites = sanitizeInput($_POST['prerequisites'] ?? '');
            $syllabus = sanitizeInput($_POST['syllabus'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $sector_id = (int)($_POST['sector_id'] ?? 0);
            $scheme_id = (int)($_POST['scheme_id'] ?? 0);

            if (empty($name) || empty($description) || $duration <= 0 || $sector_id <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            // Validate sector
            $stmt = $pdo->prepare("SELECT id FROM sectors WHERE id = ?");
            $stmt->execute([$sector_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid sector');
            }

            // Validate scheme if provided
            if ($scheme_id > 0) {
                $stmt = $pdo->prepare("SELECT id FROM schemes WHERE id = ?");
                $stmt->execute([$scheme_id]);
                if (!$stmt->fetch()) {
                    sendJSONResponse(false, 'Invalid scheme');
                }
            }

            $stmt = $pdo->prepare("
                INSERT INTO courses (
                    name, description, duration, duration_unit, fee,
                    max_students, prerequisites, syllabus, status,
                    sector_id, scheme_id, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $name, $description, $duration, $duration_unit, $fee,
                $max_students, $prerequisites, $syllabus, $status,
                $sector_id, $scheme_id
            ]);

            logAudit($_SESSION['user']['id'], 'create_course', [
                'name' => $name,
                'sector_id' => $sector_id,
                'scheme_id' => $scheme_id
            ]);

            sendJSONResponse(true, 'Course created successfully', [
                'id' => $pdo->lastInsertId()
            ]);
            break;

        case 'read':
            $page = (int)($_POST['page'] ?? 1);
            $perPage = (int)($_POST['per_page'] ?? 10);
            $search = sanitizeInput($_POST['search'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? '');
            $sector_id = (int)($_POST['sector_id'] ?? 0);
            $scheme_id = (int)($_POST['scheme_id'] ?? 0);

            $where = [];
            $params = [];

            if (!empty($search)) {
                $searchFields = ['name', 'description', 'prerequisites', 'syllabus'];
                $searchResult = buildSearchQuery($searchFields, $search);
                $where[] = "(" . $searchResult['conditions'] . ")";
                $params = array_merge($params, $searchResult['params']);
            }

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

            // Get total count
            $countStmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM courses c
                $whereClause
            ");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            // Get pagination info
            $pagination = getPagination($page, $total, $perPage);

            // Get data with related info
            $stmt = $pdo->prepare("
                SELECT c.*, s.name as sector_name, sc.name as scheme_name
                FROM courses c
                LEFT JOIN sectors s ON c.sector_id = s.id
                LEFT JOIN schemes sc ON c.scheme_id = sc.id
                $whereClause
                ORDER BY c.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $pagination['per_page'], $pagination['offset']]);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Courses retrieved successfully', [
                'data' => $courses,
                'pagination' => $pagination
            ]);
            break;

        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            $name = sanitizeInput($_POST['name'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $duration = (int)($_POST['duration'] ?? 0);
            $duration_unit = sanitizeInput($_POST['duration_unit'] ?? 'hours');
            $fee = (float)($_POST['fee'] ?? 0);
            $max_students = (int)($_POST['max_students'] ?? 0);
            $prerequisites = sanitizeInput($_POST['prerequisites'] ?? '');
            $syllabus = sanitizeInput($_POST['syllabus'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $sector_id = (int)($_POST['sector_id'] ?? 0);
            $scheme_id = (int)($_POST['scheme_id'] ?? 0);

            if (empty($id) || empty($name) || empty($description) || $duration <= 0 || $sector_id <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            // Validate sector
            $stmt = $pdo->prepare("SELECT id FROM sectors WHERE id = ?");
            $stmt->execute([$sector_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid sector');
            }

            // Validate scheme if provided
            if ($scheme_id > 0) {
                $stmt = $pdo->prepare("SELECT id FROM schemes WHERE id = ?");
                $stmt->execute([$scheme_id]);
                if (!$stmt->fetch()) {
                    sendJSONResponse(false, 'Invalid scheme');
                }
            }

            $stmt = $pdo->prepare("
                UPDATE courses 
                SET name = ?, description = ?, duration = ?, duration_unit = ?,
                    fee = ?, max_students = ?, prerequisites = ?, syllabus = ?,
                    status = ?, sector_id = ?, scheme_id = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $name, $description, $duration, $duration_unit, $fee,
                $max_students, $prerequisites, $syllabus, $status,
                $sector_id, $scheme_id, $id
            ]);

            logAudit($_SESSION['user']['id'], 'update_course', [
                'id' => $id,
                'name' => $name,
                'sector_id' => $sector_id,
                'scheme_id' => $scheme_id
            ]);

            sendJSONResponse(true, 'Course updated successfully');
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            // Check if course has active batches
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM batches 
                WHERE course_id = ? AND status = 'active'
            ");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                sendJSONResponse(false, 'Cannot delete course with active batches');
            }

            // Get course info for audit log
            $stmt = $pdo->prepare("
                SELECT c.name, s.name as sector_name, sc.name as scheme_name
                FROM courses c
                LEFT JOIN sectors s ON c.sector_id = s.id
                LEFT JOIN schemes sc ON c.scheme_id = sc.id
                WHERE c.id = ?
            ");
            $stmt->execute([$id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$course) {
                sendJSONResponse(false, 'Course not found');
            }

            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$id]);

            logAudit($_SESSION['user']['id'], 'delete_course', [
                'id' => $id,
                'name' => $course['name'],
                'sector' => $course['sector_name'],
                'scheme' => $course['scheme_name']
            ]);

            sendJSONResponse(true, 'Course deleted successfully');
            break;

        case 'get':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT c.*, s.name as sector_name, sc.name as scheme_name
                FROM courses c
                LEFT JOIN sectors s ON c.sector_id = s.id
                LEFT JOIN schemes sc ON c.scheme_id = sc.id
                WHERE c.id = ?
            ");
            $stmt->execute([$id]);
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