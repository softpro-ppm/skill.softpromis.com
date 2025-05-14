<?php
require_once '../../config.php';
require_once '../../crud_functions.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'list':
            $roles = Role::getAll();
            echo json_encode(['success' => true, 'data' => $roles]);
            break;
        case 'add':
            $roleName = trim($_POST['roleName'] ?? '');
            $description = trim($_POST['roleDescription'] ?? '');
            $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
            if ($roleName === '' || $description === '') {
                throw new Exception('Role name and description are required.');
            }
            $roleId = Role::create($roleName, $description, $permissions);
            $role = [
                'role_id' => $roleId,
                'role_name' => $roleName,
                'description' => $description,
                'user_count' => 0,
                'created_at' => date('Y-m-d'),
                'permissions' => $permissions
            ];
            echo json_encode(['success' => true, 'data' => $role]);
            break;
        case 'edit':
            $roleId = intval($_POST['roleId'] ?? 0);
            $roleName = trim($_POST['roleName'] ?? '');
            $description = trim($_POST['roleDescription'] ?? '');
            $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
            if ($roleId <= 0 || $roleName === '' || $description === '') {
                throw new Exception('Invalid input.');
            }
            $ok = Role::update($roleId, $roleName, $description, $permissions);
            echo json_encode(['success' => $ok]);
            break;
        case 'delete':
            $roleId = intval($_POST['roleId'] ?? 0);
            if ($roleId <= 0) {
                throw new Exception('Invalid role ID.');
            }
            $ok = Role::delete($roleId);
            echo json_encode(['success' => $ok]);
            break;
        case 'get':
            $roleId = intval($_GET['roleId'] ?? 0);
            if ($roleId <= 0) {
                throw new Exception('Invalid role ID.');
            }
            $role = Role::getById($roleId);
            if ($role) {
                $role['permissions'] = $role['permissions'] ? json_decode($role['permissions'], true) : [];
                echo json_encode(['success' => true, 'data' => $role]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Role not found.']);
            }
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 