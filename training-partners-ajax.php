<?php
require_once 'config.php';
require_once 'crud_functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if user has admin privileges
if (!hasPermission('admin')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => 'Invalid action'];

    switch ($action) {
        case 'list':
            $partners = TrainingPartner::getAll();
            $response = [
                'success' => true,
                'data' => $partners
            ];
            break;

        case 'get':
            $partnerId = $_POST['partner_id'] ?? 0;
            $partner = TrainingPartner::getById($partnerId);
            
            if ($partner) {
                $response = [
                    'success' => true,
                    'data' => $partner
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Training partner not found'
                ];
            }
            break;

        case 'create':
            $data = [
                'partner_name' => $_POST['partner_name'] ?? '',
                'contact_person' => $_POST['contact_person'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address' => $_POST['address'] ?? ''
            ];

            // Validate required fields
            foreach ($data as $key => $value) {
                if (empty($value)) {
                    throw new Exception("$key is required");
                }
            }

            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }

            if (TrainingPartner::create($data)) {
                $response = [
                    'success' => true,
                    'message' => 'Training partner created successfully'
                ];
            } else {
                throw new Exception('Failed to create training partner');
            }
            break;

        case 'update':
            $partnerId = $_POST['partner_id'] ?? 0;
            $data = [
                'partner_name' => $_POST['partner_name'] ?? '',
                'contact_person' => $_POST['contact_person'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address' => $_POST['address'] ?? '',
                'status' => $_POST['status'] ?? 'active'
            ];

            // Validate required fields
            foreach ($data as $key => $value) {
                if (empty($value) && $key !== 'status') {
                    throw new Exception("$key is required");
                }
            }

            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }

            if (TrainingPartner::update($partnerId, $data)) {
                $response = [
                    'success' => true,
                    'message' => 'Training partner updated successfully'
                ];
            } else {
                throw new Exception('Failed to update training partner');
            }
            break;

        case 'delete':
            $partnerId = $_POST['partner_id'] ?? 0;
            
            // Check if partner has associated training centers
            $centers = TrainingCenter::getAll($partnerId);
            if (!empty($centers)) {
                throw new Exception('Cannot delete partner with associated training centers');
            }

            if (TrainingPartner::delete($partnerId)) {
                $response = [
                    'success' => true,
                    'message' => 'Training partner deleted successfully'
                ];
            } else {
                throw new Exception('Failed to delete training partner');
            }
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
?> 