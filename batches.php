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

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Batches</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Batches</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Batches List</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" id="addBatchBtn">
                                    <i class="fas fa-plus"></i> Add New Batch
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="batchesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Batch ID</th>
                                        <th>Batch Code</th>
                                        <th>Course Name</th>
                                        <th>Batch Name</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
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
            </div>
        </div>
    </section>
</div><!-- /.content-wrapper -->

<!-- Add/Edit Batch Modal -->
<div class="modal fade" id="batchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="batchModalTitle">Add New Batch</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="batchForm">
                <div class="modal-body">
                    <input type="hidden" id="batch_id" name="batch_id">
                    <div class="form-group">
                        <label for="batch_name">Batch Name</label>
                        <input type="text" class="form-control" id="batch_name" name="batch_name" required>
                    </div>
                    <div class="form-group">
                        <label for="batch_code">Batch Code</label>
                        <input type="text" class="form-control" id="batch_code" name="batch_code" required>
                    </div>
                    <div class="form-group">
                        <label for="course_id"><strong>Course</strong></label>
                        <select class="form-control" id="course_id" name="course_id" required>
                            <option value="">Select Course</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label for="capacity">Capacity</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBatchBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Candidate to Batch Modal -->
<div class="modal fade" id="addCandidateModal" tabindex="-1" aria-labelledby="addCandidateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="addCandidateModalLabel">Add Candidate to Batch</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addCandidateForm">
        <div class="modal-body">
          <input type="hidden" id="candidate_batch_id" name="batch_id">
          <div class="form-group">
            <label for="candidate_student_id">Select Candidate</label>
            <select class="form-control" id="candidate_student_id" name="student_id" required>
              <option value="">Select Candidate</option>
            </select>
          </div>
          <div class="form-group">
            <label for="candidate_enrollment_date">Enrollment Date</label>
            <input type="date" class="form-control" id="candidate_enrollment_date" name="enrollment_date" value="<?= date('Y-m-d') ?>" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Add to Batch</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/js.php'; ?>
<script src="assets/js/batches.js"></script>
<script>
$(function() {
  // Open Add Candidate Modal
  $(document).on('click', '.add-candidate-btn', function() {
    var batchId = $(this).data('batch-id');
    $('#candidate_batch_id').val(batchId);
    // Load students not already in this batch
    $.ajax({
      url: 'inc/ajax/batches_ajax.php',
      type: 'POST',
      data: { action: 'get_available_students', batch_id: batchId },
      dataType: 'json',
      success: function(res) {
        var $sel = $('#candidate_student_id');
        $sel.empty().append('<option value="">Select Candidate</option>');
        if(res.success && res.data) {
          $.each(res.data, function(i, s) {
            $sel.append('<option value="'+s.student_id+'">'+s.first_name+' '+s.last_name+' ('+s.enrollment_no+')</option>');
          });
        }
      }
    });
    $('#addCandidateModal').modal('show');
  });

  // Submit Add Candidate Form
  $('#addCandidateForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize() + '&action=add_candidate_to_batch';
    $.ajax({
      url: 'inc/ajax/batches_ajax.php',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function(res) {
        if(res.success) {
          toastr.success(res.message || 'Candidate added to batch');
          $('#addCandidateModal').modal('hide');
          // Optionally reload batch table or enrolled list
        } else {
          toastr.error(res.message || 'Error adding candidate');
        }
      },
      error: function() {
        toastr.error('Error adding candidate');
      }
    });
  });

  // Add 'Add Candidate' button to each row
  $('#batchesTable').on('draw.dt', function() {
    $('#batchesTable tbody tr').each(function() {
      var row = $(this);
      var data = $('#batchesTable').DataTable().row(row).data();
      if (data && row.find('.add-candidate-btn').length === 0) {
        var btn = $('<button type="button" class="btn btn-sm btn-success add-candidate-btn" title="Add Candidate" data-batch-id="'+data.batch_id+'"><i class="fas fa-user-plus"></i></button>');
        row.find('td:last-child .edit-batch-btn').after(btn);
      }
    });
  });
});
</script>
</body>
</html>
