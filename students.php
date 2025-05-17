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
    $courses = $pdo->query("SELECT course_id, course_name FROM courses WHERE status = 'active' ORDER BY course_name ASC")->fetchAll(PDO::FETCH_ASSOC);
    $batches = $pdo->query("SELECT batch_id, CONCAT(batch_code, ' - ', batch_name) AS batch_label FROM batches ORDER BY batch_code ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Could not fetch students: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Students</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
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
                            <div id="studentError" class="alert alert-danger d-none"></div>
                            <table id="studentsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Enrollment No</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Date of Birth</th>
                                        <th>Gender</th>
                                        <th>Address</th>
                                        
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addStudentModalLabel">Add New Student</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addStudentForm" novalidate enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="addStudentError" class="alert alert-danger d-none"></div>
                    <div class="container-fluid">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="addEnrollmentNo" class="form-label">Enrollment No <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="addEnrollmentNo" name="enrollment_no" value="<?= htmlspecialchars($nextEnrollmentNo) ?>" readonly required aria-required="true">
                            </div>
                            <div class="col-md-6">
                                <label for="addFullName" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="addFullName" name="full_name" required aria-required="true">
                            </div>
                            <div class="col-md-4">
                                <label for="addPhoto" class="form-label">Student Photo</label>
                                <input type="file" class="form-control" id="addPhoto" name="photo" accept="image/*">
                            </div>
                            <div class="col-md-4">
                                <label for="addAadhaar" class="form-label">Aadhaar Card</label>
                                <input type="file" class="form-control" id="addAadhaar" name="aadhaar" accept="application/pdf,image/*">
                            </div>
                            <div class="col-md-4">
                                <label for="addQualification" class="form-label">Qualification Document</label>
                                <input type="file" class="form-control" id="addQualification" name="qualification" accept="application/pdf,image/*">
                            </div>
                            <div class="col-md-6">
                                <label for="addEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="addEmail" name="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$">
                            </div>
                            <div class="col-md-6">
                                <label for="addMobile" class="form-label">Mobile</label>
                                <input type="tel" class="form-control" id="addMobile" name="mobile" pattern="^[0-9]{10}$" maxlength="10" minlength="10" required>
                            </div>
                            <div class="col-md-6">
                                <label for="addDOB" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="addDOB" name="date_of_birth">
                            </div>
                            <div class="col-md-6">
                                <label for="addGender" class="form-label">Gender</label>
                                <select class="form-control" id="addGender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="addAddress" class="form-label">Address</label>
                                <textarea class="form-control" id="addAddress" name="address"></textarea>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- View Student Modal -->
<div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content smart-modal">
            <div class="modal-header bg-primary text-white align-items-center">
            <div class="d-flex align-items-center w-100 position-relative">
                <div class="me-2" style="font-size:2rem;"><i class="fas fa-user-graduate"></i></div>
                <div>
                    <h4 class="modal-title mb-0" id="viewStudentModalLabel">Student Details</h4>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2" style="filter: invert(1); background: none;" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            </div>
            <div class="modal-body p-4">
                <div class="container-fluid">
                    <div class="row g-4 align-items-start">
                        <div class="col-md-5 col-12">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3"><i class="fas fa-id-card me-2"></i>Personal Info</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2"><span class="fw-bold">Enrollment No:</span> <span data-field="enrollment_no"></span></li>
                                        <li class="mb-2"><span class="fw-bold">Full Name:</span> <span data-field="full_name"></span></li>
                                        <li class="mb-2"><span class="fw-bold">Email:</span> <span data-field="email"></span></li>
                                        <li class="mb-2"><span class="fw-bold">Mobile:</span> <span data-field="mobile"></span></li>
                                        <li class="mb-2"><span class="fw-bold">DOB:</span> <span data-field="date_of_birth"></span></li>
                                        <li class="mb-2"><span class="fw-bold">Gender:</span> <span data-field="gender"></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 col-12">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3"><i class="fas fa-graduation-cap me-2"></i>Academic Info</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2"><span class="fw-bold">Training Partner:</span> <span data-field="partner_name"></span></li>
                                        <li class="mb-2"><span class="fw-bold">Training Center:</span> <span data-field="center_name"></span></li>
                                        <li class="mb-2"><span class="fw-bold">Scheme:</span> <span data-field="scheme_name"></span></li>
                                        <li class="mb-2"><span class="fw-bold">Sector:</span> <span data-field="sector_name"></span></li>
                                        <li class="mb-2"><span class="fw-bold">Course:</span> <span data-field="course_name"></span></li>
                                        <li class="mb-2"><span class="fw-bold">Batch:</span> <span data-field="batch_code"></span> <span data-field="batch_name"></span></li>
                                        <li class="mb-2"><span class="fw-bold">Address:</span> <span data-field="address"></span></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3"><i class="fas fa-file-alt me-2"></i>Documents</h6>
                                    <div class="row g-2">
                                        <div class="col-md-4 col-12 mb-2">
                                            <span class="fw-bold">Photo:</span> <span data-field="photo"></span>
                                        </div>
                                        <div class="col-md-4 col-12 mb-2">
                                            <span class="fw-bold">Aadhaar:</span> <span data-field="aadhaar"></span>
                                        </div>
                                        <div class="col-md-4 col-12 mb-2">
                                            <span class="fw-bold">Qualification:</span> <span data-field="qualification"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editStudentModalLabel">Edit Student</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editStudentForm" novalidate enctype="multipart/form-data">
                <input type="hidden" id="editStudentId" name="student_id">
                <div class="modal-body">
                    <div id="editStudentError" class="alert alert-danger d-none"></div>
                    <div class="container-fluid">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="editEnrollmentNo" class="form-label">Enrollment No <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editEnrollmentNo" name="enrollment_no" readonly required aria-required="true">
                            </div>
                            <div class="col-md-6">
                                <label for="editFullName" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editFullName" name="full_name" required aria-required="true" autocomplete="off" placeholder="Enter full name">
                            </div>
                            <div class="col-md-4">
                                <label for="editPhoto" class="form-label">Student Photo</label>
                                <input type="file" class="form-control" id="editPhoto" name="photo" accept="image/*">
                            </div>
                            <div class="col-md-4">
                                <label for="editAadhaar" class="form-label">Aadhaar Card</label>
                                <input type="file" class="form-control" id="editAadhaar" name="aadhaar" accept="application/pdf,image/*">
                            </div>
                            <div class="col-md-4">
                                <label for="editQualification" class="form-label">Qualification Document</label>
                                <input type="file" class="form-control" id="editQualification" name="qualification" accept="application/pdf,image/*">
                            </div>
                            <div class="col-md-6">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editEmail" name="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$">
                            </div>
                            <div class="col-md-6">
                                <label for="editMobile" class="form-label">Mobile</label>
                                <input type="tel" class="form-control" id="editMobile" name="mobile" pattern="^[0-9]{10}$" maxlength="10" minlength="10" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editDOB" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="editDOB" name="date_of_birth">
                            </div>
                            <div class="col-md-6">
                                <label for="editGender" class="form-label">Gender</label>
                                <select class="form-control" id="editGender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="editAddress" class="form-label">Address</label>
                                <textarea class="form-control" id="editAddress" name="address"></textarea>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Student</button>
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
                <h4 class="modal-title" id="deleteStudentModalLabel">Delete Student</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteStudentId">
                <p>Are you sure you want to delete this student? This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteStudent">Delete Student</button>
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
            dataSrc: function (json) { return json.data || []; }
        },
        columns: [
            { data: null, render: function (data, type, row, meta) { return meta.row + 1; } },
            { data: 'enrollment_no' },
            { data: 'first_name' },
            { data: 'last_name' },
            { data: 'email' },
            { data: 'mobile' },
            { data: 'date_of_birth' },
            { data: 'gender', render: function (data) { return data ? data.charAt(0).toUpperCase() + data.slice(1) : ''; } },
            { data: 'address' },
            
            { data: null, orderable: false, searchable: false, render: function (data, type, row) {
                return '<div class="btn-group btn-group-sm">' +
                    '<button type="button" class="btn btn-info view-student-btn" data-student-id="' + row.student_id + '"><i class="fas fa-eye"></i></button>' +
                    '<button type="button" class="btn btn-primary edit-student-btn" data-student-id="' + row.student_id + '"><i class="fas fa-edit"></i></button>' +
                    '<button type="button" class="btn btn-danger delete-student-btn" data-student-id="' + row.student_id + '"><i class="fas fa-trash"></i></button>' +
                    '</div>';
            } }
        ],
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        order: [[0, 'asc']]
    });

    function showError($el, message) {
        $el.removeClass('d-none').text(message).show();
    }
    function hideError($el) {
        $el.addClass('d-none').text('').hide();
    }

    // Reset forms and errors on modal close
    $('#addStudentModal, #editStudentModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        hideError($(this).find('.alert'));
        $(this).find('.is-invalid').removeClass('is-invalid');
    });

    $('#addStudentForm').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var $error = $('#addStudentError');
        hideError($error);
        var valid = true;
        $form.find('[required]').each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        if (!valid) {
            showError($error, 'Please fill all required fields.');
            return;
        }
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
        var formData = new FormData($form[0]);
        formData.append('action', 'create');
        $.ajax({
            url: 'inc/ajax/students_ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#addStudentModal').modal('hide');
                    table.ajax.reload();
                    toastr.success(response.message || 'Student added successfully.');
                } else {
                    showError($error, response.message || 'Failed to add student.');
                    toastr.error(response.message || 'Failed to add student.');
                }
                $btn.prop('disabled', false).text('Save Student');
            },
            error: function () {
                showError($error, 'Failed to add student.');
                toastr.error('Failed to add student.');
                $btn.prop('disabled', false).text('Save Student');
            }
        });
    });

    $('#editStudentForm').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var $error = $('#editStudentError');
        hideError($error);
        var valid = true;
        $form.find('[required]').each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        if (!valid) {
            showError($error, 'Please fill all required fields.');
            return;
        }
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');
        var formData = new FormData($form[0]);
        formData.append('action', 'update');
        $.ajax({
            url: 'inc/ajax/students_ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#editStudentModal').modal('hide');
                    table.ajax.reload();
                    toastr.success(response.message || 'Student updated successfully.');
                } else {
                    showError($error, response.message || 'Failed to update student.');
                    toastr.error(response.message || 'Failed to update student.');
                }
                $btn.prop('disabled', false).text('Update Student');
            },
            error: function () {
                showError($error, 'Failed to update student.');
                toastr.error('Failed to update student.');
                $btn.prop('disabled', false).text('Update Student');
            }
        });
    });

    $('#confirmDeleteStudent').on('click', function () {
        var studentId = $('#deleteStudentId').val();
        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Deleting...');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('inc/ajax/students_ajax.php', { action: 'delete', student_id: studentId }, function (response) {
                    if (response.success) {
                        $('#deleteStudentModal').modal('hide');
                        table.ajax.reload();
                        toastr.success(response.message || 'Student deleted successfully.');
                    } else {
                        toastr.error(response.message || 'Failed to delete student.');
                    }
                    $btn.prop('disabled', false).text('Delete Student');
                }, 'json');
            } else {
                $btn.prop('disabled', false).text('Delete Student');
            }
        });
    });

    $(document).on('click', '.view-student-btn', function () {
        var studentId = $(this).data('student-id');
        var modal = $('#viewStudentModal');
        modal.find('.alert').remove();
        $.post('inc/ajax/students_ajax.php', { action: 'get', student_id: studentId }, function (response) {
            if (response.success && response.data) {
                var data = response.data;
                // Compose full_name if not present, fallback to first_name + last_name
                var fullName = '';
                if (data.full_name && data.full_name.trim() !== '') {
                    fullName = data.full_name;
                } else if ((data.first_name && data.first_name.trim() !== '') || (data.last_name && data.last_name.trim() !== '')) {
                    fullName = ((data.first_name || '') + (data.last_name ? (' ' + data.last_name) : '')).trim();
                }
                modal.find('[data-field]').each(function () {
                    var field = $(this).data('field');
                    if (field === 'full_name') {
                        $(this).text(fullName);
                    } else if (field === 'photo') {
                        if (data.photo) {
                            var url = 'uploads/students/' + data.photo;
                            $(this).html('<a href="' + url + '" target="_blank">View Photo</a>');
                        } else {
                            $(this).text('No photo uploaded');
                        }
                    } else if (field === 'aadhaar') {
                        if (data.aadhaar) {
                            var url = 'uploads/students/' + data.aadhaar;
                            $(this).html('<a href="' + url + '" target="_blank">View Aadhaar</a>');
                        } else {
                            $(this).text('No Aadhaar uploaded');
                        }
                    } else if (field === 'qualification') {
                        if (data.qualification) {
                            var url = 'uploads/students/' + data.qualification;
                            $(this).html('<a href="' + url + '" target="_blank">View Qualification</a>');
                        } else {
                            $(this).text('No qualification uploaded');
                        }
                    } else if (field === 'batch_code') {
                        // Show batch code and batch name together if both exist
                        var batchText = (data.batch_code ? data.batch_code : '') + (data.batch_name ? ' - ' + data.batch_name : '');
                        $(this).text(batchText.trim());
                    } else {
                        $(this).text(data[field] || '');
                    }
                });
            } else {
                modal.find('[data-field]').text('');
                modal.find('.modal-body').prepend('<div class="alert alert-danger">' + (response.message || 'Failed to fetch student details.') + '</div>');
            }
            modal.modal('show');
        }, 'json');
    });

    $(document).on('click', '.edit-student-btn', function () {
        var studentId = $(this).data('student-id');
        // Show loading spinner in modal
        var modal = document.getElementById('editStudentModal');
        var bsModal = bootstrap.Modal.getOrCreateInstance(modal);
        $('#editStudentForm')[0].reset();
        $('#editStudentError').addClass('d-none').text('');
        bsModal.show();
        // Fetch data and populate form
        $.post('inc/ajax/students_ajax.php', { action: 'get', student_id: studentId }, function (response) {
            if (response.success) {
                var data = response.data;
                var fullName = '';
                if (data.full_name && data.full_name.trim() !== '') {
                    fullName = data.full_name;
                } else if ((data.first_name && data.first_name.trim() !== '') || (data.last_name && data.last_name.trim() !== '')) {
                    fullName = ((data.first_name || '') + (data.last_name ? (' ' + data.last_name) : '')).trim();
                }
                $('#editStudentForm').find('[name]').each(function () {
                    var name = $(this).attr('name');
                    if (name !== 'full_name') {
                        // For file inputs, do not set value (security restriction)
                        if ($(this).attr('type') === 'file') {
                            // Show link if file exists
                            var fileField = name;
                            var fileVal = data[fileField];
                            var linkId = '#current_' + fileField + '_link';
                            if (fileVal) {
                                var url = 'uploads/students/' + fileVal;
                                $(linkId).html('<a href="' + url + '" target="_blank">View Uploaded</a>');
                            } else {
                                $(linkId).html('');
                            }
                        } else {
                            $(this).val(data[name] || '');
                        }
                    }
                });
                $('#editFullName').val(fullName);
            } else {
                $('#editStudentError').removeClass('d-none').text(response.message || 'Failed to fetch student details.');
            }
        }, 'json');
    });

    $(document).on('click', '.delete-student-btn', function () {
        var studentId = $(this).data('student-id');
        $('#deleteStudentId').val(studentId);
        $('#deleteStudentModal').modal('show');
    });
});
</script>
</body>
</html>
