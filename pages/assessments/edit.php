<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get assessment ID from URL
$assessment_id = $_GET['id'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Add form validation and database update
    $title = $_POST['title'] ?? '';
    $program_id = $_POST['program_id'] ?? '';
    $description = $_POST['description'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $total_marks = $_POST['total_marks'] ?? '';
    $passing_marks = $_POST['passing_marks'] ?? '';
    $status = $_POST['status'] ?? 'active';
    $topics = $_POST['topics'] ?? [];
    
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

// TODO: Fetch assessment details from database
$assessment = [
    'id' => $assessment_id,
    'title' => 'Web Development Basics',
    'program_id' => 'P001',
    'description' => 'Assessment covering basic web development concepts',
    'duration' => 2,
    'total_marks' => 100,
    'passing_marks' => 60,
    'status' => 'active',
    'topics' => [
        'HTML Basics',
        'CSS Fundamentals',
        'JavaScript Introduction'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Assessment - Softpro Skill Solutions</title>
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
                <h1>Edit Assessment</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                </div>
            </div>

            <!-- Edit Assessment Form -->
            <div class="form-container">
                <form method="POST" action="edit.php?id=<?php echo $assessment_id; ?>" class="form">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" class="form-control" value="<?php echo $assessment['title']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="program_id">Program</label>
                        <select id="program_id" name="program_id" class="form-control" required>
                            <option value="">Select Program</option>
                            <?php foreach ($programs as $program): ?>
                            <option value="<?php echo $program['id']; ?>" <?php echo $program['id'] === $assessment['program_id'] ? 'selected' : ''; ?>>
                                <?php echo $program['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3" required><?php echo $assessment['description']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="duration">Duration (hours)</label>
                        <input type="number" id="duration" name="duration" class="form-control" min="0.5" step="0.5" value="<?php echo $assessment['duration']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="total_marks">Total Marks</label>
                        <input type="number" id="total_marks" name="total_marks" class="form-control" min="1" value="<?php echo $assessment['total_marks']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="passing_marks">Passing Marks</label>
                        <input type="number" id="passing_marks" name="passing_marks" class="form-control" min="1" value="<?php echo $assessment['passing_marks']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active" <?php echo $assessment['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $assessment['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Topics</label>
                        <div id="topics-container">
                            <?php foreach ($assessment['topics'] as $topic): ?>
                            <div class="topic-item">
                                <input type="text" name="topics[]" class="form-control" value="<?php echo $topic; ?>" required>
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeTopic(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addTopic()">
                            <i class="fas fa-plus"></i>
                            Add Topic
                        </button>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Assessment
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
        function addTopic() {
            const container = document.getElementById('topics-container');
            const topicItem = document.createElement('div');
            topicItem.className = 'topic-item';
            topicItem.innerHTML = `
                <input type="text" name="topics[]" class="form-control" placeholder="Enter topic" required>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeTopic(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(topicItem);
        }

        function removeTopic(button) {
            const topicItem = button.parentElement;
            topicItem.remove();
        }
    </script>
</body>
</html> 