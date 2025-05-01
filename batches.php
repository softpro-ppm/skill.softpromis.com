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
            <table id="batchesTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Batch Code</th>
                  <th>Course</th>
                  <th>Training Center</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Students</th>
                  <th>Trainer</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- Dynamic rows will be loaded here by DataTables -->
              </tbody>
            </table>
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
              <label for="trainer">Trainer</label>
              <select class="form-control select2" id="trainer" name="trainer_id">
                <option value="">Select Trainer</option>
                <!-- Populate dynamically if needed -->
              </select>
            </div>
            <div class="form-group">
              <label for="startDate">Start Date</label>
              <div class="input-group date" id="startDatePicker" data-target-input="nearest">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                <input type="text" class="form-control datetimepicker-input" data-target="#startDatePicker" id="startDate" name="start_date" required />
                <div class="input-group-append" data-target="#startDatePicker" data-toggle="datetimepicker">
                  <span class="input-group-text"><i class="far fa-calendar"></i></span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="endDate">End Date</label>
              <div class="input-group date" id="endDatePicker" data-target-input="nearest">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                <input type="text" class="form-control datetimepicker-input" data-target="#endDatePicker" id="endDate" name="end_date" required />
                <div class="input-group-append" data-target="#endDatePicker" data-toggle="datetimepicker">
                  <span class="input-group-text"><i class="far fa-calendar"></i></span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="capacity">Batch Capacity</label>
              <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-users"></i></span></div>
                <input type="number" class="form-control" id="capacity" name="capacity" required>
              </div>
            </div>
            <div class="form-group">
              <label for="schedule">Schedule</label>
              <input type="text" class="form-control" id="schedule" name="schedule">
            </div>
            <div class="form-group">
              <label for="remarks">Remarks</label>
              <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
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
              <label for="editTrainer">Trainer</label>
              <select class="form-control select2" id="editTrainer" name="trainer_id">
                <option value="">Select Trainer</option>
                <!-- Populate dynamically if needed -->
              </select>
            </div>
            <div class="form-group">
              <label for="editStartDate">Start Date</label>
              <div class="input-group date" id="editStartDatePicker" data-target-input="nearest">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                <input type="text" class="form-control datetimepicker-input" data-target="#editStartDatePicker" id="editStartDate" name="start_date" required />
                <div class="input-group-append" data-target="#editStartDatePicker" data-toggle="datetimepicker">
                  <span class="input-group-text"><i class="far fa-calendar"></i></span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="editEndDate">End Date</label>
              <div class="input-group date" id="editEndDatePicker" data-target-input="nearest">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                <input type="text" class="form-control datetimepicker-input" data-target="#editEndDatePicker" id="editEndDate" name="end_date" required />
                <div class="input-group-append" data-target="#editEndDatePicker" data-toggle="datetimepicker">
                  <span class="input-group-text"><i class="far fa-calendar"></i></span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="editCapacity">Batch Capacity</label>
              <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-users"></i></span></div>
                <input type="number" class="form-control" id="editCapacity" name="capacity" required>
              </div>
            </div>
            <div class="form-group">
              <label for="editSchedule">Schedule</label>
              <input type="text" class="form-control" id="editSchedule" name="schedule">
            </div>
            <div class="form-group">
              <label for="editRemarks">Remarks</label>
              <textarea class="form-control" id="editRemarks" name="remarks" rows="2"></textarea>
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
      { data: 'trainer_name', defaultContent: '-' },
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
    order: [[0, 'desc']]
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
        $modal.find('#editTrainer').val(b.trainer_id || '').trigger('change');
        $modal.find('#editStartDate').val(b.start_date || '');
        $modal.find('#editEndDate').val(b.end_date || '');
        $modal.find('#editCapacity').val(b.capacity || '');
        $modal.find('#editSchedule').val(b.schedule || '');
        $modal.find('#editRemarks').val(b.remarks || '');
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
