<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get student ID from URL
$student_id = $_GET['id'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Add form validation and database update
    $student_name = $_POST['student_name'] ?? '';
    $center_id = $_POST['center_id'] ?? '';
    $program_id = $_POST['program_id'] ?? '';
    $batch_id = $_POST['batch_id'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $status = $_POST['status'] ?? 'active';
    
    // Simulate successful update
    header('Location: list.php?success=2');
    exit;
}

// TODO: Fetch centers list from database
$centers = [
    ['id' => 'TC001', 'name' => 'Tech Solutions HQ'],
    ['id' => 'TC002', 'name' => 'Tech Solutions East'],
    ['id' => 'TC003', 'name' => 'Global Education HQ']
];

// TODO: Fetch programs list from database
$programs = [
    ['id' => 'P001', 'name' => 'Web Development'],
    ['id' => 'P002', 'name' => 'Mobile Development'],
    ['id' => 'P003', 'name' => 'Data Science']
];

// TODO: Fetch batches list from database
$batches = [
    ['id' => 'B001', 'name' => 'Web Development Batch 1'],
    ['id' => 'B002', 'name' => 'Mobile Development Batch 1'],
    ['id' => 'B003', 'name' => 'Data Science Batch 1']
];

// TODO: Fetch student details from database
$student = [
    'id' => $student_id,
    'name' => 'John Doe',
    'center_id' => 'TC001',
    'program_id' => 'P001',
    'batch_id' => 'B001',
    'email' => 'john@example.com',
    'phone' => '+1 234 567 8900',
    'address' => '123 Main St, City, State 12345',
    'status' => 'active'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Softpro Skill Solutions</title>
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
                <h1>Edit Student</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                </div>
            </div>

            <!-- Edit Student Form -->
            <div class="form-container">
                <form method="POST" action="edit.php?id=<?php echo $student_id; ?>" class="form">
                    <div class="form-group">
                        <label for="student_name">Student Name</label>
                        <input type="text" id="student_name" name="student_name" class="form-control" value="<?php echo $student['name']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="center_id">Training Center</label>
                        <select id="center_id" name="center_id" class="form-control" required>
                            <option value="">Select Center</option>
                            <?php foreach ($centers as $center): ?>
                            <option value="<?php echo $center['id']; ?>" <?php echo $center['id'] === $student['center_id'] ? 'selected' : ''; ?>>
                                <?php echo $center['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="program_id">Program</label>
                        <select id="program_id" name="program_id" class="form-control" required>
                            <option value="">Select Program</option>
                            <?php foreach ($programs as $program): ?>
                            <option value="<?php echo $program['id']; ?>" <?php echo $program['id'] === $student['program_id'] ? 'selected' : ''; ?>>
                                <?php echo $program['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="batch_id">Batch</label>
                        <select id="batch_id" name="batch_id" class="form-control" required>
                            <option value="">Select Batch</option>
                            <?php foreach ($batches as $batch): ?>
                            <option value="<?php echo $batch['id']; ?>" <?php echo $batch['id'] === $student['batch_id'] ? 'selected' : ''; ?>>
                                <?php echo $batch['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo $student['email']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo $student['phone']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="3" required><?php echo $student['address']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active" <?php echo $student['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $student['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Student
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