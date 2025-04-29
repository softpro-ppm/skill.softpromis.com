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

$sectors = Sector::getAll();
$schemes = Scheme::getAll();
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
              <tbody id="coursesTableBody">
                <!-- Dynamic rows will be loaded here by AJAX -->
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
                  <label for="courseCode">Course Code</label>
                  <input type="text" class="form-control" id="courseCode" placeholder="Enter course code" required>
                </div>
                <div class="form-group">
                  <label for="courseName">Course Name</label>
                  <input type="text" class="form-control" id="courseName" placeholder="Enter course name" required>
                </div>
                <div class="form-group">
                  <label for="sector">Sector</label>
                  <select class="form-control select2" id="sector" required>
                    <option value="">Select Sector</option>
                    <?php foreach ($sectors as $sector): ?>
                      <option value="<?= htmlspecialchars($sector['sector_id']) ?>"><?= htmlspecialchars($sector['sector_name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="scheme">Scheme</label>
                  <select class="form-control select2" id="scheme" name="scheme_id">
                    <option value="">Select Scheme</option>
                    <?php foreach ($schemes as $scheme): ?>
                      <option value="<?= htmlspecialchars($scheme['scheme_id']) ?>"><?= htmlspecialchars($scheme['scheme_name']) ?></option>
                    <?php endforeach; ?>
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
                <p data-field="course_id"></p>
              </div>
              <div class="form-group">
                <label>Course Name</label>
                <p data-field="course_name"></p>
              </div>
              <div class="form-group">
                <label>Sector</label>
                <p data-field="sector_name"></p>
              </div>
              <div class="form-group">
                <label>Duration</label>
                <p data-field="duration_hours"></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Fee</label>
                <p data-field="fee"></p>
              </div>
              <div class="form-group">
                <label>Prerequisites</label>
                <p data-field="prerequisites"></p>
              </div>
              <div class="form-group">
                <label>Active Batches</label>
                <p data-field="active_batches"></p>
              </div>
              <div class="form-group">
                <label>Status</label>
                <p data-field="status"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Description</label>
                <p data-field="description"></p>
              </div>
              <div class="form-group">
                <label>Learning Outcomes</label>
                <p data-field="syllabus"></p>
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
                  <label for="editCourseCode">Course Code</label>
                  <input type="text" class="form-control" id="editCourseCode" required>
                </div>
                <div class="form-group">
                  <label for="editCourseName">Course Name</label>
                  <input type="text" class="form-control" id="editCourseName" required>
                </div>
                <div class="form-group">
                  <label for="editSector">Sector</label>
                  <select class="form-control select2" id="editSector" required>
                    <option value="">Select Sector</option>
                    <?php foreach ($sectors as $sector): ?>
                      <option value="<?= htmlspecialchars($sector['sector_id']) ?>"><?= htmlspecialchars($sector['sector_name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="editScheme">Scheme</label>
                  <select class="form-control select2" id="editScheme" name="scheme_id">
                    <option value="">Select Scheme</option>
                    <?php foreach ($schemes as $scheme): ?>
                      <option value="<?= htmlspecialchars($scheme['scheme_id']) ?>"><?= htmlspecialchars($scheme['scheme_name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="editDuration">Duration (months)</label>
                  <input type="number" class="form-control" id="editDuration" required>
                </div>
                <div class="form-group">
                  <label for="editFee">Course Fee (₹)</label>
                  <input type="number" class="form-control" id="editFee" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editDescription">Description</label>
                  <textarea class="form-control" id="editDescription" rows="3" required></textarea>
                </div>
                <div class="form-group">
                  <label for="editPrerequisites">Prerequisites</label>
                  <textarea class="form-control" id="editPrerequisites" rows="3"></textarea>
                </div>
                <div class="form-group">
                  <label for="editLearningOutcomes">Learning Outcomes</label>
                  <textarea class="form-control" id="editLearningOutcomes" rows="3" required></textarea>
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
  var coursesTable = $('#coursesTable').DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true
  });

  $('.select2').select2({ theme: 'bootstrap4' });
  bsCustomFileInput.init();

  // Helper: Show toast
  function showToast(type, message) {
    var toast = $('<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">'
      + '<div class="toast-header bg-' + (type === 'success' ? 'success' : 'danger') + ' text-white">'
      + '<strong class="mr-auto">' + (type === 'success' ? 'Success' : 'Error') + '</strong>'
      + '<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">'
      + '<span aria-hidden="true">&times;</span>'
      + '</button></div>'
      + '<div class="toast-body">' + message + '</div></div>');
    if (!$('#toast-container').length) {
      $('body').append('<div id="toast-container" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>');
    }
    $('#toast-container').append(toast);
    toast.toast('show');
    toast.on('hidden.bs.toast', function () { $(this).remove(); });
  }

  // Load courses
  function loadCourses() {
    $.ajax({
      url: 'inc/ajax/courses_ajax.php',
      type: 'POST',
      data: { action: 'read', per_page: 100 },
      dataType: 'json',
      success: function (response) {
        if (response.success && response.data) {
          var rows = '';
          $.each(response.data, function (i, course) {
            rows += '<tr>' +
              '<td>' + (i + 1) + '</td>' +
              '<td>' + course.course_name + '</td>' +
              '<td>' + (course.sector_name || '') + '</td>' +
              '<td>' + course.duration_hours + ' hours</td>' +
              '<td>₹' + (course.fee || 0) + '</td>' +
              '<td>' + (course.active_batches || 0) + '</td>' +
              '<td><span class="badge badge-' + (course.status === 'active' ? 'success' : 'secondary') + '">' + (course.status ? course.status.charAt(0).toUpperCase() + course.status.slice(1) : '') + '</span></td>' +
              '<td>' +
                '<button type="button" class="btn btn-info btn-sm view-course-btn" data-id="' + course.course_id + '"><i class="fas fa-eye"></i></button> ' +
                '<button type="button" class="btn btn-primary btn-sm edit-course-btn" data-id="' + course.course_id + '"><i class="fas fa-edit"></i></button> ' +
                '<button type="button" class="btn btn-danger btn-sm delete-course-btn" data-id="' + course.course_id + '"><i class="fas fa-trash"></i></button>' +
              '</td>' +
            '</tr>';
          });
          $('#coursesTableBody').html(rows);
        } else {
          $('#coursesTableBody').html('<tr><td colspan="8">No courses found.</td></tr>');
        }
      }
    });
  }
  loadCourses();

  // Add Course
  $('#addCourseModal form').on('submit', function (e) {
    e.preventDefault();
    var courseCode = $('#courseCode').val().trim();
    var courseName = $('#courseName').val().trim();
    var sectorId = $('#sector').val();
    var schemeId = $('#scheme').val();
    var durationMonths = $('#duration').val();
    var durationHours = parseInt(durationMonths) * 30 * 1; // 1 month = 30 hours (adjust as needed)
    if (!courseCode || !courseName || !sectorId || sectorId === '' || sectorId === '0' || !durationMonths || durationHours <= 0) {
      showToast('error', 'Please fill all required fields: Course Code, Course Name, Sector, and Duration.');
      return;
    }
    var formData = {
      action: 'create',
      course_code: courseCode,
      course_name: courseName,
      sector_id: sectorId,
      scheme_id: schemeId,
      duration_hours: durationHours,
      fee: $('#fee').val(),
      description: $('#description').val(),
      prerequisites: $('#prerequisites').val(),
      syllabus: $('#learningOutcomes').val(),
      status: 'active'
    };
    console.log(formData);
    $.ajax({
      url: 'inc/ajax/courses_ajax.php',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          showToast('success', response.message);
          $('#addCourseModal').modal('hide');
          loadCourses();
        } else {
          showToast('error', response.message);
        }
      }
    });
  });

  // Edit Course: open modal and fill data
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
          $('#editCourseCode').val(c.course_code);
          $('#editCourseName').val(c.course_name);
          $('#editSector').val(c.sector_id).trigger('change');
          $('#editScheme').val(c.scheme_id).trigger('change');
          var months = c.duration_hours ? Math.round(c.duration_hours / 30) : '';
          $('#editDuration').val(months);
          $('#editFee').val(c.fee);
          $('#editDescription').val(c.description);
          $('#editPrerequisites').val(c.prerequisites);
          $('#editLearningOutcomes').val(c.syllabus);
          $('#editCourseModal').data('id', c.course_id).modal('show');
        } else {
          showToast('error', 'Could not fetch course details.');
        }
      }
    });
  });

  // Edit Course: submit
  $('#editCourseModal form').on('submit', function (e) {
    e.preventDefault();
    var id = $('#editCourseModal').data('id');
    var months = $('#editDuration').val();
    var hours = parseInt(months) * 30;
    var formData = {
      action: 'update',
      course_id: id,
      course_code: $('#editCourseCode').val(),
      course_name: $('#editCourseName').val(),
      sector_id: $('#editSector').val(),
      scheme_id: $('#editScheme').val(),
      duration_hours: hours,
      fee: $('#editFee').val(),
      description: $('#editDescription').val(),
      prerequisites: $('#editPrerequisites').val(),
      syllabus: $('#editLearningOutcomes').val(),
      status: 'active'
    };
    $.ajax({
      url: 'inc/ajax/courses_ajax.php',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          showToast('success', response.message);
          $('#editCourseModal').modal('hide');
          loadCourses();
        } else {
          showToast('error', response.message);
        }
      }
    });
  });

  // Delete Course: open modal
  $(document).on('click', '.delete-course-btn', function () {
    var id = $(this).data('id');
    // Store the course_id in a hidden field in the modal
    $('#deleteCourseModal').data('id', id);
    // Optionally update modal content with course info if needed
    $('#deleteCourseModal').modal('show');
  });

  // Delete Course: confirm
  $('#deleteCourseModal .btn-danger').on('click', function () {
    var id = $('#deleteCourseModal').data('id');
    if (!id) {
      showToast('error', 'Course ID is missing.');
      return;
    }
    $.ajax({
      url: 'inc/ajax/courses_ajax.php',
      type: 'POST',
      data: { action: 'delete', course_id: id },
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          showToast('success', response.message);
          $('#deleteCourseModal').modal('hide');
          loadCourses();
        } else {
          showToast('error', response.message);
        }
      }
    });
  });

  // View Course: open modal and fill data
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
          $('#viewCourseModal .modal-title').text('View Course: ' + c.course_name);
          $('#viewCourseModal [data-field="course_id"]').text(c.course_id);
          $('#viewCourseModal [data-field="course_name"]').text(c.course_name);
          $('#viewCourseModal [data-field="sector_name"]').text(c.sector_name);
          $('#viewCourseModal [data-field="duration_hours"]').text(c.duration_hours + ' hours');
          $('#viewCourseModal [data-field="fee"]').text('₹' + (c.fee || 0));
          $('#viewCourseModal [data-field="prerequisites"]').text(c.prerequisites);
          $('#viewCourseModal [data-field="status"]').html('<span class="badge badge-' + (c.status === 'active' ? 'success' : 'secondary') + '">' + (c.status ? c.status.charAt(0).toUpperCase() + c.status.slice(1) : '') + '</span>');
          $('#viewCourseModal [data-field="description"]').text(c.description);
          $('#viewCourseModal [data-field="syllabus"]').html(c.syllabus ? c.syllabus.replace(/\n/g, '<br>') : '');
          $('#viewCourseModal').modal('show');
        } else {
          showToast('error', 'Could not fetch course details.');
        }
      }
    });
  });
});
</script>
</body>
</html> 
