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

// Fetch students from DB
$students = [];
$nextEnrollmentNo = '';
try {
  $pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );
  $stmt = $pdo->query('SELECT * FROM students ORDER BY created_at DESC');
  $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // Generate next enrollment number (e.g., ENR001, ENR002, ...)
  $last = $pdo->query("SELECT enrollment_no FROM students ORDER BY student_id DESC LIMIT 1")->fetchColumn();
  if (preg_match('/ENR(\\d+)/', $last, $m)) {
    $nextEnrollmentNo = 'ENR' . str_pad(((int)$m[1]) + 1, 3, '0', STR_PAD_LEFT);
  } else {
    $nextEnrollmentNo = 'ENR001';
  }
} catch (Exception $e) {
  echo '<div class="alert alert-danger">Could not fetch students: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
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
            <div class="float-sm-right mb-3">
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
        <div class="row mb-3">
          <div class="col-12 text-right">
            <!-- Duplicate Add New Student button removed -->
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Students List</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addStudentModal">
                <i class="fas fa-plus"></i> Add New Student
              </button>
            </div>
          </div>
          <div class="card-body">
            <table id="studentsTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Enrollment No</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Gender</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Date of Birth</th>
                  <th>Address</th>
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

  <!-- Add Student Modal -->
  <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addStudentForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="addEnrollmentNo">Enrollment No</label>
              <input type="text" class="form-control" id="addEnrollmentNo" name="enrollment_no" value="<?= htmlspecialchars($nextEnrollmentNo) ?>" readonly>
            </div>
            <div class="form-group">
              <label for="addFirstName">First Name</label>
              <input type="text" class="form-control" id="addFirstName" name="first_name" required>
            </div>
            <div class="form-group">
              <label for="addLastName">Last Name</label>
              <input type="text" class="form-control" id="addLastName" name="last_name" required>
            </div>
            <div class="form-group">
              <label for="addEmail">Email</label>
              <input type="email" class="form-control" id="addEmail" name="email">
            </div>
            <div class="form-group">
              <label for="addMobile">Mobile</label>
              <input type="text" class="form-control" id="addMobile" name="mobile">
            </div>
            <div class="form-group">
              <label for="addDOB">Date of Birth</label>
              <input type="date" class="form-control" id="addDOB" name="date_of_birth">
            </div>
            <div class="form-group">
              <label for="addGender">Gender</label>
              <select class="form-control" id="addGender" name="gender">
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="form-group">
              <label for="addAddress">Address</label>
              <textarea class="form-control" id="addAddress" name="address"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Student</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Student Modal -->
  <div class="modal fade" id="viewStudentModal" tabindex="-1" role="dialog" aria-labelledby="viewStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewStudentModalLabel">View Student</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <dl class="row">
            <dt class="col-sm-4">Enrollment No</dt>
            <dd class="col-sm-8" id="viewEnrollmentNo"></dd>
            <dt class="col-sm-4">First Name</dt>
            <dd class="col-sm-8" id="viewFirstName"></dd>
            <dt class="col-sm-4">Last Name</dt>
            <dd class="col-sm-8" id="viewLastName"></dd>
            <dt class="col-sm-4">Gender</dt>
            <dd class="col-sm-8" id="viewGender"></dd>
            <dt class="col-sm-4">Mobile</dt>
            <dd class="col-sm-8" id="viewMobile"></dd>
            <dt class="col-sm-4">Email</dt>
            <dd class="col-sm-8" id="viewEmail"></dd>
            <dt class="col-sm-4">Date of Birth</dt>
            <dd class="col-sm-8" id="viewDOB"></dd>
            <dt class="col-sm-4">Address</dt>
            <dd class="col-sm-8" id="viewAddress"></dd>
          </dl>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Student Modal -->
  <div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editStudentForm">
          <div class="modal-body">
            <input type="hidden" id="editStudentId" name="student_id">
            <div class="form-group">
              <label for="editFirstName">First Name</label>
              <input type="text" class="form-control" id="editFirstName" name="first_name" required>
            </div>
            <div class="form-group">
              <label for="editLastName">Last Name</label>
              <input type="text" class="form-control" id="editLastName" name="last_name" required>
            </div>
            <div class="form-group">
              <label for="editEmail">Email</label>
              <input type="email" class="form-control" id="editEmail" name="email">
            </div>
            <div class="form-group">
              <label for="editMobile">Mobile</label>
              <input type="text" class="form-control" id="editMobile" name="mobile">
            </div>
            <div class="form-group">
              <label for="editDOB">Date of Birth</label>
              <input type="date" class="form-control" id="editDOB" name="date_of_birth">
            </div>
            <div class="form-group">
              <label for="editGender">Gender</label>
              <select class="form-control" id="editGender" name="gender">
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="form-group">
              <label for="editAddress">Address</label>
              <textarea class="form-control" id="editAddress" name="address"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
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
          <input type="hidden" id="deleteStudentId">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteStudent">Delete</button>
        </div>
      </div>
    </div>
  </div>

<?php include 'includes/js.php'; ?>

<script>
  $(function () {
    // Initialize DataTable with AJAX
    var table = $('#studentsTable').DataTable({
      processing: true,
      serverSide: false,
      ajax: {
        url: 'inc/ajax/students_ajax.php',
        type: 'POST',
        data: { action: 'list' },
        dataSrc: function(json) {
          return json.data || [];
        }
      },
      columns: [
        { data: 'enrollment_no' },
        { data: 'first_name' },
        { data: 'last_name' },
        { data: 'gender', render: function(data) { return data ? data.charAt(0).toUpperCase() + data.slice(1) : ''; } },
        { data: 'mobile' },
        { data: 'email' },
        { data: 'date_of_birth' },
        { data: 'address' },
        { data: null, orderable: false, searchable: false, render: function(data, type, row) {
            return '<div class="btn-group" role="group">' +
              '<button class="btn btn-sm btn-info view-student-btn" data-student-id="' + row.student_id + '"><i class="fas fa-eye"></i></button>' +
              '<button class="btn btn-sm btn-primary edit-student-btn" data-student-id="' + row.student_id + '"><i class="fas fa-edit"></i></button>' +
              '<button class="btn btn-sm btn-danger delete-student-btn" data-student-id="' + row.student_id + '"><i class="fas fa-trash"></i></button>' +
              '</div>';
          }
        }
      ],
      dom: 'Bfrtip',
      buttons: [
        { extend: 'copy', className: 'btn btn-secondary btn-sm', text: 'Copy' },
        { extend: 'csv', className: 'btn btn-secondary btn-sm', text: 'CSV' },
        { extend: 'excel', className: 'btn btn-secondary btn-sm', text: 'Excel' },
        { extend: 'pdf', className: 'btn btn-secondary btn-sm', text: 'PDF' },
        { extend: 'print', className: 'btn btn-secondary btn-sm', text: 'Print' }
      ]
    });

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

    // Helper: reset and hide modals
    function resetStudentForm($form) {
      $form[0].reset();
      $form.find('.is-invalid').removeClass('is-invalid');
    }

    // Add Student AJAX
    $('#addStudentForm').on('submit', function(e) {
      e.preventDefault();
      var $form = $(this);
      var $btn = $form.find('button[type="submit"]');
      var originalText = $btn.html();
      $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
      $.post('inc/ajax/students_ajax.php', $form.serialize() + '&action=create', function(response) {
        if (response.success) {
          toastr.success(response.message || 'Student added successfully');
          $('#addStudentModal').modal('hide');
          resetStudentForm($form);
          table.ajax.reload(null, false);
        } else {
          toastr.error(response.message || 'Failed to add student');
        }
        $btn.prop('disabled', false).html(originalText);
      }, 'json').fail(function() {
        toastr.error('An error occurred. Please try again.');
        $btn.prop('disabled', false).html(originalText);
      });
    });

    // Edit Student AJAX
    $('#editStudentForm').on('submit', function(e) {
      e.preventDefault();
      var $form = $(this);
      var $btn = $form.find('button[type="submit"]');
      var originalText = $btn.html();
      $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
      $.post('inc/ajax/students_ajax.php', $form.serialize() + '&action=update', function(response) {
        if (response.success) {
          toastr.success(response.message || 'Student updated successfully');
          $('#editStudentModal').modal('hide');
          resetStudentForm($form);
          table.ajax.reload(null, false);
        } else {
          toastr.error(response.message || 'Failed to update student');
        }
        $btn.prop('disabled', false).html(originalText);
      }, 'json').fail(function() {
        toastr.error('An error occurred. Please try again.');
        $btn.prop('disabled', false).html(originalText);
      });
    });

    // Confirm Delete AJAX
    $('#confirmDeleteStudent').on('click', function() {
      var student_id = $('#deleteStudentId').val();
      var $btn = $(this);
      var originalText = $btn.html();
      $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
      $.post('inc/ajax/students_ajax.php', { action: 'delete', student_id: student_id }, function(response) {
        if (response.success) {
          toastr.success(response.message || 'Student deleted successfully');
          $('#deleteStudentModal').modal('hide');
          table.ajax.reload(null, false);
        } else {
          toastr.error(response.message || 'Failed to delete student');
        }
        $btn.prop('disabled', false).html(originalText);
      }, 'json').fail(function() {
        toastr.error('An error occurred. Please try again.');
        $btn.prop('disabled', false).html(originalText);
      });
    });

    // Reset forms on modal close
    $('#addStudentModal, #editStudentModal').on('hidden.bs.modal', function () {
      var $form = $(this).find('form');
      if ($form.length) resetStudentForm($form);
    });

    // Open Edit Modal and populate fields
    $(document).on('click', '.edit-student-btn', function() {
      var student_id = $(this).data('student-id');
      $.post('inc/ajax/students_ajax.php', { action: 'get', student_id: student_id }, function(response) {
        if (response.success && response.data) {
          var s = response.data;
          $('#editStudentId').val(s.student_id);
          $('#editFirstName').val(s.first_name);
          $('#editLastName').val(s.last_name);
          $('#editGender').val(s.gender);
          $('#editMobile').val(s.mobile);
          $('#editEmail').val(s.email);
          $('#editDOB').val(s.date_of_birth);
          $('#editAddress').val(s.address);
          $('#editStudentModal').modal('show');
        } else {
          toastr.error('Could not fetch student details.');
        }
      }, 'json').fail(function() {
        toastr.error('An error occurred. Please try again.');
      });
    });

    // Open Delete Modal
    $(document).on('click', '.delete-student-btn', function() {
      $('#deleteStudentId').val($(this).data('student-id'));
      $('#deleteStudentModal').modal('show');
    });

    // Open View Modal and populate fields
    $(document).on('click', '.view-student-btn', function() {
      var student_id = $(this).data('student-id');
      $.post('inc/ajax/students_ajax.php', { action: 'get', student_id: student_id }, function(response) {
        if (response.success && response.data) {
          var s = response.data;
          $('#viewEnrollmentNo').text(s.enrollment_no);
          $('#viewFirstName').text(s.first_name);
          $('#viewLastName').text(s.last_name);
          $('#viewGender').text(s.gender);
          $('#viewMobile').text(s.mobile);
          $('#viewEmail').text(s.email);
          $('#viewDOB').text(s.date_of_birth);
          $('#viewAddress').text(s.address);
          $('#viewStudentModal').modal('show');
        } else {
          toastr.error('Could not fetch student details.');
        }
      }, 'json').fail(function() {
        toastr.error('An error occurred. Please try again.');
      });
    });
  });
</script>
</body>
</html>
