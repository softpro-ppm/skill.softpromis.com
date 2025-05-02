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
$pageTitle = 'Batches';

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
            <h1 class="m-0">Batches</h1>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Batches List -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Batches List</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBatchModal">
                <i class="fas fa-plus"></i> Add New Batch
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="batchesTable" class="table table-bordered table-striped table-hover table-sm align-middle">
                <thead class="thead-dark">
                  <tr>
                    <th class="text-center" style="width:60px;">Sr. No.</th>
                    <th>Batch Code</th>
                    <th>Course</th>
                    <th>Training Center</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th class="text-center" style="width:90px;">Capacity</th>
                    <th class="text-center" style="width:100px;">Status</th>
                    <th class="text-center" style="width:110px; white-space:nowrap;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $batches = [];
                  try {
                    $pdo = getDBConnection();
                    $stmt = $pdo->query("SELECT b.batch_id, b.batch_code, b.start_date, b.end_date, b.capacity, b.status, c.course_name, tc.center_name
                      FROM batches b
                      LEFT JOIN courses c ON b.course_id = c.course_id
                      LEFT JOIN training_centers tc ON b.center_id = tc.center_id
                      ORDER BY b.batch_id DESC");
                    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
                  } catch (Exception $e) {}
                  $sr = 1;
                  foreach ($batches as $row): ?>
                    <tr>
                      <td class="text-center"><?php echo $sr++; ?></td>
                      <td><?php echo htmlspecialchars($row['batch_code']); ?></td>
                      <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                      <td><?php echo htmlspecialchars($row['center_name']); ?></td>
                      <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                      <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                      <td class="text-center"><?php echo htmlspecialchars($row['capacity']); ?></td>
                      <td class="text-center"><span class="badge badge-<?php
                        echo $row['status'] === 'completed' ? 'success' : ($row['status'] === 'ongoing' ? 'primary' : ($row['status'] === 'upcoming' ? 'info' : 'secondary'));
                      ?>"><?php echo ucfirst($row['status']); ?></span></td>
                      <td class="text-center" style="white-space:nowrap;">
                        <div class="btn-group btn-group-sm">
                          <button type="button" class="btn btn-primary edit-batch-btn" data-batch-id="<?php echo $row['batch_id']; ?>"><i class="fas fa-edit"></i></button>
                          <button type="button" class="btn btn-danger delete-batch-btn" data-batch-id="<?php echo $row['batch_id']; ?>"><i class="fas fa-trash"></i></button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <style>
              #batchesTable td, #batchesTable th {
                vertical-align: middle !important;
              }
              #batchesTable .badge {
                font-size: 0.95em;
                padding: 0.45em 0.8em;
              }
              #batchesTable .btn-group .btn {
                margin-right: 2px;
              }
              #batchesTable .btn-group .btn:last-child {
                margin-right: 0;
              }
            </style>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Add Batch Modal -->
  <div class="modal fade" id="addBatchModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Add New Batch</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addBatchForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="batchCode">Batch Code</label>
              <input type="text" class="form-control" id="batchCode" name="batch_code" required>
            </div>
            <div class="form-group">
              <label for="course">Course</label>
              <select class="form-control select2" id="course" name="course_id" required>
                <option value="">Select Course</option>
                <?php foreach (Course::getAll() as $course) {
                  echo "<option value='{$course['course_id']}'>{$course['course_name']}</option>";
                } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="trainingCenter">Training Center</label>
              <select class="form-control select2" id="trainingCenter" name="center_id" required>
                <option value="">Select Center</option>
                <?php foreach (TrainingCenter::getAll() as $center) {
                  echo "<option value='{$center['center_id']}'>{$center['center_name']}</option>";
                } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="startDate">Start Date</label>
              <input type="date" class="form-control" id="startDate" name="start_date" required>
            </div>
            <div class="form-group">
              <label for="endDate">End Date</label>
              <input type="date" class="form-control" id="endDate" name="end_date" required>
            </div>
            <div class="form-group">
              <label for="capacity">Batch Capacity</label>
              <input type="number" class="form-control" id="capacity" name="capacity" required>
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <select class="form-control" id="status" name="status" required>
                <option value="upcoming">Upcoming</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Batch</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Batch Modal -->
  <div class="modal fade" id="editBatchModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Batch</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editBatchForm">
          <input type="hidden" id="editBatchId" name="batch_id">
          <div class="modal-body">
            <div class="form-group">
              <label for="editBatchCode">Batch Code</label>
              <input type="text" class="form-control" id="editBatchCode" name="batch_code" required>
            </div>
            <div class="form-group">
              <label for="editCourse">Course</label>
              <select class="form-control select2" id="editCourse" name="course_id" required>
                <option value="">Select Course</option>
                <?php foreach (Course::getAll() as $course) {
                  echo "<option value='{$course['course_id']}'>{$course['course_name']}</option>";
                } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="editTrainingCenter">Training Center</label>
              <select class="form-control select2" id="editTrainingCenter" name="center_id" required>
                <option value="">Select Center</option>
                <?php foreach (TrainingCenter::getAll() as $center) {
                  echo "<option value='{$center['center_id']}'>{$center['center_name']}</option>";
                } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="editStartDate">Start Date</label>
              <input type="date" class="form-control" id="editStartDate" name="start_date" required>
            </div>
            <div class="form-group">
              <label for="editEndDate">End Date</label>
              <input type="date" class="form-control" id="editEndDate" name="end_date" required>
            </div>
            <div class="form-group">
              <label for="editCapacity">Batch Capacity</label>
              <input type="number" class="form-control" id="editCapacity" name="capacity" required>
            </div>
            <div class="form-group">
              <label for="editStatus">Status</label>
              <select class="form-control" id="editStatus" name="status" required>
                <option value="upcoming">Upcoming</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update Batch</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php
