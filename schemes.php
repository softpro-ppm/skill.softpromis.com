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
$pageTitle = 'Schemes';

// Include header
require_once 'includes/header.php';

// Include sidebar
require_once 'includes/sidebar.php';

// Fetch schemes from DB
$schemes = [];
try {
  $pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );
  $stmt = $pdo->query('SELECT * FROM schemes ORDER BY created_at DESC');
  $schemes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  echo '<div class="alert alert-danger">Could not fetch schemes: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Schemes</h1>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Schemes List -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Schemes List</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSchemeModal">
                <i class="fas fa-plus"></i> Add New Scheme
              </button>
            </div>
          </div>
          <div class="card-body">
            <table id="schemesTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Scheme Name</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Created At</th>
                  <th>Updated At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php $serial = 1; foreach ($schemes as $scheme): ?>
                  <tr>
                    <td><?= $serial++ ?></td>
                    <td><?= htmlspecialchars($scheme['scheme_name']) ?></td>
                    <td><?= htmlspecialchars($scheme['description']) ?></td>
                    <td>
                      <?php if ($scheme['status'] === 'active'): ?>
                        <span class="badge badge-success">Active</span>
                      <?php else: ?>
                        <span class="badge badge-secondary">Inactive</span>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($scheme['created_at']) ?></td>
                    <td><?= htmlspecialchars($scheme['updated_at']) ?></td>
                    <td>
                      <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info view-scheme-btn" data-scheme-id="<?= $scheme['scheme_id'] ?>">
                          <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-primary edit-scheme-btn" data-scheme-id="<?= $scheme['scheme_id'] ?>">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-scheme-btn" data-scheme-id="<?= $scheme['scheme_id'] ?>">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->

  <!-- Add Scheme Modal -->
  <div class="modal fade" id="addSchemeModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Add New Scheme</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addSchemeForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="schemeName">Scheme Name</label>
              <input type="text" class="form-control" id="schemeName" name="scheme_name" placeholder="Enter scheme name" required>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter scheme description"></textarea>
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <select class="form-control" id="status" name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Scheme</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Scheme Modal -->
  <div class="modal fade" id="viewSchemeModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">View Scheme Details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Scheme ID</label>
                <p data-field="scheme_id"></p>
              </div>
              <div class="form-group">
                <label>Scheme Name</label>
                <p data-field="scheme_name"></p>
              </div>
              <div class="form-group">
                <label>Description</label>
                <p data-field="description"></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Status</label>
                <p data-field="status"></p>
              </div>
              <div class="form-group">
                <label>Created At</label>
                <p data-field="created_at"></p>
              </div>
              <div class="form-group">
                <label>Updated At</label>
                <p data-field="updated_at"></p>
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

  <!-- Edit Scheme Modal -->
  <div class="modal fade" id="editSchemeModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Scheme</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editSchemeForm">
          <input type="hidden" id="editSchemeId" name="scheme_id">
          <div class="modal-body">
            <div class="form-group">
              <label for="editSchemeName">Scheme Name</label>
              <input type="text" class="form-control" id="editSchemeName" name="scheme_name" required>
            </div>
            <div class="form-group">
              <label for="editDescription">Description</label>
              <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label for="editStatus">Status</label>
              <select class="form-control" id="editStatus" name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update Scheme</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete Scheme Modal -->
  <div class="modal fade" id="deleteSchemeModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Delete Scheme</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="deleteSchemeId">
          <p>Are you sure you want to delete this scheme? This action cannot be undone.</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteScheme">Delete Scheme</button>
        </div>
      </div>
    </div>
  </div>

<?php include 'includes/js.php'; ?>

