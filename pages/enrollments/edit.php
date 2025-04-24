<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get enrollment ID from URL
$enrollment_id = $_GET['id'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Add form validation and database update
    $student_id = $_POST['student_id'] ?? '';
    $program_id = $_POST['program_id'] ?? '';
    $center_id = $_POST['center_id'] ?? '';
    $batch_id = $_POST['batch_id'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $status = $_POST['status'] ?? 'pending';
    $remarks = $_POST['remarks'] ?? '';
    
    // Simulate successful update
    header('Location: list.php?success=2');
    exit;
}

// TODO: Fetch students list from database
$students = [
    ['id' => 'S001', 'name' => 'John Doe'],
    ['id' => 'S002', 'name' => 'Jane Smith'],
    ['id' => 'S003', 'name' => 'Mike Johnson']
];

// TODO: Fetch programs list from database
$programs = [
    ['id' => 'P001', 'name' => 'Web Development'],
    ['id' => 'P002', 'name' => 'Mobile Development'],
    ['id' => 'P003', 'name' => 'Data Science']
];

// TODO: Fetch centers list from database
$centers = [
    ['id' => 'TC001', 'name' => 'Tech Solutions HQ'],
    ['id' => 'TC002', 'name' => 'Tech Solutions East'],
    ['id' => 'TC003', 'name' => 'Global Education HQ']
];

// TODO: Fetch batches list from database
$batches = [
    ['id' => 'B001', 'name' => 'Web Development Batch 1'],
    ['id' => 'B002', 'name' => 'Mobile Development Batch 1'],
    ['id' => 'B003', 'name' => 'Data Science Batch 1']
];

// TODO: Fetch enrollment details from database
$enrollment = [
    'id' => $enrollment_id,
    'student_id' => 'S001',
    'program_id' => 'P001',
    'center_id' => 'TC001',
    'batch_id' => 'B001',
    'start_date' => '2024-01-01',
    'end_date' => '2024-06-30',
    'status' => 'active',
    'remarks' => 'Regular student with good attendance.'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Enrollment - Softpro Skill Solutions</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../../components/topbar.php'; ?>
    <?php include '../../components/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Edit Enrollment</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                </div>
            </div>

            <!-- Edit Enrollment Form -->
            <div class="form-container">
                <form method="POST" action="edit.php?id=<?php echo $enrollment_id; ?>" class="form">
                    <div class="form-group">
                        <label for="student_id">Student</label>
                        <select id="student_id" name="student_id" class="form-control" required>
                            <option value="">Select Student</option>
                            <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['id']; ?>" <?php echo $student['id'] === $enrollment['student_id'] ? 'selected' : ''; ?>>
                                <?php echo $student['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="program_id">Program</label>
                        <select id="program_id" name="program_id" class="form-control" required>
                            <option value="">Select Program</option>
                            <?php foreach ($programs as $program): ?>
                            <option value="<?php echo $program['id']; ?>" <?php echo $program['id'] === $enrollment['program_id'] ? 'selected' : ''; ?>>
                                <?php echo $program['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="center_id">Center</label>
                        <select id="center_id" name="center_id" class="form-control" required>
                            <option value="">Select Center</option>
                            <?php foreach ($centers as $center): ?>
                            <option value="<?php echo $center['id']; ?>" <?php echo $center['id'] === $enrollment['center_id'] ? 'selected' : ''; ?>>
                                <?php echo $center['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="batch_id">Batch</label>
                        <select id="batch_id" name="batch_id" class="form-control" required>
                            <option value="">Select Batch</option>
                            <?php foreach ($batches as $batch): ?>
                            <option value="<?php echo $batch['id']; ?>" <?php echo $batch['id'] === $enrollment['batch_id'] ? 'selected' : ''; ?>>
                                <?php echo $batch['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $enrollment['start_date']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo $enrollment['end_date']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="pending" <?php echo $enrollment['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="active" <?php echo $enrollment['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="completed" <?php echo $enrollment['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $enrollment['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea id="remarks" name="remarks" class="form-control" rows="3"><?php echo $enrollment['remarks']; ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Enrollment
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                            Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html> 