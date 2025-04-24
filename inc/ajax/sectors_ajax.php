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
        case 'create':
            $name = sanitizeInput($_POST['name'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $parent_id = (int)($_POST['parent_id'] ?? 0);

            if (empty($name)) {
                sendJSONResponse(false, 'Name is required');
            }

            // Check if parent sector exists if provided
            if ($parent_id > 0) {
                $stmt = $pdo->prepare("SELECT id FROM sectors WHERE id = ?");
                $stmt->execute([$parent_id]);
                if (!$stmt->fetch()) {
                    sendJSONResponse(false, 'Parent sector not found');
                }
            }

            $stmt = $pdo->prepare("
                INSERT INTO sectors (name, description, status, parent_id, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $description, $status, $parent_id]);

            logAudit($_SESSION['user']['id'], 'create_sector', [
                'name' => $name,
                'parent_id' => $parent_id
            ]);

            sendJSONResponse(true, 'Sector created successfully', [
                'id' => $pdo->lastInsertId()
            ]);
            break;

        case 'read':
            $page = (int)($_POST['page'] ?? 1);
            $perPage = (int)($_POST['per_page'] ?? 10);
            $search = sanitizeInput($_POST['search'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? '');
            $parent_id = (int)($_POST['parent_id'] ?? 0);

            $where = [];
            $params = [];

            if (!empty($search)) {
                $searchFields = ['name', 'description'];
                $searchResult = buildSearchQuery($searchFields, $search);
                $where[] = "(" . $searchResult['conditions'] . ")";
                $params = array_merge($params, $searchResult['params']);
            }

            if (!empty($status)) {
                $where[] = "status = ?";
                $params[] = $status;
            }

            if ($parent_id > 0) {
                $where[] = "parent_id = ?";
                $params[] = $parent_id;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Get total count
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM sectors $whereClause");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            // Get pagination info
            $pagination = getPagination($page, $total, $perPage);

            // Get data with parent sector info
            $stmt = $pdo->prepare("
                SELECT s.*, p.name as parent_name 
                FROM sectors s
                LEFT JOIN sectors p ON s.parent_id = p.id
                $whereClause
                ORDER BY s.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $pagination['per_page'], $pagination['offset']]);
            $sectors = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Sectors retrieved successfully', [
                'data' => $sectors,
                'pagination' => $pagination
            ]);
            break;

        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            $name = sanitizeInput($_POST['name'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'active');
            $parent_id = (int)($_POST['parent_id'] ?? 0);

            if (empty($id) || empty($name)) {
                sendJSONResponse(false, 'ID and name are required');
            }

            // Check if parent sector exists if provided
            if ($parent_id > 0) {
                $stmt = $pdo->prepare("SELECT id FROM sectors WHERE id = ?");
                $stmt->execute([$parent_id]);
                if (!$stmt->fetch()) {
                    sendJSONResponse(false, 'Parent sector not found');
                }
            }

            // Prevent circular reference
            if ($parent_id === $id) {
                sendJSONResponse(false, 'A sector cannot be its own parent');
            }

            $stmt = $pdo->prepare("
                UPDATE sectors 
                SET name = ?, description = ?, status = ?, parent_id = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$name, $description, $status, $parent_id, $id]);

            logAudit($_SESSION['user']['id'], 'update_sector', [
                'id' => $id,
                'name' => $name,
                'parent_id' => $parent_id
            ]);

            sendJSONResponse(true, 'Sector updated successfully');
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            // Check if sector has child sectors
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM sectors WHERE parent_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                sendJSONResponse(false, 'Cannot delete sector with child sectors');
            }

            // Get sector info for audit log
            $stmt = $pdo->prepare("SELECT name, parent_id FROM sectors WHERE id = ?");
            $stmt->execute([$id]);
            $sector = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$sector) {
                sendJSONResponse(false, 'Sector not found');
            }

            $stmt = $pdo->prepare("DELETE FROM sectors WHERE id = ?");
            $stmt->execute([$id]);

            logAudit($_SESSION['user']['id'], 'delete_sector', [
                'id' => $id,
                'name' => $sector['name'],
                'parent_id' => $sector['parent_id']
            ]);

            sendJSONResponse(true, 'Sector deleted successfully');
            break;

        case 'get':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT s.*, p.name as parent_name 
                FROM sectors s
                LEFT JOIN sectors p ON s.parent_id = p.id
                WHERE s.id = ?
            ");
            $stmt->execute([$id]);
            $sector = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($sector) {
                sendJSONResponse(true, 'Sector retrieved successfully', $sector);
            } else {
                sendJSONResponse(false, 'Sector not found');
            }
            break;

        case 'get_tree':
            // Get all sectors for tree view
            $stmt = $pdo->query("
                SELECT id, name, parent_id, status
                FROM sectors
                ORDER BY name
            ");
            $sectors = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Build tree structure
            $tree = [];
            $map = [];

            foreach ($sectors as $sector) {
                $map[$sector['id']] = $sector;
                $map[$sector['id']]['children'] = [];
            }

            foreach ($sectors as $sector) {
                if ($sector['parent_id'] > 0 && isset($map[$sector['parent_id']])) {
                    $map[$sector['parent_id']]['children'][] = &$map[$sector['id']];
                } else {
                    $tree[] = &$map[$sector['id']];
                }
            }

            sendJSONResponse(true, 'Sector tree retrieved successfully', $tree);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Sectors error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 