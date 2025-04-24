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
        case 'get_system_settings':
            $stmt = $pdo->query("
                SELECT 
                    s.*,
                    u.name as updated_by_name,
                    u.role as updated_by_role
                FROM system_settings s
                LEFT JOIN users u ON s.updated_by = u.id
                ORDER BY s.category, s.name
            ");
            $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group settings by category
            $groupedSettings = [];
            foreach ($settings as $setting) {
                $groupedSettings[$setting['category']][] = $setting;
            }

            sendJSONResponse(true, 'System settings retrieved successfully', $groupedSettings);
            break;

        case 'update_system_settings':
            $settings = $_POST['settings'] ?? [];
            
            if (empty($settings)) {
                sendJSONResponse(false, 'No settings provided');
            }

            $pdo->beginTransaction();

            try {
                $stmt = $pdo->prepare("
                    UPDATE system_settings 
                    SET value = ?, updated_by = ?, updated_at = NOW()
                    WHERE id = ?
                ");

                foreach ($settings as $id => $value) {
                    $stmt->execute([$value, $_SESSION['user']['id'], $id]);
                }

                $pdo->commit();

                logAudit($_SESSION['user']['id'], 'update_system_settings', [
                    'settings_count' => count($settings)
                ]);

                sendJSONResponse(true, 'System settings updated successfully');
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;

        case 'get_user_preferences':
            $userId = (int)($_POST['user_id'] ?? $_SESSION['user']['id']);

            $stmt = $pdo->prepare("
                SELECT 
                    p.*,
                    u.name as user_name,
                    u.role as user_role
                FROM user_preferences p
                JOIN users u ON p.user_id = u.id
                WHERE p.user_id = ?
            ");
            $stmt->execute([$userId]);
            $preferences = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($preferences) {
                sendJSONResponse(true, 'User preferences retrieved successfully', $preferences);
            } else {
                sendJSONResponse(false, 'User preferences not found');
            }
            break;

        case 'update_user_preferences':
            $userId = (int)($_POST['user_id'] ?? $_SESSION['user']['id']);
            $preferences = $_POST['preferences'] ?? [];

            if (empty($preferences)) {
                sendJSONResponse(false, 'No preferences provided');
            }

            // Validate preferences
            $validPreferences = [
                'theme' => ['light', 'dark', 'system'],
                'language' => ['en', 'es', 'fr', 'de'],
                'timezone' => true,
                'date_format' => ['Y-m-d', 'd-m-Y', 'm-d-Y'],
                'time_format' => ['12h', '24h'],
                'notifications' => ['all', 'important', 'none'],
                'email_notifications' => ['all', 'important', 'none'],
                'dashboard_layout' => ['default', 'compact', 'detailed']
            ];

            foreach ($preferences as $key => $value) {
                if (!isset($validPreferences[$key])) {
                    sendJSONResponse(false, "Invalid preference key: $key");
                }

                if ($validPreferences[$key] !== true && !in_array($value, $validPreferences[$key])) {
                    sendJSONResponse(false, "Invalid value for preference: $key");
                }
            }

            $stmt = $pdo->prepare("
                INSERT INTO user_preferences (
                    user_id, preferences, updated_at
                ) VALUES (?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                    preferences = VALUES(preferences),
                    updated_at = VALUES(updated_at)
            ");
            $stmt->execute([$userId, json_encode($preferences)]);

            logAudit($_SESSION['user']['id'], 'update_user_preferences', [
                'user_id' => $userId,
                'preferences' => $preferences
            ]);

            sendJSONResponse(true, 'User preferences updated successfully');
            break;

        case 'get_email_templates':
            $stmt = $pdo->query("
                SELECT 
                    t.*,
                    u.name as updated_by_name,
                    u.role as updated_by_role
                FROM email_templates t
                LEFT JOIN users u ON t.updated_by = u.id
                ORDER BY t.category, t.name
            ");
            $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group templates by category
            $groupedTemplates = [];
            foreach ($templates as $template) {
                $groupedTemplates[$template['category']][] = $template;
            }

            sendJSONResponse(true, 'Email templates retrieved successfully', $groupedTemplates);
            break;

        case 'update_email_template':
            $id = (int)($_POST['id'] ?? 0);
            $subject = sanitizeInput($_POST['subject'] ?? '');
            $body = $_POST['body'] ?? '';
            $variables = $_POST['variables'] ?? [];

            if (empty($id) || empty($subject) || empty($body)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            $stmt = $pdo->prepare("
                UPDATE email_templates 
                SET subject = ?, body = ?, variables = ?, updated_by = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $subject,
                $body,
                json_encode($variables),
                $_SESSION['user']['id'],
                $id
            ]);

            logAudit($_SESSION['user']['id'], 'update_email_template', [
                'id' => $id,
                'subject' => $subject
            ]);

            sendJSONResponse(true, 'Email template updated successfully');
            break;

        case 'get_system_logs':
            $page = (int)($_POST['page'] ?? 1);
            $perPage = (int)($_POST['per_page'] ?? 10);
            $type = sanitizeInput($_POST['type'] ?? '');
            $startDate = sanitizeInput($_POST['start_date'] ?? '');
            $endDate = sanitizeInput($_POST['end_date'] ?? '');

            $where = [];
            $params = [];

            if (!empty($type)) {
                $where[] = "l.type = ?";
                $params[] = $type;
            }

            if (!empty($startDate)) {
                $where[] = "l.created_at >= ?";
                $params[] = $startDate;
            }

            if (!empty($endDate)) {
                $where[] = "l.created_at <= ?";
                $params[] = $endDate;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Get total count
            $countStmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM system_logs l
                $whereClause
            ");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            // Get pagination info
            $pagination = getPagination($page, $total, $perPage);

            // Get logs
            $stmt = $pdo->prepare("
                SELECT 
                    l.*,
                    u.name as user_name,
                    u.role as user_role
                FROM system_logs l
                LEFT JOIN users u ON l.user_id = u.id
                $whereClause
                ORDER BY l.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([...$params, $pagination['per_page'], $pagination['offset']]);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'System logs retrieved successfully', [
                'logs' => $logs,
                'pagination' => $pagination
            ]);
            break;

        case 'clear_system_logs':
            $days = (int)($_POST['days'] ?? 30);

            if ($days < 1) {
                sendJSONResponse(false, 'Invalid number of days');
            }

            $stmt = $pdo->prepare("
                DELETE FROM system_logs 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            ");
            $stmt->execute([$days]);

            logAudit($_SESSION['user']['id'], 'clear_system_logs', [
                'days' => $days
            ]);

            sendJSONResponse(true, 'System logs cleared successfully');
            break;

        case 'get_backup_settings':
            $stmt = $pdo->query("
                SELECT * FROM backup_settings
                ORDER BY created_at DESC
                LIMIT 1
            ");
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Backup settings retrieved successfully', $settings);
            break;

        case 'update_backup_settings':
            $frequency = sanitizeInput($_POST['frequency'] ?? '');
            $time = sanitizeInput($_POST['time'] ?? '');
            $retention = (int)($_POST['retention'] ?? 30);
            $includeFiles = (bool)($_POST['include_files'] ?? false);
            $notifyAdmin = (bool)($_POST['notify_admin'] ?? true);

            if (!in_array($frequency, ['daily', 'weekly', 'monthly'])) {
                sendJSONResponse(false, 'Invalid backup frequency');
            }

            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
                sendJSONResponse(false, 'Invalid backup time format');
            }

            if ($retention < 1) {
                sendJSONResponse(false, 'Invalid retention period');
            }

            $stmt = $pdo->prepare("
                INSERT INTO backup_settings (
                    frequency, time, retention, include_files,
                    notify_admin, updated_by, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                    frequency = VALUES(frequency),
                    time = VALUES(time),
                    retention = VALUES(retention),
                    include_files = VALUES(include_files),
                    notify_admin = VALUES(notify_admin),
                    updated_by = VALUES(updated_by),
                    updated_at = VALUES(updated_at)
            ");
            $stmt->execute([
                $frequency,
                $time,
                $retention,
                $includeFiles,
                $notifyAdmin,
                $_SESSION['user']['id']
            ]);

            logAudit($_SESSION['user']['id'], 'update_backup_settings', [
                'frequency' => $frequency,
                'retention' => $retention
            ]);

            sendJSONResponse(true, 'Backup settings updated successfully');
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Settings error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 