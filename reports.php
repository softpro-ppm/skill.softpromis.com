<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Softpro Skill Solutions - Reports</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
  <!-- daterangepicker -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="index.php" role="button">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </nav>

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
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
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
            <a href="reports.php" class="nav-link active">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>Reports</p>
            </a>
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
    </div>
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Reports</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Reports</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Report Type Selection -->
        <div class="row mb-3">
          <div class="col-12">
            <div class="btn-group">
              <button type="button" class="btn btn-primary active" data-report="student">Student Reports</button>
              <button type="button" class="btn btn-info" data-report="batch">Batch Reports</button>
              <button type="button" class="btn btn-success" data-report="financial">Financial Reports</button>
              <button type="button" class="btn btn-warning" data-report="certificate">Certificate Reports</button>
            </div>
          </div>
        </div>

        <!-- Filters Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Filter Options</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <form id="reportFilters">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Training Partner</label>
                    <select class="form-control select2" name="trainingPartner">
                      <option value="">All Partners</option>
                      <option value="1">Softpro Skill Solutions</option>
                      <option value="2">Partner 2</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Training Center</label>
                    <select class="form-control select2" name="trainingCenter">
                      <option value="">All Centers</option>
                      <option value="1">Center 1</option>
                      <option value="2">Center 2</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Scheme</label>
                    <select class="form-control select2" name="scheme">
                      <option value="">All Schemes</option>
                      <option value="1">PMKVY</option>
                      <option value="2">DDU-GKY</option>
                      <option value="3">NSDC</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Course</label>
                    <select class="form-control select2" name="course">
                      <option value="">All Courses</option>
                      <option value="1">Web Development</option>
                      <option value="2">Data Science</option>
                      <option value="3">Digital Marketing</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Batch</label>
                    <select class="form-control select2" name="batch">
                      <option value="">All Batches</option>
                      <option value="1">Batch 2024-01</option>
                      <option value="2">Batch 2024-02</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Date Range</label>
                    <input type="text" class="form-control" id="dateRange" name="dateRange">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Result Status</label>
                    <select class="form-control select2" name="resultStatus">
                      <option value="">All Status</option>
                      <option value="pass">Pass</option>
                      <option value="fail">Fail</option>
                      <option value="pending">Pending</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Payment Status</label>
                    <select class="form-control select2" name="paymentStatus">
                      <option value="">All Status</option>
                      <option value="paid">Paid</option>
                      <option value="pending">Pending</option>
                      <option value="partial">Partial</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <button type="submit" class="btn btn-primary">Apply Filters</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Results Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Report Results</h3>
            <div class="card-tools">
              <div class="btn-group">
                <button type="button" class="btn btn-success" id="exportExcel">
                  <i class="fas fa-file-excel"></i> Export to Excel
                </button>
                <button type="button" class="btn btn-danger" id="exportPDF">
                  <i class="fas fa-file-pdf"></i> Export to PDF
                </button>
                <button type="button" class="btn btn-info" id="printReport">
                  <i class="fas fa-print"></i> Print
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table id="reportTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Student ID</th>
                  <th>Name</th>
                  <th>Course</th>
                  <th>Batch</th>
                  <th>Training Center</th>
                  <th>Scheme</th>
                  <th>Result</th>
                  <th>Payment</th>
                  <th>Certificate</th>
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
                  <td>Center 1</td>
                  <td>PMKVY</td>
                  <td><span class="badge badge-success">Pass</span></td>
                  <td><span class="badge badge-success">Paid</span></td>
                  <td><span class="badge badge-info">Issued</span></td>
                  <td>
                    <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-success"><i class="fas fa-file-download"></i></button>
                  </td>
                </tr>
              </tbody>
            </table>
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
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {
  // Initialize Select2
  $('.select2').select2({
    theme: 'bootstrap4'
  });

  // Initialize DateRangePicker
  $('#dateRange').daterangepicker({
    opens: 'left',
    locale: {
      format: 'YYYY-MM-DD'
    }
  });

  // Initialize DataTable
  const table = $('#reportTable').DataTable({
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'excel',
        text: '<i class="fas fa-file-excel"></i> Export to Excel',
        className: 'btn btn-success',
        exportOptions: {
          columns: ':not(:last-child)'
        }
      },
      {
        extend: 'pdf',
        text: '<i class="fas fa-file-pdf"></i> Export to PDF',
        className: 'btn btn-danger',
        exportOptions: {
          columns: ':not(:last-child)'
        }
      },
      {
        extend: 'print',
        text: '<i class="fas fa-print"></i> Print',
        className: 'btn btn-info',
        exportOptions: {
          columns: ':not(:last-child)'
        }
      }
    ],
    responsive: true,
    lengthChange: true,
    autoWidth: false,
    pageLength: 10,
    order: [[0, 'asc']]
  });

  // Report Type Switching
  $('.btn-group button[data-report]').click(function() {
    $('.btn-group button[data-report]').removeClass('active');
    $(this).addClass('active');
    
    const reportType = $(this).data('report');
    // Update table columns based on report type
    updateTableColumns(reportType);
  });

  // Form Submission
  $('#reportFilters').on('submit', function(e) {
    e.preventDefault();
    // Apply filters to the table
    applyFilters();
  });

  // Export Buttons
  $('#exportExcel').click(function() {
    table.button('.buttons-excel').trigger();
  });

  $('#exportPDF').click(function() {
    table.button('.buttons-pdf').trigger();
  });

  $('#printReport').click(function() {
    table.button('.buttons-print').trigger();
  });

  // Helper Functions
  function updateTableColumns(reportType) {
    // Update table columns based on report type
    // This would be implemented based on your specific requirements
  }

  function applyFilters() {
    // Apply filters to the table
    // This would be implemented based on your specific requirements
    toastr.success('Filters applied successfully');
  }
});
</script>
</body>
</html> 
