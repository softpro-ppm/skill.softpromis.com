<?php
require_once '../../config.php';
require_once '../functions.php';

header('Content-Type: application/json');

$pdo = getDBConnection();

$action = $_REQUEST['action'] ?? '';

function sendJSON($success, $message = '', $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    switch ($action) {
        case 'list':
            $stmt = $pdo->query("
                SELECT 
                    f.fee_id, 
                    f.enrollment_id, 
                    CONCAT(s.first_name, ' ', s.last_name, ' (', s.enrollment_no, ')') AS student_display,
                    f.amount, 
                    f.payment_date, 
                    f.payment_mode, 
                    f.transaction_id, 
                    f.status, 
                    f.receipt_no, 
                    f.notes
                FROM fees f
                LEFT JOIN student_batch_enrollment e ON f.enrollment_id = e.enrollment_id
                LEFT JOIN students s ON e.student_id = s.student_id
                ORDER BY f.fee_id DESC
            ");
            $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['data' => $fees]);
            exit;
        case 'add':
            $enrollment_id = trim($_POST['enrollment_id'] ?? '');
            $amount = trim($_POST['amount'] ?? '');
            $payment_date = trim($_POST['payment_date'] ?? '');
            $payment_mode = trim($_POST['payment_mode'] ?? '');
            $transaction_id = trim($_POST['transaction_id'] ?? '');
            $status = trim($_POST['status'] ?? 'pending');
            $receipt_no = trim($_POST['receipt_no'] ?? '');
            $notes = trim($_POST['notes'] ?? '');
            if ($enrollment_id === '' || $amount === '') {
                sendJSON(false, 'Enrollment ID and amount are required.');
            }
            $stmt = $pdo->prepare("INSERT INTO fees (enrollment_id, amount, payment_date, payment_mode, transaction_id, status, receipt_no, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $result = $stmt->execute([$enrollment_id, $amount, $payment_date, $payment_mode, $transaction_id, $status, $receipt_no, $notes]);
            sendJSON($result, $result ? 'Fee added successfully.' : 'Failed to add fee.');
            break;
        case 'edit':
            $fee_id = (int)($_POST['fee_id'] ?? 0);
            $enrollment_id = trim($_POST['enrollment_id'] ?? '');
            $amount = trim($_POST['amount'] ?? '');
            $payment_date = trim($_POST['payment_date'] ?? '');
            $payment_mode = trim($_POST['payment_mode'] ?? '');
            $transaction_id = trim($_POST['transaction_id'] ?? '');
            $status = trim($_POST['status'] ?? 'pending');
            $receipt_no = trim($_POST['receipt_no'] ?? '');
            $notes = trim($_POST['notes'] ?? '');
            if ($fee_id === 0 || $enrollment_id === '' || $amount === '') {
                sendJSON(false, 'Fee ID, Enrollment ID, and amount are required.');
            }
            $stmt = $pdo->prepare("UPDATE fees SET enrollment_id = ?, amount = ?, payment_date = ?, payment_mode = ?, transaction_id = ?, status = ?, receipt_no = ?, notes = ?, updated_at = NOW() WHERE fee_id = ?");
            $result = $stmt->execute([$enrollment_id, $amount, $payment_date, $payment_mode, $transaction_id, $status, $receipt_no, $notes, $fee_id]);
            sendJSON($result, $result ? 'Fee updated successfully.' : 'Failed to update fee.');
            break;
        case 'delete':
            $fee_id = (int)($_POST['fee_id'] ?? 0);
            if ($fee_id === 0) {
                sendJSON(false, 'Fee ID is required.');
            }
            $stmt = $pdo->prepare("DELETE FROM fees WHERE fee_id = ?");
            $result = $stmt->execute([$fee_id]);
            sendJSON($result, $result ? 'Fee deleted successfully.' : 'Failed to delete fee.');
            break;
        case 'get':
            $fee_id = (int)($_POST['fee_id'] ?? 0);
            if ($fee_id === 0) {
                sendJSON(false, 'Fee ID is required.');
            }
            $stmt = $pdo->prepare("
                SELECT f.*, e.student_id 
                FROM fees f
                LEFT JOIN student_batch_enrollment e ON f.enrollment_id = e.enrollment_id
                WHERE f.fee_id = ?
            ");
            $stmt->execute([$fee_id]);
            $fee = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($fee) {
                sendJSON(true, '', $fee);
            } else {
                sendJSON(false, 'Fee not found.');
            }
            break;
        default:
            sendJSON(false, 'Invalid action.');
    }
} catch (Exception $e) {
    sendJSON(false, 'Server error: ' . $e->getMessage());
}