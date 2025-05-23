<?php
// filepath: managing-programs.php
// Main landing page for Manage Programs

session_start();
require_once 'config.php';
require_once 'crud_functions.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Manage Programs';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>
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
        <li class="nav-item<?php if(in_array($currentPage, ['training-partners.php','training-centers.php','schemes.php','sectors.php','courses.php','batches.php','managing-programs.php'])) echo ' menu-open'; ?>">
          <a href="#" class="nav-link<?php if(in_array($currentPage, ['training-partners.php','training-centers.php','schemes.php','sectors.php','courses.php','batches.php','managing-programs.php'])) echo ' active'; ?>">
            <i class="nav-icon fas fa-cogs"></i>
            <p>
              Manage Programs
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="managing-programs.php" class="nav-link<?php if($currentPage == 'managing-programs.php') echo ' active'; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Overview</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="training-partners.php" class="nav-link<?php if($currentPage == 'training-partners.php') echo ' active'; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Training Partners</p>
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
        <!-- ...existing code for Students, Reports, User Management ... -->
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manage Programs</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-4 col-12 mb-3">
                                    <a href="training-partners.php" class="btn btn-outline-primary w-100 py-4">
                                        <i class="fas fa-users fa-2x mb-2"></i><br>Training Partners
                                    </a>
                                </div>
                                <div class="col-md-4 col-12 mb-3">
                                    <a href="training-centers.php" class="btn btn-outline-primary w-100 py-4">
                                        <i class="fas fa-building fa-2x mb-2"></i><br>Training Centers
                                    </a>
                                </div>
                                <div class="col-md-4 col-12 mb-3">
                                    <a href="schemes.php" class="btn btn-outline-primary w-100 py-4">
                                        <i class="fas fa-sitemap fa-2x mb-2"></i><br>Schemes
                                    </a>
                                </div>
                                <div class="col-md-4 col-12 mb-3">
                                    <a href="sectors.php" class="btn btn-outline-primary w-100 py-4">
                                        <i class="fas fa-industry fa-2x mb-2"></i><br>Sectors
                                    </a>
                                </div>
                                <div class="col-md-4 col-12 mb-3">
                                    <a href="courses.php" class="btn btn-outline-primary w-100 py-4">
                                        <i class="fas fa-book fa-2x mb-2"></i><br>Courses
                                    </a>
                                </div>
                                <div class="col-md-4 col-12 mb-3">
                                    <a href="batches.php" class="btn btn-outline-primary w-100 py-4">
                                        <i class="fas fa-layer-group fa-2x mb-2"></i><br>Batches
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include 'includes/footer.php'; ?>
