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
                  <label for="assessmentId">Assessment ID</label>
                  <input type="text" class="form-control" id="assessmentId" readonly>
                  <small class="form-text text-muted">Auto-generated</small>
                </div>
                <div class="form-group">
                  <label for="student">Student</label>
                  <select class="form-control select2" id="student" required>
                    <option value="">Select Student</option>
                    <option value="1">Rahul Sharma (ENR001)</option>
                    <option value="2">Priya Patel (ENR002)</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="course">Course</label>
                  <input type="text" class="form-control" id="course" readonly>
                </div>
                <div class="form-group">
                  <label for="batch">Batch</label>
                  <input type="text" class="form-control" id="batch" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="assessmentType">Assessment Type</label>
                  <select class="form-control" id="assessmentType" required>
                    <option value="">Select Type</option>
                    <option value="theory">Theory</option>
                    <option value="practical">Practical</option>
                    <option value="project">Project</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="assessmentDate">Assessment Date</label>
                  <div class="input-group date" id="assessmentDate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#assessmentDate" required>
                    <div class="input-group-append" data-target="#assessmentDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="score">Score (%)</label>
                  <input type="number" class="form-control" id="score" min="0" max="100" required>
                </div>
                <div class="form-group">
                  <label for="remarks">Remarks</label>
                  <textarea class="form-control" id="remarks" rows="2"></textarea>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save Assessment</button>
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
          <div class="row mt-4">
            <div class="col-md-12">
              <h6>Assessment History</h6>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Assessment ID</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Score</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>ASS001</td>
                    <td>01/01/2024</td>
                    <td>Practical</td>
                    <td>85%</td>
                    <td><span class="badge badge-success">Passed</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="window.print()">Print Assessment</button>
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
            <!-- Same form fields as Add Assessment Modal -->
            <!-- Pre-populated with existing data -->
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save Changes</button>
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
        { data: 'type' },
        { data: 'date' },
        { data: 'score', render: function(data) { return data + '%'; } },
        { data: 'status', render: function(data) {
            var badge = 'secondary';
            if (data === 'Passed') badge = 'success';
            if (data === 'Pending Review') badge = 'warning';
            if (data === 'Failed') badge = 'danger';
            return '<span class="badge badge-' + badge + '">' + data + '</span>';
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
  });
</script>
</body>
</html> 
