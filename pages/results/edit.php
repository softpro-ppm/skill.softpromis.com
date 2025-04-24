<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get result ID from URL
$result_id = $_GET['id'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Add form validation and database update
    $student_id = $_POST['student_id'] ?? '';
    $assessment_id = $_POST['assessment_id'] ?? '';
    $score = $_POST['score'] ?? '';
    $date = $_POST['date'] ?? '';
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

// TODO: Fetch assessments list from database
$assessments = [
    ['id' => 'A001', 'title' => 'Web Development Basics'],
    ['id' => 'A002', 'title' => 'HTML & CSS'],
    ['id' => 'A003', 'title' => 'JavaScript Fundamentals']
];

// TODO: Fetch result details from database
$result = [
    'id' => $result_id,
    'student_id' => 'S001',
    'assessment_id' => 'A001',
    'score' => 85,
    'date' => '2024-01-15',
    'status' => 'passed',
    'remarks' => 'Good performance in the assessment.'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Result - Softpro Skill Solutions</title>
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
                <h1>Edit Result</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                </div>
            </div>

            <!-- Edit Result Form -->
            <div class="form-container">
                <form method="POST" action="edit.php?id=<?php echo $result_id; ?>" class="form">
                    <div class="form-group">
                        <label for="student_id">Student</label>
                        <select id="student_id" name="student_id" class="form-control" required>
                            <option value="">Select Student</option>
                            <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['id']; ?>" <?php echo $student['id'] === $result['student_id'] ? 'selected' : ''; ?>>
                                <?php echo $student['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="assessment_id">Assessment</label>
                        <select id="assessment_id" name="assessment_id" class="form-control" required>
                            <option value="">Select Assessment</option>
                            <?php foreach ($assessments as $assessment): ?>
                            <option value="<?php echo $assessment['id']; ?>" <?php echo $assessment['id'] === $result['assessment_id'] ? 'selected' : ''; ?>>
                                <?php echo $assessment['title']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="score">Score</label>
                        <input type="number" id="score" name="score" class="form-control" min="0" max="100" value="<?php echo $result['score']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" class="form-control" value="<?php echo $result['date']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="pending" <?php echo $result['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="passed" <?php echo $result['status'] === 'passed' ? 'selected' : ''; ?>>Passed</option>
                            <option value="failed" <?php echo $result['status'] === 'failed' ? 'selected' : ''; ?>>Failed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea id="remarks" name="remarks" class="form-control" rows="3"><?php echo $result['remarks']; ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Result
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