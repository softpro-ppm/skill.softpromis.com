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
$pageTitle = 'Courses';

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
            <h1 class="m-0">Courses</h1>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Courses List -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Courses List</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCourseModal">
                <i class="fas fa-plus"></i> Add New Course
              </button>
            </div>
          </div>
          <div class="card-body">
            <table id="coursesTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Course ID</th>
                  <th>Name</th>
                  <th>Sector</th>
                  <th>Duration</th>
                  <th>Fee</th>
                  <th>Active Batches</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>C001</td>
                  <td>Web Development</td>
                  <td>Information Technology</td>
                  <td>3 months</td>
                  <td>₹15,000</td>
                  <td>2</td>
                  <td><span class="badge badge-success">Active</span></td>
                  <td>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewCourseModal">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editCourseModal">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteCourseModal">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>C002</td>
                  <td>Digital Marketing</td>
                  <td>Digital Marketing</td>
                  <td>2 months</td>
                  <td>₹12,000</td>
                  <td>1</td>
                  <td><span class="badge badge-success">Active</span></td>
                  <td>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewCourseModal">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editCourseModal">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteCourseModal">
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
  </div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->

  <!-- Add Course Modal -->
  <div class="modal fade" id="addCourseModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Add New Course</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="courseName">Course Name</label>
                  <input type="text" class="form-control" id="courseName" placeholder="Enter course name" required>
                </div>
                <div class="form-group">
                  <label for="sector">Sector</label>
                  <select class="form-control select2" id="sector" required>
                    <option value="">Select Sector</option>
                    <option value="SEC001">Information Technology</option>
                    <option value="SEC002">Digital Marketing</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="duration">Duration (months)</label>
                  <input type="number" class="form-control" id="duration" placeholder="Enter duration" required>
                </div>
                <div class="form-group">
                  <label for="fee">Course Fee (₹)</label>
                  <input type="number" class="form-control" id="fee" placeholder="Enter fee" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea class="form-control" id="description" rows="3" placeholder="Enter course description" required></textarea>
                </div>
                <div class="form-group">
                  <label for="prerequisites">Prerequisites</label>
                  <textarea class="form-control" id="prerequisites" rows="3" placeholder="Enter prerequisites"></textarea>
                </div>
                <div class="form-group">
                  <label for="learningOutcomes">Learning Outcomes</label>
                  <textarea class="form-control" id="learningOutcomes" rows="3" placeholder="Enter learning outcomes" required></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Documents</label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="courseDoc">
                    <label class="custom-file-label" for="courseDoc">Course Document</label>
                  </div>
                  <div class="custom-file mt-2">
                    <input type="file" class="custom-file-input" id="curriculumDoc">
                    <label class="custom-file-label" for="curriculumDoc">Curriculum Document</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Course</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Course Modal -->
  <div class="modal fade" id="viewCourseModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">View Course Details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Course ID</label>
                <p>C001</p>
              </div>
              <div class="form-group">
                <label>Course Name</label>
                <p>Web Development</p>
              </div>
              <div class="form-group">
                <label>Sector</label>
                <p>Information Technology</p>
              </div>
              <div class="form-group">
                <label>Duration</label>
                <p>3 months</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Fee</label>
                <p>₹15,000</p>
              </div>
              <div class="form-group">
                <label>Prerequisites</label>
                <p>Basic knowledge of HTML and CSS</p>
              </div>
              <div class="form-group">
                <label>Active Batches</label>
                <p>2</p>
              </div>
              <div class="form-group">
                <label>Status</label>
                <p><span class="badge badge-success">Active</span></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Description</label>
                <p>Comprehensive course covering front-end and back-end web development technologies.</p>
              </div>
              <div class="form-group">
                <label>Learning Outcomes</label>
                <p>1. Build responsive websites<br>2. Create dynamic web applications<br>3. Implement database connectivity</p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h5>Current Batches</h5>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Batch ID</th>
                      <th>Start Date</th>
                      <th>End Date</th>
                      <th>Students</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>B001</td>
                      <td>01/01/2024</td>
                      <td>31/03/2024</td>
                      <td>25</td>
                      <td><span class="badge badge-success">Active</span></td>
                    </tr>
                    <tr>
                      <td>B005</td>
                      <td>15/02/2024</td>
                      <td>15/05/2024</td>
                      <td>20</td>
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

  <!-- Edit Course Modal -->
  <div class="modal fade" id="editCourseModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Course</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editCourseName">Course Name</label>
                  <input type="text" class="form-control" id="editCourseName" value="Web Development" required>
                </div>
                <div class="form-group">
                  <label for="editSector">Sector</label>
                  <select class="form-control select2" id="editSector" required>
                    <option value="SEC001" selected>Information Technology</option>
                    <option value="SEC002">Digital Marketing</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="editDuration">Duration (months)</label>
                  <input type="number" class="form-control" id="editDuration" value="3" required>
                </div>
                <div class="form-group">
                  <label for="editFee">Course Fee (₹)</label>
                  <input type="number" class="form-control" id="editFee" value="15000" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editDescription">Description</label>
                  <textarea class="form-control" id="editDescription" rows="3" required>Comprehensive course covering front-end and back-end web development technologies.</textarea>
                </div>
                <div class="form-group">
                  <label for="editPrerequisites">Prerequisites</label>
                  <textarea class="form-control" id="editPrerequisites" rows="3">Basic knowledge of HTML and CSS</textarea>
                </div>
                <div class="form-group">
                  <label for="editLearningOutcomes">Learning Outcomes</label>
                  <textarea class="form-control" id="editLearningOutcomes" rows="3" required>1. Build responsive websites
2. Create dynamic web applications
3. Implement database connectivity</textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Documents</label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="editCourseDoc">
                    <label class="custom-file-label" for="editCourseDoc">Course Document</label>
                  </div>
                  <div class="custom-file mt-2">
                    <input type="file" class="custom-file-input" id="editCurriculumDoc">
                    <label class="custom-file-label" for="editCurriculumDoc">Curriculum Document</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update Course</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete Course Modal -->
  <div class="modal fade" id="deleteCourseModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Delete Course</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this course? This action cannot be undone.</p>
          <p><strong>Course:</strong> Web Development</p>
          <p><strong>Active Batches:</strong> 2</p>
          <p><strong>Students:</strong> 45</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger">Delete Course</button>
        </div>
      </div>
    </div>
  </div>

<?php include 'includes/js.php'; ?>

<script>
  $(function () {
    // Initialize DataTable
    $('#coursesTable').DataTable({
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

    // Initialize custom file input
    bsCustomFileInput.init();
  });
</script>
</body>
</html> 
