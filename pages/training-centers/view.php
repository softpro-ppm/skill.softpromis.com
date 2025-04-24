<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get center ID from URL
$center_id = $_GET['id'] ?? '';

// TODO: Fetch center details from database
$center = [
    'id' => $center_id,
    'name' => 'Tech Solutions HQ',
    'partner' => 'Tech Solutions Inc.',
    'location' => 'Silicon Valley, CA',
    'address' => '123 Tech Street, Silicon Valley, CA 94043',
    'contact_person' => 'John Smith',
    'email' => 'john@techsolutions.com',
    'phone' => '+1 234 567 8900',
    'capacity' => 50,
    'status' => 'Active',
    'students' => 45,
    'batches' => 3,
    'programs' => 5
];

// TODO: Fetch center statistics
$stats = [
    'total_students' => 45,
    'active_students' => 40,
    'completed_students' => 5,
    'average_attendance' => 92,
    'average_performance' => 85,
    'capacity_utilization' => 90
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $center['name']; ?> - Softpro Skill Solutions</title>
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
                <h1><?php echo $center['name']; ?></h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                    <a href="edit.php?id=<?php echo $center_id; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Center
                    </a>
                </div>
            </div>

            <!-- Center Details -->
            <div class="center-details">
                <div class="detail-card">
                    <h3>Center Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Center ID</label>
                            <span><?php echo $center['id']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Partner</label>
                            <span><?php echo $center['partner']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Location</label>
                            <span><?php echo $center['location']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Contact Person</label>
                            <span><?php echo $center['contact_person']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Email</label>
                            <span><?php echo $center['email']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Phone</label>
                            <span><?php echo $center['phone']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Capacity</label>
                            <span><?php echo $center['capacity']; ?> students</span>
                        </div>
                        <div class="detail-item">
                            <label>Status</label>
                            <span class="badge badge-success"><?php echo $center['status']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Address</h3>
                    <p><?php echo $center['address']; ?></p>
                </div>

                <div class="detail-card">
                    <h3>Center Statistics</h3>
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
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['capacity_utilization']; ?>%</div>
                            <div class="stat-label">Capacity Utilization</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Center Batches -->
            <div class="center-batches">
                <h2>Center Batches</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Batch ID</th>
                                <th>Program</th>
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
                                <td>Web Development</td>
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
                                <td>Mobile Development</td>
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
                                <td>Data Science</td>
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