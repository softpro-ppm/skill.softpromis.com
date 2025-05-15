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
        case 'get_students_by_batch':
            $batch_id = (int)($_POST['batch_id'] ?? 0);
            if (empty($batch_id)) {
                echo json_encode(['success' => false, 'message' => 'Batch ID is required']);
                exit;
            }
            $stmt = $pdo->prepare('SELECT s.enrollment_no, s.first_name, s.last_name, s.email, s.mobile, s.gender FROM students s INNER JOIN student_batch_enrollment e ON s.student_id = e.student_id WHERE e.batch_id = ? AND e.status = "active" ORDER BY s.first_name, s.last_name');
            $stmt->execute([$batch_id]);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $students]);
            exit;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}
