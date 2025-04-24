<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Softpro Skill Solutions - <?php echo $pageTitle ?? 'Dashboard'; ?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">3 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> 4 new messages
                        <span class="float-right text-muted text-sm">3 mins</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-users mr-2"></i> 8 new students
                        <span class="float-right text-muted text-sm">12 hours</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-file mr-2"></i> 3 new reports
                        <span class="float-right text-muted text-sm">2 days</span>
                    </a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle"></i>
                    <span class="ml-1"><?php echo htmlspecialchars($user['name']); ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="profile.php">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form action="logout.php" method="POST" id="logoutForm">
                        <input type="hidden" name="logout" value="1">
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="dashboard.php" class="brand-link">
            <span class="brand-text font-weight-light">Softpro Skill Solutions</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="assets/img/user.png" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="profile.php" class="d-block"><?php echo htmlspecialchars($user['name']); ?></a>
                    <small class="text-muted"><?php echo htmlspecialchars($user['role']); ?></small>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- Training Management -->
                    <?php if (in_array($user['role'], ['admin', 'trainer'])): ?>
                    <li class="nav-item <?php echo in_array($currentPage, ['training-partners', 'training-centers']) ? 'menu-open' : ''; ?>">
                        <a href="#" class="nav-link <?php echo in_array($currentPage, ['training-partners', 'training-centers']) ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-chalkboard-teacher"></i>
                            <p>
                                Training Management
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if ($user['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a href="training-partners.php" class="nav-link <?php echo $currentPage === 'training-partners' ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-handshake"></i>
                                    <p>Training Partners</p>
                                </a>
                            </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a href="training-centers.php" class="nav-link <?php echo $currentPage === 'training-centers' ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-building"></i>
                                    <p>Training Centers</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Course Management -->
                    <?php if (in_array($user['role'], ['admin', 'trainer'])): ?>
                    <li class="nav-item <?php echo in_array($currentPage, ['courses', 'sectors', 'schemes']) ? 'menu-open' : ''; ?>">
                        <a href="#" class="nav-link <?php echo in_array($currentPage, ['courses', 'sectors', 'schemes']) ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>
                                Course Management
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="courses.php" class="nav-link <?php echo $currentPage === 'courses' ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-book"></i>
                                    <p>Courses</p>
                                </a>
                            </li>
                            <?php if ($user['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a href="sectors.php" class="nav-link <?php echo $currentPage === 'sectors' ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-industry"></i>
                                    <p>Sectors</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="schemes.php" class="nav-link <?php echo $currentPage === 'schemes' ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-project-diagram"></i>
                                    <p>Schemes</p>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Batch Management -->
                    <?php if (in_array($user['role'], ['admin', 'trainer'])): ?>
                    <li class="nav-item">
                        <a href="batches.php" class="nav-link <?php echo $currentPage === 'batches' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-users-class"></i>
                            <p>Batch Management</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- Student Management -->
                    <li class="nav-item <?php echo in_array($currentPage, ['students', 'enrollments', 'fees']) ? 'menu-open' : ''; ?>">
                        <a href="#" class="nav-link <?php echo in_array($currentPage, ['students', 'enrollments', 'fees']) ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-user-graduate"></i>
                            <p>
                                Student Management
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="students.php" class="nav-link <?php echo $currentPage === 'students' ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Students</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="enrollments.php" class="nav-link <?php echo $currentPage === 'enrollments' ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-user-plus"></i>
                                    <p>Enrollments</p>
                                </a>
                            </li>
                            <?php if (in_array($user['role'], ['admin', 'trainer'])): ?>
                            <li class="nav-item">
                                <a href="fees.php" class="nav-link <?php echo $currentPage === 'fees' ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-money-bill"></i>
                                    <p>Fee Management</p>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <!-- Assessment Management -->
                    <?php if (in_array($user['role'], ['admin', 'assessor'])): ?>
                    <li class="nav-item <?php echo in_array($currentPage, ['assessments', 'certificates']) ? 'menu-open' : ''; ?>">
                        <a href="#" class="nav-link <?php echo in_array($currentPage, ['assessments', 'certificates']) ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>
                                Assessment
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="assessments.php" class="nav-link <?php echo $currentPage === 'assessments' ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-clipboard-check"></i>
                                    <p>Assessments</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="certificates.php" class="nav-link <?php echo $currentPage === 'certificates' ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-certificate"></i>
                                    <p>Certificates</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Reports -->
                    <?php if (in_array($user['role'], ['admin', 'trainer', 'assessor'])): ?>
                    <li class="nav-item">
                        <a href="reports.php" class="nav-link <?php echo $currentPage === 'reports' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Reports</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- Settings -->
                    <?php if ($user['role'] === 'admin'): ?>
                    <li class="nav-item <?php echo in_array($currentPage, ['users', 'roles', 'settings']) ? 'menu-open' : ''; ?>">
                        <a href="#" class="nav-link <?php echo in_array($currentPage, ['users', 'roles', 'settings']) ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Settings
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="users.php" class="nav-link <?php echo $currentPage === 'users' ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-users-cog"></i>
                                    <p>User Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="roles.php" class="nav-link <?php echo $currentPage === 'roles' ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-user-tag"></i>
                                    <p>Role Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="settings.php" class="nav-link <?php echo $currentPage === 'settings' ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-wrench"></i>
                                    <p>System Settings</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <?php if (isset($pageTitle)): ?>
                            <li class="breadcrumb-item active"><?php echo $pageTitle; ?></li>
                            <?php endif; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
</body>
</html> 