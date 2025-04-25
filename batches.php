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
                  <th>Batch ID</th>
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
                <tr>
                  <td>B001</td>
                  <td>Web Development</td>
                  <td>ABC Training Center - Mumbai</td>
                  <td>01/01/2024</td>
                  <td>31/03/2024</td>
                  <td>25/30</td>
                  <td>John Doe</td>
                  <td><span class="badge badge-success">Active</span></td>
                  <td>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewBatchModal">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editBatchModal">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteBatchModal">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>B002</td>
                  <td>Digital Marketing</td>
                  <td>ABC Training Center - Pune</td>
                  <td>15/01/2024</td>
                  <td>15/04/2024</td>
                  <td>20/25</td>
                  <td>Jane Smith</td>
                  <td><span class="badge badge-success">Active</span></td>
                  <td>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewBatchModal">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editBatchModal">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteBatchModal">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->

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
        <form>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="course">Course</label>
                  <select class="form-control select2" id="course" required>
                    <option value="">Select Course</option>
                    <option value="C001">Web Development</option>
                    <option value="C002">Digital Marketing</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="trainingCenter">Training Center</label>
                  <select class="form-control select2" id="trainingCenter" required>
                    <option value="">Select Center</option>
                    <option value="TC001">ABC Training Center - Mumbai</option>
                    <option value="TC002">ABC Training Center - Pune</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="trainer">Trainer</label>
                  <select class="form-control select2" id="trainer" required>
                    <option value="">Select Trainer</option>
                    <option value="T001">John Doe</option>
                    <option value="T002">Jane Smith</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="capacity">Batch Capacity</label>
                  <input type="number" class="form-control" id="capacity" placeholder="Enter batch capacity" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="startDate">Start Date</label>
                  <div class="input-group date" id="startDate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#startDate" required>
                    <div class="input-group-append" data-target="#startDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="endDate">End Date</label>
                  <div class="input-group date" id="endDate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#endDate" required>
                    <div class="input-group-append" data-target="#endDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="schedule">Schedule</label>
                  <textarea class="form-control" id="schedule" rows="3" placeholder="Enter batch schedule" required></textarea>
                </div>
                <div class="form-group">
                  <label for="remarks">Remarks</label>
                  <textarea class="form-control" id="remarks" rows="3" placeholder="Enter any remarks"></textarea>
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
                <label>Batch ID</label>
                <p>B001</p>
              </div>
            <div class="form-group">
                <label>Course</label>
                <p>Web Development</p>
            </div>
            <div class="form-group">
                <label>Training Center</label>
                <p>ABC Training Center - Mumbai</p>
            </div>
            <div class="form-group">
                <label>Trainer</label>
                <p>John Doe</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Start Date</label>
                <p>01/01/2024</p>
              </div>
              <div class="form-group">
                <label>End Date</label>
                <p>31/03/2024</p>
              </div>
              <div class="form-group">
                <label>Schedule</label>
                <p>Monday to Friday, 10:00 AM - 1:00 PM</p>
            </div>
            <div class="form-group">
                <label>Status</label>
                <p><span class="badge badge-success">Active</span></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h5>Enrolled Students</h5>
              <div class="table-responsive">
                <table class="table table-bordered">
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
                    <tr>
                      <td>S001</td>
                      <td>Rajesh Kumar</td>
                      <td>rajesh@example.com</td>
                      <td>+91 9876543210</td>
                      <td><span class="badge badge-success">Paid</span></td>
                      <td><span class="badge badge-success">Active</span></td>
                    </tr>
                    <tr>
                      <td>S002</td>
                      <td>Priya Sharma</td>
                      <td>priya@example.com</td>
                      <td>+91 9876543211</td>
                      <td><span class="badge badge-warning">Pending</span></td>
                      <td><span class="badge badge-success">Active</span></td>
                    </tr>
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
        <form>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editCourse">Course</label>
                  <select class="form-control select2" id="editCourse" required>
                    <option value="C001" selected>Web Development</option>
                    <option value="C002">Digital Marketing</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="editTrainingCenter">Training Center</label>
                  <select class="form-control select2" id="editTrainingCenter" required>
                    <option value="TC001" selected>ABC Training Center - Mumbai</option>
                    <option value="TC002">ABC Training Center - Pune</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="editTrainer">Trainer</label>
                  <select class="form-control select2" id="editTrainer" required>
                    <option value="T001" selected>John Doe</option>
                    <option value="T002">Jane Smith</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="editCapacity">Batch Capacity</label>
                  <input type="number" class="form-control" id="editCapacity" value="30" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editStartDate">Start Date</label>
                  <div class="input-group date" id="editStartDate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#editStartDate" value="01/01/2024" required>
                    <div class="input-group-append" data-target="#editStartDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="editEndDate">End Date</label>
                  <div class="input-group date" id="editEndDate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#editEndDate" value="31/03/2024" required>
                    <div class="input-group-append" data-target="#editEndDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="editSchedule">Schedule</label>
                  <textarea class="form-control" id="editSchedule" rows="3" required>Monday to Friday, 10:00 AM - 1:00 PM</textarea>
                </div>
                <div class="form-group">
                  <label for="editRemarks">Remarks</label>
                  <textarea class="form-control" id="editRemarks" rows="3">Regular batch with focus on practical training</textarea>
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

  <!-- Delete Batch Modal -->
  <div class="modal fade" id="deleteBatchModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Delete Batch</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this batch? This action cannot be undone.</p>
          <p><strong>Batch:</strong> B001 - Web Development</p>
          <p><strong>Enrolled Students:</strong> 25</p>
          <p><strong>Training Center:</strong> ABC Training Center - Mumbai</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger">Delete Batch</button>
        </div>
      </div>
    </div>
  </div>

<?php include 'includes/js.php'; ?>

<script>
  $(function () {
    // Initialize DataTable
    $('#batchesTable').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true
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
  });
</script>
</body>
</html> 
