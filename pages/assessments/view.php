<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get assessment ID from URL
$assessment_id = $_GET['id'] ?? '';

// TODO: Fetch assessment details from database
$assessment = [
    'id' => $assessment_id,
    'title' => 'Web Development Basics',
    'program' => 'Web Development',
    'description' => 'Assessment covering basic web development concepts',
    'duration' => '2 hours',
    'total_marks' => 100,
    'passing_marks' => 60,
    'status' => 'Active',
    'topics' => [
        'HTML Basics',
        'CSS Fundamentals',
        'JavaScript Introduction'
    ]
];

// TODO: Fetch program details
$program = [
    'id' => 'P001',
    'name' => 'Web Development',
    'description' => 'Comprehensive web development training program',
    'duration' => '6 months',
    'total_batches' => 5,
    'active_students' => 150
];

// TODO: Fetch assessment statistics
$stats = [
    'total_attempts' => 45,
    'average_score' => 75,
    'passing_rate' => 80,
    'highest_score' => 95,
    'lowest_score' => 45
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Details - Softpro Skill Solutions</title>
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
                <h1>Assessment Details</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                    <a href="edit.php?id=<?php echo $assessment_id; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Assessment
                    </a>
                </div>
            </div>

            <!-- Assessment Details -->
            <div class="assessment-details">
                <div class="detail-card">
                    <h3>Assessment Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Assessment ID</label>
                            <span><?php echo $assessment['id']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Title</label>
                            <span><?php echo $assessment['title']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Program</label>
                            <span><?php echo $assessment['program']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Duration</label>
                            <span><?php echo $assessment['duration']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Total Marks</label>
                            <span><?php echo $assessment['total_marks']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Passing Marks</label>
                            <span><?php echo $assessment['passing_marks']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Status</label>
                            <span class="badge badge-success"><?php echo $assessment['status']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Description</h3>
                    <p><?php echo $assessment['description']; ?></p>
                </div>

                <div class="detail-card">
                    <h3>Topics Covered</h3>
                    <ul class="topics-list">
                        <?php foreach ($assessment['topics'] as $topic): ?>
                        <li><?php echo $topic; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="detail-card">
                    <h3>Program Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Program ID</label>
                            <span><?php echo $program['id']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Name</label>
                            <span><?php echo $program['name']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Description</label>
                            <span><?php echo $program['description']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Duration</label>
                            <span><?php echo $program['duration']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Total Batches</label>
                            <span><?php echo $program['total_batches']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Active Students</label>
                            <span><?php echo $program['active_students']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Assessment Statistics</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['total_attempts']; ?></div>
                            <div class="stat-label">Total Attempts</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['average_score']; ?>%</div>
                            <div class="stat-label">Average Score</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['passing_rate']; ?>%</div>
                            <div class="stat-label">Passing Rate</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['highest_score']; ?>%</div>
                            <div class="stat-label">Highest Score</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['lowest_score']; ?>%</div>
                            <div class="stat-label">Lowest Score</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html> 