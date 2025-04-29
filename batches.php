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
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Add New Batch</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addBatchForm">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="batchCode">Batch Code</label>
                  <input type="text" class="form-control" id="batchCode" name="batch_code" placeholder="Enter batch code" required>
                </div>
                <div class="form-group">
                  <label for="course">Course</label>
                  <select class="form-control select2" id="course" name="course_id" required>
                    <option value="">Select Course</option>
                    <?php
                    // Fetch courses from database
                    $courses = Course::getAll();
                    foreach ($courses as $course) {
                        echo "<option value='{$course['course_id']}'>{$course['course_name']}</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="trainingCenter">Training Center</label>
                  <select class="form-control select2" id="trainingCenter" name="center_id" required>
                    <option value="">Select Center</option>
                    <?php
                    // Fetch training centers from database
                    $centers = TrainingCenter::getAll();
                    foreach ($centers as $center) {
                        echo "<option value='{$center['center_id']}'>{$center['center_name']}</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="trainer">Trainer</label>
                  <select class="form-control select2" id="trainer" name="trainer_id" required>
                    <option value="">Select Trainer</option>
                    <?php
                    // Fetch trainers from database
                    $trainers = Trainer::getAll();
                    foreach ($trainers as $trainer) {
                        echo "<option value='{$trainer['id']}'>{$trainer['name']}</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="capacity">Batch Capacity</label>
                  <input type="number" class="form-control" id="capacity" name="capacity" placeholder="Enter batch capacity" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="startDate">Start Date</label>
                  <div class="input-group date" id="startDate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" name="start_date" data-target="#startDate" required>
                    <div class="input-group-append" data-target="#startDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="endDate">End Date</label>
                  <div class="input-group date" id="endDate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" name="end_date" data-target="#endDate" required>
                    <div class="input-group-append" data-target="#endDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="schedule">Schedule</label>
                  <textarea class="form-control" id="schedule" name="schedule" rows="3" placeholder="Enter batch schedule" required></textarea>
                </div>
                <div class="form-group">
                  <label for="remarks">Remarks</label>
                  <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Enter any remarks"></textarea>
                </div>
              </div>
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

  <!-- View Batch Modal -->
  <div class="modal fade" id="viewBatchModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">View Batch Details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Batch Code</label>
                <p id="viewBatchCode"></p>
              </div>
              <div class="form-group">
                <label>Course</label>
                <p id="viewCourse"></p>
              </div>
              <div class="form-group">
                <label>Training Center</label>
                <p id="viewCenter"></p>
              </div>
              <div class="form-group">
                <label>Trainer</label>
                <p id="viewTrainer"></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Start Date</label>
                <p id="viewStartDate"></p>
              </div>
              <div class="form-group">
                <label>End Date</label>
                <p id="viewEndDate"></p>
              </div>
              <div class="form-group">
                <label>Schedule</label>
                <p id="viewSchedule"></p>
              </div>
              <div class="form-group">
                <label>Status</label>
                <p id="viewStatus"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h5>Enrolled Students</h5>
              <div class="table-responsive">
                <table id="enrolledStudentsTable" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Student ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Fee Status</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Dynamic rows will be loaded here by AJAX -->
                  </tbody>
                </table>
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

  <!-- Edit Batch Modal -->
  <div class="modal fade" id="editBatchModal">
    <div class="modal-dialog modal-lg">
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
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editBatchCode">Batch Code</label>
                  <input type="text" class="form-control" id="editBatchCode" name="batch_code" placeholder="Enter batch code" required>
                </div>
                <div class="form-group">
                  <label for="editCourse">Course</label>
                  <select class="form-control select2" id="editCourse" name="course_id" required>
                    <option value="">Select Course</option>
                    <?php
                    foreach ($courses as $course) {
                        echo "<option value='{$course['course_id']}'>{$course['course_name']}</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="editTrainingCenter">Training Center</label>
                  <select class="form-control select2" id="editTrainingCenter" name="center_id" required>
                    <option value="">Select Center</option>
                    <?php
                    foreach ($centers as $center) {
                        echo "<option value='{$center['center_id']}'>{$center['center_name']}</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="editTrainer">Trainer</label>
                  <select class="form-control select2" id="editTrainer" name="trainer_id" required>
                    <option value="">Select Trainer</option>
                    <?php
                    foreach ($trainers as $trainer) {
                        echo "<option value='{$trainer['id']}'>{$trainer['name']}</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="editCapacity">Batch Capacity</label>
                  <input type="number" class="form-control" id="editCapacity" name="capacity" placeholder="Enter batch capacity" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editStartDate">Start Date</label>
                  <div class="input-group date" id="editStartDate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" name="start_date" data-target="#editStartDate" required>
                    <div class="input-group-append" data-target="#editStartDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="editEndDate">End Date</label>
                  <div class="input-group date" id="editEndDate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" name="end_date" data-target="#editEndDate" required>
                    <div class="input-group-append" data-target="#editEndDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="editSchedule">Schedule</label>
                  <textarea class="form-control" id="editSchedule" name="schedule" rows="3" placeholder="Enter batch schedule" required></textarea>
                </div>
                <div class="form-group">
                  <label for="editRemarks">Remarks</label>
                  <textarea class="form-control" id="editRemarks" name="remarks" rows="3" placeholder="Enter any remarks"></textarea>
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
