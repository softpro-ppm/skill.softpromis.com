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
              <button type="button" class="btn btn-primary" id="addCertificateBtn">
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
</div><!-- ./wrapper -->

<!-- Improved Modal Design -->
<div class="modal fade" id="certificateModal" tabindex="-1" role="dialog" aria-labelledby="certificateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="certificateForm">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="certificateModalLabel">Add/Edit Certificate</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="certificate_id" name="certificate_id">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="enrollment_id">Enrollment</label>
                <select class="form-control select2" id="enrollment_id" name="enrollment_id" required>
                  <option value="">Select Enrollment</option>
                </select>
              </div>
              <div class="form-group">
                <label for="certificate_number">Certificate Number</label>
                <input type="text" class="form-control" id="certificate_number" name="certificate_number" required>
              </div>
              <div class="form-group">
                <label for="certificate_type">Certificate Type</label>
                <select class="form-control" id="certificate_type" name="certificate_type" required>
                  <option value="">Select Type</option>
                  <option value="completion">Course Completion</option>
                  <option value="achievement">Achievement</option>
                  <option value="specialization">Specialization</option>
                </select>
              </div>
              <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                  <option value="issued">Issued</option>
                  <option value="revoked">Revoked</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="issue_date">Issue Date</label>
                <input type="date" class="form-control" id="issue_date" name="issue_date" required>
              </div>
              <div class="form-group">
                <label for="valid_until">Valid Until</label>
                <input type="date" class="form-control" id="valid_until" name="valid_until">
              </div>
              <div class="form-group">
                <label for="remarks">Remarks</label>
                <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveCertificateBtn">Save Certificate</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Enhanced View Certificate Modal -->
<div class="modal fade" id="viewCertificateModal" tabindex="-1" role="dialog" aria-labelledby="viewCertificateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="viewCertificateModalLabel">Certificate Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="viewCertificateBody">
        <!-- Populated by JS -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Enhanced Delete Certificate Modal -->
<div class="modal fade" id="deleteCertificateModal" tabindex="-1" role="dialog" aria-labelledby="deleteCertificateModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteCertificateModalLabel">Delete Certificate</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this certificate?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteCertificateBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/js.php'; ?>

