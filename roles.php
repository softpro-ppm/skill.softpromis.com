<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Softpro Skill Solutions - Role Management</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .permission-group {
      border: 1px solid #dee2e6;
      border-radius: 4px;
      padding: 15px;
      margin-bottom: 15px;
    }
    .permission-group h5 {
      margin-bottom: 15px;
      color: #495057;
    }
    .permission-item {
      margin-bottom: 10px;
    }
    .permission-item label {
      margin-bottom: 0;
      font-weight: normal;
    }
  </style>
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
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="https://via.placeholder.com/150" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Admin User</a>
          <small class="text-muted">Administrator</small>
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
            <a href="roles.html" class="nav-link active">
              <i class="nav-icon fas fa-user-tag"></i>
              <p>Role Management</p>
            </a>
<<<<<<< Updated upstream:roles.php
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="roles.php" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Roles & Permissions</p>
                </a>
              </li>
            </ul>
=======
>>>>>>> Stashed changes:roles.html
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
            <h1 class="m-0">Role Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Role Management</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Roles</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#roleModal">
                    <i class="fas fa-plus"></i> Add New Role
                  </button>
                </div>
              </div>
              <div class="card-body">
                <table id="rolesTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Role Name</th>
                      <th>Description</th>
                      <th>Users</th>
                      <th>Created Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Administrator</td>
                      <td>Full system access and control</td>
                      <td>5</td>
                      <td>2024-01-01</td>
                      <td>
                        <button class="btn btn-sm btn-info edit-role" data-id="1">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-role" data-id="1">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>Center Manager</td>
                      <td>Manage training center operations</td>
                      <td>8</td>
                      <td>2024-01-01</td>
                      <td>
                        <button class="btn btn-sm btn-info edit-role" data-id="2">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-role" data-id="2">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>Trainer</td>
                      <td>Manage training sessions and students</td>
                      <td>12</td>
                      <td>2024-01-01</td>
                      <td>
                        <button class="btn btn-sm btn-info edit-role" data-id="3">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-role" data-id="3">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<!-- Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="roleModalLabel">Add New Role</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="roleForm">
          <input type="hidden" name="roleId" id="roleId">
          <div class="form-group">
            <label for="roleName">Role Name</label>
            <input type="text" class="form-control" id="roleName" name="roleName" required>
          </div>
          <div class="form-group">
            <label for="roleDescription">Description</label>
            <textarea class="form-control" id="roleDescription" name="roleDescription" rows="3" required></textarea>
          </div>
          
          <h5 class="mt-4">Permissions</h5>
          
          <!-- Dashboard Permissions -->
          <div class="permission-group">
            <h5>Dashboard</h5>
            <div class="permission-item">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="viewDashboard" name="permissions[]" value="view_dashboard">
                <label class="custom-control-label" for="viewDashboard">View Dashboard</label>
              </div>
            </div>
          </div>

          <!-- User Management Permissions -->
          <div class="permission-group">
            <h5>User Management</h5>
            <div class="permission-item">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="viewUsers" name="permissions[]" value="view_users">
                <label class="custom-control-label" for="viewUsers">View Users</label>
              </div>
            </div>
            <div class="permission-item">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="createUsers" name="permissions[]" value="create_users">
                <label class="custom-control-label" for="createUsers">Create Users</label>
              </div>
            </div>
            <div class="permission-item">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="editUsers" name="permissions[]" value="edit_users">
                <label class="custom-control-label" for="editUsers">Edit Users</label>
              </div>
            </div>
            <div class="permission-item">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="deleteUsers" name="permissions[]" value="delete_users">
                <label class="custom-control-label" for="deleteUsers">Delete Users</label>
              </div>
            </div>
          </div>

          <!-- Training Management Permissions -->
          <div class="permission-group">
            <h5>Training Management</h5>
            <div class="permission-item">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="viewTraining" name="permissions[]" value="view_training">
                <label class="custom-control-label" for="viewTraining">View Training</label>
              </div>
            </div>
            <div class="permission-item">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="createTraining" name="permissions[]" value="create_training">
                <label class="custom-control-label" for="createTraining">Create Training</label>
              </div>
            </div>
            <div class="permission-item">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="editTraining" name="permissions[]" value="edit_training">
                <label class="custom-control-label" for="editTraining">Edit Training</label>
              </div>
            </div>
            <div class="permission-item">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="deleteTraining" name="permissions[]" value="delete_training">
                <label class="custom-control-label" for="deleteTraining">Delete Training</label>
              </div>
            </div>
          </div>

          <!-- Reports Permissions -->
          <div class="permission-group">
            <h5>Reports</h5>
            <div class="permission-item">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="viewReports" name="permissions[]" value="view_reports">
                <label class="custom-control-label" for="viewReports">View Reports</label>
              </div>
            </div>
            <div class="permission-item">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="generateReports" name="permissions[]" value="generate_reports">
                <label class="custom-control-label" for="generateReports">Generate Reports</label>
              </div>
            </div>
            <div class="permission-item">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="exportReports" name="permissions[]" value="export_reports">
                <label class="custom-control-label" for="exportReports">Export Reports</label>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveRole">Save Role</button>
      </div>
    </div>
  </div>
