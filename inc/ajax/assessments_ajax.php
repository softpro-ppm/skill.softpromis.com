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
            $enrollment_id = (int)($_POST['enrollment_id'] ?? 0);
            $assessment_type = sanitizeInput($_POST['assessment_type'] ?? '');
            $assessment_date = sanitizeInput($_POST['assessment_date'] ?? '');
            $score = (float)($_POST['score'] ?? 0);
            $max_score = (float)($_POST['max_score'] ?? 100);
            $remarks = sanitizeInput($_POST['remarks'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'pending');

            // Log received data for debugging
            error_log('Received Data: ' . json_encode($_POST));

            error_log('Create Action: enrollment_id=' . $enrollment_id . ', assessment_type=' . $assessment_type . ', assessment_date=' . $assessment_date);

            // Validate required fields
            if (empty($enrollment_id) || empty($assessment_type) || empty($assessment_date) || $score < 0 || $max_score <= 0 || empty($status)) {
                sendJSONResponse(false, 'Required fields are missing or invalid');
            }

            // Validate assessment_date
            $currentDate = date('Y-m-d');
            if ($assessment_date < '2000-01-01' || $assessment_date > $currentDate) {
                sendJSONResponse(false, 'Assessment Date is invalid');
            }

            // Validate max_score
            if ($max_score > 1000) {
                sendJSONResponse(false, 'Max Score exceeds the allowable limit');
            }

            // Validate score and max_score
            if ($score > $max_score) {
                sendJSONResponse(false, 'Score cannot exceed Max Score');
            }

            // Validate enrollment
            $stmt = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ?");
            $stmt->execute([$enrollment_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid enrollment');
            }

            $stmt = $pdo->prepare("
                INSERT INTO assessments (
                    enrollment_id, assessment_type, assessment_date, score, max_score, remarks, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $enrollment_id, $assessment_type, $assessment_date, $score, $max_score, $remarks, $status
            ]);

            logAudit($_SESSION['user']['id'], 'create_assessment', [
                'enrollment_id' => $enrollment_id,
                'assessment_type' => $assessment_type,
                'assessment_date' => $assessment_date
            ]);

            sendJSONResponse(true, 'Assessment created successfully', [
                'id' => $pdo->lastInsertId()
            ]);
            break;

        case 'list':
            error_log('List action triggered'); // Debugging log
            // For DataTable
            $stmt = $pdo->prepare("
                SELECT a.*, s.first_name, s.last_name, c.course_name
                FROM assessments a
                JOIN student_batch_enrollment e ON a.enrollment_id = e.enrollment_id
                JOIN students s ON e.student_id = s.student_id
                JOIN batches b ON e.batch_id = b.batch_id
                JOIN courses c ON b.course_id = c.course_id
                ORDER BY a.created_at DESC
            ");
            $stmt->execute();
            $assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log('Assessments fetched: ' . json_encode($assessments)); // Debugging log
            $data = [];
            foreach ($assessments as $row) {
                $row['student_name'] = $row['first_name'] . ' ' . $row['last_name'];
                $data[] = $row;
            }
            sendJSONResponse(true, 'Assessments retrieved successfully', [ 'data' => $data ]);
            break;

        case 'get':
            $assessment_id = (int)($_POST['assessment_id'] ?? 0);
            if (empty($assessment_id)) {
                sendJSONResponse(false, 'ID is required');
            }
            $stmt = $pdo->prepare("
                SELECT a.*, s.first_name, s.last_name, c.course_name
                FROM assessments a
                JOIN student_batch_enrollment e ON a.enrollment_id = e.enrollment_id
                JOIN students s ON e.student_id = s.student_id
                JOIN batches b ON e.batch_id = b.batch_id
                JOIN courses c ON b.course_id = c.course_id
                WHERE a.assessment_id = ?
            ");
            $stmt->execute([$assessment_id]);
            $assessment = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($assessment) {
                $assessment['student_name'] = $assessment['first_name'] . ' ' . $assessment['last_name'];
                sendJSONResponse(true, 'Assessment retrieved successfully', $assessment);
            } else {
                sendJSONResponse(false, 'Assessment not found');
            }
            break;

        case 'update':
            $assessment_id = (int)($_POST['assessment_id'] ?? 0);
            $enrollment_id = (int)($_POST['enrollment_id'] ?? 0);
            $assessment_type = sanitizeInput($_POST['assessment_type'] ?? '');
            $assessment_date = sanitizeInput($_POST['assessment_date'] ?? '');
            $score = (float)($_POST['score'] ?? 0);
            $max_score = (float)($_POST['max_score'] ?? 100);
            $remarks = sanitizeInput($_POST['remarks'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'pending');

            if (empty($assessment_id) || empty($enrollment_id) || empty($assessment_type) || empty($assessment_date)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            $stmt = $pdo->prepare("
                UPDATE assessments 
                SET enrollment_id = ?, assessment_type = ?, assessment_date = ?, score = ?, max_score = ?, remarks = ?, status = ?, updated_at = NOW()
                WHERE assessment_id = ?
            ");
            $stmt->execute([
                $enrollment_id, $assessment_type, $assessment_date, $score, $max_score, $remarks, $status, $assessment_id
            ]);

            logAudit($_SESSION['user']['id'], 'update_assessment', [
                'assessment_id' => $assessment_id,
                'assessment_type' => $assessment_type,
                'assessment_date' => $assessment_date
            ]);

            sendJSONResponse(true, 'Assessment updated successfully');
            break;

        case 'delete':
            $assessment_id = (int)($_POST['assessment_id'] ?? 0);
            if (empty($assessment_id)) {
                sendJSONResponse(false, 'ID is required');
            }
            $stmt = $pdo->prepare("DELETE FROM assessments WHERE assessment_id = ?");
            $stmt->execute([$assessment_id]);
            logAudit($_SESSION['user']['id'], 'delete_assessment', [ 'assessment_id' => $assessment_id ]);
            sendJSONResponse(true, 'Assessment deleted successfully');
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Assessments error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}