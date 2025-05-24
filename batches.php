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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title" id="batchModalTitle">Add New Batch</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="batchForm">
                <div class="modal-body">
                    <input type="hidden" id="batch_id" name="batch_id">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="partner_id" class="form-label fw-bold">Training Partner <span class="text-danger">*</span></label>
                            <select class="form-control" id="partner_id" name="partner_id" required>
                                <option value="">Select Training Partner</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="center_id" class="form-label fw-bold">Training Center <span class="text-danger">*</span></label>
                            <select class="form-control" id="center_id" name="center_id" required>
                                <option value="">Select Training Center</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="scheme_id" class="form-label fw-bold">Scheme <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-control" id="scheme_id" name="scheme_id" required>
                                    <option value="">Select Scheme</option>
                                </select>
                                <button type="button" class="btn btn-outline-primary" id="addSchemeBtn" data-bs-toggle="modal" data-bs-target="#addSchemeModal"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="sector_id" class="form-label fw-bold">Sector <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-control" id="sector_id" name="sector_id" required>
                                    <option value="">Select Sector</option>
                                </select>
                                <button type="button" class="btn btn-outline-primary" id="addSectorBtn" data-bs-toggle="modal" data-bs-target="#addSectorModal"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="course_id" class="form-label fw-bold">Course <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-control" id="course_id" name="course_id" required>
                                    <option value="">Select Course</option>
                                </select>
                                <button type="button" class="btn btn-outline-primary" id="addCourseBtn" data-bs-toggle="modal" data-bs-target="#addCourseModal"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="batch_name" class="form-label fw-bold">Batch Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="batch_name" name="batch_name" required>
                    </div>
                    <div style="display:none">
                        <input type="text" class="form-control" id="batch_code" name="batch_code" value="" readonly>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label fw-bold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label fw-bold">Capacity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="capacity" name="capacity" required>
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

<!-- View Batch Modal -->
<div class="modal fade" id="viewBatchModal" tabindex="-1" aria-labelledby="viewBatchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="viewBatchModalLabel">View Batch</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label class="form-label fw-bold">Training Partner</label>
            <div id="view_partner_name" class="form-control-plaintext"></div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Training Center</label>
            <div id="view_center_name" class="form-control-plaintext"></div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Scheme</label>
            <div id="view_scheme_name" class="form-control-plaintext"></div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Sector</label>
            <div id="view_sector_name" class="form-control-plaintext"></div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Course</label>
            <div id="view_course_name" class="form-control-plaintext"></div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Batch Name</label>
            <div id="view_batch_name" class="form-control-plaintext"></div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Batch Code</label>
            <div id="view_batch_code" class="form-control-plaintext"></div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Start Date</label>
            <div id="view_start_date" class="form-control-plaintext"></div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">End Date</label>
            <div id="view_end_date" class="form-control-plaintext"></div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Capacity</label>
            <div id="view_capacity" class="form-control-plaintext"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
      </div>
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
                                <label for="registerEnrollmentNo" class="form-label">Enrollment No <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="registerEnrollmentNo" name="enrollment_no" required aria-required="true">
                            </div>
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

<!-- Add Scheme Modal -->
<div class="modal fade" id="addSchemeModal" tabindex="-1" aria-labelledby="addSchemeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addSchemeModalLabel">Add Scheme</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addSchemeForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="parent_center_id" class="form-label">Training Center</label>
            <select class="form-control" id="parent_center_id" name="center_id" required></select>
          </div>
          <div class="mb-3">
            <label for="new_scheme_name" class="form-label">Scheme Name</label>
            <input type="text" class="form-control" id="new_scheme_name" name="new_scheme_name" required>
          </div>
          <div class="mb-3">
            <label for="new_scheme_desc" class="form-label">Description</label>
            <textarea class="form-control" id="new_scheme_desc" name="new_scheme_desc"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Scheme</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Add Sector Modal -->
