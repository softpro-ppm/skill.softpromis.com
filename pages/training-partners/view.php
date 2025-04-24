<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get partner ID from URL
$partner_id = $_GET['id'] ?? '';

// TODO: Fetch partner details from database
$partner = [
    'id' => $partner_id,
    'name' => 'Tech Solutions Inc.',
    'type' => 'Corporate',
    'contact_person' => 'John Smith',
    'email' => 'john@techsolutions.com',
    'phone' => '+1 234 567 8900',
    'address' => '123 Tech Street, Silicon Valley, CA 94043',
    'status' => 'Active',
    'centers' => 3,
    'students' => 150,
    'programs' => 5
];

// TODO: Fetch partner statistics
$stats = [
    'total_centers' => 3,
    'active_centers' => 2,
    'total_students' => 150,
    'active_students' => 120,
    'completed_students' => 30,
    'average_attendance' => 92,
    'average_performance' => 85
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $partner['name']; ?> - Softpro Skill Solutions</title>
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
                <h1><?php echo $partner['name']; ?></h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                    <a href="edit.php?id=<?php echo $partner_id; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Partner
                    </a>
                </div>
            </div>

            <!-- Partner Details -->
            <div class="partner-details">
                <div class="detail-card">
                    <h3>Partner Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Partner ID</label>
                            <span><?php echo $partner['id']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Type</label>
                            <span><?php echo $partner['type']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Contact Person</label>
                            <span><?php echo $partner['contact_person']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Email</label>
                            <span><?php echo $partner['email']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Phone</label>
                            <span><?php echo $partner['phone']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Status</label>
                            <span class="badge badge-success"><?php echo $partner['status']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <h3>Address</h3>
                    <p><?php echo $partner['address']; ?></p>
                </div>

                <div class="detail-card">
                    <h3>Partner Statistics</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['total_centers']; ?></div>
                            <div class="stat-label">Total Centers</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['active_centers']; ?></div>
                            <div class="stat-label">Active Centers</div>
                        </div>
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

            <!-- Training Centers -->
            <div class="partner-centers">
                <h2>Training Centers</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Center ID</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>TC001</td>
                                <td>Tech Solutions HQ</td>
                                <td>Silicon Valley, CA</td>
                                <td>50</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <a href="../training-centers/view.php?id=TC001" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>TC002</td>
                                <td>Tech Solutions East</td>
                                <td>New York, NY</td>
                                <td>40</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <a href="../training-centers/view.php?id=TC002" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>TC003</td>
                                <td>Tech Solutions West</td>
                                <td>Seattle, WA</td>
                                <td>30</td>
                                <td><span class="badge badge-warning">Inactive</span></td>
                                <td>
                                    <a href="../training-centers/view.php?id=TC003" class="btn btn-sm btn-info">
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