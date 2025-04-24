<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get batch ID from URL
$batch_id = $_GET['id'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Add form validation and database update
    $batch_name = $_POST['batch_name'] ?? '';
    $program_id = $_POST['program_id'] ?? '';
    $center_id = $_POST['center_id'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $schedule = $_POST['schedule'] ?? '';
    $instructor = $_POST['instructor'] ?? '';
    $capacity = $_POST['capacity'] ?? '';
    $status = $_POST['status'] ?? 'upcoming';
    $remarks = $_POST['remarks'] ?? '';
    
    // Simulate successful update
    header('Location: list.php?success=2');
    exit;
}

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

// TODO: Fetch instructors list from database
$instructors = [
    ['id' => 'I001', 'name' => 'Sarah Wilson'],
    ['id' => 'I002', 'name' => 'John Smith'],
    ['id' => 'I003', 'name' => 'Mike Johnson']
];

// TODO: Fetch batch details from database
$batch = [
    'id' => $batch_id,
    'name' => 'Web Development Batch 1',
    'program_id' => 'P001',
    'center_id' => 'TC001',
    'start_date' => '2024-01-01',
    'end_date' => '2024-06-30',
    'schedule' => 'Monday to Friday, 9 AM to 1 PM',
    'instructor' => 'I001',
    'capacity' => 20,
    'status' => 'active',
    'remarks' => 'Regular batch with good attendance.'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Batch - Softpro Skill Solutions</title>
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
                <h1>Edit Batch</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                </div>
            </div>

            <!-- Edit Batch Form -->
            <div class="form-container">
                <form method="POST" action="edit.php?id=<?php echo $batch_id; ?>" class="form">
                    <div class="form-group">
                        <label for="batch_name">Batch Name</label>
                        <input type="text" id="batch_name" name="batch_name" class="form-control" value="<?php echo $batch['name']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="program_id">Program</label>
                        <select id="program_id" name="program_id" class="form-control" required>
                            <option value="">Select Program</option>
                            <?php foreach ($programs as $program): ?>
                            <option value="<?php echo $program['id']; ?>" <?php echo $program['id'] === $batch['program_id'] ? 'selected' : ''; ?>>
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
                            <option value="<?php echo $center['id']; ?>" <?php echo $center['id'] === $batch['center_id'] ? 'selected' : ''; ?>>
                                <?php echo $center['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $batch['start_date']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo $batch['end_date']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="schedule">Schedule</label>
                        <input type="text" id="schedule" name="schedule" class="form-control" value="<?php echo $batch['schedule']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="instructor">Instructor</label>
                        <select id="instructor" name="instructor" class="form-control" required>
                            <option value="">Select Instructor</option>
                            <?php foreach ($instructors as $instructor): ?>
                            <option value="<?php echo $instructor['id']; ?>" <?php echo $instructor['id'] === $batch['instructor'] ? 'selected' : ''; ?>>
                                <?php echo $instructor['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="capacity">Student Capacity</label>
                        <input type="number" id="capacity" name="capacity" class="form-control" min="1" value="<?php echo $batch['capacity']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="upcoming" <?php echo $batch['status'] === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                            <option value="active" <?php echo $batch['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="completed" <?php echo $batch['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea id="remarks" name="remarks" class="form-control" rows="3"><?php echo $batch['remarks']; ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Batch
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