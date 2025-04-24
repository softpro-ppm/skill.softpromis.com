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
        case 'export_students':
            $format = sanitizeInput($_POST['format'] ?? 'csv');
            $filters = $_POST['filters'] ?? [];

            $where = [];
            $params = [];

            if (!empty($filters['status'])) {
                $where[] = "s.status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['center_id'])) {
                $where[] = "s.center_id = ?";
                $params[] = $filters['center_id'];
            }

            if (!empty($filters['start_date'])) {
                $where[] = "s.created_at >= ?";
                $params[] = $filters['start_date'];
            }

            if (!empty($filters['end_date'])) {
                $where[] = "s.created_at <= ?";
                $params[] = $filters['end_date'];
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
                LEFT JOIN certificates c ON s.id = c.student_id
                LEFT JOIN assessment_results ar ON s.id = ar.student_id
                LEFT JOIN fees f ON s.id = f.student_id
                $whereClause
                GROUP BY s.id
                ORDER BY s.created_at DESC
            ");
            $stmt->execute($params);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $filename = 'students_' . date('Y-m-d_H-i-s');
            $data = exportData($students, $format, $filename, [
                'ID' => 'id',
                'Name' => 'name',
                'Email' => 'email',
                'Phone' => 'phone',
                'Center' => 'center_name',
                'Total Batches' => 'total_batches',
                'Total Certificates' => 'total_certificates',
                'Total Assessments' => 'total_assessments',
                'Total Fees Paid' => 'total_fees_paid',
                'Status' => 'status',
                'Created At' => 'created_at'
            ]);

            logAudit($_SESSION['user']['id'], 'export_students', [
                'format' => $format,
                'filters' => $filters
            ]);

            sendJSONResponse(true, 'Students exported successfully', $data);
            break;

        case 'export_batches':
            $format = sanitizeInput($_POST['format'] ?? 'csv');
            $filters = $_POST['filters'] ?? [];

            $where = [];
            $params = [];

            if (!empty($filters['status'])) {
                $where[] = "b.status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['center_id'])) {
                $where[] = "b.center_id = ?";
                $params[] = $filters['center_id'];
            }

            if (!empty($filters['course_id'])) {
                $where[] = "b.course_id = ?";
                $params[] = $filters['course_id'];
            }

            if (!empty($filters['start_date'])) {
                $where[] = "b.start_date >= ?";
                $params[] = $filters['start_date'];
            }

            if (!empty($filters['end_date'])) {
                $where[] = "b.end_date <= ?";
                $params[] = $filters['end_date'];
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

            $filename = 'batches_' . date('Y-m-d_H-i-s');
            $data = exportData($batches, $format, $filename, [
                'ID' => 'id',
                'Name' => 'name',
                'Course' => 'course_name',
                'Center' => 'center_name',
                'Start Date' => 'start_date',
                'End Date' => 'end_date',
                'Capacity' => 'capacity',
                'Total Students' => 'total_students',
                'Total Assessments' => 'total_assessments',
                'Total Certificates' => 'total_certificates',
                'Total Fees Collected' => 'total_fees_collected',
                'Status' => 'status',
                'Created At' => 'created_at'
            ]);

            logAudit($_SESSION['user']['id'], 'export_batches', [
                'format' => $format,
                'filters' => $filters
            ]);

            sendJSONResponse(true, 'Batches exported successfully', $data);
            break;

        case 'export_courses':
            $format = sanitizeInput($_POST['format'] ?? 'csv');
            $filters = $_POST['filters'] ?? [];

            $where = [];
            $params = [];

            if (!empty($filters['status'])) {
                $where[] = "c.status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['sector_id'])) {
                $where[] = "c.sector_id = ?";
                $params[] = $filters['sector_id'];
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            $stmt = $pdo->prepare("
                SELECT 
                    c.*,
                    s.name as sector_name,
                    COUNT(DISTINCT b.id) as total_batches,
                    COUNT(DISTINCT bs.student_id) as total_students,
                    COUNT(DISTINCT a.id) as total_assessments,
                    COUNT(DISTINCT cert.id) as total_certificates
                FROM courses c
                LEFT JOIN sectors s ON c.sector_id = s.id
                LEFT JOIN batches b ON c.id = b.course_id
                LEFT JOIN batch_students bs ON b.id = bs.batch_id
                LEFT JOIN assessments a ON b.id = a.batch_id
                LEFT JOIN certificates cert ON b.id = cert.batch_id
                $whereClause
                GROUP BY c.id
                ORDER BY c.name
            ");
            $stmt->execute($params);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $filename = 'courses_' . date('Y-m-d_H-i-s');
            $data = exportData($courses, $format, $filename, [
                'ID' => 'id',
                'Name' => 'name',
                'Description' => 'description',
                'Sector' => 'sector_name',
                'Duration' => 'duration',
                'Fee' => 'fee',
                'Total Batches' => 'total_batches',
                'Total Students' => 'total_students',
                'Total Assessments' => 'total_assessments',
                'Total Certificates' => 'total_certificates',
                'Status' => 'status',
                'Created At' => 'created_at'
            ]);

            logAudit($_SESSION['user']['id'], 'export_courses', [
                'format' => $format,
                'filters' => $filters
            ]);

            sendJSONResponse(true, 'Courses exported successfully', $data);
            break;

        case 'export_fees':
            $format = sanitizeInput($_POST['format'] ?? 'csv');
            $filters = $_POST['filters'] ?? [];

            $where = [];
            $params = [];

            if (!empty($filters['status'])) {
                $where[] = "f.status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['student_id'])) {
                $where[] = "f.student_id = ?";
                $params[] = $filters['student_id'];
            }

            if (!empty($filters['batch_id'])) {
                $where[] = "f.batch_id = ?";
                $params[] = $filters['batch_id'];
            }

            if (!empty($filters['start_date'])) {
                $where[] = "f.payment_date >= ?";
                $params[] = $filters['start_date'];
            }

            if (!empty($filters['end_date'])) {
                $where[] = "f.payment_date <= ?";
                $params[] = $filters['end_date'];
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

            $filename = 'fees_' . date('Y-m-d_H-i-s');
            $data = exportData($fees, $format, $filename, [
                'ID' => 'id',
                'Student' => 'student_name',
                'Batch' => 'batch_name',
                'Course' => 'course_name',
                'Center' => 'center_name',
                'Amount' => 'amount',
                'Payment Date' => 'payment_date',
                'Payment Method' => 'payment_method',
                'Transaction ID' => 'transaction_id',
                'Status' => 'status',
                'Created At' => 'created_at'
            ]);

            logAudit($_SESSION['user']['id'], 'export_fees', [
                'format' => $format,
                'filters' => $filters
            ]);

            sendJSONResponse(true, 'Fees exported successfully', $data);
            break;

        case 'export_assessments':
            $format = sanitizeInput($_POST['format'] ?? 'csv');
            $filters = $_POST['filters'] ?? [];

            $where = [];
            $params = [];

            if (!empty($filters['status'])) {
                $where[] = "a.status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['batch_id'])) {
                $where[] = "a.batch_id = ?";
                $params[] = $filters['batch_id'];
            }

            if (!empty($filters['start_date'])) {
                $where[] = "a.assessment_date >= ?";
                $params[] = $filters['start_date'];
            }

            if (!empty($filters['end_date'])) {
                $where[] = "a.assessment_date <= ?";
                $params[] = $filters['end_date'];
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

            $filename = 'assessments_' . date('Y-m-d_H-i-s');
            $data = exportData($assessments, $format, $filename, [
                'ID' => 'id',
                'Title' => 'title',
                'Batch' => 'batch_name',
                'Course' => 'course_name',
                'Center' => 'center_name',
                'Assessment Date' => 'assessment_date',
                'Duration' => 'duration',
                'Total Marks' => 'total_marks',
                'Passing Marks' => 'passing_marks',
                'Total Students' => 'total_students',
                'Average Marks' => 'average_marks',
                'Passed Count' => 'passed_count',
                'Failed Count' => 'failed_count',
                'Status' => 'status',
                'Created At' => 'created_at'
            ]);

            logAudit($_SESSION['user']['id'], 'export_assessments', [
                'format' => $format,
                'filters' => $filters
            ]);

            sendJSONResponse(true, 'Assessments exported successfully', $data);
            break;

        case 'export_certificates':
            $format = sanitizeInput($_POST['format'] ?? 'csv');
            $filters = $_POST['filters'] ?? [];

            $where = [];
            $params = [];

            if (!empty($filters['status'])) {
                $where[] = "c.status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['student_id'])) {
                $where[] = "c.student_id = ?";
                $params[] = $filters['student_id'];
            }

            if (!empty($filters['batch_id'])) {
                $where[] = "c.batch_id = ?";
                $params[] = $filters['batch_id'];
            }

            if (!empty($filters['start_date'])) {
                $where[] = "c.issue_date >= ?";
                $params[] = $filters['start_date'];
            }

            if (!empty($filters['end_date'])) {
                $where[] = "c.issue_date <= ?";
                $params[] = $filters['end_date'];
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

            $filename = 'certificates_' . date('Y-m-d_H-i-s');
            $data = exportData($certificates, $format, $filename, [
                'ID' => 'id',
                'Certificate Number' => 'certificate_number',
                'Student' => 'student_name',
                'Batch' => 'batch_name',
                'Course' => 'course_name',
                'Center' => 'center_name',
                'Issue Date' => 'issue_date',
                'Valid Until' => 'valid_until',
                'Status' => 'status',
                'Created At' => 'created_at'
            ]);

            logAudit($_SESSION['user']['id'], 'export_certificates', [
                'format' => $format,
                'filters' => $filters
            ]);

            sendJSONResponse(true, 'Certificates exported successfully', $data);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Export error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}

function exportData($data, $format, $filename, $columns) {
    switch ($format) {
        case 'csv':
            return exportCSV($data, $filename, $columns);
        case 'excel':
            return exportExcel($data, $filename, $columns);
        case 'pdf':
            return exportPDF($data, $filename, $columns);
        default:
            throw new Exception('Unsupported export format');
    }
}

function exportCSV($data, $filename, $columns) {
    $csv = fopen('php://temp', 'r+');

    // Add headers
    fputcsv($csv, array_keys($columns));

    // Add data
    foreach ($data as $row) {
        $csvRow = [];
        foreach ($columns as $column) {
            $csvRow[] = $row[$column] ?? '';
        }
        fputcsv($csv, $csvRow);
    }

    rewind($csv);
    $csvData = stream_get_contents($csv);
    fclose($csv);

    return [
        'filename' => $filename . '.csv',
        'data' => base64_encode($csvData)
    ];
}

function exportExcel($data, $filename, $columns) {
    // Implementation for Excel export
    // This would typically use a library like PhpSpreadsheet
    throw new Exception('Excel export not implemented');
}

function exportPDF($data, $filename, $columns) {
    // Implementation for PDF export
    // This would typically use a library like TCPDF or FPDF
    throw new Exception('PDF export not implemented');
} 