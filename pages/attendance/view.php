<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get attendance ID from URL
$attendance_id = $_GET['id'] ?? '';

// TODO: Fetch attendance details from database
$attendance = [
    'id' => $attendance_id,
    'batch' => 'Web Development Batch 1',
    'date' => '15 Jan 2024',
    'status' => 'Completed',
    'remarks' => 'Regular attendance with good participation.'
];

// TODO: Fetch batch details
$batch = [
    'id' => 'B001',
    'name' => 'Web Development Batch 1',
    'program' => 'Web Development',
    'center' => 'Tech Solutions HQ',
    'instructor' => 'Sarah Wilson',
    'schedule' => 'Monday to Friday, 9 AM to 1 PM'
];

// TODO: Fetch attendance statistics
$stats = [
    'total_students' => 15,
    'present' => 12,
    'absent' => 3,
    'late' => 0,
    'attendance_rate' => 80
];

// TODO: Fetch students attendance details
$students = [
    [
        'id' => 'S001',
        'name' => 'John Doe',
        'status' => 'Present',
        'remarks' => 'On time'
    ],
    [
        'id' => 'S002',
        'name' => 'Jane Smith',
        'status' => 'Present',
        'remarks' => 'On time'
    ],
    [
        'id' => 'S003',
        'name' => 'Mike Johnson',
        'status' => 'Absent',
        'remarks' => 'Sick leave'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Details - Softpro Skill Solutions</title>
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
                <h1>Attendance Details</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                    <a href="edit.php?id=<?php echo $attendance_id; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Attendance
                    </a>
                </div>
            </div>

            <!-- Attendance Details -->
            <div class="attendance-details">
                <div class="detail-card">
                    <h3>Attendance Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Attendance ID</label>
                            <span><?php echo $attendance['id']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Batch</label>
                            <span><?php echo $attendance['batch']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Date</label>
                            <span><?php echo $attendance['date']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Status</label>
                            <span class="badge badge-success"><?php echo $attendance['status']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Remarks</h3>
                    <p><?php echo $attendance['remarks']; ?></p>
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
                            <label>Program</label>
                            <span><?php echo $batch['program']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Center</label>
                            <span><?php echo $batch['center']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Instructor</label>
                            <span><?php echo $batch['instructor']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Schedule</label>
                            <span><?php echo $batch['schedule']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Attendance Statistics</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['total_students']; ?></div>
                            <div class="stat-label">Total Students</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['present']; ?></div>
                            <div class="stat-label">Present</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['absent']; ?></div>
                            <div class="stat-label">Absent</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['late']; ?></div>
                            <div class="stat-label">Late</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['attendance_rate']; ?>%</div>
                            <div class="stat-label">Attendance Rate</div>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
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
                                    <td><span class="badge badge-<?php echo $student['status'] === 'Present' ? 'success' : 'danger'; ?>"><?php echo $student['status']; ?></span></td>
                                    <td><?php echo $student['remarks']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html> 