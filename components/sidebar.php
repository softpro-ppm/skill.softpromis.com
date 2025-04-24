<?php
// Sidebar Component
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <ul class="sidebar-menu">
        <li class="menu-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>index.php">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="menu-item <?php echo $current_page == 'list.php' && strpos($_SERVER['PHP_SELF'], 'training-partners') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/training-partners/list.php">
                <i class="fas fa-building"></i>
                <span>Training Partners</span>
            </a>
        </li>
        <li class="menu-item <?php echo $current_page == 'list.php' && strpos($_SERVER['PHP_SELF'], 'training-centers') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/training-centers/list.php">
                <i class="fas fa-school"></i>
                <span>Training Centers</span>
            </a>
        </li>
        <li class="menu-item <?php echo $current_page == 'list.php' && strpos($_SERVER['PHP_SELF'], 'training-programs') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/training-programs/list.php">
                <i class="fas fa-graduation-cap"></i>
                <span>Training Programs</span>
            </a>
        </li>
        <li class="menu-item <?php echo $current_page == 'list.php' && strpos($_SERVER['PHP_SELF'], 'students') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/students/list.php">
                <i class="fas fa-users"></i>
                <span>Students</span>
            </a>
        </li>
        <li class="menu-item <?php echo $current_page == 'list.php' && strpos($_SERVER['PHP_SELF'], 'batches') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/batches/list.php">
                <i class="fas fa-layer-group"></i>
                <span>Batches</span>
            </a>
        </li>
        <li class="menu-item <?php echo $current_page == 'list.php' && strpos($_SERVER['PHP_SELF'], 'enrollments') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/enrollments/list.php">
                <i class="fas fa-user-graduate"></i>
                <span>Enrollments</span>
            </a>
        </li>
        <li class="menu-item <?php echo $current_page == 'list.php' && strpos($_SERVER['PHP_SELF'], 'attendance') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/attendance/list.php">
                <i class="fas fa-calendar-check"></i>
                <span>Attendance</span>
            </a>
        </li>
        <li class="menu-item <?php echo $current_page == 'list.php' && strpos($_SERVER['PHP_SELF'], 'assessments') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/assessments/list.php">
                <i class="fas fa-clipboard-list"></i>
                <span>Assessments</span>
            </a>
        </li>
        <li class="menu-item <?php echo $current_page == 'list.php' && strpos($_SERVER['PHP_SELF'], 'results') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/results/list.php">
                <i class="fas fa-chart-bar"></i>
                <span>Results</span>
            </a>
        </li>
        <li class="menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'reports') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>pages/reports/dashboard.php">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
        </li>
    </ul>
</div> 