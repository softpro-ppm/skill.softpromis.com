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
        case 'get_overview_stats':
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

            sendJSONResponse(true, 'Overview statistics retrieved successfully', [
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

        case 'get_enrollment_trends':
            $period = sanitizeInput($_POST['period'] ?? 'monthly');
            $limit = (int)($_POST['limit'] ?? 12);

            $dateFormat = $period === 'monthly' ? '%Y-%m' : '%Y-%W';
            $groupBy = $period === 'monthly' ? 'MONTH' : 'WEEK';

            $stmt = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(enrolled_at, ?) as period,
                    COUNT(*) as count
                FROM batch_students
                WHERE enrolled_at >= DATE_SUB(NOW(), INTERVAL ? $groupBy)
                GROUP BY period
                ORDER BY period ASC
            ");
            $stmt->execute([$dateFormat, $limit]);
            $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Enrollment trends retrieved successfully', $enrollments);
            break;

        case 'get_fee_collection_trends':
            $period = sanitizeInput($_POST['period'] ?? 'monthly');
            $limit = (int)($_POST['limit'] ?? 12);

            $dateFormat = $period === 'monthly' ? '%Y-%m' : '%Y-%W';
            $groupBy = $period === 'monthly' ? 'MONTH' : 'WEEK';

            $stmt = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(payment_date, ?) as period,
                    SUM(amount) as total_amount
                FROM fees
                WHERE payment_date >= DATE_SUB(NOW(), INTERVAL ? $groupBy)
                AND status = 'completed'
                GROUP BY period
                ORDER BY period ASC
            ");
            $stmt->execute([$dateFormat, $limit]);
            $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Fee collection trends retrieved successfully', $fees);
            break;

        case 'get_course_distribution':
            $stmt = $pdo->query("
                SELECT 
                    c.name as course_name,
                    COUNT(DISTINCT b.id) as batch_count,
                    COUNT(DISTINCT bs.student_id) as student_count
                FROM courses c
                LEFT JOIN batches b ON c.id = b.course_id
                LEFT JOIN batch_students bs ON b.id = bs.batch_id
                WHERE c.status = 'active'
                GROUP BY c.id
                ORDER BY student_count DESC
                LIMIT 10
            ");
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Course distribution retrieved successfully', $courses);
            break;

        case 'get_center_performance':
            $stmt = $pdo->query("
                SELECT 
                    tc.name as center_name,
                    COUNT(DISTINCT b.id) as batch_count,
                    COUNT(DISTINCT bs.student_id) as student_count,
                    COUNT(DISTINCT a.id) as assessment_count,
                    COUNT(DISTINCT c.id) as certificate_count,
                    COALESCE(SUM(f.amount), 0) as total_fees
                FROM training_centers tc
                LEFT JOIN batches b ON tc.id = b.center_id
                LEFT JOIN batch_students bs ON b.id = bs.batch_id
                LEFT JOIN assessments a ON b.id = a.batch_id
                LEFT JOIN certificates c ON b.id = c.batch_id
                LEFT JOIN fees f ON b.id = f.batch_id
                WHERE tc.status = 'active'
                GROUP BY tc.id
                ORDER BY student_count DESC
            ");
            $centers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Center performance retrieved successfully', $centers);
            break;

        case 'get_recent_activities':
            $limit = (int)($_POST['limit'] ?? 10);

            $stmt = $pdo->prepare("
                SELECT 
                    a.*,
                    u.name as user_name,
                    u.role as user_role
                FROM audit_logs a
                JOIN users u ON a.user_id = u.id
                ORDER BY a.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Recent activities retrieved successfully', $activities);
            break;

        case 'get_upcoming_events':
            $limit = (int)($_POST['limit'] ?? 10);

            $stmt = $pdo->prepare("
                SELECT 
                    'assessment' as event_type,
                    a.title as event_name,
                    a.assessment_date as event_date,
                    b.name as batch_name,
                    c.name as course_name,
                    tc.name as center_name
                FROM assessments a
                JOIN batches b ON a.batch_id = b.id
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                WHERE a.assessment_date >= NOW()
                AND a.status = 'scheduled'
                UNION ALL
                SELECT 
                    'batch' as event_type,
                    b.name as event_name,
                    b.start_date as event_date,
                    b.name as batch_name,
                    c.name as course_name,
                    tc.name as center_name
                FROM batches b
                LEFT JOIN courses c ON b.course_id = c.id
                LEFT JOIN training_centers tc ON b.center_id = tc.id
                WHERE b.start_date >= NOW()
                AND b.status = 'active'
                ORDER BY event_date ASC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Upcoming events retrieved successfully', $events);
            break;

        case 'get_student_performance':
            $limit = (int)($_POST['limit'] ?? 10);

            $stmt = $pdo->prepare("
                SELECT 
                    s.name as student_name,
                    COUNT(DISTINCT ar.id) as total_assessments,
                    AVG(ar.marks_obtained) as average_marks,
                    COUNT(DISTINCT c.id) as total_certificates,
                    COUNT(DISTINCT bs.batch_id) as total_batches
                FROM students s
                LEFT JOIN assessment_results ar ON s.id = ar.student_id
                LEFT JOIN certificates c ON s.id = c.student_id
                LEFT JOIN batch_students bs ON s.id = bs.student_id
                WHERE s.status = 'active'
                GROUP BY s.id
                ORDER BY average_marks DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Student performance retrieved successfully', $students);
            break;

        case 'get_certificate_status':
            $stmt = $pdo->query("
                SELECT 
                    status,
                    COUNT(*) as count
                FROM certificates
                GROUP BY status
            ");
            $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Certificate status retrieved successfully', $statuses);
            break;

        case 'get_fee_status':
            $stmt = $pdo->query("
                SELECT 
                    status,
                    COUNT(*) as count,
                    COALESCE(SUM(amount), 0) as total_amount
                FROM fees
                GROUP BY status
            ");
            $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Fee status retrieved successfully', $statuses);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Dashboard error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 