<div class="modal fade" id="addSectorModal" tabindex="-1" aria-labelledby="addSectorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addSectorModalLabel">Add Sector</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addSectorForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="parent_center_id_sector" class="form-label">Training Center</label>
            <select class="form-control" id="parent_center_id_sector" name="center_id" required></select>
          </div>
          <div class="mb-3">
            <label for="parent_scheme_id_sector" class="form-label">Scheme</label>
            <select class="form-control" id="parent_scheme_id_sector" name="scheme_id" required></select>
          </div>
          <div class="mb-3">
            <label for="new_sector_name" class="form-label">Sector Name</label>
            <input type="text" class="form-control" id="new_sector_name" name="new_sector_name" required>
          </div>
          <div class="mb-3">
            <label for="new_sector_desc" class="form-label">Description</label>
            <textarea class="form-control" id="new_sector_desc" name="new_sector_desc"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Sector</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addCourseModalLabel">Add Course</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addCourseForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="parent_center_id_course" class="form-label">Training Center</label>
            <select class="form-control" id="parent_center_id_course" name="center_id" required></select>
          </div>
          <div class="mb-3">
            <label for="parent_scheme_id_course" class="form-label">Scheme</label>
            <select class="form-control" id="parent_scheme_id_course" name="scheme_id" required></select>
          </div>
          <div class="mb-3">
            <label for="parent_sector_id_course" class="form-label">Sector</label>
            <select class="form-control" id="parent_sector_id_course" name="sector_id" required></select>
          </div>
          <div class="mb-3">
            <label for="new_course_name" class="form-label">Course Name</label>
            <input type="text" class="form-control" id="new_course_name" name="new_course_name" required>
          </div>
          <div class="mb-3">
            <label for="new_course_desc" class="form-label">Description</label>
            <textarea class="form-control" id="new_course_desc" name="new_course_desc"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Course</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/js.php'; ?>
