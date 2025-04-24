<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Softpro Skill Solutions - Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top">
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
      <li class="nav-item">
        <a class="nav-link" href="logout.php" role="button">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

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
        <div class="info">
          <a href="#" class="d-block">Admin User</a>
          <small class="text-muted">Administrator</small>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-building"></i>
              <p>
                Training Partners
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="training-partners.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Manage Partners</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="training-centers.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Training Centers</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-graduation-cap"></i>
              <p>
                Training Programs
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="schemes.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Schemes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="sectors.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Sectors</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="courses.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Courses</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="batches.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Batches</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Students
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="students.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Manage Students</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="fees.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Fee Management</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="assessments.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Assessments</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="certificates.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Certificates</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>
                Reports
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="reports.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>View Reports</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>
                User Management
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="roles.php" class="nav-link">
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

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Role-based Dashboard Selection -->
        <div class="row mb-3">
          <div class="col-12">
            <div class="btn-group">
              <button type="button" class="btn btn-primary active" data-role="admin">Admin View</button>
              <button type="button" class="btn btn-info" data-role="reception">Reception View</button>
              <button type="button" class="btn btn-success" data-role="tc">Training Center View</button>
            </div>
          </div>
        </div>

        <!-- Admin Dashboard -->
        <div class="dashboard-view" id="admin-dashboard">
          <!-- Info boxes -->
          <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Students</span>
                  <span class="info-box-number">1,500</span>
                  <span class="info-box-text">+15% from last month</span>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Payments</span>
                  <span class="info-box-number">₹25,00,000</span>
                  <span class="info-box-text">+20% from last month</span>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Pending Payments</span>
                  <span class="info-box-number">₹5,00,000</span>
                  <span class="info-box-text">-5% from last month</span>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-certificate"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Certificates Issued</span>
                  <span class="info-box-number">850</span>
                  <span class="info-box-text">+25% from last month</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Charts Row -->
          <div class="row">
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Monthly Fee Collection</h3>
                </div>
                <div class="card-body">
                  <canvas id="feeCollectionChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Students by Scheme</h3>
                </div>
                <div class="card-body">
                  <canvas id="schemeDistributionChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Course Popularity</h3>
                </div>
                <div class="card-body">
                  <canvas id="coursePopularityChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Govt vs Paid Students</h3>
                </div>
                <div class="card-body">
                  <canvas id="studentTypeChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Notifications Section -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Recent Notifications</h3>
                </div>
                <div class="card-body p-0">
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                      <i class="fas fa-exclamation-circle text-warning"></i>
                      <span class="ml-2">5 pending batch approvals</span>
                      <a href="#" class="float-right">View</a>
                    </li>
                    <li class="list-group-item">
                      <i class="fas fa-money-bill-wave text-danger"></i>
                      <span class="ml-2">10 students with pending fees</span>
                      <a href="#" class="float-right">View</a>
                    </li>
                    <li class="list-group-item">
                      <i class="fas fa-clock text-info"></i>
                      <span class="ml-2">3 batches starting next week</span>
                      <a href="#" class="float-right">View</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Reception Dashboard -->
        <div class="dashboard-view" id="reception-dashboard" style="display: none;">
          <!-- Reception specific content -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Assigned Training Center Students</h3>
                </div>
                <div class="card-body">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Batch</th>
                        <th>Fee Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Sample data -->
                      <tr>
                        <td>ST001</td>
                        <td>John Doe</td>
                        <td>Web Development</td>
                        <td>Batch 2024-01</td>
                        <td><span class="badge badge-success">Paid</span></td>
                        <td>
                          <button class="btn btn-sm btn-info">View</button>
                          <button class="btn btn-sm btn-primary">Edit</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Training Center Dashboard -->
        <div class="dashboard-view" id="tc-dashboard" style="display: none;">
          <!-- Training Center specific content -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Assigned Batches</h3>
                </div>
                <div class="card-body">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Batch ID</th>
                        <th>Course</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Students</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Sample data -->
                      <tr>
                        <td>B001</td>
                        <td>Web Development</td>
                        <td>2024-01-01</td>
                        <td>2024-03-31</td>
                        <td>25</td>
                        <td>
                          <button class="btn btn-sm btn-info">View</button>
                          <button class="btn btn-sm btn-success">Upload Results</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<!-- Required Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

<script>
// Dashboard View Switching
$(document).ready(function() {
  $('.btn-group button').click(function() {
    $('.btn-group button').removeClass('active');
    $(this).addClass('active');
    
    const role = $(this).data('role');
    $('.dashboard-view').hide();
    $(`#${role}-dashboard`).show();
  });

  // Initialize Charts
  // Monthly Fee Collection Chart
  const feeCollectionCtx = document.getElementById('feeCollectionChart').getContext('2d');
  new Chart(feeCollectionCtx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      datasets: [{
        label: 'Fee Collection',
        data: [1200000, 1500000, 1800000, 1600000, 2000000, 2500000],
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1
      }]
    }
  });

  // Scheme Distribution Chart
  const schemeDistributionCtx = document.getElementById('schemeDistributionChart').getContext('2d');
  new Chart(schemeDistributionCtx, {
    type: 'pie',
    data: {
      labels: ['PMKVY', 'DDU-GKY', 'NSDC', 'Others'],
      datasets: [{
        data: [40, 30, 20, 10],
        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
      }]
    }
  });

  // Course Popularity Chart
  const coursePopularityCtx = document.getElementById('coursePopularityChart').getContext('2d');
  new Chart(coursePopularityCtx, {
    type: 'bar',
    data: {
      labels: ['Web Dev', 'Data Science', 'Digital Marketing', 'UI/UX', 'Mobile Dev'],
      datasets: [{
        label: 'Students Enrolled',
        data: [300, 250, 200, 150, 100],
        backgroundColor: 'rgba(54, 162, 235, 0.5)'
      }]
    }
  });

  // Student Type Chart
  const studentTypeCtx = document.getElementById('studentTypeChart').getContext('2d');
  new Chart(studentTypeCtx, {
    type: 'doughnut',
    data: {
      labels: ['Government', 'Paid'],
      datasets: [{
        data: [60, 40],
        backgroundColor: ['#FF6384', '#36A2EB']
      }]
    }
  });
});
</script>
</body>
</html> 
