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

// Get counts for dashboard cards
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $tpCount = $pdo->query("SELECT COUNT(*) FROM training_partners")->fetchColumn();
    $tcCount = $pdo->query("SELECT COUNT(*) FROM training_centers")->fetchColumn();
    $schemeCount = $pdo->query("SELECT COUNT(*) FROM schemes")->fetchColumn();
    $sectorCount = $pdo->query("SELECT COUNT(*) FROM sectors")->fetchColumn();
    $courseCount = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $batchCount = $pdo->query("SELECT COUNT(*) FROM batches")->fetchColumn();
} catch (Exception $e) {
    $tpCount = $tcCount = $schemeCount = $sectorCount = $courseCount = $batchCount = 0;
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
                    <div class="row g-4">
                        <div class="col-md-4 col-12 mb-3">
                            <a href="training-partners.php" class="text-decoration-none">
                                <div class="card shadow h-100 border-0 bg-gradient-primary text-white" style="background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-users fa-3x mb-2"></i>
                                        <h5 class="card-title">Training Partners</h5>
                                        <div class="display-4 fw-bold"><?= $tpCount ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 col-12 mb-3">
                            <a href="training-centers.php" class="text-decoration-none">
                                <div class="card shadow h-100 border-0 bg-gradient-info text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-building fa-3x mb-2"></i>
                                        <h5 class="card-title">Training Centers</h5>
                                        <div class="display-4 fw-bold"><?= $tcCount ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 col-12 mb-3">
                            <a href="schemes.php" class="text-decoration-none">
                                <div class="card shadow h-100 border-0 bg-gradient-success text-white" style="background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-sitemap fa-3x mb-2"></i>
                                        <h5 class="card-title">Schemes</h5>
                                        <div class="display-4 fw-bold"><?= $schemeCount ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 col-12 mb-3">
                            <a href="sectors.php" class="text-decoration-none">
                                <div class="card shadow h-100 border-0 bg-gradient-warning text-white" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-industry fa-3x mb-2"></i>
                                        <h5 class="card-title">Sectors</h5>
                                        <div class="display-4 fw-bold"><?= $sectorCount ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 col-12 mb-3">
                            <a href="courses.php" class="text-decoration-none">
                                <div class="card shadow h-100 border-0 bg-gradient-danger text-white" style="background: linear-gradient(135deg, #ff5858 0%, #f09819 100%);">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-book fa-3x mb-2"></i>
                                        <h5 class="card-title">Courses</h5>
                                        <div class="display-4 fw-bold"><?= $courseCount ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 col-12 mb-3">
                            <a href="batches.php" class="text-decoration-none">
                                <div class="card shadow h-100 border-0 bg-gradient-secondary text-white" style="background: linear-gradient(135deg, #7f53ac 0%, #647dee 100%);">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-layer-group fa-3x mb-2"></i>
                                        <h5 class="card-title">Batches</h5>
                                        <div class="display-4 fw-bold"><?= $batchCount ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include 'includes/footer.php'; ?>
