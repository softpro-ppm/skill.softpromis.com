<?php
// Define BASEPATH constant
define('BASEPATH', true);

// Start session and include required files
session_start();
require_once '../../config.php';
require_once '../../crud_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    die(json_encode(['success' => false, 'message' => 'Not authorized']));
}

// Get user role
$userRole = $_SESSION['user']['role'] ?? '';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'get_dashboard_data':
            $response = ['success' => true, 'data' => []];
            
            try {
                // Get data based on role
                if ($userRole === 'admin') {
                    // Get total counts
                    $response['data']['totalPartners'] = getTotalPartners();
                    $response['data']['totalCenters'] = getTotalCenters();
                    $response['data']['totalStudents'] = getTotalStudents();
                    $response['data']['totalCourses'] = getTotalCourses();
                    
                    // Get monthly changes
                    $response['data']['partnersChange'] = getMonthlyChange('training_partners');
                    $response['data']['centersChange'] = getMonthlyChange('training_centers');
                    $response['data']['studentsChange'] = getMonthlyChange('students');
                    $response['data']['coursesChange'] = getMonthlyChange('courses');
                    
                    // Get enrollment data
                    $response['data']['enrollment'] = getEnrollmentData();
                    
                    // Get course distribution
                    $response['data']['courseDistribution'] = getCourseDistribution();
                    
                    // Get recent activities
                    $response['data']['recentActivities'] = getRecentActivities();
                    
                } elseif ($userRole === 'training_partner') {
                    $partnerId = $_SESSION['user']['partner_id'];
                    
                    // Get partner-specific data
                    $response['data']['totalCenters'] = getPartnerCenters($partnerId);
                    $response['data']['totalStudents'] = getPartnerStudents($partnerId);
                    $response['data']['totalCourses'] = getPartnerCourses($partnerId);
                    $response['data']['totalBatches'] = getPartnerBatches($partnerId);
                    
                    // Get monthly changes
                    $response['data']['centersChange'] = getPartnerMonthlyChange($partnerId, 'centers');
                    $response['data']['studentsChange'] = getPartnerMonthlyChange($partnerId, 'students');
                    $response['data']['coursesChange'] = getPartnerMonthlyChange($partnerId, 'courses');
                    $response['data']['batchesChange'] = getPartnerMonthlyChange($partnerId, 'batches');
                    
                    // Get center performance
                    $response['data']['centerPerformance'] = getCenterPerformance($partnerId);
                    
                } elseif ($userRole === 'training_center') {
                    $centerId = $_SESSION['user']['center_id'];
                    
                    // Get center-specific data
                    $response['data']['totalStudents'] = getCenterStudents($centerId);
                    $response['data']['totalCourses'] = getCenterCourses($centerId);
                    $response['data']['totalBatches'] = getCenterBatches($centerId);
                    $response['data']['completedCourses'] = getCenterCompletedCourses($centerId);
                    
                    // Get monthly changes
                    $response['data']['studentsChange'] = getCenterMonthlyChange($centerId, 'students');
                    $response['data']['coursesChange'] = getCenterMonthlyChange($centerId, 'courses');
                    $response['data']['batchesChange'] = getCenterMonthlyChange($centerId, 'batches');
                    $response['data']['completedChange'] = getCenterMonthlyChange($centerId, 'completed');
                    
                    // Get active batches
                    $response['data']['activeBatches'] = getActiveBatches($centerId);
                }
                
            } catch (Exception $e) {
                $response = ['success' => false, 'message' => $e->getMessage()];
            }
            
            echo json_encode($response);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}

// Helper functions
function getTotalPartners() {
    global $conn;
    $query = "SELECT COUNT(*) as count FROM training_partners WHERE status = 'active'";
    $result = $conn->query($query);
    return $result->fetch_assoc()['count'];
}

function getTotalCenters() {
    global $conn;
    $query = "SELECT COUNT(*) as count FROM training_centers WHERE status = 'active'";
    $result = $conn->query($query);
    return $result->fetch_assoc()['count'];
}

