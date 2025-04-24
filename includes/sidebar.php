<?php
// Ensure this file is included, not accessed directly
defined('BASEPATH') or exit('No direct script access allowed');
?>

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