<?php
require_once '../functions.php';
require_once '../../config.php';

// Start secure session and check for login
startSecureSession();
checkLogin();

// Set headers for JSON response
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse(false, 'Invalid request method');
}

// Get the action type
$action = isset($_POST['action']) ? sanitizeInput($_POST['action']) : '';

try {
    $pdo = getDBConnection();
    
    switch ($action) {
        case 'export_students':
            exportStudents($pdo);
            break;
        case 'export_batches':
            exportBatches($pdo);
            break;
        case 'export_courses':
            exportCourses($pdo);
            break;
        case 'export_fees':
            exportFees($pdo);
            break;
        case 'export_assessments':
            exportAssessments($pdo);
            break;
        case 'export_certificates':
            exportCertificates($pdo);
            break;
        default:
            sendJSONResponse(false, 'Invalid action specified');
    }
} catch (Exception $e) {
    logError('Export error: ' . $e->getMessage());
    sendJSONResponse(false, 'An error occurred during export: ' . $e->getMessage());
}

// Export Students
function exportStudents($pdo) {
    // Get filter parameters
    $format = isset($_POST['format']) ? sanitizeInput($_POST['format']) : 'csv';
    $status = isset($_POST['status']) ? sanitizeInput($_POST['status']) : '';
    $centerId = isset($_POST['center_id']) ? (int) $_POST['center_id'] : 0;
    $startDate = isset($_POST['start_date']) ? sanitizeInput($_POST['start_date']) : '';
    $endDate = isset($_POST['end_date']) ? sanitizeInput($_POST['end_date']) : '';
    $columns = isset($_POST['columns']) ? $_POST['columns'] : [];
    
    // Build query
    $query = "SELECT s.*, c.name as center_name, b.batch_name, co.course_name 
              FROM students s
              LEFT JOIN centers c ON s.center_id = c.id
              LEFT JOIN student_batches sb ON s.id = sb.student_id
              LEFT JOIN batches b ON sb.batch_id = b.id
              LEFT JOIN courses co ON b.course_id = co.id
              WHERE 1=1";
    $params = [];
    
    // Apply filters
    if (!empty($status)) {
        $query .= " AND s.status = ?";
        $params[] = $status;
    }
    
    if ($centerId > 0) {
        $query .= " AND s.center_id = ?";
        $params[] = $centerId;
    }
    
    if (!empty($startDate)) {
        $query .= " AND s.created_at >= ?";
        $params[] = $startDate . ' 00:00:00';
    }
    
    if (!empty($endDate)) {
        $query .= " AND s.created_at <= ?";
        $params[] = $endDate . ' 23:59:59';
    }
    
    // Execute query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $students = $stmt->fetchAll();
    
    // Add related data if needed
    foreach ($students as &$student) {
        // Get fees data
        $feeStmt = $pdo->prepare("SELECT SUM(amount) as total_paid FROM fees WHERE student_id = ?");
        $feeStmt->execute([$student['id']]);
        $feeData = $feeStmt->fetch();
        $student['total_fees_paid'] = $feeData['total_paid'] ?? 0;
        
        // Get certificates
        $certStmt = $pdo->prepare("SELECT id, certificate_number, issue_date FROM certificates WHERE student_id = ?");
        $certStmt->execute([$student['id']]);
        $student['certificates'] = $certStmt->fetchAll();
    }
    
    // Log the export
    logAudit($_SESSION['user']['id'], 'export_students', [
        'format' => $format,
        'filters' => [
            'status' => $status,
            'center_id' => $centerId,
            'date_range' => [$startDate, $endDate]
        ],
        'record_count' => count($students)
    ]);
    
    // Export the data
    $filename = 'students_export_' . date('Y-m-d_H-i-s');
    exportData($students, $filename, $format, $columns);
}