function getTotalStudents() {
    global $conn;
    $query = "SELECT COUNT(*) as count FROM students WHERE status = 'active'";
    $result = $conn->query($query);
    return $result->fetch_assoc()['count'];
}

function getTotalCourses() {
    global $conn;
    $query = "SELECT COUNT(*) as count FROM courses WHERE status = 'active'";
    $result = $conn->query($query);
    return $result->fetch_assoc()['count'];
}

function getMonthlyChange($table) {
    global $conn;
    $query = "SELECT 
        (COUNT(*) - LAG(COUNT(*)) OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m'))) * 100.0 / 
        LAG(COUNT(*)) OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m')) as change
        FROM $table 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 2 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY DATE_FORMAT(created_at, '%Y-%m') DESC
        LIMIT 1";
    $result = $conn->query($query);
    return round($result->fetch_assoc()['change'] ?? 0, 1);
}

function getEnrollmentData() {
    global $conn;
    $query = "SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as count
        FROM students
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month";
    $result = $conn->query($query);
    
    $labels = [];
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $labels[] = date('M Y', strtotime($row['month'] . '-01'));
        $data[] = $row['count'];
    }
    
    return ['labels' => $labels, 'data' => $data];
}

function getCourseDistribution() {
    global $conn;
    $query = "SELECT 
        c.course_name,
        COUNT(s.student_id) as count
        FROM courses c
        LEFT JOIN student_batch_enrollment sbe ON c.course_id = sbe.course_id
        LEFT JOIN students s ON sbe.student_id = s.student_id
        WHERE c.status = 'active'
        GROUP BY c.course_id
        ORDER BY count DESC
        LIMIT 5";
    $result = $conn->query($query);
    
    $labels = [];
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['course_name'];
        $data[] = $row['count'];
    }
    
    return ['labels' => $labels, 'data' => $data];
}

function getRecentActivities() {
    global $conn;
    $query = "SELECT 
        a.activity_id,
        a.description,
        a.created_at as date,
        u.username as user,
        a.status
        FROM activities a
        LEFT JOIN users u ON a.user_id = u.user_id
        ORDER BY a.created_at DESC
        LIMIT 10";
    $result = $conn->query($query);
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = [
            'date' => date('Y-m-d H:i', strtotime($row['date'])),
            'description' => $row['description'],
            'user' => $row['user'],
            'status' => $row['status']
        ];
    }
    
    return $activities;
}