<script>
  $(function () {
    $('#certificatesTable').DataTable({
      processing: true,
      serverSide: false,
      ajax: {
        url: 'inc/ajax/certificates_ajax.php',
        type: 'POST',
        data: { action: 'list' },
        dataSrc: function(json) {
          return json.data || [];
        }
      },
      columns: [
        { data: 'certificate_number' },
        { data: 'student_name' },
        { data: 'course_name' },
        { data: 'issue_date' },
        { data: 'valid_until' },
        { data: 'status', render: function(data) {
            var badge = 'secondary';
            if (data === 'Active') badge = 'success';
            if (data === 'Pending') badge = 'warning';
            if (data === 'Expired') badge = 'danger';
            return '<span class="badge badge-' + badge + '">' + data + '</span>';
          }
        },
        { data: null, orderable: false, searchable: false, render: function(data, type, row) {
            return '<button class="btn btn-sm btn-info view-certificate-btn" data-id="' + row.certificate_id + '"><i class="fas fa-eye"></i></button>' +
                   '<button class="btn btn-sm btn-primary edit-certificate-btn" data-id="' + row.certificate_id + '"><i class="fas fa-edit"></i></button>' +
                   '<button class="btn btn-sm btn-danger delete-certificate-btn" data-id="' + row.certificate_id + '"><i class="fas fa-trash"></i></button>';
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

    // Load enrollments for select
    function loadEnrollments() {
      $.ajax({
        url: 'inc/ajax/certificates_ajax.php',
        type: 'POST',
        data: { action: 'get_enrollments' },
        dataType: 'json',
        success: function (response) {
          var $select = $('#enrollment_id');
          $select.empty().append('<option value="">Select Enrollment</option>');
          if (response.success && response.data) {
            $.each(response.data, function (_, e) {
              $select.append('<option value="' + e.enrollment_id + '">' + e.student_name + ' (' + e.enrollment_id + ')</option>');
            });
          }
        }
      });
    }
    loadEnrollments();

    // Add/Edit Certificate
    $('#certificateForm').on('submit', function (e) {
      e.preventDefault();
      var formData = $(this).serialize();
      var action = $('#certificate_id').val() ? 'update' : 'create';
      formData += '&action=' + action;
      $.ajax({
        url: 'inc/ajax/certificates_ajax.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            toastr.success(response.message || 'Certificate saved successfully');
            $('#certificateModal').modal('hide');
            $('#certificateForm')[0].reset();
            $('#certificatesTable').DataTable().ajax.reload();
          } else {
            toastr.error(response.message || 'Error saving certificate');
          }
        },
        error: function () {
          toastr.error('An error occurred. Please try again.');
        }
      });
    });

    // Open Add Modal
    $(document).on('click', '#addCertificateBtn', function () {
      $('#certificateForm')[0].reset();
      $('#certificate_id').val('');
      $('#certificateModalLabel').text('Add Certificate');
      $('#certificateModal').modal('show');
    });

    // Open Edit Modal
    $(document).on('click', '.edit-certificate-btn', function () {
      var id = $(this).data('id');
      $.ajax({
        url: 'inc/ajax/certificates_ajax.php',
        type: 'POST',
        data: { action: 'get', certificate_id: id },
        dataType: 'json',
        success: function (response) {
          if (response.success && response.data) {
            var d = response.data;
            $('#certificate_id').val(d.certificate_id);
            $('#enrollment_id').val(d.enrollment_id).trigger('change');
            $('#certificate_number').val(d.certificate_number);
            $('#certificate_type').val(d.certificate_type);
            $('#status').val(d.status);
            $('#issue_date').val(d.issue_date);
            $('#valid_until').val(d.valid_until);
            $('#remarks').val(d.remarks);
            $('#certificateModalLabel').text('Edit Certificate');
            $('#certificateModal').modal('show');
          } else {
            toastr.error('Could not fetch certificate details.');
          }
        }
      });
    });

    // Open View Modal
    $(document).on('click', '.view-certificate-btn', function () {
      var id = $(this).data('id');
      $.ajax({
        url: 'inc/ajax/certificates_ajax.php',
        type: 'POST',
        data: { action: 'get', certificate_id: id },
        dataType: 'json',
        success: function (response) {
          if (response.success && response.data) {
            var d = response.data;
            var html = '<p><strong>Certificate Number:</strong> ' + d.certificate_number + '</p>' +
                       '<p><strong>Type:</strong> ' + d.certificate_type + '</p>' +
                       '<p><strong>Status:</strong> ' + d.status + '</p>' +
                       '<p><strong>Issue Date:</strong> ' + d.issue_date + '</p>' +
                       '<p><strong>Valid Until:</strong> ' + d.valid_until + '</p>' +
                       '<p><strong>Remarks:</strong> ' + (d.remarks || '') + '</p>';
            $('#viewCertificateBody').html(html);
            $('#viewCertificateModal').modal('show');
          } else {
            toastr.error('Could not fetch certificate details.');
          }
        }
      });
    });

    // Open Delete Modal
    var deleteCertificateId = null;
    $(document).on('click', '.delete-certificate-btn', function () {
      deleteCertificateId = $(this).data('id');
      $('#deleteCertificateModal').modal('show');
    });

    // Confirm Delete
    $('#confirmDeleteCertificateBtn').on('click', function () {
      if (!deleteCertificateId) return;
      $.ajax({
        url: 'inc/ajax/certificates_ajax.php',
        type: 'POST',
        data: { action: 'delete', certificate_id: deleteCertificateId },
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            toastr.success(response.message || 'Certificate deleted successfully');
            $('#deleteCertificateModal').modal('hide');
            $('#certificatesTable').DataTable().ajax.reload();
          } else {
            toastr.error(response.message || 'Error deleting certificate');
          }
        }
      });
    });
  });
</script>
</body>
</html>
