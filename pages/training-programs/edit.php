<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get program ID from URL
$program_id = $_GET['id'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Add form validation and database update
    $program_name = $_POST['program_name'] ?? '';
    $category = $_POST['category'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $description = $_POST['description'] ?? '';
    $status = $_POST['status'] ?? 'active';
    
    // Simulate successful update
    header('Location: list.php?success=2');
    exit;
}

// TODO: Fetch program details from database
$program = [
    'id' => $program_id,
    'name' => 'Web Development Bootcamp',
    'category' => 'web',
    'duration' => 6,
    'description' => 'Comprehensive web development training program covering front-end and back-end technologies.',
    'status' => 'active'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Training Program - Softpro Skill Solutions</title>
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
                <h1>Edit Training Program</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                </div>
            </div>

            <!-- Edit Program Form -->
            <div class="form-container">
                <form method="POST" action="edit.php?id=<?php echo $program_id; ?>" class="form">
                    <div class="form-group">
                        <label for="program_name">Program Name</label>
                        <input type="text" id="program_name" name="program_name" class="form-control" value="<?php echo $program['name']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <option value="web" <?php echo $program['category'] === 'web' ? 'selected' : ''; ?>>Web Development</option>
                            <option value="mobile" <?php echo $program['category'] === 'mobile' ? 'selected' : ''; ?>>Mobile Development</option>
                            <option value="data" <?php echo $program['category'] === 'data' ? 'selected' : ''; ?>>Data Science</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="duration">Duration (months)</label>
                        <input type="number" id="duration" name="duration" class="form-control" min="1" max="12" value="<?php echo $program['duration']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4" required><?php echo $program['description']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active" <?php echo $program['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $program['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Program
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