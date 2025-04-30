<?php
require_once '../../config.php';
require_once '../../crud_functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'list':
                try {
                    $sql = "SELECT tc.*, tp.partner_name 
                            FROM training_centers tc 
                            LEFT JOIN training_partners tp ON tc.partner_id = tp.id 
                            ORDER BY tc.id DESC";
                    
                    $result = $conn->query($sql);
                    
                    if ($result) {
                        $data = array();
                        while ($row = $result->fetch_assoc()) {
                            $data[] = array(
                                'id' => $row['id'],
                                'partner_name' => htmlspecialchars($row['partner_name']),
                                'center_name' => htmlspecialchars($row['center_name']),
                                'contact_person' => htmlspecialchars($row['contact_person']),
                                'email' => htmlspecialchars($row['email']),
                                'phone' => htmlspecialchars($row['phone']),
                                'address' => htmlspecialchars($row['address']),
                                'city' => htmlspecialchars($row['city']),
                                'state' => htmlspecialchars($row['state']),
                                'pincode' => htmlspecialchars($row['pincode']),
                                'status' => $row['status']
                            );
                        }
                        echo json_encode([
                            'status' => 'success',
                            'data' => $data
                        ]);
                    } else {
                        throw new Exception($conn->error);
                    }
                } catch (Exception $e) {
                    error_log("Error fetching training centers list: " . $e->getMessage());
                    echo json_encode(['status' => 'error', 'message' => 'Error fetching training centers list']);
                }
                exit;
                break;

            case 'get':
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
                break;
        }
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
exit;
?> 