</div>

<!-- Required Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {
  // Configure Toastr
  toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: 3000
  };

  // Initialize DataTable
  $('#rolesTable').DataTable({
    responsive: true,
    autoWidth: false
  });

  // Edit Role
  $('.edit-role').click(function() {
    const roleId = $(this).data('id');
    const row = $(this).closest('tr');
    const roleName = row.find('td:first').text();
    const description = row.find('td:eq(1)').text();

    $('#roleModalLabel').text('Edit Role');
    $('#roleId').val(roleId);
    $('#roleName').val(roleName);
    $('#roleDescription').val(description);

    // Simulate loading permissions
    setTimeout(() => {
      // Check all permissions for Administrator role
      if (roleName === 'Administrator') {
        $('input[name="permissions[]"]').prop('checked', true);
      } else if (roleName === 'Center Manager') {
        // Set specific permissions for Center Manager
        $('#viewDashboard, #viewUsers, #viewTraining, #viewReports, #generateReports').prop('checked', true);
      } else if (roleName === 'Trainer') {
        // Set specific permissions for Trainer
        $('#viewDashboard, #viewTraining').prop('checked', true);
      }
    }, 500);

    $('#roleModal').modal('show');
  });

  // Delete Role
  $('.delete-role').click(function() {
    const roleId = $(this).data('id');
    const roleName = $(this).closest('tr').find('td:first').text();

    Swal.fire({
      title: 'Are you sure?',
      text: `Do you want to delete the role "${roleName}"?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        // Simulate server request
        setTimeout(() => {
          $(this).closest('tr').fadeOut(400, function() {
            $(this).remove();
            toastr.success('Role deleted successfully');
          });
        }, 500);
      }
    });
  });

  // Save Role
  $('#saveRole').click(function() {
    const form = $('#roleForm');
    const roleId = $('#roleId').val();
    const roleName = $('#roleName').val();
    const description = $('#roleDescription').val();
    const permissions = $('input[name="permissions[]"]:checked').map(function() {
      return $(this).val();
    }).get();

    if (!roleName || !description) {
      toastr.error('Please fill in all required fields');
      return;
    }

    if (permissions.length === 0) {
      toastr.error('Please select at least one permission');
      return;
    }

    // Show loading state
    const submitBtn = $(this);
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    // Simulate server request
    setTimeout(() => {
      if (roleId) {
        // Update existing role
        const row = $(`button[data-id="${roleId}"]`).closest('tr');
        row.find('td:first').text(roleName);
        row.find('td:eq(1)').text(description);
        toastr.success('Role updated successfully');
      } else {
        // Add new role
        const newRow = `
          <tr>
            <td>${roleName}</td>
            <td>${description}</td>
            <td>0</td>
            <td>${new Date().toISOString().split('T')[0]}</td>
            <td>
              <button class="btn btn-sm btn-info edit-role" data-id="${Date.now()}">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-danger delete-role" data-id="${Date.now()}">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        `;
        $('#rolesTable tbody').prepend(newRow);
        toastr.success('Role created successfully');
      }

      // Reset form and close modal
      form[0].reset();
      $('#roleId').val('');
      $('#roleModal').modal('hide');
      submitBtn.html(originalText).prop('disabled', false);
    }, 1000);
  });

  // Reset form when modal is closed
  $('#roleModal').on('hidden.bs.modal', function() {
    $('#roleForm')[0].reset();
    $('#roleId').val('');
    $('#roleModalLabel').text('Add New Role');
  });
});
</script>
</body>
</html> 
