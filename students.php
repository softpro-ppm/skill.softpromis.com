<?php
// Define BASEPATH constant
define('BASEPATH', true);

session_start();
require_once 'config.php';
require_once 'crud_functions.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Students';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$students = [];
$nextEnrollmentNo = '';
try {
  $pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<div id="success-alert" class="alert alert-success d-none" role="alert"></div>
<div id="error-alert" class="alert alert-danger d-none" role="alert"></div>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Students</h1>
        </div>
        <div class="col-sm-6">
          <div class="float-sm-right mb-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
              <i class="fas fa-plus"></i> Add New Student
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Students List</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
              <i class="fas fa-plus"></i> Add New Student
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="studentsTable" class="table table-bordered table-striped table-hover table-sm align-middle">
              <thead class="thead-dark">
                <tr>
                  <th class="text-center" style="width:60px;">Sr. No.</th>
                  <th>Enrollment No</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Gender</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Date of Birth</th>
                  <th>Address</th>
                  <th class="text-center" style="width:110px; white-space:nowrap;">Actions</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addStudentForm" novalidate>
        <div class="modal-body">
          <div class="mb-3">
            <label for="addEnrollmentNo" class="form-label">Enrollment No</label>
            <input type="text" class="form-control" id="addEnrollmentNo" name="enrollment_no" value="<?= htmlspecialchars($nextEnrollmentNo) ?>" readonly required>
          </div>
          <div class="mb-3">
            <label for="addFirstName" class="form-label">First Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="addFirstName" name="first_name" required>
            <div class="invalid-feedback">First name is required.</div>
          </div>
          <div class="mb-3">
            <label for="addLastName" class="form-label">Last Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="addLastName" name="last_name" required>
            <div class="invalid-feedback">Last name is required.</div>
          </div>
          <div class="mb-3">
            <label for="addEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="addEmail" name="email">
            <div class="invalid-feedback">Please enter a valid email.</div>
          </div>
          <div class="mb-3">
            <label for="addMobile" class="form-label">Mobile</label>
            <input type="tel" class="form-control" id="addMobile" name="mobile" pattern="^[0-9]{10,15}$">
            <div class="invalid-feedback">Please enter a valid mobile number.</div>
          </div>
          <div class="mb-3">
            <label for="addDOB" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="addDOB" name="date_of_birth">
          </div>
          <div class="mb-3">
            <label for="addGender" class="form-label">Gender</label>
            <select class="form-select" id="addGender" name="gender">
              <option value="">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="addAddress" class="form-label">Address</label>
            <textarea class="form-control" id="addAddress" name="address"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="btnAddStudent">Save Student</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editStudentForm" novalidate>
        <input type="hidden" id="editStudentId" name="student_id">
        <div class="modal-body">
          <div class="mb-3">
            <label for="editEnrollmentNo" class="form-label">Enrollment No</label>
            <input type="text" class="form-control" id="editEnrollmentNo" name="enrollment_no" readonly required>
          </div>
          <div class="mb-3">
            <label for="editFirstName" class="form-label">First Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="editFirstName" name="first_name" required>
            <div class="invalid-feedback">First name is required.</div>
          </div>
          <div class="mb-3">
            <label for="editLastName" class="form-label">Last Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="editLastName" name="last_name" required>
            <div class="invalid-feedback">Last name is required.</div>
          </div>
          <div class="mb-3">
            <label for="editEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="editEmail" name="email">
            <div class="invalid-feedback">Please enter a valid email.</div>
          </div>
          <div class="mb-3">
            <label for="editMobile" class="form-label">Mobile</label>
            <input type="tel" class="form-control" id="editMobile" name="mobile" pattern="^[0-9]{10,15}$">
            <div class="invalid-feedback">Please enter a valid mobile number.</div>
          </div>
          <div class="mb-3">
            <label for="editDOB" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="editDOB" name="date_of_birth">
          </div>
          <div class="mb-3">
            <label for="editGender" class="form-label">Gender</label>
            <select class="form-select" id="editGender" name="gender">
              <option value="">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="editAddress" class="form-label">Address</label>
            <textarea class="form-control" id="editAddress" name="address"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="btnEditStudent">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Delete Student Modal -->
<div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteStudentModalLabel">Delete Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="deleteStudentId" name="student_id">
        <p>Are you sure you want to delete this student? This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteStudent">Delete</button>
      </div>
    </div>
  </div>
</div>
<!-- View Student Modal -->
<div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewStudentModalLabel">View Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2"><strong>Enrollment No:</strong> <span id="viewEnrollmentNo"></span></div>
        <div class="mb-2"><strong>First Name:</strong> <span id="viewFirstName"></span></div>
        <div class="mb-2"><strong>Last Name:</strong> <span id="viewLastName"></span></div>
        <div class="mb-2"><strong>Gender:</strong> <span id="viewGender"></span></div>
        <div class="mb-2"><strong>Mobile:</strong> <span id="viewMobile"></span></div>
        <div class="mb-2"><strong>Email:</strong> <span id="viewEmail"></span></div>
        <div class="mb-2"><strong>Date of Birth:</strong> <span id="viewDOB"></span></div>
        <div class="mb-2"><strong>Address:</strong> <span id="viewAddress"></span></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/js.php'; ?>
<script>
$(function () {
  var table = $('#studentsTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
      url: 'inc/ajax/students_ajax.php',
      type: 'POST',
      data: { action: 'list' },
      dataSrc: function(json) { return json.data || []; }
    },
    columns: [
      { data: null, render: function (data, type, row, meta) { return meta.row + 1; }, orderable: false, searchable: false, className: 'text-center' },
      { data: 'enrollment_no' },
      { data: 'first_name' },
      { data: 'last_name' },
      { data: 'gender', render: function(data) { return data ? data.charAt(0).toUpperCase() + data.slice(1) : ''; } },
      { data: 'mobile' },
      { data: 'email' },
      { data: 'date_of_birth' },
      { data: 'address' },
      { data: null, orderable: false, searchable: false, className: 'text-center', render: function(data, type, row) {
          return '<div class="btn-group btn-group-sm">' +
            '<button class="btn btn-info view-student-btn" data-student-id="' + row.student_id + '"><i class="fas fa-eye"></i></button>' +
            '<button class="btn btn-primary edit-student-btn" data-student-id="' + row.student_id + '"><i class="fas fa-edit"></i></button>' +
            '<button class="btn btn-danger delete-student-btn" data-student-id="' + row.student_id + '"><i class="fas fa-trash"></i></button>' +
            '</div>';
        }
      }
    ],
    responsive: true,
    order: [[1, 'desc']]
  });
  function showAlert(type, message) {
    var alertId = type === 'success' ? '#success-alert' : '#error-alert';
    $(alertId).removeClass('d-none').html(message).fadeIn().delay(3000).fadeOut(function(){ $(this).addClass('d-none'); });
  }
  $('#addStudentForm').on('submit', function(e) {
    e.preventDefault();
    var $form = $(this);
    var $btn = $('#btnAddStudent');
    if (!$form[0].checkValidity()) {
      $form.addClass('was-validated');
      return;
    }
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
    $.post('inc/ajax/students_ajax.php', $form.serialize() + '&action=create', function(response) {
      if (response.status === 'success') {
        showAlert('success', response.message || 'Student added successfully.');
        $('#addStudentModal').modal('hide');
        $form[0].reset();
        $form.removeClass('was-validated');
        table.ajax.reload(null, false);
      } else {
        showAlert('error', response.message || 'Failed to add student.');
      }
      $btn.prop('disabled', false).html('Save Student');
    }, 'json').fail(function() {
      showAlert('error', 'An error occurred. Please try again.');
      $btn.prop('disabled', false).html('Save Student');
    });
  });
  $('#editStudentForm').on('submit', function(e) {
    e.preventDefault();
    var $form = $(this);
    var $btn = $('#btnEditStudent');
    if (!$form[0].checkValidity()) {
      $form.addClass('was-validated');
      return;
    }
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
    $.post('inc/ajax/students_ajax.php', $form.serialize() + '&action=update', function(response) {
      if (response.status === 'success') {
        showAlert('success', response.message || 'Student updated successfully.');
        $('#editStudentModal').modal('hide');
        $form[0].reset();
        $form.removeClass('was-validated');
        table.ajax.reload(null, false);
      } else {
        showAlert('error', response.message || 'Failed to update student.');
      }
      $btn.prop('disabled', false).html('Save Changes');
    }, 'json').fail(function() {
      showAlert('error', 'An error occurred. Please try again.');
      $btn.prop('disabled', false).html('Save Changes');
    });
  });
  $('#confirmDeleteStudent').on('click', function() {
    var student_id = $('#deleteStudentId').val();
    var $btn = $(this);
    var originalText = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Deleting...');
    $.post('inc/ajax/students_ajax.php', { action: 'delete', student_id: student_id }, function(response) {
      if (response.status === 'success') {
        showAlert('success', response.message || 'Student deleted successfully.');
        $('#deleteStudentModal').modal('hide');
        table.ajax.reload(null, false);
      } else {
        showAlert('error', response.message || 'Failed to delete student.');
      }
      $btn.prop('disabled', false).html(originalText);
    }, 'json').fail(function() {
      showAlert('error', 'An error occurred. Please try again.');
      $btn.prop('disabled', false).html(originalText);
    });
  });
  $('#addStudentModal, #editStudentModal').on('hidden.bs.modal', function () {
    var $form = $(this).find('form');
    if ($form.length) {
      $form[0].reset();
      $form.removeClass('was-validated');
    }
  });
  $(document).on('click', '.edit-student-btn', function() {
    var student_id = $(this).data('student-id');
    $.post('inc/ajax/students_ajax.php', { action: 'get', student_id: student_id }, function(response) {
      if (response.status === 'success' && response.data) {
        var s = response.data;
        $('#editStudentId').val(s.student_id);
        $('#editEnrollmentNo').val(s.enrollment_no);
        $('#editFirstName').val(s.first_name);
        $('#editLastName').val(s.last_name);
        $('#editGender').val(s.gender);
        $('#editMobile').val(s.mobile);
        $('#editEmail').val(s.email);
        $('#editDOB').val(s.date_of_birth);
        $('#editAddress').val(s.address);
        $('#editStudentModal').modal('show');
      } else {
        showAlert('error', 'Could not fetch student details.');
      }
    }, 'json').fail(function() {
      showAlert('error', 'An error occurred. Please try again.');
    });
  });
  $(document).on('click', '.delete-student-btn', function() {
    $('#deleteStudentId').val($(this).data('student-id'));
    $('#deleteStudentModal').modal('show');
  });
  $(document).on('click', '.view-student-btn', function() {
    var student_id = $(this).data('student-id');
    $.post('inc/ajax/students_ajax.php', { action: 'get', student_id: student_id }, function(response) {
      if (response.status === 'success' && response.data) {
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
        showAlert('error', 'Could not fetch student details.');
      }
    }, 'json').fail(function() {
      showAlert('error', 'An error occurred. Please try again.');
    });
  });
});
</script>
</body>
</html>
