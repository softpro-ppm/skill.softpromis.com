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
                      <th>User Type</th>
                      <th>Description</th>
                      <th>Users</th>
                      <th>Created Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                  require_once 'crud_functions.php';
                  $roles = Role::getAll();
                  if ($roles) {
                    foreach ($roles as $role) {
                      echo '<tr>';
                      echo '<td>' . htmlspecialchars($role['role_name']) . '</td>';
                      echo '<td>' . htmlspecialchars(ucfirst($role['role_name'])) . '</td>';
                      echo '<td>' . htmlspecialchars($role['description']) . '</td>';
                      echo '<td>' . (int)$role['user_count'] . '</td>';
                      echo '<td>' . htmlspecialchars(substr($role['created_at'], 0, 10)) . '</td>';
                      echo '<td>';
                      echo '<button class="btn btn-sm btn-info edit-role" data-id="' . $role['role_id'] . '"><i class="fas fa-edit"></i></button> ';
                      echo '<button class="btn btn-sm btn-danger delete-role" data-id="' . $role['role_id'] . '"><i class="fas fa-trash"></i></button>';
                      echo '</td>';
                      echo '</tr>';
                    }
                  } else {
                    echo '<tr><td colspan="6">No roles found.</td></tr>';
                  }
                  ?>
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
    // Initialize DataTable with default configuration
    var rolesTable = $('#rolesTable').DataTable();

    // Helper to reload roles table (optional, for future use)
    function reloadRolesTable() {
      $.post('inc/ajax/roles_ajax.php', {action: 'list'}, function(res) {
        if (res.success) {
          rolesTable.clear();
          res.data.forEach(function(role) {
            rolesTable.row.add([
              $('<div>').text(role.role_name).html(),
              $('<div>').text(role.role_name.charAt(0).toUpperCase() + role.role_name.slice(1)).html(),
              $('<div>').text(role.description).html(),
              role.user_count,
              role.created_at ? role.created_at.substr(0, 10) : '',
              '<button class="btn btn-sm btn-info edit-role" data-id="' + role.role_id + '"><i class="fas fa-edit"></i></button> ' +
              '<button class="btn btn-sm btn-danger delete-role" data-id="' + role.role_id + '"><i class="fas fa-trash"></i></button>'
            ]);
          });
          rolesTable.draw();
        }
      }, 'json');
    }

    // Edit Role
    $('#rolesTable').on('click', '.edit-role', function() {
      const roleId = $(this).data('id');
      // Fetch role details including permissions
      $.get('inc/ajax/roles_ajax.php', {action: 'get', roleId: roleId}, function(res) {
        if (res.success) {
          const role = res.data;
          $('#roleModalLabel').text('Edit Role');
          $('#roleId').val(role.role_id);
          $('#roleName').val(role.role_name);
          $('#roleDescription').val(role.description);
          // Uncheck all permissions first
          $('input[name="permissions[]"]').prop('checked', false);
          // Check permissions from DB
          if (Array.isArray(role.permissions)) {
            role.permissions.forEach(function(perm) {
              $('input[name="permissions[]"][value="' + perm + '"]').prop('checked', true);
            });
          }
          $('#roleModal').modal('show');
        } else {
          toastr.error(res.message || 'Failed to load role');
        }
      }, 'json');
    });

    // Delete Role
    $('#rolesTable').on('click', '.delete-role', function() {
      const btn = $(this);
      const roleId = btn.data('id');
      const roleName = btn.closest('tr').find('td:first').text();

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
          $.post('inc/ajax/roles_ajax.php', {action: 'delete', roleId: roleId}, function(res) {
            if (res.success) {
              rolesTable.row(btn.closest('tr')).remove().draw();
              toastr.success('Role deleted successfully');
            } else {
              toastr.error(res.message || 'Failed to delete role');
            }
          }, 'json');
        }
      });
    });

    // Save Role (Add/Edit)
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

      const submitBtn = $(this);
      const originalText = submitBtn.html();
      submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

      const action = roleId ? 'edit' : 'add';
      const postData = {
        action: action,
        roleId: roleId,
        roleName: roleName,
        roleDescription: description,
        'permissions[]': permissions
      };
      $.post('inc/ajax/roles_ajax.php', postData, function(res) {
        if (res.success) {
          if (action === 'add') {
            // Add new row to DataTable
            const role = res.data;
            rolesTable.row.add([
              $('<div>').text(role.role_name).html(),
              $('<div>').text(role.role_name.charAt(0).toUpperCase() + role.role_name.slice(1)).html(),
              $('<div>').text(role.description).html(),
              role.user_count,
              role.created_at ? role.created_at.substr(0, 10) : '',
              '<button class="btn btn-sm btn-info edit-role" data-id="' + role.role_id + '"><i class="fas fa-edit"></i></button> ' +
              '<button class="btn btn-sm btn-danger delete-role" data-id="' + role.role_id + '"><i class="fas fa-trash"></i></button>'
            ]).draw(false);
            toastr.success('Role created successfully');
          } else {
            // Update existing row
            const row = $(`button.edit-role[data-id="${roleId}"]`).closest('tr');
            rolesTable.row(row).data([
              $('<div>').text(roleName).html(),
              $('<div>').text(roleName.charAt(0).toUpperCase() + roleName.slice(1)).html(),
              $('<div>').text(description).html(),
              row.find('td:eq(3)').text(), // user count
              row.find('td:eq(4)').text(), // created date
              '<button class="btn btn-sm btn-info edit-role" data-id="' + roleId + '"><i class="fas fa-edit"></i></button> ' +
              '<button class="btn btn-sm btn-danger delete-role" data-id="' + roleId + '"><i class="fas fa-trash"></i></button>'
            ]).draw(false);
            toastr.success('Role updated successfully');
          }
          form[0].reset();
          $('#roleId').val('');
          $('#roleModal').modal('hide');
        } else {
          toastr.error(res.message || 'Failed to save role');
        }
        submitBtn.html(originalText).prop('disabled', false);
      }, 'json');
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
