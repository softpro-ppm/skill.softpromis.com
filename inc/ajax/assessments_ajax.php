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
            $batch_id = (int)($_POST['batch_id'] ?? 0);
            $title = sanitizeInput($_POST['title'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $assessment_date = sanitizeInput($_POST['assessment_date'] ?? '');
            $duration = (int)($_POST['duration'] ?? 0);
            $total_marks = (int)($_POST['total_marks'] ?? 0);
            $passing_marks = (int)($_POST['passing_marks'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? 'scheduled');
            $instructions = sanitizeInput($_POST['instructions'] ?? '');

            if (empty($batch_id) || empty($title) || empty($assessment_date) || $duration <= 0 || $total_marks <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if ($passing_marks > $total_marks) {
                sendJSONResponse(false, 'Passing marks cannot be greater than total marks');
            }

            // Validate batch
            $stmt = $pdo->prepare("SELECT id FROM batches WHERE id = ?");
            $stmt->execute([$batch_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid batch');
            }

            $stmt = $pdo->prepare("
                INSERT INTO assessments (
                    batch_id, title, description, assessment_date, duration,
                    total_marks, passing_marks, status, instructions, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $batch_id, $title, $description, $assessment_date, $duration,
                $total_marks, $passing_marks, $status, $instructions
            ]);

            logAudit($_SESSION['user']['id'], 'create_assessment', [
                'batch_id' => $batch_id,
                'title' => $title,
                'assessment_date' => $assessment_date
            ]);

            sendJSONResponse(true, 'Assessment created successfully', [
                'id' => $pdo->lastInsertId()
            ]);
            break;

        case 'read':
            $page = (int)($_POST['page'] ?? 1);
            $perPage = (int)($_POST['per_page'] ?? 10);
            $search = sanitizeInput($_POST['search'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? '');
            $batch_id = (int)($_POST['batch_id'] ?? 0);

            $where = [];
            $params = [];

            if (!empty($search)) {
                $searchFields = ['a.title', 'a.description', 'a.instructions'];
                $searchResult = buildSearchQuery($searchFields, $search);
                $where[] = "(" . $searchResult['conditions'] . ")";
                $params = array_merge($params, $searchResult['params']);
            }

            if (!empty($status)) {
                $where[] = "a.status = ?";
                $params[] = $status;
            }

            if ($batch_id > 0) {
                $where[] = "a.batch_id = ?";
                $params[] = $batch_id;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Get total count
            $countStmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM assessments a
                $whereClause
            ");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            // Get pagination info
            $pagination = getPagination($page, $total, $perPage);

            // Get data with related info
            $stmt = $pdo->prepare("
                SELECT a.*, b.name as batch_name, c.name as course_name,
                       tc.name as center_name,
                       (SELECT COUNT(*) FROM assessment_results WHERE assessment_id = a.id) as student_count
                FROM assessments a
                JOIN batches b ON a.batch_id = b.id
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                $whereClause
                ORDER BY a.assessment_date DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $pagination['per_page'], $pagination['offset']]);
            $assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Assessments retrieved successfully', [
                'data' => $assessments,
                'pagination' => $pagination
            ]);
            break;

        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            $title = sanitizeInput($_POST['title'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $assessment_date = sanitizeInput($_POST['assessment_date'] ?? '');
            $duration = (int)($_POST['duration'] ?? 0);
            $total_marks = (int)($_POST['total_marks'] ?? 0);
            $passing_marks = (int)($_POST['passing_marks'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? 'scheduled');
            $instructions = sanitizeInput($_POST['instructions'] ?? '');

            if (empty($id) || empty($title) || empty($assessment_date) || $duration <= 0 || $total_marks <= 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if ($passing_marks > $total_marks) {
                sendJSONResponse(false, 'Passing marks cannot be greater than total marks');
            }

            $stmt = $pdo->prepare("
                UPDATE assessments 
                SET title = ?, description = ?, assessment_date = ?, duration = ?,
                    total_marks = ?, passing_marks = ?, status = ?, instructions = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $title, $description, $assessment_date, $duration,
                $total_marks, $passing_marks, $status, $instructions, $id
            ]);

            logAudit($_SESSION['user']['id'], 'update_assessment', [
                'id' => $id,
                'title' => $title,
                'assessment_date' => $assessment_date
            ]);

            sendJSONResponse(true, 'Assessment updated successfully');
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            // Check if assessment has results
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM assessment_results 
                WHERE assessment_id = ?
            ");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                sendJSONResponse(false, 'Cannot delete assessment with results');
            }

            // Get assessment info for audit log
            $stmt = $pdo->prepare("
                SELECT a.title, a.assessment_date, b.name as batch_name
                FROM assessments a
                JOIN batches b ON a.batch_id = b.id
                WHERE a.id = ?
            ");
            $stmt->execute([$id]);
            $assessment = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$assessment) {
                sendJSONResponse(false, 'Assessment not found');
            }

            $stmt = $pdo->prepare("DELETE FROM assessments WHERE id = ?");
            $stmt->execute([$id]);

            logAudit($_SESSION['user']['id'], 'delete_assessment', [
                'id' => $id,
                'title' => $assessment['title'],
                'assessment_date' => $assessment['assessment_date'],
                'batch' => $assessment['batch_name']
            ]);

            sendJSONResponse(true, 'Assessment deleted successfully');
            break;

        case 'get':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT a.*, b.name as batch_name, c.name as course_name,
                       tc.name as center_name,
                       (SELECT COUNT(*) FROM assessment_results WHERE assessment_id = a.id) as student_count
                FROM assessments a
                JOIN batches b ON a.batch_id = b.id
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                WHERE a.id = ?
            ");
            $stmt->execute([$id]);
            $assessment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($assessment) {
                sendJSONResponse(true, 'Assessment retrieved successfully', $assessment);
            } else {
                sendJSONResponse(false, 'Assessment not found');
            }
            break;

        case 'get_results':
            $assessment_id = (int)($_POST['assessment_id'] ?? 0);

            if (empty($assessment_id)) {
                sendJSONResponse(false, 'Assessment ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT ar.*, s.name as student_name
                FROM assessment_results ar
                JOIN students s ON ar.student_id = s.id
                WHERE ar.assessment_id = ?
                ORDER BY ar.marks_obtained DESC
            ");
            $stmt->execute([$assessment_id]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Assessment results retrieved successfully', $results);
            break;

        case 'submit_result':
            $assessment_id = (int)($_POST['assessment_id'] ?? 0);
            $student_id = (int)($_POST['student_id'] ?? 0);
            $marks_obtained = (float)($_POST['marks_obtained'] ?? 0);
            $remarks = sanitizeInput($_POST['remarks'] ?? '');

            if (empty($assessment_id) || empty($student_id) || $marks_obtained < 0) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            // Validate assessment
            $stmt = $pdo->prepare("
                SELECT total_marks, passing_marks 
                FROM assessments 
                WHERE id = ?
            ");
            $stmt->execute([$assessment_id]);
            $assessment = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$assessment) {
                sendJSONResponse(false, 'Invalid assessment');
            }

            if ($marks_obtained > $assessment['total_marks']) {
                sendJSONResponse(false, 'Marks obtained cannot be greater than total marks');
            }

            // Check if result already exists
            $stmt = $pdo->prepare("
                SELECT id FROM assessment_results 
                WHERE assessment_id = ? AND student_id = ?
            ");
            $stmt->execute([$assessment_id, $student_id]);
            if ($stmt->fetch()) {
                sendJSONResponse(false, 'Result already exists for this student');
            }

            $status = $marks_obtained >= $assessment['passing_marks'] ? 'passed' : 'failed';

            $stmt = $pdo->prepare("
                INSERT INTO assessment_results (
                    assessment_id, student_id, marks_obtained, status,
                    remarks, created_at
                ) VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $assessment_id, $student_id, $marks_obtained, $status,
                $remarks
            ]);

            logAudit($_SESSION['user']['id'], 'submit_assessment_result', [
                'assessment_id' => $assessment_id,
                'student_id' => $student_id,
                'marks_obtained' => $marks_obtained,
                'status' => $status
            ]);

            sendJSONResponse(true, 'Assessment result submitted successfully');
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Assessments error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 