<script>
  $(function () {
    // Initialize DataTable
    var table = $('#schemesTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "inc/ajax/schemes.php",
            "type": "GET",
            "data": function(d) {
                d.action = "list";
            },
            "dataSrc": function(json) {
                if (json.status === 'error') {
                    toastr.error(json.message || 'Error loading data');
                    return [];
                }
                return json.data || [];
            }
        },
        "columns": [
            { "data": "scheme_id" },
            { "data": "scheme_name" },
            { "data": "description" },
            { 
                "data": "status",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        var badgeClass = data === 'active' ? 'success' : 'danger';
                        return '<span class="badge badge-' + badgeClass + '">' + 
                               data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                    }
                    return data;
                }
            },
            { "data": "created_at" },
            { "data": "updated_at" },
            { 
                "data": null,
                "orderable": false,
                "searchable": false,
                "render": function(data, type, row) {
                    return '<div class="btn-group btn-group-sm">' +
                           '<button type="button" class="btn btn-info view-scheme" data-id="' + row.scheme_id + '"><i class="fas fa-eye"></i></button>' +
                           '<button type="button" class="btn btn-primary edit-scheme" data-id="' + row.scheme_id + '"><i class="fas fa-edit"></i></button>' +
                           '<button type="button" class="btn btn-danger delete-scheme" data-id="' + row.scheme_id + '" data-name="' + row.scheme_name + '"><i class="fas fa-trash"></i></button>' +
                           '</div>';
                }
            }
        ],
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "order": [[0, 'desc']]
    });

    // View Scheme
    $(document).on('click', '.view-scheme', function() {
        var schemeId = $(this).data('id');
        
        $.ajax({
            url: 'inc/ajax/schemes.php',
            type: 'GET',
            data: {
                action: 'get',
                scheme_id: schemeId
            },
            success: function(response) {
                if(response.status === 'success') {
                    var data = response.data;
                    
                    // Populate view modal
                    $('#view-scheme-name').text(data.scheme_name);
                    $('#view-description').text(data.description);
                    $('#view-status').html('<span class="badge badge-' + (data.status === 'active' ? 'success' : 'danger') + '">' + 
                                         data.status.charAt(0).toUpperCase() + data.status.slice(1) + '</span>');
                    $('#view-created-at').text(data.created_at);
                    $('#view-updated-at').text(data.updated_at);
                    
                    // Show view modal
                    $('#viewSchemeModal').modal('show');
                } else {
                    toastr.error(response.message || 'Error fetching scheme details');
                }
            },
            error: function() {
                toastr.error('Error fetching scheme details');
            }
        });
    });

    // Edit Scheme
    $(document).on('click', '.edit-scheme', function() {
        var schemeId = $(this).data('id');
        
        // Reset form and show modal
        $('#schemeForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('#schemeModal').modal('show');
        
        // Update modal title
        $('.modal-title').text('Edit Scheme');
        
        // Show loading state
        var submitBtn = $('#schemeModal button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        
        $.ajax({
            url: 'inc/ajax/schemes.php',
            type: 'GET',
            data: {
                action: 'get',
                scheme_id: schemeId
            },
            success: function(response) {
                if(response.status === 'success') {
                    var data = response.data;
                    
                    // Set form values
                    $('#scheme_id').val(data.scheme_id);
                    $('#scheme_name').val(data.scheme_name);
                    $('#description').val(data.description);
                    $('#status').val(data.status);
                    
                    // Enable submit button
                    submitBtn.prop('disabled', false).text('Save Changes');
                } else {
                    toastr.error(response.message || 'Error fetching scheme data');
                    $('#schemeModal').modal('hide');
                }
            },
            error: function() {
                toastr.error('Error fetching scheme data');
                $('#schemeModal').modal('hide');
            }
        });
    });

    // Delete Scheme
    $(document).on('click', '.delete-scheme', function() {
        var schemeId = $(this).data('id');
        var schemeName = $(this).data('name');
        
        // Set scheme name in confirmation modal
        $('#delete_scheme_name').text(schemeName);
        
        // Show confirmation modal
        $('#deleteModal').modal('show');
        
        // Handle delete confirmation
        $('#confirmDelete').off('click').on('click', function() {
            $.ajax({
                url: 'inc/ajax/schemes.php',
                type: 'POST',
                data: {
                    action: 'delete',
                    scheme_id: schemeId
                },
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    if(response.status === 'success') {
                        table.ajax.reload();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message || 'Error deleting scheme');
                    }
                },
                error: function() {
                    $('#deleteModal').modal('hide');
                    toastr.error('Error deleting scheme');
                }
            });
        });
    });

    // Handle form submission
    $('#schemeForm').on('submit', function(e) {
        e.preventDefault();
        
        // Basic form validation
        var requiredFields = ['scheme_name', 'description', 'status'];
        var isValid = true;
        
        requiredFields.forEach(function(field) {
            var value = $('#' + field).val();
            if (!value || value.trim() === '') {
                isValid = false;
                $('#' + field).addClass('is-invalid');
                toastr.error(field.replace('_', ' ').toUpperCase() + ' is required');
            } else {
                $('#' + field).removeClass('is-invalid');
            }
        });
        
        if (!isValid) return false;
        
        // Prepare form data
        var formData = new FormData(this);
        var schemeId = $('#scheme_id').val();
        formData.append('action', schemeId ? 'edit' : 'add');
        
        // Disable submit button and show loading state
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.text();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: 'inc/ajax/schemes.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.status === 'success') {
                    $('#schemeModal').modal('hide');
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message || 'Error processing request');
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Error processing request: ' + error);
                console.error('Ajax error:', error);
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
  });
</script>
</body>
</html> 
