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
            $email = sanitizeInput($_POST['email'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            $city = sanitizeInput($_POST['city'] ?? '');
            $state = sanitizeInput($_POST['state'] ?? '');
            $pincode = sanitizeInput($_POST['pincode'] ?? '');
            $dob = sanitizeInput($_POST['dob'] ?? '');
            $gender = sanitizeInput($_POST['gender'] ?? '');
            $education = sanitizeInput($_POST['education'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $center_id = (int)($_POST['center_id'] ?? 0);

            if (empty($name) || empty($email) || empty($phone)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (!validateEmail($email)) {
                sendJSONResponse(false, 'Invalid email format');
            }

            if (!validatePhone($phone)) {
                sendJSONResponse(false, 'Invalid phone format');
            }

            // Validate center if provided
            if ($center_id > 0) {
                $stmt = $pdo->prepare("SELECT id FROM training_centers WHERE id = ?");
                $stmt->execute([$center_id]);
                if (!$stmt->fetch()) {
                    sendJSONResponse(false, 'Invalid training center');
                }
            }

            $stmt = $pdo->prepare("
                INSERT INTO students (
                    name, email, phone, address, city, state, pincode,
                    dob, gender, education, status, center_id, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $name, $email, $phone, $address, $city, $state, $pincode,
                $dob, $gender, $education, $status, $center_id
            ]);

            logAudit($_SESSION['user']['id'], 'create_student', [
                'name' => $name,
                'email' => $email,
                'center_id' => $center_id
            ]);

            sendJSONResponse(true, 'Student created successfully', [
                'id' => $pdo->lastInsertId()
            ]);
            break;

        case 'read':
            $page = (int)($_POST['page'] ?? 1);
            $perPage = (int)($_POST['per_page'] ?? 10);
            $search = sanitizeInput($_POST['search'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? '');
            $center_id = (int)($_POST['center_id'] ?? 0);

            $where = [];
            $params = [];

            if (!empty($search)) {
                $searchFields = ['name', 'email', 'phone', 'address', 'city', 'state'];
                $searchResult = buildSearchQuery($searchFields, $search);
                $where[] = "(" . $searchResult['conditions'] . ")";
                $params = array_merge($params, $searchResult['params']);
            }

            if (!empty($status)) {
                $where[] = "s.status = ?";
                $params[] = $status;
            }

            if ($center_id > 0) {
                $where[] = "s.center_id = ?";
                $params[] = $center_id;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Get total count
            $countStmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM students s
                $whereClause
            ");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            // Get pagination info
            $pagination = getPagination($page, $total, $perPage);

            // Get data with related info
            $stmt = $pdo->prepare("
                SELECT s.*, tc.name as center_name,
                       (SELECT COUNT(*) FROM batch_students WHERE student_id = s.id) as batch_count
                FROM students s
                LEFT JOIN training_centers tc ON s.center_id = tc.id
                $whereClause
                ORDER BY s.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $pagination['per_page'], $pagination['offset']]);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Students retrieved successfully', [
                'data' => $students,
                'pagination' => $pagination
            ]);
            break;

        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            $name = sanitizeInput($_POST['name'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            $city = sanitizeInput($_POST['city'] ?? '');
            $state = sanitizeInput($_POST['state'] ?? '');
            $pincode = sanitizeInput($_POST['pincode'] ?? '');
            $dob = sanitizeInput($_POST['dob'] ?? '');
            $gender = sanitizeInput($_POST['gender'] ?? '');
            $education = sanitizeInput($_POST['education'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $center_id = (int)($_POST['center_id'] ?? 0);

            if (empty($id) || empty($name) || empty($email) || empty($phone)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (!validateEmail($email)) {
                sendJSONResponse(false, 'Invalid email format');
            }

            if (!validatePhone($phone)) {
                sendJSONResponse(false, 'Invalid phone format');
            }

            // Validate center if provided
            if ($center_id > 0) {
                $stmt = $pdo->prepare("SELECT id FROM training_centers WHERE id = ?");
                $stmt->execute([$center_id]);
                if (!$stmt->fetch()) {
                    sendJSONResponse(false, 'Invalid training center');
                }
            }

            $stmt = $pdo->prepare("
                UPDATE students 
                SET name = ?, email = ?, phone = ?, address = ?, city = ?,
                    state = ?, pincode = ?, dob = ?, gender = ?, education = ?,
                    status = ?, center_id = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $name, $email, $phone, $address, $city, $state, $pincode,
                $dob, $gender, $education, $status, $center_id, $id
            ]);

            logAudit($_SESSION['user']['id'], 'update_student', [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'center_id' => $center_id
            ]);

            sendJSONResponse(true, 'Student updated successfully');
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            // Check if student has active batch enrollments
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM batch_students 
                WHERE student_id = ? AND status = 'active'
            ");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                sendJSONResponse(false, 'Cannot delete student with active batch enrollments');
            }

            // Get student info for audit log
            $stmt = $pdo->prepare("
                SELECT s.name, s.email, tc.name as center_name
                FROM students s
                LEFT JOIN training_centers tc ON s.center_id = tc.id
                WHERE s.id = ?
            ");
            $stmt->execute([$id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$student) {
                sendJSONResponse(false, 'Student not found');
            }

            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$id]);

            logAudit($_SESSION['user']['id'], 'delete_student', [
                'id' => $id,
                'name' => $student['name'],
                'email' => $student['email'],
                'center' => $student['center_name']
            ]);

            sendJSONResponse(true, 'Student deleted successfully');
            break;

        case 'get':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT s.*, tc.name as center_name,
                       (SELECT COUNT(*) FROM batch_students WHERE student_id = s.id) as batch_count
                FROM students s
                LEFT JOIN training_centers tc ON s.center_id = tc.id
                WHERE s.id = ?
            ");
            $stmt->execute([$id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student) {
                sendJSONResponse(true, 'Student retrieved successfully', $student);
            } else {
                sendJSONResponse(false, 'Student not found');
            }
            break;

        case 'get_batches':
            $student_id = (int)($_POST['student_id'] ?? 0);

            if (empty($student_id)) {
                sendJSONResponse(false, 'Student ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT b.*, c.name as course_name, tc.name as center_name,
                       bs.status as enrollment_status, bs.enrolled_at
                FROM batches b
                JOIN batch_students bs ON b.id = bs.batch_id
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                WHERE bs.student_id = ?
                ORDER BY b.start_date DESC
            ");
            $stmt->execute([$student_id]);
            $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Student batches retrieved successfully', $batches);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Students error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 