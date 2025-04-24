<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Softpro Skill Solutions - Sectors</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
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
        </div>
      </div>

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
            <a href="#" class="nav-link active">
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
                <a href="sectors.php" class="nav-link active">
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
            <h1 class="m-0">Sectors</h1>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Sectors List -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Sectors List</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSectorModal">
                <i class="fas fa-plus"></i> Add New Sector
              </button>
            </div>
          </div>
          <div class="card-body">
            <table id="sectorsTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Sector ID</th>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Total Courses</th>
                  <th>Active Students</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>SEC001</td>
                  <td>Information Technology</td>
                  <td>IT and Software Development</td>
                  <td>5</td>
                  <td>150</td>
                  <td><span class="badge badge-success">Active</span></td>
                  <td>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewSectorModal">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editSectorModal">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteSectorModal">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>SEC002</td>
                  <td>Digital Marketing</td>
                  <td>Online Marketing and SEO</td>
                  <td>3</td>
                  <td>75</td>
                  <td><span class="badge badge-success">Active</span></td>
                  <td>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewSectorModal">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editSectorModal">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteSectorModal">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Add Sector Modal -->
  <div class="modal fade" id="addSectorModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Add New Sector</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="sectorName">Sector Name</label>
                  <input type="text" class="form-control" id="sectorName" placeholder="Enter sector name" required>
                </div>
                <div class="form-group">
                  <label for="sectorCode">Sector Code</label>
                  <input type="text" class="form-control" id="sectorCode" placeholder="Enter sector code" required>
                </div>
                <div class="form-group">
                  <label for="sectorType">Sector Type</label>
                  <select class="form-control select2" id="sectorType" required>
                    <option value="">Select Type</option>
                    <option value="Technical">Technical</option>
                    <option value="Non-Technical">Non-Technical</option>
                    <option value="Vocational">Vocational</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea class="form-control" id="description" rows="3" placeholder="Enter sector description" required></textarea>
                </div>
                <div class="form-group">
                  <label for="jobRoles">Job Roles</label>
                  <textarea class="form-control" id="jobRoles" rows="3" placeholder="Enter potential job roles" required></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Documents</label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="sectorDoc">
                    <label class="custom-file-label" for="sectorDoc">Sector Document</label>
                  </div>
                  <div class="custom-file mt-2">
                    <input type="file" class="custom-file-input" id="curriculumDoc">
                    <label class="custom-file-label" for="curriculumDoc">Curriculum Document</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Sector</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Sector Modal -->
  <div class="modal fade" id="viewSectorModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">View Sector Details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Sector ID</label>
                <p>SEC001</p>
              </div>
              <div class="form-group">
                <label>Sector Name</label>
                <p>Information Technology</p>
              </div>
              <div class="form-group">
                <label>Sector Type</label>
                <p>Technical</p>
              </div>
              <div class="form-group">
                <label>Description</label>
                <p>IT and Software Development</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Job Roles</label>
                <p>Software Developer, Web Developer, Database Administrator</p>
              </div>
              <div class="form-group">
                <label>Total Courses</label>
                <p>5</p>
              </div>
              <div class="form-group">
                <label>Active Students</label>
                <p>150</p>
              </div>
              <div class="form-group">
                <label>Status</label>
                <p><span class="badge badge-success">Active</span></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h5>Associated Courses</h5>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Course ID</th>
                      <th>Name</th>
                      <th>Duration</th>
                      <th>Fee</th>
                      <th>Students</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>C001</td>
                      <td>Web Development</td>
                      <td>3 months</td>
                      <td>₹15,000</td>
                      <td>50</td>
                      <td><span class="badge badge-success">Active</span></td>
                    </tr>
                    <tr>
                      <td>C002</td>
                      <td>Mobile App Development</td>
                      <td>4 months</td>
                      <td>₹20,000</td>
                      <td>35</td>
                      <td><span class="badge badge-success">Active</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Sector Modal -->
  <div class="modal fade" id="editSectorModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Sector</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editSectorName">Sector Name</label>
                  <input type="text" class="form-control" id="editSectorName" value="Information Technology" required>
                </div>
                <div class="form-group">
                  <label for="editSectorCode">Sector Code</label>
                  <input type="text" class="form-control" id="editSectorCode" value="SEC001" required>
                </div>
                <div class="form-group">
                  <label for="editSectorType">Sector Type</label>
                  <select class="form-control select2" id="editSectorType" required>
                    <option value="Technical" selected>Technical</option>
                    <option value="Non-Technical">Non-Technical</option>
                    <option value="Vocational">Vocational</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editDescription">Description</label>
                  <textarea class="form-control" id="editDescription" rows="3" required>IT and Software Development</textarea>
                </div>
                <div class="form-group">
                  <label for="editJobRoles">Job Roles</label>
                  <textarea class="form-control" id="editJobRoles" rows="3" required>Software Developer, Web Developer, Database Administrator</textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Documents</label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="editSectorDoc">
                    <label class="custom-file-label" for="editSectorDoc">Sector Document</label>
                  </div>
                  <div class="custom-file mt-2">
                    <input type="file" class="custom-file-input" id="editCurriculumDoc">
                    <label class="custom-file-label" for="editCurriculumDoc">Curriculum Document</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update Sector</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete Sector Modal -->
  <div class="modal fade" id="deleteSectorModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Delete Sector</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this sector? This action cannot be undone.</p>
          <p><strong>Sector:</strong> Information Technology</p>
          <p><strong>Associated Courses:</strong> 5</p>
          <p><strong>Active Students:</strong> 150</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger">Delete Sector</button>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer">
    <strong>Copyright &copy; 2024 <a href="#">Softpro Skill Solutions</a>.</strong>
    All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- bs-custom-file-input -->
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

<script>
  $(function () {
    // Initialize DataTable
    $('#sectorsTable').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true
    });

    // Initialize Select2
    $('.select2').select2({
      theme: 'bootstrap4'
    });

    // Initialize custom file input
    bsCustomFileInput.init();
  });
</script>
</body>
</html> 
