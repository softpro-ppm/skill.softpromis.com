<?php
require_once '../../config.php';
require_once '../functions.php';

startSecureSession();
checkLogin();
checkPermission('admin');

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
error_log('Action received: ' . $action);

try {
    $pdo = getDBConnection();

    switch ($action) {
        case 'create':
            $enrollment_no = sanitizeInput($_POST['enrollment_no'] ?? '');
            $full_name = sanitizeInput($_POST['full_name'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $mobile = sanitizeInput($_POST['mobile'] ?? '');
            $date_of_birth = sanitizeInput($_POST['date_of_birth'] ?? '');
            $gender = sanitizeInput($_POST['gender'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : null;
            $batch_id = isset($_POST['batch_id']) ? (int)$_POST['batch_id'] : null;

            // Split full name into first and last name
            $first_name = '';
            $last_name = '';
            if (!empty($full_name)) {
                $parts = preg_split('/\s+/', trim($full_name), 2);
                $first_name = $parts[0];
                $last_name = isset($parts[1]) ? $parts[1] : '';
            }

            if (empty($enrollment_no) || empty($first_name)) {
                sendJSONResponse(false, 'Required fields are missing');
            }
            if (!empty($email) && !validateEmail($email)) {
                sendJSONResponse(false, 'Invalid email format');
            }
            if (!empty($mobile) && !preg_match('/^[0-9]{10}$/', $mobile)) {
                sendJSONResponse(false, 'Invalid mobile format');
            }
            // Check for unique enrollment_no
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM students WHERE enrollment_no = ?');
            $stmt->execute([$enrollment_no]);
            if ($stmt->fetchColumn() > 0) {
                sendJSONResponse(false, 'Enrollment number already exists');
            }

            // Handle file uploads
            $photo = null;
            $aadhaar = null;
            $qualification = null;
            $upload_dir = '../../uploads/students/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $photo = uniqid('photo_') . '.' . $ext;
                move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo);
            }
            if (isset($_FILES['aadhaar']) && $_FILES['aadhaar']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['aadhaar']['name'], PATHINFO_EXTENSION);
                $aadhaar = uniqid('aadhaar_') . '.' . $ext;
                move_uploaded_file($_FILES['aadhaar']['tmp_name'], $upload_dir . $aadhaar);
            }
            if (isset($_FILES['qualification']) && $_FILES['qualification']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['qualification']['name'], PATHINFO_EXTENSION);
                $qualification = uniqid('qualification_') . '.' . $ext;
                move_uploaded_file($_FILES['qualification']['tmp_name'], $upload_dir . $qualification);
            }

            $stmt = $pdo->prepare("INSERT INTO students (enrollment_no, first_name, last_name, email, mobile, date_of_birth, gender, address, course_id, batch_id, photo, aadhaar, qualification, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$enrollment_no, $first_name, $last_name, $email, $mobile, $date_of_birth, $gender, $address, $course_id, $batch_id, $photo, $aadhaar, $qualification]);
            $student_id = $pdo->lastInsertId();
            // Insert into student_batch_enrollment if batch_id is set
            if ($batch_id) {
                $enrollment_date = date('Y-m-d');
                $stmtEnroll = $pdo->prepare("INSERT INTO student_batch_enrollment (student_id, batch_id, enrollment_date, status, created_at, updated_at) VALUES (?, ?, ?, 'active', NOW(), NOW())");
                $stmtEnroll->execute([$student_id, $batch_id, $enrollment_date]);
            }
            sendJSONResponse(true, 'Student created successfully', ['student_id' => $student_id]);
            break;

        case 'update':
            $student_id = (int)($_POST['student_id'] ?? 0);
            $full_name = sanitizeInput($_POST['full_name'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $mobile = sanitizeInput($_POST['mobile'] ?? '');
            $date_of_birth = sanitizeInput($_POST['date_of_birth'] ?? '');
            $gender = sanitizeInput($_POST['gender'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : null;
            $batch_id = isset($_POST['batch_id']) ? (int)$_POST['batch_id'] : null;

            // Split full name into first and last name
            $first_name = '';
            $last_name = '';
            if (!empty($full_name)) {
                $parts = preg_split('/\s+/', trim($full_name), 2);
                $first_name = $parts[0];
                $last_name = isset($parts[1]) ? $parts[1] : '';
            }

            if (empty($student_id) || empty($first_name)) {
                sendJSONResponse(false, 'Required fields are missing');
            }
            if (!empty($email) && !validateEmail($email)) {
                sendJSONResponse(false, 'Invalid email format');
            }
            if (!empty($mobile) && !preg_match('/^[0-9]{10}$/', $mobile)) {
                sendJSONResponse(false, 'Invalid mobile format');
            }

            // Handle file uploads (optional, update if new file provided)
            $photo = null;
            $aadhaar = null;
            $qualification = null;
            $upload_dir = '../../uploads/students/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $photo = uniqid('photo_') . '.' . $ext;
                move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo);
            }
            if (isset($_FILES['aadhaar']) && $_FILES['aadhaar']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['aadhaar']['name'], PATHINFO_EXTENSION);
                $aadhaar = uniqid('aadhaar_') . '.' . $ext;
                move_uploaded_file($_FILES['aadhaar']['tmp_name'], $upload_dir . $aadhaar);
            }
            if (isset($_FILES['qualification']) && $_FILES['qualification']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['qualification']['name'], PATHINFO_EXTENSION);
                $qualification = uniqid('qualification_') . '.' . $ext;
                move_uploaded_file($_FILES['qualification']['tmp_name'], $upload_dir . $qualification);
            }

            $updateFields = "first_name = ?, last_name = ?, email = ?, mobile = ?, date_of_birth = ?, gender = ?, address = ?, course_id = ?, batch_id = ?";
            $params = [$first_name, $last_name, $email, $mobile, $date_of_birth, $gender, $address, $course_id, $batch_id];
            if ($photo !== null) {
                $updateFields .= ", photo = ?";
                $params[] = $photo;
            }
            if ($aadhaar !== null) {
                $updateFields .= ", aadhaar = ?";
                $params[] = $aadhaar;
            }
            if ($qualification !== null) {
                $updateFields .= ", qualification = ?";
                $params[] = $qualification;
            }
            $updateFields .= ", updated_at = NOW()";
            $params[] = $student_id;
            $stmt = $pdo->prepare("UPDATE students SET $updateFields WHERE student_id = ?");
            $stmt->execute($params);
            // Update or insert into student_batch_enrollment
            if ($batch_id) {
                $enrollment_date = date('Y-m-d');
                // Check if already enrolled
                $stmtCheck = $pdo->prepare("SELECT enrollment_id FROM student_batch_enrollment WHERE student_id = ? AND batch_id = ?");
                $stmtCheck->execute([$student_id, $batch_id]);
                if ($stmtCheck->fetchColumn()) {
                    // Update status and date
                    $stmtUpdate = $pdo->prepare("UPDATE student_batch_enrollment SET status = 'active', enrollment_date = ?, updated_at = NOW() WHERE student_id = ? AND batch_id = ?");
                    $stmtUpdate->execute([$enrollment_date, $student_id, $batch_id]);
                } else {
                    // Insert new enrollment
                    $stmtEnroll = $pdo->prepare("INSERT INTO student_batch_enrollment (student_id, batch_id, enrollment_date, status, created_at, updated_at) VALUES (?, ?, ?, 'active', NOW(), NOW())");
                    $stmtEnroll->execute([$student_id, $batch_id, $enrollment_date]);
                }
            }
            sendJSONResponse(true, 'Student updated successfully');
            break;

        case 'delete':
            $student_id = (int)($_POST['student_id'] ?? 0);
            error_log('Delete Action: student_id=' . $student_id);
            if (empty($student_id)) {
                sendJSONResponse(false, 'Student ID is required');
            }
            try {
                // Delete from fees
                $stmt = $pdo->prepare('DELETE FROM fees WHERE student_id = ?');
                $stmt->execute([$student_id]);

                // Delete from student_batch_enrollment
                $stmt = $pdo->prepare('DELETE FROM student_batch_enrollment WHERE student_id = ?');
                $stmt->execute([$student_id]);

                // (Add more deletes for other related tables if needed)

                // Finally, delete the student
                $stmt = $pdo->prepare('DELETE FROM students WHERE student_id = ?');
                $stmt->execute([$student_id]);

                if ($stmt->rowCount() > 0) {
                    sendJSONResponse(true, 'Student deleted successfully');
                } else {
                    sendJSONResponse(false, 'No student deleted. The student may not exist.');
                }
            } catch (PDOException $e) {
                error_log('Delete Error: ' . $e->getMessage());
                sendJSONResponse(false, 'Failed to delete student: ' . $e->getMessage());
            }
            break;

        case 'get':
            $student_id = (int)($_POST['student_id'] ?? 0);
            if (empty($student_id)) {
                sendJSONResponse(false, 'Student ID is required');
            }
            // Fetch student with all related info for view modal
            $stmt = $pdo->prepare('
                SELECT s.*, c.course_name, b.batch_code, b.batch_name, c.sector_id, c.scheme_id, c.center_id,
                       tc.center_name, sc.scheme_name, se.sector_name, tp.partner_name
                FROM students s
                LEFT JOIN courses c ON s.course_id = c.course_id
                LEFT JOIN batches b ON s.batch_id = b.batch_id
                LEFT JOIN sectors se ON c.sector_id = se.sector_id
                LEFT JOIN schemes sc ON c.scheme_id = sc.scheme_id
                LEFT JOIN training_centers tc ON c.center_id = tc.center_id
                LEFT JOIN training_partners tp ON tc.partner_id = tp.partner_id
                WHERE s.student_id = ?
            ');
            $stmt->execute([$student_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($student) {
                $student['full_name'] = trim($student['first_name'] . ' ' . $student['last_name']);
                sendJSONResponse(true, 'Student retrieved successfully', $student);
            } else {
                sendJSONResponse(false, 'Student not found');
            }
            break;

        case 'list':
            $stmt = $pdo->query('SELECT s.student_id, s.enrollment_no, s.first_name, s.last_name, s.gender, s.mobile, s.email, s.date_of_birth, s.address, 
                c.course_name, b.batch_code
                FROM students s
                LEFT JOIN courses c ON s.course_id = c.course_id
                LEFT JOIN batches b ON s.batch_id = b.batch_id
                ORDER BY s.created_at DESC');
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Add full_name field for DataTables compatibility
            foreach ($students as &$student) {
                $student['full_name'] = trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));
            }
            echo json_encode(['success' => true, 'data' => $students]);
            exit;

        case 'getEnrollments':
            try {
                $stmt = $pdo->query('SELECT student_id AS enrollment_id, enrollment_no, CONCAT(first_name, " ", last_name) AS student_name FROM students ORDER BY first_name ASC');
                $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log('Enrollments fetched: ' . json_encode($enrollments));

                echo json_encode([
                    'success' => true,
                    'data' => $enrollments
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to fetch enrollments: ' . $e->getMessage()
                ]);
            }
            exit;

        case 'get_enrollments_by_student':
            $student_id = (int)($_POST['student_id'] ?? 0);
            if (empty($student_id)) {
                sendJSONResponse(false, 'Student ID is required');
            }
            $stmt = $pdo->prepare('SELECT enrollment_id FROM student_batch_enrollment WHERE student_id = ?');
            $stmt->execute([$student_id]);
            $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendJSONResponse(true, 'Enrollments fetched', $enrollments);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError('Students error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'data' => [], 'error' => 'An error occurred. Please try again later.']);
    exit;
}