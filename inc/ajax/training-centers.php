<?php
require_once '../../config.php';
require_once '../../crud_functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get') {
    $center_id = isset($_GET['center_id']) ? intval($_GET['center_id']) : 0;
    
    if ($center_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid center ID']);
        exit;
    }

    try {
        $sql = "SELECT tc.*, tp.partner_name 
                FROM training_centers tc 
                LEFT JOIN training_partners tp ON tc.partner_id = tp.id 
                WHERE tc.id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $center_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $center = $result->fetch_assoc();
            echo json_encode(['status' => 'success', 'data' => $center]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Training center not found']);
        }
    } catch (Exception $e) {
        error_log("Error fetching training center details: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Error fetching training center details']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
exit;
?> 