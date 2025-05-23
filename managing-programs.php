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
