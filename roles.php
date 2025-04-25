<?php
// Define BASEPATH constant
define('BASEPATH', true);

// Start session and include required files
session_start();
require_once 'config.php';
require_once 'crud_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Set page title
$pageTitle = 'Roles & Permissions';

// Include header
require_once 'includes/header.php';

// Include sidebar
require_once 'includes/sidebar.php';
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Roles & Permissions</h1>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

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
  </div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->

  <!-- Add Role Modal -->
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

<?php include 'includes/js.php'; ?>

<script>
  $(function () {
    // Initialize DataTable
    $('#rolesTable').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "language": {
        "search": "_INPUT_",
        "searchPlaceholder": "Enter search term..."
      },
      "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });

    // Initialize checkbox iCheck
    $('input[type="checkbox"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
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
