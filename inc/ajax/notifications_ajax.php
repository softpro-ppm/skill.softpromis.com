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
        case 'get_notifications':
            $limit = (int)($_POST['limit'] ?? 10);
            $type = sanitizeInput($_POST['type'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? '');

            $where = [];
            $params = [];

            if (!empty($type)) {
                $where[] = "n.type = ?";
                $params[] = $type;
            }

            if (!empty($status)) {
                $where[] = "n.status = ?";
                $params[] = $status;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            $stmt = $pdo->prepare("
                SELECT 
                    n.*,
                    u.name as created_by_name,
                    u.role as created_by_role
                FROM notifications n
                LEFT JOIN users u ON n.created_by = u.id
                $whereClause
                ORDER BY n.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([...$params, $limit]);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'Notifications retrieved successfully', $notifications);
            break;

        case 'create_notification':
            $type = sanitizeInput($_POST['type'] ?? '');
            $title = sanitizeInput($_POST['title'] ?? '');
            $message = sanitizeInput($_POST['message'] ?? '');
            $priority = sanitizeInput($_POST['priority'] ?? 'normal');
            $targetUsers = $_POST['target_users'] ?? [];
            $targetRoles = $_POST['target_roles'] ?? [];
            $expiryDate = sanitizeInput($_POST['expiry_date'] ?? '');

            if (empty($type) || empty($title) || empty($message)) {
                sendJSONResponse(false, 'Required fields are missing');
            }

            if (!in_array($priority, ['low', 'normal', 'high', 'urgent'])) {
                sendJSONResponse(false, 'Invalid priority level');
            }

            if (!empty($expiryDate) && !validateDate($expiryDate)) {
                sendJSONResponse(false, 'Invalid expiry date format');
            }

            $stmt = $pdo->prepare("
                INSERT INTO notifications (
                    type, title, message, priority, target_users,
                    target_roles, expiry_date, status, created_by, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', ?, NOW())
            ");
            $stmt->execute([
                $type,
                $title,
                $message,
                $priority,
                json_encode($targetUsers),
                json_encode($targetRoles),
                $expiryDate,
                $_SESSION['user']['id']
            ]);

            $notificationId = $pdo->lastInsertId();

            // Create user notifications for target users
            if (!empty($targetUsers)) {
                $stmt = $pdo->prepare("
                    INSERT INTO user_notifications (
                        notification_id, user_id, status, created_at
                    ) VALUES (?, ?, 'unread', NOW())
                ");
                foreach ($targetUsers as $userId) {
                    $stmt->execute([$notificationId, $userId]);
                }
            }

            // Create role-based notifications
            if (!empty($targetRoles)) {
                $stmt = $pdo->prepare("
                    INSERT INTO user_notifications (
                        notification_id, user_id, status, created_at
                    )
                    SELECT ?, u.id, 'unread', NOW()
                    FROM users u
                    WHERE u.role IN (" . str_repeat('?,', count($targetRoles) - 1) . "?)
                ");
                $stmt->execute([$notificationId, ...$targetRoles]);
            }

            logAudit($_SESSION['user']['id'], 'create_notification', [
                'type' => $type,
                'title' => $title,
                'priority' => $priority
            ]);

            sendJSONResponse(true, 'Notification created successfully', [
                'id' => $notificationId
            ]);
            break;

        case 'update_notification':
            $id = (int)($_POST['id'] ?? 0);
            $title = sanitizeInput($_POST['title'] ?? '');
            $message = sanitizeInput($_POST['message'] ?? '');
            $priority = sanitizeInput($_POST['priority'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? '');
            $expiryDate = sanitizeInput($_POST['expiry_date'] ?? '');

            if (empty($id)) {
                sendJSONResponse(false, 'Notification ID is required');
            }

            if (!empty($priority) && !in_array($priority, ['low', 'normal', 'high', 'urgent'])) {
                sendJSONResponse(false, 'Invalid priority level');
            }

            if (!empty($expiryDate) && !validateDate($expiryDate)) {
                sendJSONResponse(false, 'Invalid expiry date format');
            }

            $updates = [];
            $params = [];

            if (!empty($title)) {
                $updates[] = "title = ?";
                $params[] = $title;
            }

            if (!empty($message)) {
                $updates[] = "message = ?";
                $params[] = $message;
            }

            if (!empty($priority)) {
                $updates[] = "priority = ?";
                $params[] = $priority;
            }

            if (!empty($status)) {
                $updates[] = "status = ?";
                $params[] = $status;
            }

            if (!empty($expiryDate)) {
                $updates[] = "expiry_date = ?";
                $params[] = $expiryDate;
            }

            if (empty($updates)) {
                sendJSONResponse(false, 'No updates provided');
            }

            $updates[] = "updated_at = NOW()";
            $params[] = $id;

            $stmt = $pdo->prepare("
                UPDATE notifications 
                SET " . implode(", ", $updates) . "
                WHERE id = ?
            ");
            $stmt->execute($params);

            logAudit($_SESSION['user']['id'], 'update_notification', [
                'id' => $id,
                'updates' => $updates
            ]);

            sendJSONResponse(true, 'Notification updated successfully');
            break;

        case 'delete_notification':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'Notification ID is required');
            }

            // Delete user notifications first
            $stmt = $pdo->prepare("DELETE FROM user_notifications WHERE notification_id = ?");
            $stmt->execute([$id]);

            // Delete the notification
            $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ?");
            $stmt->execute([$id]);

            logAudit($_SESSION['user']['id'], 'delete_notification', [
                'id' => $id
            ]);

            sendJSONResponse(true, 'Notification deleted successfully');
            break;

        case 'get_user_notifications':
            $limit = (int)($_POST['limit'] ?? 10);
            $status = sanitizeInput($_POST['status'] ?? '');

            $where = ["un.user_id = ?"];
            $params = [$_SESSION['user']['id']];

            if (!empty($status)) {
                $where[] = "un.status = ?";
                $params[] = $status;
            }

            $whereClause = "WHERE " . implode(" AND ", $where);

            $stmt = $pdo->prepare("
                SELECT 
                    un.*,
                    n.type,
                    n.title,
                    n.message,
                    n.priority,
                    n.expiry_date,
                    u.name as created_by_name,
                    u.role as created_by_role
                FROM user_notifications un
                JOIN notifications n ON un.notification_id = n.id
                LEFT JOIN users u ON n.created_by = u.id
                $whereClause
                ORDER BY un.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([...$params, $limit]);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'User notifications retrieved successfully', $notifications);
            break;

        case 'mark_notification_read':
            $id = (int)($_POST['id'] ?? 0);

            if (empty($id)) {
                sendJSONResponse(false, 'Notification ID is required');
            }

            $stmt = $pdo->prepare("
                UPDATE user_notifications 
                SET status = 'read', read_at = NOW()
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$id, $_SESSION['user']['id']]);

            sendJSONResponse(true, 'Notification marked as read');
            break;

        case 'mark_all_notifications_read':
            $stmt = $pdo->prepare("
                UPDATE user_notifications 
                SET status = 'read', read_at = NOW()
                WHERE user_id = ? AND status = 'unread'
            ");
            $stmt->execute([$_SESSION['user']['id']]);

            sendJSONResponse(true, 'All notifications marked as read');
            break;

        case 'get_unread_count':
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM user_notifications 
                WHERE user_id = ? AND status = 'unread'
            ");
            $stmt->execute([$_SESSION['user']['id']]);
            $count = $stmt->fetchColumn();

            sendJSONResponse(true, 'Unread count retrieved successfully', [
                'count' => $count
            ]);
            break;

        case 'get_system_alerts':
            $limit = (int)($_POST['limit'] ?? 5);

            $stmt = $pdo->prepare("
                SELECT 
                    n.*,
                    u.name as created_by_name
                FROM notifications n
                LEFT JOIN users u ON n.created_by = u.id
                WHERE n.type = 'alert'
                AND n.status = 'active'
                AND (n.expiry_date IS NULL OR n.expiry_date >= NOW())
                AND (
                    n.target_users IS NULL 
                    OR JSON_CONTAINS(n.target_users, ?)
                    OR n.target_roles IS NULL 
                    OR JSON_CONTAINS(n.target_roles, ?)
                )
                ORDER BY n.priority DESC, n.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([
                json_encode($_SESSION['user']['id']),
                json_encode($_SESSION['user']['role']),
                $limit
            ]);
            $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendJSONResponse(true, 'System alerts retrieved successfully', $alerts);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Notifications error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
} 