// Export Batches
function exportBatches($pdo) {
    // Get filter parameters
    $format = isset($_POST['format']) ? sanitizeInput($_POST['format']) : 'csv';
    $status = isset($_POST['status']) ? sanitizeInput($_POST['status']) : '';
    $courseId = isset($_POST['course_id']) ? (int) $_POST['course_id'] : 0;
    $centerId = isset($_POST['center_id']) ? (int) $_POST['center_id'] : 0;
    $startDate = isset($_POST['start_date']) ? sanitizeInput($_POST['start_date']) : '';
    $endDate = isset($_POST['end_date']) ? sanitizeInput($_POST['end_date']) : '';
    $columns = isset($_POST['columns']) ? $_POST['columns'] : [];
    
    // Build query
    $query = "SELECT b.*, co.course_name, c.name as center_name
              FROM batches b
              LEFT JOIN courses co ON b.course_id = co.id
              LEFT JOIN centers c ON b.center_id = c.id
              WHERE 1=1";
    $params = [];
    
    // Apply filters
    if (!empty($status)) {
        $query .= " AND b.status = ?";
        $params[] = $status;
    }
    
    if ($courseId > 0) {
        $query .= " AND b.course_id = ?";
        $params[] = $courseId;
    }
    
    if ($centerId > 0) {
        $query .= " AND b.center_id = ?";
        $params[] = $centerId;
    }
    
    if (!empty($startDate)) {
        $query .= " AND b.start_date >= ?";
        $params[] = $startDate;
    }
    
    if (!empty($endDate)) {
        $query .= " AND b.end_date <= ?";
        $params[] = $endDate;
    }
    
    // Execute query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $batches = $stmt->fetchAll();
    
    // Add related data
    foreach ($batches as &$batch) {
        // Get student count
        $studentsStmt = $pdo->prepare("SELECT COUNT(*) as count FROM student_batches WHERE batch_id = ?");
        $studentsStmt->execute([$batch['id']]);
        $studentData = $studentsStmt->fetch();
        $batch['student_count'] = $studentData['count'];
        
        // Get assessment count
        $assessStmt = $pdo->prepare("SELECT COUNT(*) as count FROM assessments WHERE batch_id = ?");
        $assessStmt->execute([$batch['id']]);
        $assessData = $assessStmt->fetch();
        $batch['assessment_count'] = $assessData['count'];
        
        // Get fee collection
        $feeStmt = $pdo->prepare("
            SELECT SUM(f.amount) as total_fees
            FROM fees f
            JOIN student_batches sb ON f.student_id = sb.student_id
            WHERE sb.batch_id = ?
        ");
        $feeStmt->execute([$batch['id']]);
        $feeData = $feeStmt->fetch();
        $batch['total_fees_collected'] = $feeData['total_fees'] ?? 0;
    }
    
    // Log the export
    logAudit($_SESSION['user']['id'], 'export_batches', [
        'format' => $format,
        'filters' => [
            'status' => $status,
            'course_id' => $courseId,
            'center_id' => $centerId,
            'date_range' => [$startDate, $endDate]
        ],
        'record_count' => count($batches)
    ]);
    
    // Export the data
    $filename = 'batches_export_' . date('Y-m-d_H-i-s');
    exportData($batches, $filename, $format, $columns);
}

// Export Courses
function exportCourses($pdo) {
    // Get filter parameters
    $format = isset($_POST['format']) ? sanitizeInput($_POST['format']) : 'csv';
    $status = isset($_POST['status']) ? sanitizeInput($_POST['status']) : '';
    $sectorId = isset($_POST['sector_id']) ? (int) $_POST['sector_id'] : 0;
    $columns = isset($_POST['columns']) ? $_POST['columns'] : [];
    
    // Build query
    $query = "SELECT c.*, s.sector_name 
              FROM courses c
              LEFT JOIN sectors s ON c.sector_id = s.id
              WHERE 1=1";
    $params = [];
    
    // Apply filters
    if (!empty($status)) {
        $query .= " AND c.status = ?";
        $params[] = $status;
    }
    
    if ($sectorId > 0) {
        $query .= " AND c.sector_id = ?";
        $params[] = $sectorId;
    }
    
    // Execute query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $courses = $stmt->fetchAll();
    
    // Add related data
    foreach ($courses as &$course) {
        // Get batch count
        $batchStmt = $pdo->prepare("SELECT COUNT(*) as count FROM batches WHERE course_id = ?");
        $batchStmt->execute([$course['id']]);
        $batchData = $batchStmt->fetch();
        $course['batch_count'] = $batchData['count'];
        
        // Get student count
        $studentStmt = $pdo->prepare("
            SELECT COUNT(DISTINCT sb.student_id) as count
            FROM student_batches sb
            JOIN batches b ON sb.batch_id = b.id
            WHERE b.course_id = ?
        ");
        $studentStmt->execute([$course['id']]);
        $studentData = $studentStmt->fetch();
        $course['student_count'] = $studentData['count'];
    }
    
    // Log the export
    logAudit($_SESSION['user']['id'], 'export_courses', [
        'format' => $format,
        'filters' => [
            'status' => $status,
            'sector_id' => $sectorId
        ],
        'record_count' => count($courses)
    ]);
    
    // Export the data
    $filename = 'courses_export_' . date('Y-m-d_H-i-s');
    exportData($courses, $filename, $format, $columns);
}

// Export Fees
function exportFees($pdo) {
    // Get filter parameters
    $format = isset($_POST['format']) ? sanitizeInput($_POST['format']) : 'csv';
    $studentId = isset($_POST['student_id']) ? (int) $_POST['student_id'] : 0;
    $batchId = isset($_POST['batch_id']) ? (int) $_POST['batch_id'] : 0;
    $paymentMethod = isset($_POST['payment_method']) ? sanitizeInput($_POST['payment_method']) : '';
    $startDate = isset($_POST['start_date']) ? sanitizeInput($_POST['start_date']) : '';
    $endDate = isset($_POST['end_date']) ? sanitizeInput($_POST['end_date']) : '';
    $columns = isset($_POST['columns']) ? $_POST['columns'] : [];
    
    // Build query
    $query = "SELECT f.*, s.first_name, s.last_name, b.batch_name, c.course_name
              FROM fees f
              LEFT JOIN students s ON f.student_id = s.id
              LEFT JOIN student_batches sb ON f.student_id = sb.student_id
              LEFT JOIN batches b ON sb.batch_id = b.id
              LEFT JOIN courses c ON b.course_id = c.id
              WHERE 1=1";
    $params = [];
    
    // Apply filters
    if ($studentId > 0) {
        $query .= " AND f.student_id = ?";
        $params[] = $studentId;
    }
    
    if ($batchId > 0) {
        $query .= " AND sb.batch_id = ?";
        $params[] = $batchId;
    }
    
    if (!empty($paymentMethod)) {
        $query .= " AND f.payment_method = ?";
        $params[] = $paymentMethod;
    }
    
    if (!empty($startDate)) {
        $query .= " AND f.payment_date >= ?";
        $params[] = $startDate;
    }
    
    if (!empty($endDate)) {
        $query .= " AND f.payment_date <= ?";
        $params[] = $endDate;
    }
    
    // Execute query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $fees = $stmt->fetchAll();
    
    // Log the export
    logAudit($_SESSION['user']['id'], 'export_fees', [
        'format' => $format,
        'filters' => [
            'student_id' => $studentId,
            'batch_id' => $batchId,
            'payment_method' => $paymentMethod,
            'date_range' => [$startDate, $endDate]
        ],
        'record_count' => count($fees)
    ]);
    
    // Export the data
    $filename = 'fees_export_' . date('Y-m-d_H-i-s');
    exportData($fees, $filename, $format, $columns);
}

// Export Assessments
function exportAssessments($pdo) {
    // Get filter parameters
    $format = isset($_POST['format']) ? sanitizeInput($_POST['format']) : 'csv';
    $status = isset($_POST['status']) ? sanitizeInput($_POST['status']) : '';
    $batchId = isset($_POST['batch_id']) ? (int) $_POST['batch_id'] : 0;
    $startDate = isset($_POST['start_date']) ? sanitizeInput($_POST['start_date']) : '';
    $endDate = isset($_POST['end_date']) ? sanitizeInput($_POST['end_date']) : '';
    $columns = isset($_POST['columns']) ? $_POST['columns'] : [];
    
    // Build query
    $query = "SELECT a.*, b.batch_name, c.course_name
              FROM assessments a
              LEFT JOIN batches b ON a.batch_id = b.id
              LEFT JOIN courses c ON b.course_id = c.id
              WHERE 1=1";
    $params = [];
    
    // Apply filters
    if (!empty($status)) {
        $query .= " AND a.status = ?";
        $params[] = $status;
    }
    
    if ($batchId > 0) {
        $query .= " AND a.batch_id = ?";
        $params[] = $batchId;
    }
    
    if (!empty($startDate)) {
        $query .= " AND a.assessment_date >= ?";
        $params[] = $startDate;
    }
    
    if (!empty($endDate)) {
        $query .= " AND a.assessment_date <= ?";
        $params[] = $endDate;
    }
    
    // Execute query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $assessments = $stmt->fetchAll();
    
    // Add related data
    foreach ($assessments as &$assessment) {
        // Get student results
        $resultStmt = $pdo->prepare("
            SELECT ar.*, s.first_name, s.last_name
            FROM assessment_results ar
            JOIN students s ON ar.student_id = s.id
            WHERE ar.assessment_id = ?
        ");
        $resultStmt->execute([$assessment['id']]);
        $assessment['results'] = $resultStmt->fetchAll();
        
        // Calculate statistics
        $totalStudents = count($assessment['results']);
        $passedStudents = 0;
        $totalMarks = 0;
        
        foreach ($assessment['results'] as $result) {
            $totalMarks += $result['marks_obtained'];
            if ($result['passed'] == 1) {
                $passedStudents++;
            }
        }
        
        $assessment['total_students'] = $totalStudents;
        $assessment['passed_students'] = $passedStudents;
        $assessment['pass_percentage'] = $totalStudents > 0 ? ($passedStudents / $totalStudents) * 100 : 0;
        $assessment['average_marks'] = $totalStudents > 0 ? $totalMarks / $totalStudents : 0;
    }
    
    // Log the export
    logAudit($_SESSION['user']['id'], 'export_assessments', [
        'format' => $format,
        'filters' => [
            'status' => $status,
            'batch_id' => $batchId,
            'date_range' => [$startDate, $endDate]
        ],
        'record_count' => count($assessments)
    ]);
    
    // Export the data
    $filename = 'assessments_export_' . date('Y-m-d_H-i-s');
    exportData($assessments, $filename, $format, $columns);
}

// Export Certificates
function exportCertificates($pdo) {
    // Get filter parameters
    $format = isset($_POST['format']) ? sanitizeInput($_POST['format']) : 'csv';
    $studentId = isset($_POST['student_id']) ? (int) $_POST['student_id'] : 0;
    $batchId = isset($_POST['batch_id']) ? (int) $_POST['batch_id'] : 0;
    $startDate = isset($_POST['start_date']) ? sanitizeInput($_POST['start_date']) : '';
    $endDate = isset($_POST['end_date']) ? sanitizeInput($_POST['end_date']) : '';
    $columns = isset($_POST['columns']) ? $_POST['columns'] : [];
    
    // Build query
    $query = "SELECT c.*, s.first_name, s.last_name, b.batch_name, co.course_name
              FROM certificates c
              LEFT JOIN students s ON c.student_id = s.id
              LEFT JOIN student_batches sb ON c.student_id = sb.student_id
              LEFT JOIN batches b ON sb.batch_id = b.id
              LEFT JOIN courses co ON b.course_id = co.id
              WHERE 1=1";
    $params = [];
    
    // Apply filters
    if ($studentId > 0) {
        $query .= " AND c.student_id = ?";
        $params[] = $studentId;
    }
    
    if ($batchId > 0) {
        $query .= " AND sb.batch_id = ?";
        $params[] = $batchId;
    }
    
    if (!empty($startDate)) {
        $query .= " AND c.issue_date >= ?";
        $params[] = $startDate;
    }
    
    if (!empty($endDate)) {
        $query .= " AND c.issue_date <= ?";
        $params[] = $endDate;
    }
    
    // Execute query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $certificates = $stmt->fetchAll();
    
    // Log the export
    logAudit($_SESSION['user']['id'], 'export_certificates', [
        'format' => $format,
        'filters' => [
            'student_id' => $studentId,
            'batch_id' => $batchId,
            'date_range' => [$startDate, $endDate]
        ],
        'record_count' => count($certificates)
    ]);
    
    // Export the data
    $filename = 'certificates_export_' . date('Y-m-d_H-i-s');
    exportData($certificates, $filename, $format, $columns);
}

// Helper functions for exporting data
function exportData($data, $filename, $format, $columns = []) {
    // Filter columns if needed
    if (!empty($columns)) {
        $filteredData = [];
        foreach ($data as $row) {
            $filteredRow = [];
            foreach ($columns as $column) {
                $filteredRow[$column] = $row[$column] ?? '';
            }
            $filteredData[] = $filteredRow;
        }
        $data = $filteredData;
    }
    
    switch ($format) {
        case 'csv':
            exportCSV($data, $filename);
            break;
        case 'excel':
            exportExcel($data, $filename);
            break;
        case 'pdf':
            exportPDF($data, $filename);
            break;
        default:
            sendJSONResponse(false, 'Unsupported export format');
    }
}

function exportCSV($data, $filename) {
    if (empty($data)) {
        sendJSONResponse(false, 'No data to export');
    }
    
    // Create CSV content
    $csvContent = '';
    
    // Add headers
    $headers = array_keys($data[0]);
    $csvContent .= implode(',', $headers) . "\n";
    
    // Add rows
    foreach ($data as $row) {
        $csvRow = [];
        foreach ($headers as $header) {
            $value = $row[$header] ?? '';
            // Handle arrays and objects by converting to JSON
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }
            // Escape any commas and quotes
            $value = str_replace('"', '""', $value);
            $csvRow[] = '"' . $value . '"';
        }
        $csvContent .= implode(',', $csvRow) . "\n";
    }
    
    // Encode for browser download
    $base64 = base64_encode($csvContent);
    
    sendJSONResponse(true, 'Data exported successfully', [
        'filename' => $filename . '.csv',
        'content' => $base64,
        'mime' => 'text/csv'
    ]);
}

function exportExcel($data, $filename) {
    // This is a placeholder - would require PhpSpreadsheet or similar library
    sendJSONResponse(false, 'Excel export requires PhpSpreadsheet library');
}

function exportPDF($data, $filename) {
    // This is a placeholder - would require TCPDF or FPDF library
    sendJSONResponse(false, 'PDF export requires TCPDF or FPDF library');
} 