// Include footer
require_once 'includes/footer.php';
?>

<!-- Required JavaScript -->
<script src="assets/js/batches.js"></script>

<script>
$(function () {
  // Fallback: Always open Add Batch modal on button click
  $(document).on('click', '[data-toggle="modal"][data-target="#addBatchModal"]', function(e) {
    e.preventDefault();
    $('#addBatchModal').modal('show');
  });

  var table = $('#batchesTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
      url: 'inc/ajax/batches_ajax.php',
      type: 'GET',
      data: function (d) { d.action = 'list'; },
      dataSrc: function (json) {
        if (!json || !json.data) {
          toastr.error('No data returned from server.');
          return [];
        }
        return json.data;
      }
    },
    columns: [
      { data: 'batch_code' },
      { data: 'course_name', defaultContent: '-' },
      { data: 'center_name', defaultContent: '-' },
      { data: 'start_date' },
      { data: 'end_date' },
      { data: 'capacity' },
      { data: 'status', render: function(data) { return '<span class="badge badge-' + (data === 'completed' ? 'success' : (data === 'ongoing' ? 'primary' : (data === 'upcoming' ? 'info' : 'secondary'))) + '">' + (data.charAt(0).toUpperCase() + data.slice(1)) + '</span>'; } },
      { data: null, orderable: false, searchable: false, render: function (data, type, row) {
        return '<div class="btn-group btn-group-sm">' +
          '<button type="button" class="btn btn-primary edit-batch-btn" data-batch-id="' + row.batch_id + '"><i class="fas fa-edit"></i></button>' +
          '<button type="button" class="btn btn-danger delete-batch-btn" data-batch-id="' + row.batch_id + '"><i class="fas fa-trash"></i></button>' +
          '</div>';
      } }
    ],
    responsive: true,
    lengthChange: true,
    autoWidth: false,
    order: [[1, 'desc']]
  });

  // --- ADD ---
  $('#addBatchForm').on('submit', function(e) {
    e.preventDefault();
    var $form = $(this);
    var isValid = true;
    $form.find('[required]').each(function() {
      if (!$(this).val()) {
        isValid = false;
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid');
      }
    });
    if (!isValid) return false;
    $.ajax({
      url: 'inc/ajax/batches_ajax.php',
      type: 'POST',
      data: $form.serialize() + '&action=add',
      dataType: 'json',
      success: function(response) {
        if(response.success) {
          $('#addBatchModal').modal('hide');
          toastr.success(response.message || 'Batch added successfully');
          $form[0].reset();
          $form.find('.is-invalid').removeClass('is-invalid');
          $('#addBatchModal').one('hidden.bs.modal', function() {
            table.ajax.reload(null, true);
          });
        } else {
          toastr.error(response.message || 'Error adding batch');
        }
      },
      error: function() {
        toastr.error('Error adding batch');
      }
    });
  });

  // --- EDIT (populate modal) ---
  $(document).on('click', '.edit-batch-btn', function() {
    var batchId = $(this).data('batch-id');
    var $modal = $('#editBatchModal');
    $modal.find('form')[0].reset();
    $modal.find('.is-invalid').removeClass('is-invalid');
    $.ajax({
      url: 'inc/ajax/batches_ajax.php',
      type: 'GET',
      data: { action: 'get', batch_id: batchId },
      dataType: 'json',
      success: function(response) {
        var b = response.data || {};
        $modal.find('#editBatchId').val(b.batch_id || '');
        $modal.find('#editBatchCode').val(b.batch_code || '');
        $modal.find('#editCourse').val(b.course_id || '').trigger('change');
        $modal.find('#editTrainingCenter').val(b.center_id || '').trigger('change');
        $modal.find('#editStartDate').val(b.start_date || '');
        $modal.find('#editEndDate').val(b.end_date || '');
        $modal.find('#editCapacity').val(b.capacity || '');
        $modal.find('#editStatus').val(b.status || 'upcoming');
        $modal.modal('show');
      }
    });
  });

  // --- EDIT (submit) ---
  $('#editBatchForm').on('submit', function(e) {
    e.preventDefault();
    var $form = $(this);
    var isValid = true;
    $form.find('[required]').each(function() {
      if (!$(this).val()) {
        isValid = false;
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid');
      }
    });
    if (!isValid) return false;
    $.ajax({
      url: 'inc/ajax/batches_ajax.php',
      type: 'POST',
      data: $form.serialize() + '&action=edit',
      dataType: 'json',
      success: function(response) {
        if(response.success) {
          $('#editBatchModal').modal('hide');
          toastr.success(response.message || 'Batch updated successfully');
          $form[0].reset();
          $form.find('.is-invalid').removeClass('is-invalid');
          $('#editBatchModal').one('hidden.bs.modal', function() {
            table.ajax.reload(null, true);
          });
        } else {
          toastr.error(response.message || 'Error updating batch');
        }
      },
      error: function() {
        toastr.error('Error updating batch');
      }
    });
  });

  // --- DELETE ---
  $(document).on('click', '.delete-batch-btn', function() {
    var batchId = $(this).data('batch-id');
    if (confirm('Are you sure you want to delete this batch?')) {
      $.ajax({
        url: 'inc/ajax/batches_ajax.php',
        type: 'POST',
        data: { action: 'delete', batch_id: batchId },
        dataType: 'json',
        success: function(response) {
          if(response.success) {
            toastr.success(response.message || 'Batch deleted successfully');
            table.ajax.reload(null, true);
          } else {
            toastr.error(response.message || 'Error deleting batch');
          }
        },
        error: function() {
          toastr.error('Error deleting batch');
        }
      });
    }
  });
});
</script>
