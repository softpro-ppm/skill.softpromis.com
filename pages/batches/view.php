<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get batch ID from URL
$batch_id = $_GET['id'] ?? '';

// TODO: Fetch batch details from database
$batch = [
    'id' => $batch_id,
    'name' => 'Web Development Batch 1',
    'program' => 'Web Development',
    'center' => 'Tech Solutions HQ',
    'start_date' => '01 Jan 2024',
    'end_date' => '30 Jun 2024',
    'schedule' => 'Monday to Friday, 9 AM to 1 PM',
    'instructor' => 'Sarah Wilson',
    'capacity' => 20,
    'status' => 'Active',
    'remarks' => 'Regular batch with good attendance.'
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

// TODO: Fetch instructor details
$instructor = [
    'id' => 'I001',
    'name' => 'Sarah Wilson',
    'email' => 'sarah@example.com',
    'phone' => '+1 234 567 8901',
    'specialization' => 'Web Development',
    'experience' => '5 years'
];

// TODO: Fetch students list
$students = [
    [
        'id' => 'S001',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '+1 234 567 8902',
        'status' => 'Active'
    ],
    [
        'id' => 'S002',
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'phone' => '+1 234 567 8903',
        'status' => 'Active'
    ],
    [
        'id' => 'S003',
        'name' => 'Mike Johnson',
        'email' => 'mike@example.com',
        'phone' => '+1 234 567 8904',
        'status' => 'Active'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Details - Softpro Skill Solutions</title>
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
                <h1>Batch Details</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                    <a href="edit.php?id=<?php echo $batch_id; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Batch
                    </a>
                </div>
            </div>

            <!-- Batch Details -->
            <div class="batch-details">
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
                            <label>Status</label>
                            <span class="badge badge-success"><?php echo $batch['status']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Remarks</h3>
                    <p><?php echo $batch['remarks']; ?></p>
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
                    <h3>Instructor Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Instructor ID</label>
                            <span><?php echo $instructor['id']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Name</label>
                            <span><?php echo $instructor['name']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Email</label>
                            <span><?php echo $instructor['email']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Phone</label>
                            <span><?php echo $instructor['phone']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Specialization</label>
                            <span><?php echo $instructor['specialization']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Experience</label>
                            <span><?php echo $instructor['experience']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Enrolled Students</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $student['id']; ?></td>
                                    <td><?php echo $student['name']; ?></td>
                                    <td><?php echo $student['email']; ?></td>
                                    <td><?php echo $student['phone']; ?></td>
                                    <td><span class="badge badge-success"><?php echo $student['status']; ?></span></td>
                                    <td>
                                        <a href="../students/view.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-info">
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
        </div>
    </main>

    <script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html> 