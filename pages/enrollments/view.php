<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get enrollment ID from URL
$enrollment_id = $_GET['id'] ?? '';

// TODO: Fetch enrollment details from database
$enrollment = [
    'id' => $enrollment_id,
    'student' => 'John Doe',
    'program' => 'Web Development',
    'center' => 'Tech Solutions HQ',
    'batch' => 'Web Development Batch 1',
    'start_date' => '01 Jan 2024',
    'end_date' => '30 Jun 2024',
    'status' => 'Active',
    'remarks' => 'Regular student with good attendance.'
];

// TODO: Fetch student details
$student = [
    'id' => 'S001',
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+1 234 567 8900',
    'address' => '123 Main St, City, State 12345'
];

// TODO: Fetch program details
$program = [
    'id' => 'P001',
    'name' => 'Web Development',
    'description' => 'Comprehensive web development program covering front-end and back-end technologies',
    'duration' => '6 months',
    'modules' => [
        'HTML & CSS',
        'JavaScript',
        'React.js',
        'Node.js',
        'Database Management',
        'Project Work'
    ]
];

// TODO: Fetch batch details
$batch = [
    'id' => 'B001',
    'name' => 'Web Development Batch 1',
    'start_date' => '01 Jan 2024',
    'end_date' => '30 Jun 2024',
    'schedule' => 'Monday to Friday, 9 AM to 1 PM',
    'instructor' => 'Sarah Wilson'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Details - Softpro Skill Solutions</title>
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
                <h1>Enrollment Details</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                    <a href="edit.php?id=<?php echo $enrollment_id; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Enrollment
                    </a>
                </div>
            </div>

            <!-- Enrollment Details -->
            <div class="enrollment-details">
                <div class="detail-card">
                    <h3>Enrollment Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Enrollment ID</label>
                            <span><?php echo $enrollment['id']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Student</label>
                            <span><?php echo $enrollment['student']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Program</label>
                            <span><?php echo $enrollment['program']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Center</label>
                            <span><?php echo $enrollment['center']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Batch</label>
                            <span><?php echo $enrollment['batch']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Start Date</label>
                            <span><?php echo $enrollment['start_date']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>End Date</label>
                            <span><?php echo $enrollment['end_date']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Status</label>
                            <span class="badge badge-success"><?php echo $enrollment['status']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Remarks</h3>
                    <p><?php echo $enrollment['remarks']; ?></p>
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
                            <label>Email</label>
                            <span><?php echo $student['email']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Phone</label>
                            <span><?php echo $student['phone']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Address</label>
                            <span><?php echo $student['address']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Program Details</h3>
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
                    </div>
                    <div class="modules-list">
                        <h4>Program Modules</h4>
                        <ul>
                            <?php foreach ($program['modules'] as $module): ?>
                            <li><?php echo $module; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Batch Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Batch ID</label>
                            <span><?php echo $batch['id']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Name</label>
                            <span><?php echo $batch['name']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Start Date</label>
                            <span><?php echo $batch['start_date']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>End Date</label>
                            <span><?php echo $batch['end_date']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Schedule</label>
                            <span><?php echo $batch['schedule']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Instructor</label>
                            <span><?php echo $batch['instructor']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html> 