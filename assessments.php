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
$pageTitle = 'Assessments';

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
            <h1 class="m-0">Assessments</h1>
          </div>
          <div class="col-sm-6">
            <div class="float-sm-right">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAssessmentModal">
                <i class="fas fa-plus"></i> Add New Assessment
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Summary Cards -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>85%</h3>
                <p>Average Score</p>
              </div>
              <div class="icon">
                <i class="fas fa-chart-line"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>75%</h3>
                <p>Pass Rate</p>
              </div>
              <div class="icon">
                <i class="fas fa-check-circle"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>15</h3>
                <p>Pending Assessments</p>
              </div>
              <div class="icon">
                <i class="fas fa-clock"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>5</h3>
                <p>Failed Assessments</p>
              </div>
              <div class="icon">
                <i class="fas fa-times-circle"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Assessment List -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Assessment History</h3>
            <div class="float-right">
              <button type="button" class="btn btn-success" id="exportButton">
                <i class="fas fa-file-export"></i> Export Data
              </button>
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAssessmentModal">
                <i class="fas fa-plus"></i> Add Assessments
              </button>
            </div>
          </div>
          <div class="card-body">
            <table id="assessmentsTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Assessment ID</th>
                  <th>Student</th>
                  <th>Course</th>
                  <th>Type</th>
                  <th>Date</th>
                  <th>Score</th>
                  <th>Max Score</th>
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
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Enhanced Modals -->
  <!-- Add Assessment Modal -->
  <div class="modal fade" id="addAssessmentModal" tabindex="-1" role="dialog" aria-labelledby="addAssessmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addAssessmentModalLabel">Add New Assessment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addAssessmentForm">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="addEnrollmentId">Student (Enrollment)</label>
                  <select class="form-control select2" id="addEnrollmentId" name="enrollment_id" required>
                    <option value="">Select Enrollment</option>
                    <!-- Dynamically populated -->
                  </select>
                </div>
                <div class="form-group">
                  <label for="addAssessmentType">Assessment Type</label>
                  <select class="form-control" id="addAssessmentType" name="assessment_type" required>
                    <option value="">Select Type</option>
                    <option value="theory">Theory</option>
                    <option value="practical">Practical</option>
                    <option value="project">Project</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="addAssessmentDate">Assessment Date</label>
                  <input type="date" class="form-control" id="addAssessmentDate" name="assessment_date" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="addScore">Score</label>
                  <input type="number" class="form-control" id="addScore" name="score" min="0" max="100" required>
                </div>
                <div class="form-group">
                  <label for="addMaxScore">Max Score</label>
                  <input type="number" class="form-control" id="addMaxScore" name="max_score" value="100" min="1" max="1000" required>
                </div>
                <div class="form-group">
                  <label for="addStatus">Status</label>
                  <select class="form-control" id="addStatus" name="status" required>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="addRemarks">Remarks</label>
                  <textarea class="form-control" id="addRemarks" name="remarks" rows="2"></textarea>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" form="addAssessmentForm">Save Assessment</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Assessment Modal -->
  <div class="modal fade" id="viewAssessmentModal" tabindex="-1" role="dialog" aria-labelledby="viewAssessmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewAssessmentModalLabel">View Assessment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Assessment ID:</strong> <span id="viewAssessmentId"></span></p>
              <p><strong>Student:</strong> <span id="viewStudent"></span></p>
              <p><strong>Course:</strong> <span id="viewCourse"></span></p>
              <p><strong>Batch:</strong> <span id="viewBatch"></span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Assessment Type:</strong> <span id="viewAssessmentType"></span></p>
              <p><strong>Assessment Date:</strong> <span id="viewAssessmentDate"></span></p>
              <p><strong>Score:</strong> <span id="viewScore"></span></p>
              <p><strong>Status:</strong> <span id="viewStatus"></span></p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Assessment Modal -->
  <div class="modal fade" id="editAssessmentModal" tabindex="-1" role="dialog" aria-labelledby="editAssessmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editAssessmentModalLabel">Edit Assessment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="editAssessmentForm">
            <input type="hidden" id="editAssessmentId" name="assessment_id">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editEnrollmentId">Student (Enrollment)</label>
                  <select class="form-control select2" id="editEnrollmentId" name="enrollment_id" required>
                    <option value="">Select Enrollment</option>
                    <!-- Dynamically populated -->
                  </select>
                </div>
                <div class="form-group">
                  <label for="editAssessmentType">Assessment Type</label>
                  <select class="form-control" id="editAssessmentType" name="assessment_type" required>
                    <option value="">Select Type</option>
                    <option value="theory">Theory</option>
                    <option value="practical">Practical</option>
                    <option value="project">Project</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="editAssessmentDate">Assessment Date</label>
                  <input type="date" class="form-control" id="editAssessmentDate" name="assessment_date" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editScore">Score</label>
                  <input type="number" class="form-control" id="editScore" name="score" min="0" max="100" required>
                </div>
                <div class="form-group">
                  <label for="editMaxScore">Max Score</label>
                  <input type="number" class="form-control" id="editMaxScore" name="max_score" value="100" min="1" max="1000" required>
                </div>
                <div class="form-group">
                  <label for="editStatus">Status</label>
                  <select class="form-control" id="editStatus" name="status" required>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="editRemarks">Remarks</label>
                  <textarea class="form-control" id="editRemarks" name="remarks" rows="2"></textarea>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" form="editAssessmentForm">Save Changes</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Assessment Modal -->
  <div class="modal fade" id="deleteAssessmentModal" tabindex="-1" role="dialog" aria-labelledby="deleteAssessmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteAssessmentModalLabel">Delete Assessment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this assessment? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger">Delete</button>
        </div>
      </div>
    </div>
  </div>

