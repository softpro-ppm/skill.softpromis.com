<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Add form validation and database insertion
    $batch_id = $_POST['batch_id'] ?? '';
    $date = $_POST['date'] ?? '';
    $status = $_POST['status'] ?? 'pending';
    $remarks = $_POST['remarks'] ?? '';
    $attendance_data = $_POST['attendance_data'] ?? [];
    
    // Simulate successful addition
    header('Location: list.php?success=1');
    exit;
}

// TODO: Fetch batches list from database
$batches = [
    ['id' => 'B001', 'name' => 'Web Development Batch 1'],
    ['id' => 'B002', 'name' => 'Mobile Development Batch 1'],
    ['id' => 'B003', 'name' => 'Data Science Batch 1']
];

// TODO: Fetch students list for selected batch
$students = [
    [
        'id' => 'S001',
        'name' => 'John Doe',
        'status' => 'present'
    ],
    [
        'id' => 'S002',
        'name' => 'Jane Smith',
        'status' => 'present'
    ],
    [
        'id' => 'S003',
        'name' => 'Mike Johnson',
        'status' => 'absent'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Attendance - Softpro Skill Solutions</title>
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
                <h1>Add Attendance</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                </div>
            </div>

            <!-- Add Attendance Form -->
            <div class="form-container">
                <form method="POST" action="add.php" class="form">
                    <div class="form-group">
                        <label for="batch_id">Batch</label>
                        <select id="batch_id" name="batch_id" class="form-control" required>
                            <option value="">Select Batch</option>
                            <?php foreach ($batches as $batch): ?>
                            <option value="<?php echo $batch['id']; ?>"><?php echo $batch['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea id="remarks" name="remarks" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="attendance-list">
                        <h3>Student Attendance</h3>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo $student['id']; ?></td>
                                        <td><?php echo $student['name']; ?></td>
                                        <td>
                                            <select name="attendance_data[<?php echo $student['id']; ?>][status]" class="form-control" required>
                                                <option value="present" <?php echo $student['status'] === 'present' ? 'selected' : ''; ?>>Present</option>
                                                <option value="absent" <?php echo $student['status'] === 'absent' ? 'selected' : ''; ?>>Absent</option>
                                                <option value="late" <?php echo $student['status'] === 'late' ? 'selected' : ''; ?>>Late</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="attendance_data[<?php echo $student['id']; ?>][remarks]" class="form-control" placeholder="Add remarks">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Save Attendance
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
    <script>
        // TODO: Add JavaScript to fetch students list when batch is selected
        document.getElementById('batch_id').addEventListener('change', function() {
            const batchId = this.value;
            if (batchId) {
                // TODO: Fetch students list for selected batch
                console.log('Fetching students for batch:', batchId);
            }
        });
    </script>
</body>
</html> 