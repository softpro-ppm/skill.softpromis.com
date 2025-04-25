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
$pageTitle = 'Certificates';

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
            <h1 class="m-0">Certificates</h1>
          </div>
          <div class="col-sm-6">
            <div class="float-sm-right">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCertificateModal">
                <i class="fas fa-plus"></i> Add New Certificate
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
                <h3>150</h3>
                <p>Total Certificates</p>
              </div>
              <div class="icon">
                <i class="fas fa-certificate"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>120</h3>
                <p>Issued Certificates</p>
              </div>
              <div class="icon">
                <i class="fas fa-check-circle"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>20</h3>
                <p>Pending Certificates</p>
              </div>
              <div class="icon">
                <i class="fas fa-clock"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>10</h3>
                <p>Expired Certificates</p>
              </div>
              <div class="icon">
                <i class="fas fa-times-circle"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Certificate List -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Certificate History</h3>
          </div>
          <div class="card-body">
            <table id="certificatesTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Certificate No</th>
                  <th>Student</th>
                  <th>Course</th>
                  <th>Issue Date</th>
                  <th>Expiry Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>CERT001</td>
                  <td>Rahul Sharma</td>
                  <td>Web Development</td>
                  <td>01/01/2024</td>
                  <td>01/01/2027</td>
                  <td><span class="badge badge-success">Active</span></td>
                  <td>
                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewCertificateModal">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editCertificateModal">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteCertificateModal">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>CERT002</td>
                  <td>Priya Patel</td>
                  <td>Digital Marketing</td>
                  <td>15/01/2024</td>
                  <td>15/01/2027</td>
                  <td><span class="badge badge-warning">Pending</span></td>
                  <td>
                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewCertificateModal">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editCertificateModal">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteCertificateModal">
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
</div><!-- ./wrapper -->

  <!-- Add Certificate Modal -->
  <div class="modal fade" id="addCertificateModal" tabindex="-1" role="dialog" aria-labelledby="addCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCertificateModalLabel">Add New Certificate</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addCertificateForm">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="certificateNo">Certificate Number</label>
                  <input type="text" class="form-control" id="certificateNo" readonly>
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
                  <label for="issueDate">Issue Date</label>
                  <div class="input-group date" id="issueDate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#issueDate" required>
                    <div class="input-group-append" data-target="#issueDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="expiryDate">Expiry Date</label>
                  <div class="input-group date" id="expiryDate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#expiryDate" required>
                    <div class="input-group-append" data-target="#expiryDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="certificateType">Certificate Type</label>
                  <select class="form-control" id="certificateType" required>
                    <option value="">Select Type</option>
                    <option value="completion">Course Completion</option>
                    <option value="achievement">Achievement</option>
                    <option value="specialization">Specialization</option>
                  </select>
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
          <button type="button" class="btn btn-primary">Save Certificate</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Certificate Modal -->
  <div class="modal fade" id="viewCertificateModal" tabindex="-1" role="dialog" aria-labelledby="viewCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewCertificateModalLabel">View Certificate</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Certificate No:</strong> <span id="viewCertificateNo"></span></p>
              <p><strong>Student:</strong> <span id="viewStudent"></span></p>
              <p><strong>Course:</strong> <span id="viewCourse"></span></p>
              <p><strong>Batch:</strong> <span id="viewBatch"></span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Issue Date:</strong> <span id="viewIssueDate"></span></p>
              <p><strong>Expiry Date:</strong> <span id="viewExpiryDate"></span></p>
              <p><strong>Certificate Type:</strong> <span id="viewCertificateType"></span></p>
              <p><strong>Status:</strong> <span id="viewStatus"></span></p>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-md-12">
              <h6>Certificate History</h6>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Certificate No</th>
                    <th>Issue Date</th>
                    <th>Expiry Date</th>
                    <th>Type</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>CERT001</td>
                    <td>01/01/2024</td>
                    <td>01/01/2027</td>
                    <td>Course Completion</td>
                    <td><span class="badge badge-success">Active</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="window.print()">Print Certificate</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Certificate Modal -->
  <div class="modal fade" id="editCertificateModal" tabindex="-1" role="dialog" aria-labelledby="editCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editCertificateModalLabel">Edit Certificate</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="editCertificateForm">
            <!-- Same form fields as Add Certificate Modal -->
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

  <!-- Delete Certificate Modal -->
  <div class="modal fade" id="deleteCertificateModal" tabindex="-1" role="dialog" aria-labelledby="deleteCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteCertificateModalLabel">Delete Certificate</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this certificate? This action cannot be undone.</p>
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
    // Initialize DataTable
    $('#certificatesTable').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "language": {
        "search": "_INPUT_",
        "searchPlaceholder": "Enter search term..."
      },
      "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
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
