<?php
// Sidebar Component
$current_path = $_SERVER['PHP_SELF'];
$current_page = basename($current_path);
$current_dir = basename(dirname($current_path));

// Function to check if current path matches a section
function isActiveSection($section) {
    global $current_path;
    return strpos($current_path, '/pages/' . $section . '/') !== false;
}

// Function to check if current path is dashboard
function isDashboard() {
    global $current_path;
    return $current_path === '/index.php' || $current_path === '/pages/index.php';
}
?>
<div class="sidebar">
    <!-- Quick Search -->
    <div class="sidebar-search">
        <div class="search-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="Quick navigation..." class="search-input">
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <button class="quick-action-btn" data-action="new-report">
            <i class="fas fa-plus"></i>
            <span>New Report</span>
        </button>
        <button class="quick-action-btn" data-action="export">
            <i class="fas fa-download"></i>
            <span>Export</span>
        </button>
    </div>

    <!-- Main Menu -->
    <ul class="sidebar-menu">
        <li class="menu-item <?php echo isDashboard() ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>index.php">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="menu-item <?php echo isActiveSection('training-partners') ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/training-partners/list.php">
                <i class="fas fa-building"></i>
                <span>Training Partners</span>
            </a>
        </li>
        <li class="menu-item <?php echo isActiveSection('training-centers') ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/training-centers/list.php">
                <i class="fas fa-school"></i>
                <span>Training Centers</span>
            </a>
        </li>
        <li class="menu-item <?php echo isActiveSection('training-programs') ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/training-programs/list.php">
                <i class="fas fa-graduation-cap"></i>
                <span>Training Programs</span>
            </a>
        </li>
        <li class="menu-item <?php echo isActiveSection('students') ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/students/list.php">
                <i class="fas fa-users"></i>
                <span>Students</span>
            </a>
        </li>
        <li class="menu-item <?php echo isActiveSection('batches') ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/batches/list.php">
                <i class="fas fa-layer-group"></i>
                <span>Batches</span>
            </a>
        </li>
        <li class="menu-item <?php echo isActiveSection('enrollments') ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/enrollments/list.php">
                <i class="fas fa-user-graduate"></i>
                <span>Enrollments</span>
            </a>
        </li>
        <li class="menu-item <?php echo isActiveSection('attendance') ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/attendance/list.php">
                <i class="fas fa-calendar-check"></i>
                <span>Attendance</span>
            </a>
        </li>
        <li class="menu-item <?php echo isActiveSection('assessments') ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/assessments/list.php">
                <i class="fas fa-clipboard-list"></i>
                <span>Assessments</span>
            </a>
        </li>
        <li class="menu-item <?php echo isActiveSection('results') ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/results/list.php">
                <i class="fas fa-chart-bar"></i>
                <span>Results</span>
            </a>
        </li>
        <li class="menu-item has-submenu <?php echo isActiveSection('reports') ? 'open' : ''; ?>">
            <a href="#" class="submenu-toggle">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
                <i class="fas fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="<?php echo $base_url; ?>pages/reports/enrollment.php"><i class="fas fa-users"></i> Student Enrollment</a></li>
                <li><a href="<?php echo $base_url; ?>pages/reports/attendance.php"><i class="fas fa-clipboard-check"></i> Attendance Report</a></li>
                <li><a href="<?php echo $base_url; ?>pages/reports/performance.php"><i class="fas fa-chart-line"></i> Performance Report</a></li>
                <li><a href="<?php echo $base_url; ?>pages/reports/financial.php"><i class="fas fa-dollar-sign"></i> Financial Report</a></li>
                <li><a href="<?php echo $base_url; ?>pages/reports/training-center.php"><i class="fas fa-school"></i> Training Center Report</a></li>
                <li><a href="<?php echo $base_url; ?>pages/reports/student-progress.php"><i class="fas fa-graduation-cap"></i> Student Progress</a></li>
                <li><a href="<?php echo $base_url; ?>pages/reports/training-partner.php"><i class="fas fa-handshake"></i> Training Partner Report</a></li>
                <li><a href="<?php echo $base_url; ?>pages/reports/program.php"><i class="fas fa-book"></i> Program Effectiveness</a></li>
                <li><a href="<?php echo $base_url; ?>pages/reports/financial-analysis.php"><i class="fas fa-chart-pie"></i> Financial Analysis</a></li>
            </ul>
        </li>
    </ul>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="collapse-sidebar">
            <button class="collapse-btn">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>
    </div>
</div>