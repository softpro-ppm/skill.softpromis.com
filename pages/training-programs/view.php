<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get program ID from URL
$program_id = $_GET['id'] ?? '';

// TODO: Fetch program details from database
$program = [
    'id' => $program_id,
    'name' => 'Web Development Bootcamp',
    'category' => 'Web Development',
    'duration' => 6,
    'description' => 'Comprehensive web development training program covering front-end and back-end technologies.',
    'status' => 'Active',
    'students' => 45,
    'batches' => 3,
    'start_date' => '2024-01-01',
    'end_date' => '2024-06-30'
];

// TODO: Fetch program statistics
$stats = [
    'total_students' => 45,
    'active_students' => 40,
    'completed_students' => 5,
    'average_attendance' => 92,
    'average_performance' => 85
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $program['name']; ?> - Softpro Skill Solutions</title>
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
                <h1><?php echo $program['name']; ?></h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                    <a href="edit.php?id=<?php echo $program_id; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Program
                    </a>
                </div>
            </div>

            <!-- Program Details -->
            <div class="program-details">
                <div class="detail-card">
                    <h3>Program Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Program ID</label>
                            <span><?php echo $program['id']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Category</label>
                            <span><?php echo $program['category']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Duration</label>
                            <span><?php echo $program['duration']; ?> months</span>
                        </div>
                        <div class="detail-item">
                            <label>Status</label>
                            <span class="badge badge-success"><?php echo $program['status']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Start Date</label>
                            <span><?php echo date('d M Y', strtotime($program['start_date'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>End Date</label>
                            <span><?php echo date('d M Y', strtotime($program['end_date'])); ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Description</h3>
                    <p><?php echo $program['description']; ?></p>
                </div>

                <div class="detail-card">
                    <h3>Program Statistics</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['total_students']; ?></div>
                            <div class="stat-label">Total Students</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['active_students']; ?></div>
                            <div class="stat-label">Active Students</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['completed_students']; ?></div>
                            <div class="stat-label">Completed Students</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['average_attendance']; ?>%</div>
                            <div class="stat-label">Average Attendance</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['average_performance']; ?>%</div>
                            <div class="stat-label">Average Performance</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Program Batches -->
            <div class="program-batches">
                <h2>Program Batches</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Batch ID</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>B001</td>
                                <td>01 Jan 2024</td>
                                <td>30 Jun 2024</td>
                                <td>15</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <a href="../batches/view.php?id=B001" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>B002</td>
                                <td>01 Mar 2024</td>
                                <td>31 Aug 2024</td>
                                <td>20</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <a href="../batches/view.php?id=B002" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>B003</td>
                                <td>01 Jul 2024</td>
                                <td>31 Dec 2024</td>
                                <td>10</td>
                                <td><span class="badge badge-warning">Upcoming</span></td>
                                <td>
                                    <a href="../batches/view.php?id=B003" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html> 