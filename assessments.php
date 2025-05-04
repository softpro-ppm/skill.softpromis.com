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

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Assessments</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button type="button" class="btn btn-primary" id="addAssessmentBtn" data-bs-toggle="modal" data-bs-target="#assessmentModal">
                        <i class="fas fa-plus"></i> Assessment
                    </button>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Assessment History</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" id="addAssessmentBtn" data-bs-toggle="modal" data-bs-target="#assessmentModal">
                            <i class="fas fa-plus"></i> Assessment
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="assessmentsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
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
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Add/Edit Assessment Modal -->
<div class="modal fade" id="assessmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="assessmentModalTitle">Add New Assessment</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="assessmentForm">
                <div class="modal-body">
                    <input type="hidden" id="assessment_id" name="assessment_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="student_id">Student</label>
                                <select class="form-control select2" id="student_id" name="student_id" required>
                                    <option value="">Select Student</option>
                                </select>
                            </div>
                            <div class="form-group" id="enrollment_id_group" style="display:none;">
                                <label for="enrollment_id">Enrollment</label>
                                <select class="form-control" id="enrollment_id" name="enrollment_id">
                                    <option value="">Select Enrollment</option>
                                </select>
                            </div>
                            <input type="hidden" id="enrollment_id_hidden" name="enrollment_id" required>
                            <div class="form-group">
                                <label for="course_name">Course</label>
                                <input type="text" class="form-control" id="course_name" name="course_name" readonly>
                            </div>
                            <div class="form-group">
                                <label for="assessment_type">Assessment Type</label>
                                <select class="form-control" id="assessment_type" name="assessment_type" required>
                                    <option value="">Select Type</option>
                                    <option value="theory">Theory</option>
                                    <option value="practical">Practical</option>
                                    <option value="project">Project</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="assessment_date">Assessment Date</label>
                                <input type="date" class="form-control" id="assessment_date" name="assessment_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="score">Score</label>
                                <input type="number" class="form-control" id="score" name="score" min="0" max="100" required>
                            </div>
                            <div class="form-group">
                                <label for="max_score">Max Score</label>
                                <input type="number" class="form-control" id="max_score" name="max_score" value="100" min="1" max="1000" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveAssessmentBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Assessment Modal -->
<div class="modal fade" id="viewAssessmentModal" tabindex="-1" aria-labelledby="viewAssessmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="viewAssessmentModalLabel">Assessment Details</h5>
        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="viewAssessmentBody">
        <!-- Populated by JS -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/js.php'; ?>
<script src="assets/js/assessments.js"></script>
</body>
</html>
