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
                                        <th>Sr No.</th>
                                        <!--<th>Batch ID</th>-->
                                        <th>Batch Code</th>
                                        <th>Course Name</th>
                                        <th>Batch Name</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Students / Capacity</th>
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

<!-- Assign Students to Batch Modal -->
<div class="modal fade" id="assignStudentsModal" tabindex="-1" aria-labelledby="assignStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="assignStudentsModalLabel">Assign Students to Batch</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignStudentsForm">
                <div class="modal-body">
                    <input type="hidden" id="assignBatchId" name="batch_id">
                    <div class="form-group mb-3">
                        <label for="assignStudentIds">Select Students</label>
                        <select class="form-control" id="assignStudentIds" name="student_ids[]" multiple required style="min-height:150px;"></select>
                        <small class="form-text text-muted">Only students not assigned to any batch are shown.</small>
                    </div>
                    <div id="assignStudentsError" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add/Edit Batch Modal -->
<div class="modal fade" id="batchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="batchModalTitle">Add New Batch</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="batchForm">
                <div class="modal-body">
                    <input type="hidden" id="batch_id" name="batch_id">
                    <div class="form-group">
                        <label for="batch_name">Batch Name</label>
                        <input type="text" class="form-control" id="batch_name" name="batch_name" required>
                    </div>
                    <div class="form-group">
                        <!-- <label for="batch_code">Batch Code</label> -->
                        <input type="text" class="form-control" id="batch_code" name="batch_code" value="" style="display:none" readonly>
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
                        <!-- Status is now determined automatically by start/end date -->
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span> Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBatchBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Student List Modal -->
<div class="modal fade" id="batchStudentsModal" tabindex="-1" aria-labelledby="batchStudentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="batchStudentsModalLabel">Batch Students</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div id="batchStudentsError" class="alert alert-danger d-none"></div>
        <table class="table table-bordered table-striped" id="batchStudentsTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Enrollment No</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Gender</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span> Close</button>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/js.php'; ?>
<script src="assets/js/batches.js"></script>
<script>
// Assign Students Modal logic
$(function() {
    // Open assign modal
    $(document).on('click', '.assign-students-btn', function() {
        var batchId = $(this).data('batch-id');
        $('#assignBatchId').val(batchId);
        var $select = $('#assignStudentIds');
        $select.empty();
        $('#assignStudentsError').addClass('d-none').text('');
        // Load available students
        $.post('inc/ajax/batches_ajax.php', { action: 'get_available_students' }, function(res) {
            if(res.success && res.data.length) {
                $.each(res.data, function(i, s) {
                    $select.append('<option value="'+s.student_id+'">'+s.full_name+' ('+s.enrollment_no+')</option>');
                });
            } else {
                $select.append('<option disabled>No available students</option>');
            }
            $('#assignStudentsModal').modal('show');
        }, 'json');
    });

    // Submit assign form
    $('#assignStudentsForm').on('submit', function(e) {
        e.preventDefault();
        var batchId = $('#assignBatchId').val();
        var studentIds = $('#assignStudentIds').val();
        var $error = $('#assignStudentsError');
        $error.addClass('d-none').text('');
        if(!studentIds || studentIds.length === 0) {
            $error.removeClass('d-none').text('Please select at least one student.');
            return;
        }
        $.post('inc/ajax/batches_ajax.php', { action: 'assign_students', batch_id: batchId, student_ids: studentIds }, function(res) {
            if(res.success) {
                $('#assignStudentsModal').modal('hide');
                toastr.success(res.message || 'Students assigned successfully');
                // Reload batches table after assigning
                if (window.batchesTable && typeof window.batchesTable.ajax === 'object') {
                    window.batchesTable.ajax.reload(null, false);
                } else if ($('#batchesTable').DataTable) {
                    $('#batchesTable').DataTable().ajax.reload(null, false);
                }
            } else {
                $error.removeClass('d-none').text(res.message || 'Failed to assign students.');
            }
        }, 'json');
    });
});
</script>
</body>
</html>
