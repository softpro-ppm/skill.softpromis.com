<?php $currentPage = basename($_SERVER['SCRIPT_NAME']); ?>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="dashboard.php" class="brand-link">
      <span class="brand-text font-weight-light">Softpro Skill Solutions</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item<?php if($currentPage == 'dashboard.php') echo ' menu-open'; ?>">
            <a href="dashboard.php" class="nav-link<?php if($currentPage == 'dashboard.php') echo ' active'; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item<?php if(in_array($currentPage, ['training-partners.php','training-centers.php','schemes.php','sectors.php','courses.php','batches.php'])) echo ' menu-open'; ?>">
            <a href="#" class="nav-link<?php if(in_array($currentPage, ['training-partners.php','training-centers.php','schemes.php','sectors.php','courses.php','batches.php'])) echo ' active'; ?>">
              <i class="nav-icon fas fa-cogs"></i>
              <p>
                Manage Programs
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="training-partners.php" class="nav-link<?php if($currentPage == 'training-partners.php') echo ' active'; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Manage Partners</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="training-centers.php" class="nav-link<?php if($currentPage == 'training-centers.php') echo ' active'; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Training Centers</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="schemes.php" class="nav-link<?php if($currentPage == 'schemes.php') echo ' active'; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Schemes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="sectors.php" class="nav-link<?php if($currentPage == 'sectors.php') echo ' active'; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Sectors</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="courses.php" class="nav-link<?php if($currentPage == 'courses.php') echo ' active'; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Courses</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="batches.php" class="nav-link<?php if($currentPage == 'batches.php') echo ' active'; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Batches</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item<?php if(in_array($currentPage, ['students.php','fees.php','assessments.php','certificates.php'])) echo ' menu-open'; ?>">
            <a href="#" class="nav-link<?php if(in_array($currentPage, ['students.php','fees.php','assessments.php','certificates.php'])) echo ' active'; ?>">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Students
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="students.php" class="nav-link<?php if($currentPage == 'students.php') echo ' active'; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Manage Students</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="fees.php" class="nav-link<?php if($currentPage == 'fees.php') echo ' active'; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Fee Management</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="assessments.php" class="nav-link<?php if($currentPage == 'assessments.php') echo ' active'; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Assessments</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="certificates.php" class="nav-link<?php if($currentPage == 'certificates.php') echo ' active'; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Certificates</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item<?php if($currentPage == 'reports.php') echo ' menu-open'; ?>">
            <a href="#" class="nav-link<?php if($currentPage == 'reports.php') echo ' active'; ?>">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>
                Reports
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="reports.php" class="nav-link<?php if($currentPage == 'reports.php') echo ' active'; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>View Reports</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item<?php if($currentPage == 'roles.php') echo ' menu-open'; ?>">
            <a href="#" class="nav-link<?php if($currentPage == 'roles.php') echo ' active'; ?>">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>
                User Management
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="roles.php" class="nav-link<?php if($currentPage == 'roles.php') echo ' active'; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Roles & Permissions</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>