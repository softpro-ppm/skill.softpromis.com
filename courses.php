<?php
// courses.php - Courses Management Page
// --------------------------------------------------
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
require_once 'config.php';
require_once 'crud_functions.php';
define('BASEPATH', true);
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
$pageTitle = 'Courses';
$sectors = Sector::getAll();
$schemes = Scheme::getAll();
$centers = TrainingCenter::getAll();
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Courses</h1>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title">Courses List</h3>
          <div class="card-tools d-flex justify-content-end w-100" style="gap: 10px;">
            <button type="button" class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#addCourseModal">
              <i class="fas fa-plus"></i> Add New Course
            </button>
          </div>
        </div>
        <div class="card-body">
          <table id="coursesTable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Sector</th>
                <th>Scheme</th>
                <th>Duration (hours)</th>
                <th>Fee</th>
                <th>Description</th>
                <th>Prerequisites</th>
                <th>Syllabus</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="coursesTableBody">
              <!-- Dynamic rows loaded by AJAX -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title">Add New Course</h4>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form>
        <div class="modal-body">
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label for="center_id" class="form-label fw-bold">Training Center</label>
              <select class="form-select form-control" id="center_id" name="center_id" required>
                <option value="">Select Training Center</option>
                <?php foreach ($centers as $center): ?>
                  <option value="<?= htmlspecialchars($center['center_id']) ?>"><?= htmlspecialchars($center['center_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label for="scheme_id" class="form-label fw-bold">Scheme</label>
              <select class="form-select form-control" id="scheme_id" name="scheme_id">
                <option value="">Select Scheme</option>
                <?php foreach ($schemes as $scheme): ?>
                  <option value="<?= htmlspecialchars($scheme['scheme_id']) ?>"><?= htmlspecialchars($scheme['scheme_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label for="sector_id" class="form-label fw-bold">Sector</label>
              <select class="form-select form-control" id="sector_id" name="sector_id" required>
                <option value="">Select Sector</option>
                <?php foreach ($sectors as $sector): ?>
                  <option value="<?= htmlspecialchars($sector['sector_id']) ?>"><?= htmlspecialchars($sector['sector_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label for="course_code" class="form-label fw-bold">Course Code</label>
              <input type="text" class="form-control mb-2" id="course_code" name="course_code" required>
            </div>
            <div class="col-md-6">
              <label for="course_name" class="form-label fw-bold">Course Name</label>
              <input type="text" class="form-control mb-2" id="course_name" name="course_name" required>
            </div>
            <div class="col-md-6">
              <label for="duration_hours" class="form-label fw-bold">Duration (hours)</label>
              <input type="number" class="form-control mb-2" id="duration_hours" name="duration_hours" required>
            </div>
            <div class="col-md-6">
              <label for="fee" class="form-label fw-bold">Fee</label>
              <input type="number" step="0.01" class="form-control mb-2" id="fee" name="fee">
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label for="description" class="form-label fw-bold">Description</label>
              <textarea class="form-control mb-2" id="description" name="description" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label for="prerequisites" class="form-label fw-bold">Prerequisites</label>
              <textarea class="form-control mb-2" id="prerequisites" name="prerequisites" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label for="syllabus" class="form-label fw-bold">Syllabus</label>
              <textarea class="form-control mb-2" id="syllabus" name="syllabus" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label for="status" class="form-label fw-bold">Status</label>
              <select class="form-select form-control mb-2" id="status" name="status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Course</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Course Modal -->
<div class="modal fade" id="viewCourseModal" tabindex="-1" aria-labelledby="viewCourseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title" id="viewCourseModalLabel">View Course: <span id="viewCourseTitle"></span></h4>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-md-6">
              <div class="mb-2"><strong>Training Center:</strong> <span data-field="center_name"></span></div>
              <div class="mb-2"><strong>Scheme:</strong> <span data-field="scheme_name"></span></div>
              <div class="mb-2"><strong>Sector:</strong> <span data-field="sector_name"></span></div>
              <div class="mb-2"><strong>Course Code:</strong> <span data-field="course_code"></span></div>
              <div class="mb-2"><strong>Course Name:</strong> <span data-field="course_name"></span></div>
              <div class="mb-2"><strong>Fee:</strong> <span data-field="fee"></span></div>
            </div>
            <div class="col-md-6">
              <div class="mb-2"><strong>Duration (hours):</strong> <span data-field="duration_hours"></span></div>
              <div class="mb-2"><strong>Status:</strong> <span data-field="status"></span></div>
              <div class="mb-2"><strong>Created At:</strong> <span data-field="created_at"></span></div>
              <div class="mb-2"><strong>Updated At:</strong> <span data-field="updated_at"></span></div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-md-12">
              <div class="mb-2"><strong>Description:</strong><br><span data-field="description"></span></div>
              <div class="mb-2"><strong>Prerequisites:</strong><br><span data-field="prerequisites"></span></div>
              <div class="mb-2"><strong>Syllabus:</strong><br><span data-field="syllabus"></span></div>
              <div class="mb-2"><strong>Assigned To (Sector / Scheme / Center):</strong>
                <ul id="assigned-courses-list" class="ps-3 mb-0"></ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title">Edit Course</h4>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form>
        <div class="modal-body">
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label for="edit_center_id" class="form-label fw-bold">Training Center</label>
              <select class="form-select form-control" id="edit_center_id" name="center_id" required>
                <option value="">Select Training Center</option>
                <?php foreach ($centers as $center): ?>
                  <option value="<?= htmlspecialchars($center['center_id']) ?>"><?= htmlspecialchars($center['center_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label for="edit_scheme_id" class="form-label fw-bold">Scheme</label>
              <select class="form-select form-control" id="edit_scheme_id" name="scheme_id">
                <option value="">Select Scheme</option>
                <?php foreach ($schemes as $scheme): ?>
                  <option value="<?= htmlspecialchars($scheme['scheme_id']) ?>"><?= htmlspecialchars($scheme['scheme_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label for="edit_sector_id" class="form-label fw-bold">Sector</label>
              <select class="form-select form-control" id="edit_sector_id" name="sector_id" required>
                <option value="">Select Sector</option>
                <?php foreach ($sectors as $sector): ?>
                  <option value="<?= htmlspecialchars($sector['sector_id']) ?>"><?= htmlspecialchars($sector['sector_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label for="edit_course_code" class="form-label fw-bold">Course Code</label>
              <input type="text" class="form-control mb-2" id="edit_course_code" name="course_code" required autocomplete="off">
            </div>
            <div class="col-md-6">
              <label for="edit_course_name" class="form-label fw-bold">Course Name</label>
              <input type="text" class="form-control mb-2" id="edit_course_name" name="course_name" required autocomplete="off">
            </div>
            <div class="col-md-6">
              <label for="edit_duration_hours" class="form-label fw-bold">Duration (hours)</label>
              <input type="number" class="form-control mb-2" id="edit_duration_hours" name="duration_hours" required autocomplete="off">
            </div>
            <div class="col-md-6">
              <label for="edit_fee" class="form-label fw-bold">Fee</label>
              <input type="number" step="0.01" class="form-control mb-2" id="edit_fee" name="fee" autocomplete="off">
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label for="edit_description" class="form-label fw-bold">Description</label>
              <textarea class="form-control mb-2" id="edit_description" name="description" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label for="edit_prerequisites" class="form-label fw-bold">Prerequisites</label>
              <textarea class="form-control mb-2" id="edit_prerequisites" name="prerequisites" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label for="edit_syllabus" class="form-label fw-bold">Syllabus</label>
              <textarea class="form-control mb-2" id="edit_syllabus" name="syllabus" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label for="edit_status" class="form-label fw-bold">Status</label>
              <select class="form-select form-control mb-2" id="edit_status" name="status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
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
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this course? This action cannot be undone.</p>
        <p><strong>Course:</strong> <span id="deleteCourseName">Web Development</span></p>
        <p><strong>Active Batches:</strong> <span id="deleteActiveBatches">2</span></p>
        <p><strong>Students:</strong> <span id="deleteStudents">45</span></p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger">Delete Course</button>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/js.php'; ?>
<script>
$(function () {
  // DataTable
  var coursesTable = $('#coursesTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
      url: 'inc/ajax/courses_ajax.php',
      type: 'POST',
      data: function (d) { d.action = 'read'; d.per_page = 100; },
      dataSrc: function (json) { return json.data || []; }
    },
    columns: [
      { data: 'course_code' },
      { data: 'course_name' },
      { data: 'sector_name' },
      { data: 'scheme_name' },
      { data: 'duration_hours' },
      { data: 'fee' },
      { data: 'description' },
      { data: 'prerequisites' },
      { data: 'syllabus' },
      { data: 'status', render: function (data) { return '<span class="badge badge-' + (data === 'active' ? 'success' : 'secondary') + '">' + (data ? data.charAt(0).toUpperCase() + data.slice(1) : '') + '</span>'; } },
      { data: null, orderable: false, searchable: false, render: function (data, type, row) {
        return '<div class="btn-group btn-group-sm">' +
          '<button type="button" class="btn btn-info view-course-btn" data-id="' + row.course_id + '"><i class="fas fa-eye"></i></button>' +
          '<button type="button" class="btn btn-primary edit-course-btn" data-id="' + row.course_id + '"><i class="fas fa-edit"></i></button>' +
          '<button type="button" class="btn btn-danger delete-course-btn" data-id="' + row.course_id + '"><i class="fas fa-trash"></i></button>' +
        '</div>';
      } }
    ],
    responsive: true,
    lengthChange: true,
    autoWidth: false,
    order: [[0, 'asc']]
  });

  // Add Course
  $('#addCourseModal form').on('submit', function (e) {
    e.preventDefault();
    var $form = $(this);
    var courseCode = $('#course_code').val().trim();
    var courseName = $('#course_name').val().trim();
    var sectorId = $('#sector_id').val();
    var durationHours = $('#duration_hours').val();
    if (!courseCode || !courseName || !sectorId || sectorId === '' || sectorId === '0' || !durationHours || durationHours <= 0) {
      toastr.error('Please fill all required fields: Course Code, Course Name, Sector, and Duration.');
      return;
    }
    var formData = {
      action: 'create',
      course_code: courseCode,
      course_name: courseName,
      center_id: $('#center_id').val(),
      sector_id: sectorId,
      scheme_id: $('#scheme_id').val(),
      duration_hours: durationHours,
      fee: $('#fee').val(),
      description: $('#description').val(),
      prerequisites: $('#prerequisites').val(),
      syllabus: $('#syllabus').val(),
      status: $('#status').val()
    };
    $.ajax({
      url: 'inc/ajax/courses_ajax.php',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          $('#addCourseModal').modal('hide');
          toastr.success(response.message || 'Course added successfully');
          $('#addCourseModal').one('hidden.bs.modal', function() {
            coursesTable.ajax.reload(null, false);
          });
          setTimeout(function() {
            $form[0].reset();
            $form.find('.is-invalid').removeClass('is-invalid');
          }, 500);
        } else {
          toastr.error(response.message || 'Error adding course');
        }
      },
      error: function() {
        toastr.error('Error adding course');
      }
    });
  });

  // Edit Course
  $(document).on('click', '.edit-course-btn', function () {
    var id = $(this).data('id');
    $.ajax({
      url: 'inc/ajax/courses_ajax.php',
      type: 'POST',
      data: { action: 'get', course_id: id },
      dataType: 'json',
      success: function (response) {
        if (response.success && response.data) {
          var c = response.data;
          $('#edit_course_code').val(c.course_code);
          $('#edit_course_name').val(c.course_name);
          $('#edit_center_id').val(c.center_id).trigger('change');
          $('#edit_sector_id').val(c.sector_id).trigger('change');
          $('#edit_scheme_id').val(c.scheme_id).trigger('change');
          $('#edit_duration_hours').val(c.duration_hours);
          $('#edit_fee').val(c.fee);
          $('#edit_description').val(c.description);
          $('#edit_prerequisites').val(c.prerequisites);
          $('#edit_syllabus').val(c.syllabus);
          $('#edit_status').val(c.status);
          $('#editCourseModal').data('id', c.course_id).modal('show');
        } else {
          toastr.error('Could not fetch course details.');
        }
      },
      error: function() {
        toastr.error('Could not fetch course details.');
      }
    });
  });
  $('#editCourseModal form').on('submit', function (e) {
    e.preventDefault();
    var $form = $(this);
    var id = $('#editCourseModal').data('id');
    var formData = {
      action: 'update',
      course_id: id,
      course_code: $('#edit_course_code').val(),
      course_name: $('#edit_course_name').val(),
      center_id: $('#edit_center_id').val(),
      sector_id: $('#edit_sector_id').val(),
      scheme_id: $('#edit_scheme_id').val(),
      duration_hours: $('#edit_duration_hours').val(),
      fee: $('#edit_fee').val(),
      description: $('#edit_description').val(),
      prerequisites: $('#edit_prerequisites').val(),
      syllabus: $('#edit_syllabus').val(),
      status: $('#edit_status').val()
    };
    $.ajax({
      url: 'inc/ajax/courses_ajax.php',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          $('#editCourseModal').modal('hide');
          toastr.success(response.message || 'Course updated successfully');
          $('#editCourseModal').one('hidden.bs.modal', function() {
            coursesTable.ajax.reload(null, false);
          });
          setTimeout(function() {
            $form[0].reset();
            $form.find('.is-invalid').removeClass('is-invalid');
          }, 500);
        } else {
          toastr.error(response.message || 'Error updating course');
        }
      },
      error: function() {
        toastr.error('Error updating course');
      }
    });
  });

  // Delete Course
  $(document).on('click', '.delete-course-btn', function () {
    var id = $(this).data('id');
    if (!id) {
        toastr.error('Course ID is missing.');
        return;
    }
    $('#deleteCourseModal').data('id', id).modal('show');
  });
  $('#deleteCourseModal .btn-danger').on('click', function () {
    var id = $('#deleteCourseModal').data('id');
    if (!id) {
        toastr.error('Course ID is missing.');
        return;
    }
    $.ajax({
      url: 'inc/ajax/courses_ajax.php',
      type: 'POST',
      data: { action: 'delete', course_id: id },
      dataType: 'json',
      success: function (response) {
        $('#deleteCourseModal').modal('hide');
        if (response.success) {
          toastr.success(response.message || 'Course deleted successfully');
          coursesTable.ajax.reload(null, false);
        } else {
          toastr.error(response.message || 'Error deleting course');
        }
      },
      error: function() {
        $('#deleteCourseModal').modal('hide');
        toastr.error('Error deleting course');
      }
    });
  });

  // View Course
  $(document).on('click', '.view-course-btn', function () {
    var id = $(this).data('id');
    $.ajax({
      url: 'inc/ajax/courses_ajax.php',
      type: 'POST',
      data: { action: 'get', course_id: id },
      dataType: 'json',
      success: function (response) {
        if (response.success && response.data) {
          var c = response.data;
          $('#viewCourseTitle').text(c.course_name);
          $('#viewCourseModal [data-field="course_code"]').text(c.course_code);
          $('#viewCourseModal [data-field="course_name"]').text(c.course_name);
          $('#viewCourseModal [data-field="center_name"]').text(c.center_name);
          $('#viewCourseModal [data-field="scheme_name"]').text(c.scheme_name);
          $('#viewCourseModal [data-field="sector_name"]').text(c.sector_name);
          $('#viewCourseModal [data-field="duration_hours"]').text(c.duration_hours);
          $('#viewCourseModal [data-field="fee"]').text(c.fee);
          $('#viewCourseModal [data-field="description"]').text(c.description);
          $('#viewCourseModal [data-field="prerequisites"]').text(c.prerequisites);
          $('#viewCourseModal [data-field="syllabus"]').text(c.syllabus);
          $('#viewCourseModal [data-field="status"]').html('<span class="badge badge-' + (c.status === 'active' ? 'success' : 'secondary') + '">' + (c.status ? c.status.charAt(0).toUpperCase() + c.status.slice(1) : '') + '</span>');
          $('#viewCourseModal [data-field="created_at"]').text(c.created_at || '');
          $('#viewCourseModal [data-field="updated_at"]').text(c.updated_at || '');
          $('#viewCourseModal').modal('show');
        } else {
          toastr.error('Could not fetch course details.');
        }
      },
      error: function() {
        toastr.error('Could not fetch course details.');
      }
    });
    $.ajax({
      url: 'inc/ajax/courses_ajax.php',
      type: 'POST',
      data: { action: 'get_assigned_courses', course_id: id },
      dataType: 'json',
      success: function(res) {
        var $list = $('#assigned-courses-list');
        $list.empty();
        if (res.success && res.data && res.data.length) {
          res.data.forEach(function(item) {
            $list.append('<li>' + item.sector_name + ' / ' + item.scheme_name + ' / ' + item.center_name + '</li>');
          });
        } else {
          $list.append('<li><em>No assignments</em></li>');
        }
      }
    });
  });

  // Reset forms on modal close
  $('#addCourseModal, #editCourseModal').on('hidden.bs.modal', function () {
    var $form = $(this).find('form');
    if ($form.length) {
      $form[0].reset();
      $form.find('.is-invalid').removeClass('is-invalid');
    }
  });
});
</script>
</body>
</html>