<?php include 'includes/js.php'; ?>

<script>
  $(function () {
    $('#assessmentsTable').DataTable({
      processing: true,
      serverSide: false,
      ajax: {
        url: 'inc/ajax/assessments_ajax.php',
        type: 'POST',
        data: { action: 'list' },
        dataSrc: function(json) {
          return json.data || [];
        }
      },
      columns: [
        { data: 'assessment_id' },
        { data: 'student_name' },
        { data: 'course_name' },
        { data: 'assessment_type' },
        { data: 'assessment_date' },
        { data: 'score' },
        { data: 'max_score' },
        { data: 'status', render: function(data) {
            var badge = 'secondary';
            if (data === 'completed') badge = 'success';
            if (data === 'pending') badge = 'warning';
            if (data === 'failed') badge = 'danger';
            return '<span class="badge badge-' + badge + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
          }
        },
        { data: null, orderable: false, searchable: false, render: function(data, type, row) {
            return '<button class="btn btn-sm btn-info view-assessment-btn" data-id="' + row.assessment_id + '"><i class="fas fa-eye"></i></button>' +
                   '<button class="btn btn-sm btn-primary edit-assessment-btn" data-id="' + row.assessment_id + '"><i class="fas fa-edit"></i></button>' +
                   '<button class="btn btn-sm btn-danger delete-assessment-btn" data-id="' + row.assessment_id + '"><i class="fas fa-trash"></i></button>';
          }
        }
      ],
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      responsive: true
    });

    // Initialize Select2
    $('.select2').select2({
      theme: 'bootstrap4'
    });

    // Initialize date picker
    $('.datepicker').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      locale: {
        format: 'DD/MM/YYYY'
      }
    });

    // Load course and batch details when student is selected
    $('#student').on('change', function() {
      const studentId = $(this).val();
      if (studentId) {
        // Load course and batch details
        $('#course').val('Web Development');
        $('#batch').val('B001');
      } else {
        $('#course').val('');
        $('#batch').val('');
      }
    });

    // Add Toastr success messages for create, update, and delete operations
    $(document).on('submit', '#addAssessmentForm', function(e) {
      e.preventDefault();
      const formData = $(this).serialize();
      $.post('inc/ajax/assessments_ajax.php', { action: 'create', ...formData }, function(response) {
        if (response.success) {
          toastr.success('Assessment added successfully!');
          $('#addAssessmentModal').modal('hide');
          $('#assessmentsTable').DataTable().ajax.reload();
        } else {
          toastr.error(response.message || 'Failed to add assessment.');
        }
      }, 'json');
    });

    $(document).on('submit', '#editAssessmentForm', function(e) {
      e.preventDefault();
      const formData = $(this).serialize();
      $.post('inc/ajax/assessments_ajax.php', { action: 'update', ...formData }, function(response) {
        if (response.success) {
          toastr.success('Assessment updated successfully!');
          $('#editAssessmentModal').modal('hide');
          $('#assessmentsTable').DataTable().ajax.reload();
        } else {
          toastr.error(response.message || 'Failed to update assessment.');
        }
      }, 'json');
    });

    $(document).on('click', '.delete-assessment-btn', function() {
      const assessmentId = $(this).data('id');
      Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          $.post('inc/ajax/assessments_ajax.php', { action: 'delete', assessment_id: assessmentId }, function(response) {
            if (response.success) {
              toastr.success('Assessment deleted successfully!');
              $('#assessmentsTable').DataTable().ajax.reload();
            } else {
              toastr.error(response.message || 'Failed to delete assessment.');
            }
          }, 'json');
        }
      });
    });

    // Ensure the modal is properly initialized
    $(document).ready(function() {
      // Handle Add Assessment button click
      $(document).on('click', '.btn-primary[data-target="#addAssessmentModal"]', function() {
        $('#addAssessmentModal').modal('show');
      });
    });

    $(document).on('click', '#addAssessmentModal', function() {
      $('#addAssessmentModal').modal('show');
    });
  });
</script>
</body>
</html>
