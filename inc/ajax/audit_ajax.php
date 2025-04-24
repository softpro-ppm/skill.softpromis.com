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
        case 'get_audit_logs':
            $page = (int)($_POST['page'] ?? 1);
            $perPage = (int)($_POST['per_page'] ?? 10);
            $type = sanitizeInput($_POST['type'] ?? '');
            $userId = (int)($_POST['user_id'] ?? 0);
            $startDate = sanitizeInput($_POST['start_date'] ?? '');
            $endDate = sanitizeInput($_POST['end_date'] ?? '');
            $search = sanitizeInput($_POST['search'] ?? '');

            $where = [];
            $params = [];

            if (!empty($type)) {
                $where[] = "a.type = ?";
                $params[] = $type;
            }

            if ($userId > 0) {
                $where[] = "a.user_id = ?";
                $params[] = $userId;
            }

            if (!empty($startDate)) {
                $where[] = "a.created_at >= ?";
                $params[] = $startDate;
            }

            if (!empty($endDate)) {
                $where[] = "a.created_at <= ?";
                $params[] = $endDate;
            }

            if (!empty($search)) {
                $searchFields = ['a.type', 'a.details', 'u.name', 'u.role'];
                $searchResult = buildSearchQuery($searchFields, $search);
                $where[] = "(" . $searchResult['conditions'] . ")";
                $params = array_merge($params, $searchResult['params']);
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Get total count
            $countStmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM audit_logs a
                LEFT JOIN users u ON a.user_id = u.id
                $whereClause
            ");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            // Get pagination info
            $pagination = getPagination($page, $total, $perPage);

            // Get logs
            $stmt = $pdo->prepare("
                SELECT 
                    a.*,
                    u.name as user_name,
                    u.role as user_role
                FROM audit_logs a
                LEFT JOIN users u ON a.user_id = u.id
                $whereClause
                ORDER BY a.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $pagination['per_page'], $pagination['offset']]);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Audit logs retrieved successfully', [
                'logs' => $logs,
                'pagination' => $pagination
            ]);
            break;

        case 'get_activity_summary':
            $period = sanitizeInput($_POST['period'] ?? 'daily');
            $limit = (int)($_POST['limit'] ?? 30);

            $dateFormat = $period === 'daily' ? '%Y-%m-%d' : '%Y-%m';
            $groupBy = $period === 'daily' ? 'DAY' : 'MONTH';

            $stmt = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(created_at, ?) as period,
                    type,
                    COUNT(*) as count
                FROM audit_logs
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? $groupBy)
                GROUP BY period, type
                ORDER BY period DESC, count DESC
            ");
            $stmt->execute([$dateFormat, $limit]);
            $summary = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group by period
            $groupedSummary = [];
            foreach ($summary as $row) {
                $groupedSummary[$row['period']][] = [
                    'type' => $row['type'],
                    'count' => $row['count']
                ];
            }

            sendJSONResponse(true, 'Activity summary retrieved successfully', $groupedSummary);
            break;

        case 'get_user_activity':
            $userId = (int)($_POST['user_id'] ?? 0);
            $limit = (int)($_POST['limit'] ?? 10);

            if (empty($userId)) {
                sendJSONResponse(false, 'User ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT 
                    a.*,
                    u.name as user_name,
                    u.role as user_role
                FROM audit_logs a
                JOIN users u ON a.user_id = u.id
                WHERE a.user_id = ?
                ORDER BY a.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$userId, $limit]);
            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'User activity retrieved successfully', $activities);
            break;

        case 'get_entity_history':
            $entityType = sanitizeInput($_POST['entity_type'] ?? '');
            $entityId = (int)($_POST['entity_id'] ?? 0);
            $limit = (int)($_POST['limit'] ?? 10);

            if (empty($entityType) || empty($entityId)) {
                sendJSONResponse(false, 'Entity type and ID are required');
            }

            $stmt = $pdo->prepare("
                SELECT 
                    a.*,
                    u.name as user_name,
                    u.role as user_role
                FROM audit_logs a
                JOIN users u ON a.user_id = u.id
                WHERE a.entity_type = ?
                AND a.entity_id = ?
                ORDER BY a.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$entityType, $entityId, $limit]);
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Entity history retrieved successfully', $history);
            break;

        case 'export_audit_logs':
            $type = sanitizeInput($_POST['type'] ?? '');
            $userId = (int)($_POST['user_id'] ?? 0);
            $startDate = sanitizeInput($_POST['start_date'] ?? '');
            $endDate = sanitizeInput($_POST['end_date'] ?? '');

            $where = [];
            $params = [];

            if (!empty($type)) {
                $where[] = "a.type = ?";
                $params[] = $type;
            }

            if ($userId > 0) {
                $where[] = "a.user_id = ?";
                $params[] = $userId;
            }

            if (!empty($startDate)) {
                $where[] = "a.created_at >= ?";
                $params[] = $startDate;
            }

            if (!empty($endDate)) {
                $where[] = "a.created_at <= ?";
                $params[] = $endDate;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            $stmt = $pdo->prepare("
                SELECT 
                    a.*,
                    u.name as user_name,
                    u.role as user_role
                FROM audit_logs a
                LEFT JOIN users u ON a.user_id = u.id
                $whereClause
                ORDER BY a.created_at DESC
            ");
            $stmt->execute($params);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Generate CSV
            $filename = 'audit_logs_' . date('Y-m-d_H-i-s') . '.csv';
            $csv = fopen('php://temp', 'r+');

            // Add headers
            fputcsv($csv, [
                'ID',
                'Type',
                'User',
                'Role',
                'Details',
                'IP Address',
                'Created At'
            ]);

            // Add data
            foreach ($logs as $log) {
                fputcsv($csv, [
                    $log['id'],
                    $log['type'],
                    $log['user_name'],
                    $log['user_role'],
                    json_encode($log['details']),
                    $log['ip_address'],
                    $log['created_at']
                ]);
            }

            rewind($csv);
            $csvData = stream_get_contents($csv);
            fclose($csv);

            logAudit($_SESSION['user']['id'], 'export_audit_logs', [
                'type' => $type,
                'user_id' => $userId,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            sendJSONResponse(true, 'Audit logs exported successfully', [
                'filename' => $filename,
                'data' => base64_encode($csvData)
            ]);
            break;

        case 'clear_audit_logs':
            $days = (int)($_POST['days'] ?? 30);

            if ($days < 1) {
                sendJSONResponse(false, 'Invalid number of days');
            }

            $stmt = $pdo->prepare("
                DELETE FROM audit_logs 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            ");
            $stmt->execute([$days]);

            logAudit($_SESSION['user']['id'], 'clear_audit_logs', [
                'days' => $days
            ]);

            sendJSONResponse(true, 'Audit logs cleared successfully');
            break;

        case 'get_audit_stats':
            // Get total logs
            $stmt = $pdo->query("SELECT COUNT(*) FROM audit_logs");
            $totalLogs = $stmt->fetchColumn();

            // Get logs by type
            $stmt = $pdo->query("
                SELECT type, COUNT(*) as count
                FROM audit_logs
                GROUP BY type
                ORDER BY count DESC
            ");
            $logsByType = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get logs by user
            $stmt = $pdo->query("
                SELECT 
                    u.name as user_name,
                    u.role as user_role,
                    COUNT(*) as count
                FROM audit_logs a
                JOIN users u ON a.user_id = u.id
                GROUP BY a.user_id
                ORDER BY count DESC
                LIMIT 10
            ");
            $logsByUser = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get recent activity
            $stmt = $pdo->query("
                SELECT 
                    a.*,
                    u.name as user_name,
                    u.role as user_role
                FROM audit_logs a
                JOIN users u ON a.user_id = u.id
                ORDER BY a.created_at DESC
                LIMIT 5
            ");
            $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Audit statistics retrieved successfully', [
                'total_logs' => $totalLogs,
                'logs_by_type' => $logsByType,
                'logs_by_user' => $logsByUser,
                'recent_activity' => $recentActivity
            ]);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Audit error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 