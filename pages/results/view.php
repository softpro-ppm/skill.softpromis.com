<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get result ID from URL
$result_id = $_GET['id'] ?? '';

// TODO: Fetch result details from database
$result = [
    'id' => $result_id,
    'student' => 'John Doe',
    'assessment' => 'Web Development Basics',
    'center' => 'Tech Solutions HQ',
    'score' => 85,
    'date' => '15 Jan 2024',
    'status' => 'Passed',
    'remarks' => 'Good performance in the assessment.'
];

// TODO: Fetch assessment details
$assessment = [
    'id' => 'A001',
    'title' => 'Web Development Basics',
    'description' => 'Assessment covering basic web development concepts',
    'total_marks' => 100,
    'passing_marks' => 60,
    'duration' => '2 hours',
    'topics' => [
        'HTML Basics',
        'CSS Fundamentals',
        'JavaScript Introduction'
    ]
];

// TODO: Fetch student details
$student = [
    'id' => 'S001',
    'name' => 'John Doe',
    'center' => 'Tech Solutions HQ',
    'program' => 'Web Development',
    'batch' => 'Web Development Batch 1'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result Details - Softpro Skill Solutions</title>
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
                <h1>Result Details</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                    <a href="edit.php?id=<?php echo $result_id; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Result
                    </a>
                </div>
            </div>

            <!-- Result Details -->
            <div class="result-details">
                <div class="detail-card">
                    <h3>Result Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Result ID</label>
                            <span><?php echo $result['id']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Student</label>
                            <span><?php echo $result['student']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Assessment</label>
                            <span><?php echo $result['assessment']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Center</label>
                            <span><?php echo $result['center']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Score</label>
                            <span><?php echo $result['score']; ?>%</span>
                        </div>
                        <div class="detail-item">
                            <label>Date</label>
                            <span><?php echo $result['date']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Status</label>
                            <span class="badge badge-success"><?php echo $result['status']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Remarks</h3>
                    <p><?php echo $result['remarks']; ?></p>
                </div>

                <div class="detail-card">
                    <h3>Assessment Details</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Title</label>
                            <span><?php echo $assessment['title']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Description</label>
                            <span><?php echo $assessment['description']; ?></span>
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
                            <label>Duration</label>
                            <span><?php echo $assessment['duration']; ?></span>
                        </div>
                    </div>
                    <div class="topics-list">
                        <h4>Topics Covered</h4>
                        <ul>
                            <?php foreach ($assessment['topics'] as $topic): ?>
                            <li><?php echo $topic; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Student Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Student ID</label>
                            <span><?php echo $student['id']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Name</label>
                            <span><?php echo $student['name']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Center</label>
                            <span><?php echo $student['center']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Program</label>
                            <span><?php echo $student['program']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Batch</label>
                            <span><?php echo $student['batch']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html> 