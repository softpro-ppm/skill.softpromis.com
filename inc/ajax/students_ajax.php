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
            $enrollment_no = sanitizeInput($_POST['enrollment_no'] ?? '');
            $first_name = sanitizeInput($_POST['first_name'] ?? '');
            $last_name = sanitizeInput($_POST['last_name'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $mobile = sanitizeInput($_POST['mobile'] ?? '');
            $date_of_birth = sanitizeInput($_POST['date_of_birth'] ?? '');
            $gender = sanitizeInput($_POST['gender'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');

            if (empty($enrollment_no) || empty($first_name) || empty($last_name)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (!empty($email) && !validateEmail($email)) {
                sendJSONResponse(false, 'Invalid email format');
            }
            if (!empty($mobile) && !validatePhone($mobile)) {
                sendJSONResponse(false, 'Invalid mobile format');
            }

            $stmt = $pdo->prepare("INSERT INTO students (
                enrollment_no, first_name, last_name, email, mobile, date_of_birth, gender, address, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $enrollment_no, $first_name, $last_name, $email, $mobile, $date_of_birth, $gender, $address
            ]);

            sendJSONResponse(true, 'Student created successfully', [
                'student_id' => $pdo->lastInsertId()
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

            // Only use columns that exist in the students table
            if (!empty($search)) {
                $searchFields = ['enrollment_no', 'first_name', 'last_name', 'email', 'mobile', 'address'];
                $searchResult = buildSearchQuery($searchFields, $search);
                $where[] = "(" . $searchResult['conditions'] . ")";
                $params = array_merge($params, $searchResult['params']);
            }

            // If you have a status or center_id column, keep these filters, else remove
            if (!empty($status) && in_array('status', array_map('strtolower', array_keys($pdo->query('DESCRIBE students')->fetchAll(PDO::FETCH_ASSOC))))) {
                $where[] = "s.status = ?";
                $params[] = $status;
            }
            if ($center_id > 0 && in_array('center_id', array_map('strtolower', array_keys($pdo->query('DESCRIBE students')->fetchAll(PDO::FETCH_ASSOC))))) {
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

            // Get data
            $stmt = $pdo->prepare("
                SELECT * FROM students s
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
            $student_id = (int)($_POST['student_id'] ?? 0);
            $first_name = sanitizeInput($_POST['first_name'] ?? '');
            $last_name = sanitizeInput($_POST['last_name'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $mobile = sanitizeInput($_POST['mobile'] ?? '');
            $date_of_birth = sanitizeInput($_POST['date_of_birth'] ?? '');
            $gender = sanitizeInput($_POST['gender'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');

            if (empty($student_id) || empty($first_name) || empty($last_name)) {
                sendJSONResponse(false, 'Required fields are missing');
            }
            if (!empty($email) && !validateEmail($email)) {
                sendJSONResponse(false, 'Invalid email format');
            }
            if (!empty($mobile) && !validatePhone($mobile)) {
                sendJSONResponse(false, 'Invalid mobile format');
            }

            $stmt = $pdo->prepare("UPDATE students SET
                first_name = ?, last_name = ?, email = ?, mobile = ?, date_of_birth = ?, gender = ?, address = ?, updated_at = NOW()
                WHERE student_id = ?");
            $stmt->execute([
                $first_name, $last_name, $email, $mobile, $date_of_birth, $gender, $address, $student_id
            ]);

            sendJSONResponse(true, 'Student updated successfully');
            break;

        case 'delete':
            $student_id = (int)($_POST['student_id'] ?? 0);
            if (empty($student_id)) {
                sendJSONResponse(false, 'Student ID is required');
            }
            $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
            $stmt->execute([$student_id]);
            sendJSONResponse(true, 'Student deleted successfully');
            break;

        case 'get':
            $student_id = (int)($_POST['student_id'] ?? 0);
            if (empty($student_id)) {
                sendJSONResponse(false, 'Student ID is required');
            }
            $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
            $stmt->execute([$student_id]);
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

        case 'list':
            $stmt = $pdo->query('SELECT student_id, enrollment_no, first_name, last_name, gender, mobile, email, date_of_birth, address FROM students ORDER BY created_at DESC');
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([ 'status' => 'success', 'data' => $students ]);
            exit;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Students error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}

function sendJSONResponse($success, $message, $data = null) {
    $response = [
        'status' => $success ? 'success' : 'error',
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}