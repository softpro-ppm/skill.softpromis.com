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
  <div class="modal fade" id="viewSchemeModal" tabindex="-1" role="dialog" aria-labelledby="viewSchemeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="viewSchemeModalLabel">View Scheme Details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Scheme ID:</label>
                <p data-field="scheme_id"></p>
              </div>
              <div class="form-group">
                <label class="font-weight-bold">Scheme Name:</label>
                <p data-field="scheme_name"></p>
              </div>
              <div class="form-group">
                <label class="font-weight-bold">Description:</label>
                <p data-field="description"></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Status:</label>
                <p data-field="status"></p>
              </div>
              <div class="form-group">
                <label class="font-weight-bold">Created At:</label>
                <p data-field="created_at"></p>
              </div>
              <div class="form-group">
                <label class="font-weight-bold">Updated At:</label>
                <p data-field="updated_at"></p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            "url": "inc/ajax/schemes_ajax.php",
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
            { 
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { "data": "scheme_name" },
            { "data": "description" },
            { 
                "data": "status",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return '<span class="badge badge-' + (data === 'active' ? 'success' : 'danger') + '">' + 
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
                           '<button type="button" class="btn btn-danger delete-scheme" data-id="' + row.scheme_id + '"><i class="fas fa-trash"></i></button>' +
                           '</div>';
                }
            }
        ],
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "order": [[0, 'asc']]
    });

    // View Scheme
    $(document).on('click', '.view-scheme-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var schemeId = $(this).data('scheme-id');
        var $modal = $('#viewSchemeModal');
        
        // Show loading state
        $modal.find('.modal-body').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading...</p></div>');
        
        // Show modal
        $modal.modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
        
        // Fetch scheme details
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'GET',
            data: {
                action: 'get',
                scheme_id: schemeId
            },
            success: function(response) {
                if(response.status === 'success' && response.data) {
                    var scheme = response.data;
                    $modal.find('.modal-body').html(`
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Scheme ID:</label>
                                    <p data-field="scheme_id">${scheme.scheme_id || ''}</p>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Scheme Name:</label>
                                    <p data-field="scheme_name">${scheme.scheme_name || ''}</p>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Description:</label>
                                    <p data-field="description">${scheme.description || 'N/A'}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Status:</label>
                                    <p data-field="status"><span class="badge badge-${scheme.status === 'active' ? 'success' : 'danger'}">${scheme.status ? (scheme.status.charAt(0).toUpperCase() + scheme.status.slice(1)) : 'N/A'}</span></p>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Created At:</label>
                                    <p data-field="created_at">${scheme.created_at || 'N/A'}</p>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Updated At:</label>
                                    <p data-field="updated_at">${scheme.updated_at || 'N/A'}</p>
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    $modal.find('.modal-body').html('<div class="alert alert-danger">' + (response.message || 'Error fetching scheme details') + '</div>');
                }
            },
            error: function() {
                $modal.find('.modal-body').html('<div class="alert alert-danger">Error fetching scheme details. Please try again.</div>');
            }
        });
    });

    // Close modal handler
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('.modal-body').html('');
    });

    // Edit Scheme
    $(document).on('click', '.edit-scheme-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var schemeId = $(this).data('scheme-id');
        var $modal = $('#editSchemeModal');
        
        // Reset form and show loading state
        $modal.find('form')[0].reset();
        $modal.find('.is-invalid').removeClass('is-invalid');
        $modal.find('.modal-body .alert').remove(); // Remove any previous alerts
        $modal.find('.modal-body').append('<div class="text-center py-2" id="edit-loading"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
        $modal.modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
        
        // Fetch scheme details
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'GET',
            data: {
                action: 'get',
                scheme_id: schemeId
            },
            success: function(response) {
                $('#edit-loading').remove();
                if(response.status === 'success' && response.data) {
                    var scheme = response.data;
                    $modal.find('#editSchemeId').val(scheme.scheme_id || '');
                    $modal.find('#editSchemeName').val(scheme.scheme_name || '');
                    $modal.find('#editDescription').val(scheme.description || '');
                    $modal.find('#editStatus').val(scheme.status || 'active');
                } else {
                    $modal.find('.modal-body').prepend('<div class="alert alert-danger">' + (response.message || 'Error fetching scheme data') + '</div>');
                }
            },
            error: function() {
                $('#edit-loading').remove();
                $modal.find('.modal-body').prepend('<div class="alert alert-danger">Error fetching scheme data. Please try again.</div>');
            }
        });
    });

    // Edit Scheme Form Submission
    $('#editSchemeForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $modal = $('#editSchemeModal');
        var isValid = true;
        $form.find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        if (!isValid) {
            toastr.error('Please fill in all required fields');
            return false;
        }
        // Submit form
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: $form.serialize() + '&action=edit',
            success: function(response) {
                if(response.status === 'success') {
                    $modal.modal('hide');
                    toastr.success(response.message || 'Scheme updated successfully');
                    $('#schemesTable').DataTable().ajax.reload();
                } else {
                    toastr.error(response.message || 'Error updating scheme');
                }
            },
            error: function() {
                toastr.error('Error updating scheme');
            }
        });
    });

    // Delete Scheme
    $(document).on('click', '.delete-scheme-btn', function() {
        var schemeId = $(this).data('scheme-id');
        
        // Set scheme ID in modal
        $('#deleteSchemeId').val(schemeId);
        
        // Show confirmation modal
        $('#deleteSchemeModal').modal('show');
    });

    // Handle delete confirmation
    $('#confirmDeleteScheme').on('click', function() {
        var schemeId = $('#deleteSchemeId').val();
        
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: {
                action: 'delete',
                scheme_id: schemeId
            },
            success: function(response) {
                $('#deleteSchemeModal').modal('hide');
                if(response.status === 'success') {
                    table.ajax.reload();
                    toastr.success(response.message || 'Scheme deleted successfully');
                } else {
                    toastr.error(response.message || 'Error deleting scheme');
                }
            },
            error: function() {
                $('#deleteSchemeModal').modal('hide');
                toastr.error('Error deleting scheme');
            }
        });
    });

    // Add Scheme Form Submission
    $('#addSchemeForm').on('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        var isValid = true;
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            toastr.error('Please fill in all required fields');
            return false;
        }
        
        // Submit form
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: $(this).serialize() + '&action=add',
            success: function(response) {
                if(response.status === 'success') {
                    $('#addSchemeModal').modal('hide');
                    table.ajax.reload();
                    toastr.success(response.message || 'Scheme added successfully');
                    $('#addSchemeForm')[0].reset();
                } else {
                    toastr.error(response.message || 'Error adding scheme');
                }
            },
            error: function() {
                toastr.error('Error adding scheme');
            }
        });
    });

    // Make sure Bootstrap is properly initialized
    if (typeof $.fn.modal === 'undefined') {
        console.error('Bootstrap modal is not loaded');
    }
    
    // Initialize all modals
    $('.modal').modal({
        show: false
    });
  });
</script>
</body>
</html> 
