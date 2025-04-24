<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get student ID from URL
$student_id = $_GET['id'] ?? '';

// TODO: Fetch student details from database
$student = [
    'id' => $student_id,
    'name' => 'John Doe',
    'center' => 'Tech Solutions HQ',
    'program' => 'Web Development',
    'batch' => 'Web Development Batch 1',
    'email' => 'john@example.com',
    'phone' => '+1 234 567 8900',
    'address' => '123 Main St, City, State 12345',
    'status' => 'Active',
    'enrollment_date' => '01 Jan 2024',
    'completion_date' => '30 Jun 2024'
];

// TODO: Fetch student statistics
$stats = [
    'attendance' => 92,
    'performance' => 85,
    'assessments' => 8,
    'certifications' => 2
];

// TODO: Fetch student assessments
$assessments = [
    [
        'id' => 'A001',
        'title' => 'Web Development Basics',
        'date' => '15 Jan 2024',
        'score' => 85,
        'status' => 'Completed'
    ],
    [
        'id' => 'A002',
        'title' => 'HTML & CSS',
        'date' => '30 Jan 2024',
        'score' => 90,
        'status' => 'Completed'
    ],
    [
        'id' => 'A003',
        'title' => 'JavaScript Fundamentals',
        'date' => '15 Feb 2024',
        'score' => 80,
        'status' => 'Completed'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $student['name']; ?> - Softpro Skill Solutions</title>
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
                <h1><?php echo $student['name']; ?></h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                    <a href="edit.php?id=<?php echo $student_id; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Student
                    </a>
                </div>
            </div>

            <!-- Student Details -->
            <div class="student-details">
                <div class="detail-card">
                    <h3>Student Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Student ID</label>
                            <span><?php echo $student['id']; ?></span>
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
                        <div class="detail-item">
                            <label>Email</label>
                            <span><?php echo $student['email']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Phone</label>
                            <span><?php echo $student['phone']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Status</label>
                            <span class="badge badge-success"><?php echo $student['status']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Enrollment Date</label>
                            <span><?php echo $student['enrollment_date']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Address</h3>
                    <p><?php echo $student['address']; ?></p>
                </div>

                <div class="detail-card">
                    <h3>Student Statistics</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['attendance']; ?>%</div>
                            <div class="stat-label">Attendance</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['performance']; ?>%</div>
                            <div class="stat-label">Performance</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['assessments']; ?></div>
                            <div class="stat-label">Assessments</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['certifications']; ?></div>
                            <div class="stat-label">Certifications</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Assessments -->
            <div class="student-assessments">
                <h2>Student Assessments</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Assessment ID</th>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Score</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assessments as $assessment): ?>
                            <tr>
                                <td><?php echo $assessment['id']; ?></td>
                                <td><?php echo $assessment['title']; ?></td>
                                <td><?php echo $assessment['date']; ?></td>
                                <td><?php echo $assessment['score']; ?>%</td>
                                <td><span class="badge badge-success"><?php echo $assessment['status']; ?></span></td>
                                <td>
                                    <a href="../assessments/view.php?id=<?php echo $assessment['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html> 