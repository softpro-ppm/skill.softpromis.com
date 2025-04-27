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
$pageTitle = 'Students';

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
            <h1 class="m-0">Students</h1>
          </div>
          <div class="col-sm-6">
            <div class="float-sm-right">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addStudentModal">
                <i class="fas fa-plus"></i> Add New Student
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
        <div class="card">
          <div class="card-body">
            <table id="studentsTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Enrollment No</th>
                  <th>Name</th>
                  <th>Gender</th>
                  <th>Phone</th>
                  <th>Email</th>
                  <th>Course</th>
                  <th>Batch</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>ENR001</td>
                  <td>Rahul Sharma</td>
                  <td>Male</td>
                  <td>9876543210</td>
                  <td>rahul@example.com</td>
                  <td>Web Development</td>
                  <td>B001</td>
                  <td><span class="badge badge-success">Active</span></td>
                  <td>
                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewStudentModal">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editStudentModal">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteStudentModal">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>ENR002</td>
                  <td>Priya Patel</td>
                  <td>Female</td>
                  <td>9876543211</td>
                  <td>priya@example.com</td>
                  <td>Digital Marketing</td>
                  <td>B002</td>
                  <td><span class="badge badge-success">Active</span></td>
                  <td>
                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewStudentModal">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editStudentModal">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteStudentModal">
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

  <!-- Add Student Modal -->
  <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addStudentForm">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="enrollmentNo">Enrollment Number</label>
                  <input type="text" class="form-control" id="enrollmentNo" readonly>
                  <small class="form-text text-muted">Auto-generated</small>
                </div>
                <div class="form-group">
                  <label for="name">Full Name</label>
                  <input type="text" class="form-control" id="name" required>
                </div>
                <div class="form-group">
                  <label for="gender">Gender</label>
                  <select class="form-control" id="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="phone">Phone Number</label>
                  <input type="tel" class="form-control" id="phone" required>
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" required>
                </div>
                <div class="form-group">
                  <label for="aadhaar">Aadhaar Number</label>
                  <input type="text" class="form-control" id="aadhaar" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="address">Full Address</label>
                  <textarea class="form-control" id="address" rows="3" required></textarea>
                </div>
                <div class="form-group">
                  <label for="trainingPartner">Training Partner</label>
                  <select class="form-control select2" id="trainingPartner" required>
                    <option value="">Select Training Partner</option>
                    <option value="1">Softpro Skill Solutions</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="trainingCenter">Training Center</label>
                  <select class="form-control select2" id="trainingCenter" required>
                    <option value="">Select Training Center</option>
                    <option value="1">Delhi Center</option>
                    <option value="2">Mumbai Center</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="scheme">Scheme</label>
                  <select class="form-control select2" id="scheme" required>
                    <option value="">Select Scheme</option>
                    <option value="1">PMKVY 4.0</option>
                    <option value="2">DDU-GKY</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="course">Course</label>
                  <select class="form-control select2" id="course" required>
                    <option value="">Select Course</option>
                    <option value="1">Web Development</option>
                    <option value="2">Digital Marketing</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="batch">Batch</label>
                  <select class="form-control select2" id="batch" required>
                    <option value="">Select Batch</option>
                    <option value="1">B001 - Web Development</option>
                    <option value="2">B002 - Digital Marketing</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row mt-4">
              <div class="col-md-12">
                <h6>Documents</h6>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="photo">Photograph</label>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="photo" accept="image/jpeg,image/jpg,application/pdf" required>
                        <label class="custom-file-label" for="photo">Choose file</label>
                      </div>
                      <small class="form-text text-muted">Max size: 1MB, Format: JPG, JPEG, PDF</small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="aadhaarDoc">Aadhaar Card</label>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="aadhaarDoc" accept="image/jpeg,image/jpg,application/pdf" required>
                        <label class="custom-file-label" for="aadhaarDoc">Choose file</label>
                      </div>
                      <small class="form-text text-muted">Max size: 1MB, Format: JPG, JPEG, PDF</small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="educationDoc">Educational Documents</label>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="educationDoc" accept="image/jpeg,image/jpg,application/pdf" required>
                        <label class="custom-file-label" for="educationDoc">Choose file</label>
                      </div>
                      <small class="form-text text-muted">Max size: 1MB, Format: JPG, JPEG, PDF</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save Student</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Student Modal -->
  <div class="modal fade" id="viewStudentModal" tabindex="-1" role="dialog" aria-labelledby="viewStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewStudentModalLabel">View Student</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Enrollment No:</strong> <span id="viewEnrollmentNo"></span></p>
              <p><strong>Name:</strong> <span id="viewName"></span></p>
              <p><strong>Gender:</strong> <span id="viewGender"></span></p>
              <p><strong>Phone:</strong> <span id="viewPhone"></span></p>
              <p><strong>Email:</strong> <span id="viewEmail"></span></p>
              <p><strong>Aadhaar:</strong> <span id="viewAadhaar"></span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Address:</strong> <span id="viewAddress"></span></p>
              <p><strong>Training Partner:</strong> <span id="viewTrainingPartner"></span></p>
              <p><strong>Training Center:</strong> <span id="viewTrainingCenter"></span></p>
              <p><strong>Scheme:</strong> <span id="viewScheme"></span></p>
              <p><strong>Course:</strong> <span id="viewCourse"></span></p>
              <p><strong>Batch:</strong> <span id="viewBatch"></span></p>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-md-12">
              <h6>Documents</h6>
              <div class="row">
                <div class="col-md-4">
                  <div class="card">
                    <div class="card-header">
                      <h6 class="card-title">Photograph</h6>
                    </div>
                    <div class="card-body">
                      <img src="#" id="viewPhoto" class="img-fluid" alt="Student Photo">
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card">
                    <div class="card-header">
                      <h6 class="card-title">Aadhaar Card</h6>
                    </div>
                    <div class="card-body">
                      <a href="#" id="viewAadhaarDoc" class="btn btn-primary btn-sm" target="_blank">View Document</a>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card">
                    <div class="card-header">
                      <h6 class="card-title">Educational Documents</h6>
                    </div>
                    <div class="card-body">
                      <a href="#" id="viewEducationDoc" class="btn btn-primary btn-sm" target="_blank">View Document</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-md-12">
              <h6>Payment History</h6>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Receipt No</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>RCPT001</td>
                    <td>01/01/2024</td>
                    <td>₹5,000</td>
                    <td>Online</td>
                    <td><span class="badge badge-success">Paid</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="window.print()">Print Application</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Student Modal -->
  <div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="editStudentForm">
            <!-- Same form fields as Add Student Modal -->
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

  <!-- Delete Student Modal -->
  <div class="modal fade" id="deleteStudentModal" tabindex="-1" role="dialog" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteStudentModalLabel">Delete Student</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this student? This action cannot be undone.</p>
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
    // Initialize DataTable with default configuration
    $('#studentsTable').DataTable();

    // Initialize Select2
    $('.select2').select2({
      theme: 'bootstrap4'
    });

    // Initialize custom file input
    bsCustomFileInput.init();

    // File size validation
    $('input[type="file"]').on('change', function() {
      const file = this.files[0];
      const maxSize = 1024 * 1024; // 1MB
      
      if (file && file.size > maxSize) {
        alert('File size exceeds 1MB limit');
        this.value = '';
        $(this).next('.custom-file-label').text('Choose file');
      }
    });

    // Cascading dropdowns
    $('#trainingPartner').on('change', function() {
      const partnerId = $(this).val();
      // Load training centers based on selected partner
    });

    $('#trainingCenter').on('change', function() {
      const centerId = $(this).val();
      // Load schemes based on selected center
    });

    $('#scheme').on('change', function() {
      const schemeId = $(this).val();
      // Load courses based on selected scheme
    });

    $('#course').on('change', function() {
      const courseId = $(this).val();
      // Load batches based on selected course
    });
  });
</script>
</body>
</html> 
