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
        case 'bulk_upload_students':
            if (!isset($_FILES['files'])) {
                sendJSONResponse(false, 'No files uploaded');
                break;
            }

            $files = $_FILES['files'];
            $center_id = sanitizeInput($_POST['center_id'] ?? '');
            $format = sanitizeInput($_POST['format'] ?? 'csv');

            if (empty($center_id)) {
                sendJSONResponse(false, 'Center ID is required');
                break;
            }

            // Validate center exists
            $stmt = $pdo->prepare("SELECT id FROM training_centers WHERE id = ?");
            $stmt->execute([$center_id]);
            if (!$stmt->fetch()) {
                sendJSONResponse(false, 'Invalid center ID');
                break;
            }

            $total_success = 0;
            $total_failed = 0;
            $file_results = [];

            foreach ($files['tmp_name'] as $key => $tmp_name) {
                if ($files['error'][$key] !== UPLOAD_ERR_OK) {
                    $file_results[] = [
                        'filename' => $files['name'][$key],
                        'success' => false,
                        'message' => 'File upload failed'
                    ];
                    continue;
                }

                $file = [
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $tmp_name,
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                ];

                try {
                    $data = importData($file, $format);
                    if (!$data) {
                        throw new Exception('Failed to import data');
                    }

                    $required_headers = ['name', 'email', 'phone'];
                    $headers = array_keys($data[0]);
                    $missing_headers = array_diff($required_headers, $headers);

                    if (!empty($missing_headers)) {
                        throw new Exception('Missing required headers: ' . implode(', ', $missing_headers));
                    }

                    $success = 0;
                    $failed = 0;
                    $errors = [];

                    $pdo->beginTransaction();

                    try {
                        $stmt = $pdo->prepare("
                            INSERT INTO students (name, email, phone, center_id, status, created_at)
                            VALUES (?, ?, ?, ?, 'active', NOW())
                        ");

                        foreach ($data as $row) {
                            try {
                                // Validate email format
                                if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                                    throw new Exception('Invalid email format');
                                }

                                // Check email uniqueness
                                $check = $pdo->prepare("SELECT id FROM students WHERE email = ?");
                                $check->execute([$row['email']]);
                                if ($check->fetch()) {
                                    throw new Exception('Email already exists');
                                }

                                // Validate phone format
                                if (!preg_match('/^[0-9]{10}$/', $row['phone'])) {
                                    throw new Exception('Invalid phone format');
                                }

                                $stmt->execute([
                                    $row['name'],
                                    $row['email'],
                                    $row['phone'],
                                    $center_id
                                ]);

                                $success++;
                            } catch (Exception $e) {
                                $failed++;
                                $errors[] = "Row {$row['name']}: " . $e->getMessage();
                            }
                        }

                        $pdo->commit();

                        $total_success += $success;
                        $total_failed += $failed;

                        $file_results[] = [
                            'filename' => $files['name'][$key],
                            'success' => true,
                            'success_count' => $success,
                            'failed_count' => $failed,
                            'errors' => $errors
                        ];
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        throw $e;
                    }
                } catch (Exception $e) {
                    $file_results[] = [
                        'filename' => $files['name'][$key],
                        'success' => false,
                        'message' => $e->getMessage()
                    ];
                }
            }

            logAudit($_SESSION['user']['id'], 'bulk_upload_students', [
                'format' => $format,
                'center_id' => $center_id,
                'total_success' => $total_success,
                'total_failed' => $total_failed,
                'file_results' => $file_results
            ]);

            sendJSONResponse(true, "Bulk upload completed. Total Success: $total_success, Total Failed: $total_failed", [
                'total_success' => $total_success,
                'total_failed' => $total_failed,
                'file_results' => $file_results
            ]);
            break;

        case 'bulk_upload_courses':
            if (!isset($_FILES['files'])) {
                sendJSONResponse(false, 'No files uploaded');
                break;
            }

            $files = $_FILES['files'];
            $format = sanitizeInput($_POST['format'] ?? 'csv');

            $total_success = 0;
            $total_failed = 0;
            $file_results = [];

            foreach ($files['tmp_name'] as $key => $tmp_name) {
                if ($files['error'][$key] !== UPLOAD_ERR_OK) {
                    $file_results[] = [
                        'filename' => $files['name'][$key],
                        'success' => false,
                        'message' => 'File upload failed'
                    ];
                    continue;
                }

                $file = [
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $tmp_name,
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                ];

                try {
                    $data = importData($file, $format);
                    if (!$data) {
                        throw new Exception('Failed to import data');
                    }

                    $required_headers = ['name', 'description', 'duration', 'fee', 'sector_id'];
                    $headers = array_keys($data[0]);
                    $missing_headers = array_diff($required_headers, $headers);

                    if (!empty($missing_headers)) {
                        throw new Exception('Missing required headers: ' . implode(', ', $missing_headers));
                    }

                    $success = 0;
                    $failed = 0;
                    $errors = [];

                    $pdo->beginTransaction();

                    try {
                        $stmt = $pdo->prepare("
                            INSERT INTO courses (name, description, duration, fee, sector_id, status, created_at)
                            VALUES (?, ?, ?, ?, ?, 'active', NOW())
                        ");

                        foreach ($data as $row) {
                            try {
                                // Validate duration
                                if (!is_numeric($row['duration']) || $row['duration'] <= 0) {
                                    throw new Exception('Invalid duration');
                                }

                                // Validate fee
                                if (!is_numeric($row['fee']) || $row['fee'] < 0) {
                                    throw new Exception('Invalid fee');
                                }

                                // Validate sector exists
                                $check = $pdo->prepare("SELECT id FROM sectors WHERE id = ?");
                                $check->execute([$row['sector_id']]);
                                if (!$check->fetch()) {
                                    throw new Exception('Invalid sector ID');
                                }

                                // Check course name uniqueness
                                $check = $pdo->prepare("SELECT id FROM courses WHERE name = ?");
                                $check->execute([$row['name']]);
                                if ($check->fetch()) {
                                    throw new Exception('Course name already exists');
                                }

                                $stmt->execute([
                                    $row['name'],
                                    $row['description'],
                                    $row['duration'],
                                    $row['fee'],
                                    $row['sector_id']
                                ]);

                                $success++;
                            } catch (Exception $e) {
                                $failed++;
                                $errors[] = "Row {$row['name']}: " . $e->getMessage();
                            }
                        }

                        $pdo->commit();

                        $total_success += $success;
                        $total_failed += $failed;

                        $file_results[] = [
                            'filename' => $files['name'][$key],
                            'success' => true,
                            'success_count' => $success,
                            'failed_count' => $failed,
                            'errors' => $errors
                        ];
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        throw $e;
                    }
                } catch (Exception $e) {
                    $file_results[] = [
                        'filename' => $files['name'][$key],
                        'success' => false,
                        'message' => $e->getMessage()
                    ];
                }
            }

            logAudit($_SESSION['user']['id'], 'bulk_upload_courses', [
                'format' => $format,
                'total_success' => $total_success,
                'total_failed' => $total_failed,
                'file_results' => $file_results
            ]);

            sendJSONResponse(true, "Bulk upload completed. Total Success: $total_success, Total Failed: $total_failed", [
                'total_success' => $total_success,
                'total_failed' => $total_failed,
                'file_results' => $file_results
            ]);
            break;

        case 'bulk_upload_batches':
            if (!isset($_FILES['files'])) {
                sendJSONResponse(false, 'No files uploaded');
                break;
            }

            $files = $_FILES['files'];
            $format = sanitizeInput($_POST['format'] ?? 'csv');

            $total_success = 0;
            $total_failed = 0;
            $file_results = [];

            foreach ($files['tmp_name'] as $key => $tmp_name) {
                if ($files['error'][$key] !== UPLOAD_ERR_OK) {
                    $file_results[] = [
                        'filename' => $files['name'][$key],
                        'success' => false,
                        'message' => 'File upload failed'
                    ];
                    continue;
                }

                $file = [
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $tmp_name,
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                ];

                try {
                    $data = importData($file, $format);
                    if (!$data) {
                        throw new Exception('Failed to import data');
                    }

                    $required_headers = ['name', 'course_id', 'center_id', 'start_date', 'end_date', 'capacity'];
                    $headers = array_keys($data[0]);
                    $missing_headers = array_diff($required_headers, $headers);

                    if (!empty($missing_headers)) {
                        throw new Exception('Missing required headers: ' . implode(', ', $missing_headers));
                    }

                    $success = 0;
                    $failed = 0;
                    $errors = [];

                    $pdo->beginTransaction();

                    try {
                        $stmt = $pdo->prepare("
                            INSERT INTO batches (name, course_id, center_id, start_date, end_date, capacity, status, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())
                        ");

                        foreach ($data as $row) {
                            try {
                                // Validate dates
                                $start_date = strtotime($row['start_date']);
                                $end_date = strtotime($row['end_date']);
                                if (!$start_date || !$end_date || $end_date <= $start_date) {
                                    throw new Exception('Invalid dates');
                                }

                                // Validate capacity
                                if (!is_numeric($row['capacity']) || $row['capacity'] <= 0) {
                                    throw new Exception('Invalid capacity');
                                }

                                // Validate course exists
                                $check = $pdo->prepare("SELECT id FROM courses WHERE id = ?");
                                $check->execute([$row['course_id']]);
                                if (!$check->fetch()) {
                                    throw new Exception('Invalid course ID');
                                }

                                // Validate center exists
                                $check = $pdo->prepare("SELECT id FROM training_centers WHERE id = ?");
                                $check->execute([$row['center_id']]);
                                if (!$check->fetch()) {
                                    throw new Exception('Invalid center ID');
                                }

                                $stmt->execute([
                                    $row['name'],
                                    $row['course_id'],
                                    $row['center_id'],
                                    date('Y-m-d', $start_date),
                                    date('Y-m-d', $end_date),
                                    $row['capacity']
                                ]);

                                $success++;
                            } catch (Exception $e) {
                                $failed++;
                                $errors[] = "Row {$row['name']}: " . $e->getMessage();
                            }
                        }

                        $pdo->commit();

                        $total_success += $success;
                        $total_failed += $failed;

                        $file_results[] = [
                            'filename' => $files['name'][$key],
                            'success' => true,
                            'success_count' => $success,
                            'failed_count' => $failed,
                            'errors' => $errors
                        ];
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        throw $e;
                    }
                } catch (Exception $e) {
                    $file_results[] = [
                        'filename' => $files['name'][$key],
                        'success' => false,
                        'message' => $e->getMessage()
                    ];
                }
            }

            logAudit($_SESSION['user']['id'], 'bulk_upload_batches', [
                'format' => $format,
                'total_success' => $total_success,
                'total_failed' => $total_failed,
                'file_results' => $file_results
            ]);

            sendJSONResponse(true, "Bulk upload completed. Total Success: $total_success, Total Failed: $total_failed", [
                'total_success' => $total_success,
                'total_failed' => $total_failed,
                'file_results' => $file_results
            ]);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (PDOException $e) {
    logError("Bulk upload error: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}

function importData($file, $format) {
    switch ($format) {
        case 'csv':
            return importCSV($file);
        case 'excel':
            return importExcel($file);
        default:
            throw new Exception('Unsupported import format');
    }
}

function importCSV($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload failed');
    }

    $handle = fopen($file['tmp_name'], 'r');
    if (!$handle) {
        throw new Exception('Failed to open file');
    }

    // Read headers
    $headers = fgetcsv($handle);
    if (!$headers) {
        fclose($handle);
        throw new Exception('Failed to read headers');
    }

    // Read data
    $data = [];
    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) !== count($headers)) {
            continue; // Skip malformed rows
        }
        $data[] = array_combine($headers, $row);
    }

    fclose($handle);
    return $data;
}

function importExcel($file) {
    // Implementation for Excel import
    // This would typically use a library like PhpSpreadsheet
    throw new Exception('Excel import not implemented');
} 