<script src="assets/js/batches.js"></script>
<script src="assets/js/batches-scheme-sector.js"></script>
<script src="assets/js/batches-partner-center.js"></script>
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

    // Helper to copy options and set value
    function copySelectOptions($from, $to, selectedVal) {
        $to.empty();
        $from.find('option').each(function() {
            $to.append($(this).clone());
        });
        $to.val(selectedVal);
    }
    // Helper to load schemes for a center
    function loadSchemesForCenter(centerId, $select, selectedId) {
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: { action: 'list_by_center', center_id: centerId },
            dataType: 'json',
            success: function(res) {
                $select.empty();
                if(res.success && res.data && res.data.length) {
                    $select.append('<option value="">Select Scheme</option>');
                    $.each(res.data, function(i, s) {
                        $select.append(`<option value="${s.scheme_id}"${selectedId==s.scheme_id?' selected':''}>${s.scheme_name}</option>`);
                    });
                } else {
                    $select.append('<option value="">No schemes found</option>');
                }
                if(selectedId) $select.val(selectedId);
            }
        });
    }
    // Helper to load sectors for a scheme
    function loadSectorsForScheme(schemeId, $select, selectedId) {
        $.ajax({
            url: 'inc/ajax/sectors_ajax.php',
            type: 'POST',
            data: { action: 'list', scheme_id: schemeId },
            dataType: 'json',
            success: function(res) {
                $select.empty();
                if(res.data && res.data.length) {
                    $select.append('<option value="">Select Sector</option>');
                    $.each(res.data, function(i, s) {
                        $select.append(`<option value="${s.sector_id}"${selectedId==s.sector_id?' selected':''}>${s.sector_name}</option>`);
                    });
                } else {
                    $select.append('<option value="">No sectors found</option>');
                }
                if(selectedId) $select.val(selectedId);
            }
        });
    }
    // Pre-fill and filter parent selects when opening modals
    $('#addSchemeModal').on('show.bs.modal', function() {
        copySelectOptions($('#center_id'), $('#parent_center_id'), $('#center_id').val());
    });
    $('#addSectorModal').on('show.bs.modal', function() {
        copySelectOptions($('#center_id'), $('#parent_center_id_sector'), $('#center_id').val());
        loadSchemesForCenter($('#center_id').val(), $('#parent_scheme_id_sector'), $('#scheme_id').val());
    });
    $('#parent_center_id_sector').on('change', function() {
        loadSchemesForCenter($(this).val(), $('#parent_scheme_id_sector'));
    });
    $('#addCourseModal').on('show.bs.modal', function() {
        copySelectOptions($('#center_id'), $('#parent_center_id_course'), $('#center_id').val());
        loadSchemesForCenter($('#center_id').val(), $('#parent_scheme_id_course'), $('#scheme_id').val());
        loadSectorsForScheme($('#scheme_id').val(), $('#parent_sector_id_course'), $('#sector_id').val());
    });
    $('#parent_center_id_course').on('change', function() {
        loadSchemesForCenter($(this).val(), $('#parent_scheme_id_course'));
        $('#parent_sector_id_course').empty().append('<option value="">Select Sector</option>');
    });
    $('#parent_scheme_id_course').on('change', function() {
        loadSectorsForScheme($(this).val(), $('#parent_sector_id_course'));
    });
    // Update AJAX for parent fields
    $('#addSchemeForm').on('submit', function(e) {
        e.preventDefault();
        var name = $('#new_scheme_name').val().trim();
        var centerId = $('#parent_center_id').val();
        var desc = $('#new_scheme_desc').val().trim();
        if (!name || !centerId) return;
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: { action: 'add', scheme_name: name, center_id: centerId, description: desc },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    var $select = $('#scheme_id');
                    var newOption = $('<option>').val(res.scheme_id || name).text(name);
                    $select.append(newOption).val(res.scheme_id || name);
                    $('#addSchemeModal').modal('hide');
                    $('#new_scheme_name').val('');
                    $('#new_scheme_desc').val('');
                    toastr.success('Scheme added!');
                } else {
                    toastr.error(res.message || 'Failed to add scheme');
                }
            },
            error: function() { toastr.error('Failed to add scheme'); }
        });
    });
    $('#addSectorForm').on('submit', function(e) {
        e.preventDefault();
        var name = $('#new_sector_name').val().trim();
        var centerId = $('#parent_center_id_sector').val();
        var schemeId = $('#parent_scheme_id_sector').val();
        var desc = $('#new_sector_desc').val().trim();
        if (!name || !centerId || !schemeId) return;
        $.ajax({
            url: 'inc/ajax/sectors_ajax.php',
            type: 'POST',
            data: { action: 'add', sector_name: name, center_id: centerId, scheme_id: schemeId, description: desc },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    var $select = $('#sector_id');
                    var newOption = $('<option>').val(res.sector_id || name).text(name);
                    $select.append(newOption).val(res.sector_id || name);
                    $('#addSectorModal').modal('hide');
                    $('#new_sector_name').val('');
                    $('#new_sector_desc').val('');
                    toastr.success('Sector added!');
                } else {
                    toastr.error(res.message || 'Failed to add sector');
                }
            },
            error: function() { toastr.error('Failed to add sector'); }
        });
    });
    $('#addCourseForm').on('submit', function(e) {
        e.preventDefault();
        var name = $('#new_course_name').val().trim();
        var centerId = $('#parent_center_id_course').val();
        var schemeId = $('#parent_scheme_id_course').val();
        var sectorId = $('#parent_sector_id_course').val();
        var desc = $('#new_course_desc').val().trim();
        if (!name || !centerId || !schemeId || !sectorId) return;
        $.ajax({
            url: 'inc/ajax/courses_ajax.php',
            type: 'POST',
            data: { action: 'create', course_name: name, course_code: name, center_id: centerId, scheme_id: schemeId, sector_id: sectorId, duration_hours: 1, description: desc },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    var $select = $('#course_id');
                    var newOption = $('<option>').val(res.course_id || name).text(name);
                    $select.append(newOption).val(res.course_id || name);
                    $('#addCourseModal').modal('hide');
                    $('#new_course_name').val('');
                    $('#new_course_desc').val('');
                    toastr.success('Course added!');
                } else {
                    toastr.error(res.message || 'Failed to add course');
                }
            },
            error: function() { toastr.error('Failed to add course'); }
        });
    });
});
</script>
</body>
</html>