// Partner-specific functions
function getPartnerCenters($partnerId) {
    global $conn;
    $query = "SELECT COUNT(*) as count FROM training_centers WHERE partner_id = ? AND status = 'active'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $partnerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

function getPartnerStudents($partnerId) {
    global $conn;
    $query = "SELECT COUNT(DISTINCT s.student_id) as count 
        FROM students s
        JOIN student_batch_enrollment sbe ON s.student_id = sbe.student_id
        JOIN batches b ON sbe.batch_id = b.batch_id
        JOIN training_centers tc ON b.center_id = tc.center_id
        WHERE tc.partner_id = ? AND s.status = 'active'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $partnerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

function getPartnerCourses($partnerId) {
    global $conn;
    $query = "SELECT COUNT(DISTINCT c.course_id) as count 
        FROM courses c
        JOIN batches b ON c.course_id = b.course_id
        JOIN training_centers tc ON b.center_id = tc.center_id
        WHERE tc.partner_id = ? AND c.status = 'active'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $partnerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

function getPartnerBatches($partnerId) {
    global $conn;
    $query = "SELECT COUNT(DISTINCT b.batch_id) as count 
        FROM batches b
        JOIN training_centers tc ON b.center_id = tc.center_id
        WHERE tc.partner_id = ? AND b.status = 'active'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $partnerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

function getPartnerMonthlyChange($partnerId, $type) {
    global $conn;
    $table = $type === 'centers' ? 'training_centers' : 
             ($type === 'students' ? 'students' : 
             ($type === 'courses' ? 'courses' : 'batches'));
    
    $query = "SELECT 
        (COUNT(*) - LAG(COUNT(*)) OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m'))) * 100.0 / 
        LAG(COUNT(*)) OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m')) as change
        FROM $table 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 2 MONTH)
        AND partner_id = ?
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY DATE_FORMAT(created_at, '%Y-%m') DESC
        LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $partnerId);
    $stmt->execute();
    return round($stmt->get_result()->fetch_assoc()['change'] ?? 0, 1);
}

function getCenterPerformance($partnerId) {
    global $conn;
    $query = "SELECT 
        tc.center_name,
        COUNT(DISTINCT s.student_id) as students
        FROM training_centers tc
        LEFT JOIN batches b ON tc.center_id = b.center_id
        LEFT JOIN student_batch_enrollment sbe ON b.batch_id = sbe.batch_id
        LEFT JOIN students s ON sbe.student_id = s.student_id
        WHERE tc.partner_id = ? AND tc.status = 'active'
        GROUP BY tc.center_id
        ORDER BY students DESC
        LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $partnerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $labels = [];
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['center_name'];
        $data[] = $row['students'];
    }
    
    return ['labels' => $labels, 'data' => $data];
}

// Center-specific functions
function getCenterStudents($centerId) {
    global $conn;
    $query = "SELECT COUNT(DISTINCT s.student_id) as count 
        FROM students s
        JOIN student_batch_enrollment sbe ON s.student_id = sbe.student_id
        JOIN batches b ON sbe.batch_id = b.batch_id
        WHERE b.center_id = ? AND s.status = 'active'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $centerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

function getCenterCourses($centerId) {
    global $conn;
    $query = "SELECT COUNT(DISTINCT c.course_id) as count 
        FROM courses c
        JOIN batches b ON c.course_id = b.course_id
        WHERE b.center_id = ? AND c.status = 'active'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $centerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

function getCenterBatches($centerId) {
    global $conn;
    $query = "SELECT COUNT(*) as count 
        FROM batches 
        WHERE center_id = ? AND status = 'active'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $centerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

function getCenterCompletedCourses($centerId) {
    global $conn;
    $query = "SELECT COUNT(DISTINCT s.student_id) as count 
        FROM students s
        JOIN student_batch_enrollment sbe ON s.student_id = sbe.student_id
        JOIN batches b ON sbe.batch_id = b.batch_id
        WHERE b.center_id = ? AND sbe.status = 'completed'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $centerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

function getCenterMonthlyChange($centerId, $type) {
    global $conn;
    $table = $type === 'students' ? 'students' : 
             ($type === 'courses' ? 'courses' : 
             ($type === 'batches' ? 'batches' : 'student_batch_enrollment'));
    
    $query = "SELECT 
        (COUNT(*) - LAG(COUNT(*)) OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m'))) * 100.0 / 
        LAG(COUNT(*)) OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m')) as change
        FROM $table 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 2 MONTH)
        AND center_id = ?
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY DATE_FORMAT(created_at, '%Y-%m') DESC
        LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $centerId);
    $stmt->execute();
    return round($stmt->get_result()->fetch_assoc()['change'] ?? 0, 1);
}

function getActiveBatches($centerId) {
    global $conn;
    $query = "SELECT 
        b.batch_id as id,
        b.batch_code as code,
        c.course_name as course,
        DATE_FORMAT(b.start_date, '%Y-%m-%d') as startDate,
        DATE_FORMAT(b.end_date, '%Y-%m-%d') as endDate,
        COUNT(sbe.student_id) as students,
        b.status
        FROM batches b
        JOIN courses c ON b.course_id = c.course_id
        LEFT JOIN student_batch_enrollment sbe ON b.batch_id = sbe.batch_id
        WHERE b.center_id = ? AND b.status = 'active'
        GROUP BY b.batch_id
        ORDER BY b.start_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $centerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $batches = [];
    while ($row = $result->fetch_assoc()) {
        $batches[] = $row;
    }
    
    return $batches;
} 