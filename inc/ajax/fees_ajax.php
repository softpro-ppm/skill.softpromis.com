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
            $stmt = $pdo->query("SELECT * FROM fees ORDER BY fee_id DESC");
            $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['data' => $fees]);
            exit;
        case 'add':
            $fee_name = trim($_POST['fee_name'] ?? '');
            $amount = trim($_POST['amount'] ?? '');
            $status = trim($_POST['status'] ?? 'active');
            if ($fee_name === '' || $amount === '') {
                sendJSON(false, 'Fee name and amount are required.');
            }
            $stmt = $pdo->prepare("INSERT INTO fees (fee_name, amount, status) VALUES (?, ?, ?)");
            $result = $stmt->execute([$fee_name, $amount, $status]);
            sendJSON($result, $result ? 'Fee added successfully.' : 'Failed to add fee.');
            break;
        case 'edit':
            $fee_id = (int)($_POST['fee_id'] ?? 0);
            $fee_name = trim($_POST['fee_name'] ?? '');
            $amount = trim($_POST['amount'] ?? '');
            $status = trim($_POST['status'] ?? 'active');
            if ($fee_id === 0 || $fee_name === '' || $amount === '') {
                sendJSON(false, 'Fee ID, name, and amount are required.');
            }
            $stmt = $pdo->prepare("UPDATE fees SET fee_name = ?, amount = ?, status = ? WHERE fee_id = ?");
            $result = $stmt->execute([$fee_name, $amount, $status, $fee_id]);
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
            $stmt = $pdo->prepare("SELECT * FROM fees WHERE fee_id = ?");
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