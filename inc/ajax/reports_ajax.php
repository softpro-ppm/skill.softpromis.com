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
        case 'get_dashboard_stats':
            // Get total students
            $stmt = $pdo->query("SELECT COUNT(*) FROM students WHERE status = 'active'");
            $totalStudents = $stmt->fetchColumn();

            // Get total batches
            $stmt = $pdo->query("SELECT COUNT(*) FROM batches WHERE status = 'active'");
            $totalBatches = $stmt->fetchColumn();

            // Get total courses
            $stmt = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'active'");
            $totalCourses = $stmt->fetchColumn();

            // Get total centers
            $stmt = $pdo->query("SELECT COUNT(*) FROM training_centers WHERE status = 'active'");
            $totalCenters = $stmt->fetchColumn();

            // Get total fees collected
            $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM fees WHERE status = 'completed'");
            $totalFees = $stmt->fetchColumn();

            // Get recent enrollments
            $stmt = $pdo->query("
                SELECT COUNT(*) 
                FROM batch_students 
                WHERE enrolled_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $recentEnrollments = $stmt->fetchColumn();

            // Get upcoming assessments
            $stmt = $pdo->query("
                SELECT COUNT(*) 
                FROM assessments 
                WHERE assessment_date >= NOW() 
                AND assessment_date <= DATE_ADD(NOW(), INTERVAL 7 DAY)
            ");
            $upcomingAssessments = $stmt->fetchColumn();

            // Get recent certificates
            $stmt = $pdo->query("
                SELECT COUNT(*) 
                FROM certificates 
                WHERE issue_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $recentCertificates = $stmt->fetchColumn();

            sendJSONResponse(true, 'Dashboard statistics retrieved successfully', [
                'total_students' => $totalStudents,
                'total_batches' => $totalBatches,
                'total_courses' => $totalCourses,
                'total_centers' => $totalCenters,
                'total_fees' => $totalFees,
                'recent_enrollments' => $recentEnrollments,
                'upcoming_assessments' => $upcomingAssessments,
                'recent_certificates' => $recentCertificates
            ]);
            break;

        case 'get_student_report':
            $startDate = sanitizeInput($_POST['start_date'] ?? '');
            $endDate = sanitizeInput($_POST['end_date'] ?? '');
            $centerId = (int)($_POST['center_id'] ?? 0);
            $courseId = (int)($_POST['course_id'] ?? 0);

            $where = [];
            $params = [];

            if (!empty($startDate)) {
                $where[] = "s.created_at >= ?";
                $params[] = $startDate;
            }

            if (!empty($endDate)) {
                $where[] = "s.created_at <= ?";
                $params[] = $endDate;
            }

            if ($centerId > 0) {
                $where[] = "s.center_id = ?";
                $params[] = $centerId;
            }

            if ($courseId > 0) {
                $where[] = "b.course_id = ?";
                $params[] = $courseId;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            $stmt = $pdo->prepare("
                SELECT 
                    s.*,
                    tc.name as center_name,
                    COUNT(DISTINCT bs.batch_id) as total_batches,
                    COUNT(DISTINCT c.id) as total_certificates,
                    COUNT(DISTINCT ar.id) as total_assessments,
                    COALESCE(SUM(f.amount), 0) as total_fees_paid
                FROM students s
                LEFT JOIN training_centers tc ON s.center_id = tc.id
                LEFT JOIN batch_students bs ON s.id = bs.student_id
                LEFT JOIN batches b ON bs.batch_id = b.id
                LEFT JOIN certificates c ON s.id = c.student_id
                LEFT JOIN assessment_results ar ON s.id = ar.student_id
                LEFT JOIN fees f ON s.id = f.student_id
                $whereClause
                GROUP BY s.id
                ORDER BY s.created_at DESC
            ");
            $stmt->execute($params);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Student report generated successfully', $students);
            break;

        case 'get_batch_report':
            $startDate = sanitizeInput($_POST['start_date'] ?? '');
            $endDate = sanitizeInput($_POST['end_date'] ?? '');
            $centerId = (int)($_POST['center_id'] ?? 0);
            $courseId = (int)($_POST['course_id'] ?? 0);

            $where = [];
            $params = [];

            if (!empty($startDate)) {
                $where[] = "b.start_date >= ?";
                $params[] = $startDate;
            }

            if (!empty($endDate)) {
                $where[] = "b.end_date <= ?";
                $params[] = $endDate;
            }

            if ($centerId > 0) {
                $where[] = "b.center_id = ?";
                $params[] = $centerId;
            }

            if ($courseId > 0) {
                $where[] = "b.course_id = ?";
                $params[] = $courseId;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            $stmt = $pdo->prepare("
                SELECT 
                    b.*,
                    c.name as course_name,
                    tc.name as center_name,
                    COUNT(DISTINCT bs.student_id) as total_students,
                    COUNT(DISTINCT a.id) as total_assessments,
                    COUNT(DISTINCT cert.id) as total_certificates,
                    COALESCE(SUM(f.amount), 0) as total_fees_collected
                FROM batches b
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                LEFT JOIN batch_students bs ON b.id = bs.batch_id
                LEFT JOIN assessments a ON b.id = a.batch_id
                LEFT JOIN certificates cert ON b.id = cert.batch_id
                LEFT JOIN fees f ON b.id = f.batch_id
                $whereClause
                GROUP BY b.id
                ORDER BY b.start_date DESC
            ");
            $stmt->execute($params);
            $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Batch report generated successfully', $batches);
            break;

        case 'get_fee_report':
            $startDate = sanitizeInput($_POST['start_date'] ?? '');
            $endDate = sanitizeInput($_POST['end_date'] ?? '');
            $centerId = (int)($_POST['center_id'] ?? 0);
            $courseId = (int)($_POST['course_id'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? '');

            $where = [];
            $params = [];

            if (!empty($startDate)) {
                $where[] = "f.payment_date >= ?";
                $params[] = $startDate;
            }

            if (!empty($endDate)) {
                $where[] = "f.payment_date <= ?";
                $params[] = $endDate;
            }

            if ($centerId > 0) {
                $where[] = "b.center_id = ?";
                $params[] = $centerId;
            }

            if ($courseId > 0) {
                $where[] = "b.course_id = ?";
                $params[] = $courseId;
            }

            if (!empty($status)) {
                $where[] = "f.status = ?";
                $params[] = $status;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            $stmt = $pdo->prepare("
                SELECT 
                    f.*,
                    s.name as student_name,
                    b.name as batch_name,
                    c.name as course_name,
                    tc.name as center_name
                FROM fees f
                JOIN students s ON f.student_id = s.id
                JOIN batches b ON f.batch_id = b.id
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                $whereClause
                ORDER BY f.payment_date DESC
            ");
            $stmt->execute($params);
            $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate summary
            $summary = [
                'total_amount' => 0,
                'total_pending' => 0,
                'total_completed' => 0,
                'total_failed' => 0
            ];

            foreach ($fees as $fee) {
                $summary['total_amount'] += $fee['amount'];
                if ($fee['status'] === 'pending') {
                    $summary['total_pending'] += $fee['amount'];
                } elseif ($fee['status'] === 'completed') {
                    $summary['total_completed'] += $fee['amount'];
                } elseif ($fee['status'] === 'failed') {
                    $summary['total_failed'] += $fee['amount'];
                }
            }

            sendJSONResponse(true, 'Fee report generated successfully', [
                'fees' => $fees,
                'summary' => $summary
            ]);
            break;

        case 'get_assessment_report':
            $startDate = sanitizeInput($_POST['start_date'] ?? '');
            $endDate = sanitizeInput($_POST['end_date'] ?? '');
            $centerId = (int)($_POST['center_id'] ?? 0);
            $courseId = (int)($_POST['course_id'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? '');

            $where = [];
            $params = [];

            if (!empty($startDate)) {
                $where[] = "a.assessment_date >= ?";
                $params[] = $startDate;
            }

            if (!empty($endDate)) {
                $where[] = "a.assessment_date <= ?";
                $params[] = $endDate;
            }

            if ($centerId > 0) {
                $where[] = "b.center_id = ?";
                $params[] = $centerId;
            }

            if ($courseId > 0) {
                $where[] = "b.course_id = ?";
                $params[] = $courseId;
            }

            if (!empty($status)) {
                $where[] = "a.status = ?";
                $params[] = $status;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            $stmt = $pdo->prepare("
                SELECT 
                    a.*,
                    b.name as batch_name,
                    c.name as course_name,
                    tc.name as center_name,
                    COUNT(DISTINCT ar.student_id) as total_students,
                    AVG(ar.marks_obtained) as average_marks,
                    MIN(ar.marks_obtained) as minimum_marks,
                    MAX(ar.marks_obtained) as maximum_marks,
                    COUNT(CASE WHEN ar.status = 'passed' THEN 1 END) as passed_count,
                    COUNT(CASE WHEN ar.status = 'failed' THEN 1 END) as failed_count
                FROM assessments a
                JOIN batches b ON a.batch_id = b.id
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                LEFT JOIN assessment_results ar ON a.id = ar.assessment_id
                $whereClause
                GROUP BY a.id
                ORDER BY a.assessment_date DESC
            ");
            $stmt->execute($params);
            $assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Assessment report generated successfully', $assessments);
            break;

        case 'get_certificate_report':
            $startDate = sanitizeInput($_POST['start_date'] ?? '');
            $endDate = sanitizeInput($_POST['end_date'] ?? '');
            $centerId = (int)($_POST['center_id'] ?? 0);
            $courseId = (int)($_POST['course_id'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? '');

            $where = [];
            $params = [];

            if (!empty($startDate)) {
                $where[] = "c.issue_date >= ?";
                $params[] = $startDate;
            }

            if (!empty($endDate)) {
                $where[] = "c.issue_date <= ?";
                $params[] = $endDate;
            }

            if ($centerId > 0) {
                $where[] = "b.center_id = ?";
                $params[] = $centerId;
            }

            if ($courseId > 0) {
                $where[] = "b.course_id = ?";
                $params[] = $courseId;
            }

            if (!empty($status)) {
                $where[] = "c.status = ?";
                $params[] = $status;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            $stmt = $pdo->prepare("
                SELECT 
                    c.*,
                    s.name as student_name,
                    b.name as batch_name,
                    co.name as course_name,
                    tc.name as center_name
                FROM certificates c
                JOIN students s ON c.student_id = s.id
                JOIN batches b ON c.batch_id = b.id
                LEFT JOIN courses co ON b.course_id = co.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                $whereClause
                ORDER BY c.issue_date DESC
            ");
            $stmt->execute($params);
            $certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate summary
            $summary = [
                'total_certificates' => count($certificates),
                'active_certificates' => 0,
                'expired_certificates' => 0,
                'revoked_certificates' => 0
            ];

            foreach ($certificates as $certificate) {
                if ($certificate['status'] === 'active') {
                    $summary['active_certificates']++;
                } elseif ($certificate['status'] === 'expired') {
                    $summary['expired_certificates']++;
                } elseif ($certificate['status'] === 'revoked') {
                    $summary['revoked_certificates']++;
                }
            }

            sendJSONResponse(true, 'Certificate report generated successfully', [
                'certificates' => $certificates,
                'summary' => $summary
            ]);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Reports error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 