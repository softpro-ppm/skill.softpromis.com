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
    $('#schemesTable').DataTable({
      order: [[4, 'desc']], // Order by Created At (5th column, 0-indexed)
      dom: 'Bfrtip',
      buttons: [
        { extend: 'copy', className: 'btn btn-secondary btn-sm', text: 'Copy' },
        { extend: 'csv', className: 'btn btn-secondary btn-sm', text: 'CSV' },
        { extend: 'excel', className: 'btn btn-secondary btn-sm', text: 'Excel' },
        { extend: 'pdf', className: 'btn btn-secondary btn-sm', text: 'PDF' },
        { extend: 'print', className: 'btn btn-secondary btn-sm', text: 'Print' }
      ]
    });

    // Initialize Select2
    $('.select2').select2({
      theme: 'bootstrap4'
    });

    // Initialize custom file input
    bsCustomFileInput.init();

    // --- AJAX Add Scheme ---
    $('#addSchemeForm').on('submit', function(e) {
      e.preventDefault();
      var data = {
        action: 'create',
        scheme_name: $('#schemeName').val(),
        description: $('#description').val(),
        status: $('#status').val()
      };
      $.post('inc/ajax/schemes_ajax.php', data, function(response) {
        if (response.success) {
          location.reload();
        } else {
          alert(response.message);
        }
      }, 'json');
    });

    // --- AJAX View Scheme ---
    $(document).on('click', '.view-scheme-btn', function() {
      var scheme_id = $(this).data('scheme-id');
      $.post('inc/ajax/schemes_ajax.php', { action: 'get', id: scheme_id }, function(response) {
        if (response.success && response.data) {
          var s = response.data;
          $('#viewSchemeModal .modal-title').text('View Scheme: ' + s.scheme_name);
          $('#viewSchemeModal [data-field="scheme_id"]').text(s.scheme_id);
          $('#viewSchemeModal [data-field="scheme_name"]').text(s.scheme_name);
          $('#viewSchemeModal [data-field="description"]').text(s.description);
          $('#viewSchemeModal [data-field="status"]').text(s.status);
          $('#viewSchemeModal [data-field="created_at"]').text(s.created_at);
          $('#viewSchemeModal [data-field="updated_at"]').text(s.updated_at);
          $('#viewSchemeModal').modal('show');
        } else {
          alert('Could not fetch scheme details.');
        }
      }, 'json');
    });

    // --- AJAX Edit Scheme: fill modal ---
    $(document).on('click', '.edit-scheme-btn', function() {
      var scheme_id = $(this).data('scheme-id');
      $.post('inc/ajax/schemes_ajax.php', { action: 'get', id: scheme_id }, function(response) {
        if (response.success && response.data) {
          var s = response.data;
          $('#editSchemeId').val(s.scheme_id);
          $('#editSchemeName').val(s.scheme_name);
          $('#editDescription').val(s.description);
          $('#editStatus').val(s.status);
          $('#editSchemeModal').modal('show');
        } else {
          alert('Could not fetch scheme details.');
        }
      }, 'json');
    });

    // --- AJAX Edit Scheme: submit ---
    $('#editSchemeForm').on('submit', function(e) {
      e.preventDefault();
      var data = {
        action: 'update',
        id: $('#editSchemeId').val(),
        name: $('#editSchemeName').val(),
        description: $('#editDescription').val(),
        status: $('#editStatus').val()
      };
      $.post('inc/ajax/schemes_ajax.php', data, function(response) {
        if (response.success) {
          location.reload();
        } else {
          alert(response.message);
        }
      }, 'json');
    });

    // --- AJAX Delete Scheme ---
    var deleteSchemeId = null;
    $(document).on('click', '.delete-scheme-btn', function() {
      deleteSchemeId = $(this).data('scheme-id');
      $('#deleteSchemeId').val(deleteSchemeId);
      $('#deleteSchemeModal').modal('show');
    });
    $('#confirmDeleteScheme').on('click', function() {
      var scheme_id = $('#deleteSchemeId').val();
      console.log('Deleting scheme_id:', scheme_id); // Debug: print the ID being sent
      var $btn = $(this);
      $btn.prop('disabled', true); // Prevent double click
      $.post('inc/ajax/schemes_ajax.php', { action: 'delete', id: scheme_id }, function(response) {
        $btn.prop('disabled', false); // Re-enable after response
        if (response.success) {
          location.reload();
        } else {
          alert(response.message);
        }
      }, 'json');
    });
  });
</script>
</body>
</html> 
