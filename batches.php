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

<!-- Register New Student Modal (Batch Context) -->
<div class="modal fade" id="registerStudentModal" tabindex="-1" aria-labelledby="registerStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="registerStudentModalLabel">Register New Student for Batch</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="registerStudentForm" novalidate enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="registerStudentError" class="alert alert-danger d-none"></div>
                    <input type="hidden" id="registerBatchId" name="batch_id">
                    <div class="container-fluid">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="registerFullName" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="registerFullName" name="full_name" required aria-required="true">
                            </div>
                            <div class="col-md-6">
                                <label for="registerEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="registerEmail" name="email">
                            </div>
                            <div class="col-md-6">
                                <label for="registerMobile" class="form-label">Mobile</label>
                                <input type="tel" class="form-control" id="registerMobile" name="mobile" pattern="^[0-9]{10}$" maxlength="10" minlength="10">
                            </div>
                            <div class="col-md-6">
                                <label for="registerDOB" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="registerDOB" name="date_of_birth">
                            </div>
                            <div class="col-md-6">
                                <label for="registerGender" class="form-label">Gender</label>
                                <select class="form-control" id="registerGender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="registerAddress" class="form-label">Address</label>
                                <textarea class="form-control" id="registerAddress" name="address"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="registerPhoto" class="form-label">Student Photo</label>
                                <input type="file" class="form-control" id="registerPhoto" name="photo" accept="image/*">
                            </div>
                            <div class="col-md-4">
                                <label for="registerAadhaar" class="form-label">Aadhaar Card</label>
                                <input type="file" class="form-control" id="registerAadhaar" name="aadhaar" accept="application/pdf,image/*">
                            </div>
                            <div class="col-md-4">
                                <label for="registerQualification" class="form-label">Qualification Document</label>
                                <input type="file" class="form-control" id="registerQualification" name="qualification" accept="application/pdf,image/*">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Register Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/js.php'; ?>
<script src="assets/js/batches.js"></script>
<script>
$(function() {
    // Register Student Modal logic
    $(document).on('click', '.register-student-btn', function() {
        var batchId = $(this).data('batch-id');
        $('#registerBatchId').val(batchId);
        $('#registerStudentForm')[0].reset();
        $('#registerStudentError').addClass('d-none').text('');
        $('#registerStudentModal').modal('show');
    });

    // Submit register student form
    $('#registerStudentForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var $error = $('#registerStudentError');
        $error.addClass('d-none').text('');
        $.ajax({
            url: 'inc/ajax/batches_ajax.php?action=register_student',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if(res.success) {
                    $('#registerStudentModal').modal('hide');
                    toastr.success(res.message || 'Student registered successfully');
                    // Reload batches table if needed
                    if (window.batchesTable && typeof window.batchesTable.ajax === 'object') {
                        window.batchesTable.ajax.reload(null, false);
                    } else if ($('#batchesTable').DataTable) {
                        $('#batchesTable').DataTable().ajax.reload(null, false);
                    }
                } else {
                    $error.removeClass('d-none').text(res.message || 'Failed to register student.');
                }
            },
            error: function() {
                $error.removeClass('d-none').text('An error occurred. Please try again.');
            }
        });
    });
});
</script>
</body>
</html>
