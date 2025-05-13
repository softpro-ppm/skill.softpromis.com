<?php
if (!defined('BASEPATH')) {
    define('BASEPATH', true);
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSchemeModal">
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
                  <th>Training Center</th>
                  <th>Status</th>
                  <th>Created At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- DataTables will populate this -->
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
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="addSchemeForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="center_id">Training Center</label>
              <select class="form-control" id="center_id" name="center_id" required>
                <option value="">Select Training Center</option>
                <?php foreach (TrainingCenter::getAll() as $center): ?>
                  <option value="<?= htmlspecialchars($center['center_id']) ?>"><?= htmlspecialchars($center['center_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
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
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
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
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <label class="font-weight-bold">Training Center:</label>
                <p data-field="center_name"></p>
              </div>
              <div class="form-group">
                <label class="font-weight-bold">Description:</label>
                <p data-field="description"></p>
              </div>
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Scheme Modal -->
  <div class="modal fade" id="editSchemeModal" tabindex="-1" aria-labelledby="editSchemeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="editSchemeModalLabel">Edit Scheme</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editSchemeForm" autocomplete="off">
          <input type="hidden" id="editSchemeId" name="scheme_id">
          <div class="modal-body">
            <div class="form-group mb-3">
              <label for="edit_center_id">Training Center</label>
              <select class="form-control" id="edit_center_id" name="center_id" required>
                <option value="">Select Training Center</option>
                <?php foreach (TrainingCenter::getAll() as $center): ?>
                  <option value="<?= htmlspecialchars($center['center_id']) ?>"><?= htmlspecialchars($center['center_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group mb-3">
              <label for="editSchemeName">Scheme Name</label>
              <input type="text" class="form-control" id="editSchemeName" name="scheme_name" required>
            </div>
            <div class="form-group mb-3">
              <label for="editDescription">Description</label>
              <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
            </div>
            <div class="form-group mb-3">
              <label for="editStatus">Status</label>
              <select class="form-control" id="editStatus" name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="deleteSchemeId">
          <p>Are you sure you want to delete this scheme? This action cannot be undone.</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteScheme">Delete Scheme</button>
        </div>
      </div>
    </div>
  </div>

<?php include 'includes/js.php'; ?>

<script>
// Map center_id to center_name for DataTable rendering
var centerMap = {};
<?php foreach (TrainingCenter::getAll() as $center): ?>
centerMap['<?= htmlspecialchars($center['center_id']) ?>'] = '<?= htmlspecialchars($center['center_name']) ?>';
<?php endforeach; ?>

$(function () {
    // DataTable initialization
    var table = $('#schemesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'inc/ajax/schemes_ajax.php',
            type: 'GET',
            data: function (d) { d.action = 'list'; },
            dataSrc: function (json) { return json.data || []; }
        },
        columns: [
            { data: null, render: function (data, type, row, meta) { return meta.row + 1; } },
            { data: 'scheme_name' },
            { data: 'description' },
            { data: 'center_id', render: function(data, type, row) {
                return centerMap[data] || '';
            }},
            { data: 'status', render: function (data) { return '<span class="badge badge-' + (data === 'active' ? 'success' : 'danger') + '">' + (data.charAt(0).toUpperCase() + data.slice(1)) + '</span>'; } },
            { data: 'created_at' },
            { data: null, orderable: false, searchable: false, render: function (data, type, row) {
                return '<div class="btn-group btn-group-sm">' +
                    '<button type="button" class="btn btn-info view-scheme-btn" data-scheme-id="' + row.scheme_id + '"><i class="fas fa-eye"></i></button>' +
                    '<button type="button" class="btn btn-primary edit-scheme-btn" data-bs-toggle="modal" data-bs-target="#editSchemeModal" data-scheme-id="' + row.scheme_id + '"><i class="fas fa-edit"></i></button>' +
                    '<button type="button" class="btn btn-danger delete-scheme-btn" data-scheme-id="' + row.scheme_id + '"><i class="fas fa-trash"></i></button>' +
                    '</div>';
            } }
        ],
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        order: [[0, 'asc']]
    });

    // --- VIEW ---
    $(document).on('click', '.view-scheme-btn', function(e) {
        e.preventDefault();
        var schemeId = $(this).data('scheme-id');
        console.log('View button clicked. schemeId:', schemeId); // DEBUG
        var $modal = $('#viewSchemeModal');
        $modal.find('.modal-body .alert').remove();
        $modal.find('[data-field]').text('');
        $modal.modal('show'); // Force show modal for debug
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'GET',
            dataType: 'json',
            data: { action: 'get', scheme_id: schemeId },
            success: function(response) {
                console.log('View AJAX success:', response); // DEBUG
                var s = response.data || {};
                $modal.find('[data-field="scheme_id"]').text(s.scheme_id || '');
                $modal.find('[data-field="scheme_name"]').text(s.scheme_name || '');
                $modal.find('[data-field="description"]').text(s.description || '');
                $modal.find('[data-field="status"]').html(s.status ? '<span class="badge badge-' + (s.status === 'active' ? 'success' : 'danger') + '">' + (s.status.charAt(0).toUpperCase() + s.status.slice(1)) + '</span>' : '');
                $modal.find('[data-field="created_at"]').text(s.created_at || '');
                $modal.find('[data-field="updated_at"]').text(s.updated_at || '');
                // Show Training Center
                var centerName = '';
                if (s.center_id) {
                    centerName = $('#center_id option[value="' + s.center_id + '"]').text();
                }
                $modal.find('[data-field="center_name"]').text(centerName || '');
            },
            error: function(xhr, status, error) {
                console.log('View AJAX error:', status, error); // DEBUG
                $modal.find('[data-field]').text('');
            }
        });
    });

    // --- EDIT (populate modal) ---
    $(document).on('click', '.edit-scheme-btn', function(e) {
        e.preventDefault();
        var schemeId = $(this).data('scheme-id');
        var $modal = $('#editSchemeModal');
        var $form = $modal.find('form');
        $form[0].reset();
        $form.find('.is-invalid').removeClass('is-invalid');
        $modal.find('.modal-body .alert').remove();
        $form.find('#editSchemeId').val('');
        $form.find('#editSchemeName').val('');
        $form.find('#editDescription').val('');
        $form.find('#editStatus').val('active');
        $form.find('#edit_center_id').val('');
        $modal.find('.modal-body').append('<div class="text-center py-2" id="edit-loading"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
        var bsModal = new bootstrap.Modal($modal[0]);
        bsModal.show();
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'GET',
            dataType: 'json',
            data: { action: 'get', scheme_id: schemeId },
            success: function(response) {
                $('#edit-loading').remove();
                var s = response.data || {};
                $form.find('#editSchemeId').val(s.scheme_id || '');
                $form.find('#editSchemeName').val(s.scheme_name || '');
                $form.find('#editDescription').val(s.description || '');
                $form.find('#editStatus').val(s.status || 'active');
                // Set Training Center dropdown after ensuring options are loaded
                setTimeout(function() {
                    $form.find('#edit_center_id').val(s.center_id || '').trigger('change');
                }, 100);
            },
            error: function(xhr, status, error) {
                $('#edit-loading').remove();
                $form.find('#editSchemeId').val('');
                $form.find('#editSchemeName').val('');
                $form.find('#editDescription').val('');
                $form.find('#editStatus').val('active');
                $form.find('#edit_center_id').val('');
            }
        });
    });

    // --- EDIT (submit) ---
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
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: $form.serialize() + '&action=edit',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    // Use Bootstrap 5 API to hide modal and clean up backdrop
                    var bsModal = bootstrap.Modal.getInstance($modal[0]);
                    if (bsModal) bsModal.hide();
                    setTimeout(function() {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                    }, 300);
                    toastr.success(response.message || 'Scheme updated successfully');
                    setTimeout(function() {
                        table.ajax.reload(null, false);
                    }, 400);
                    setTimeout(function() {
                        $form[0].reset();
                        $form.find('.is-invalid').removeClass('is-invalid');
                        $form.find('input[type="text"], input[type="hidden"], textarea').val('');
                        $form.find('select').prop('selectedIndex', 0);
                    }, 500);
                } else {
                    toastr.error(response.message || 'Error updating scheme');
                }
            },
            error: function() {
                toastr.error('Error updating scheme');
            }
        });
    });

    // --- ADD ---
    $('#addSchemeForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Add form submitted'); // DEBUG
        var $form = $(this);
        var $modal = $('#addSchemeModal');
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
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: $form.serialize() + '&action=add',
            dataType: 'json',
            success: function(response) {
                console.log('Add AJAX success:', response); // DEBUG
                if(response.success) {
                    // Use Bootstrap 5 API to hide modal and clean up backdrop
                    var bsModal = bootstrap.Modal.getInstance($modal[0]);
                    if (bsModal) bsModal.hide();
                    setTimeout(function() {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                    }, 300);
                    toastr.success(response.message || 'Scheme added successfully');
                    setTimeout(function() {
                        table.ajax.reload(null, false);
                    }, 400);
                    setTimeout(function() {
                        $form[0].reset();
                        $form.find('.is-invalid').removeClass('is-invalid');
                        $form.find('input[type="text"], input[type="hidden"], textarea').val('');
                        $form.find('select').prop('selectedIndex', 0);
                    }, 500);
                } else {
                    toastr.error(response.message || 'Error adding scheme');
                }
            },
            error: function(xhr, status, error) {
                console.log('Add AJAX error:', status, error); // DEBUG
                toastr.error('Error adding scheme');
            }
        });
    });

    // --- DELETE ---
    $(document).on('click', '.delete-scheme-btn', function() {
        var schemeId = $(this).data('scheme-id');
        $('#deleteSchemeId').val(schemeId);
        $('#deleteSchemeModal').modal('show');
    });
    $('#confirmDeleteScheme').on('click', function() {
        var schemeId = $('#deleteSchemeId').val();
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: { action: 'delete', scheme_id: schemeId },
            dataType: 'json',
            success: function(response) {
                $('#deleteSchemeModal').modal('hide');
                if(response.success) {
                    toastr.success(response.message || 'Scheme deleted successfully');
                    table.ajax.reload(null, false);
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

    // --- Reload DataTable when any modal is closed (Add/Edit/Delete) ---
    $('#addSchemeModal, #editSchemeModal, #deleteSchemeModal').on('hidden.bs.modal', function () {
        setTimeout(function() {
            $('#schemesTable').DataTable().ajax.reload(null, false);
        }, 200);
    });
});
</script>
</body>
</html>
