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

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Batches</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Batches</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Batches List</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBatchModal">
                                    <i class="fas fa-plus"></i> Add New Batch
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="batchesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Batch ID</th>
                                        <th>Center Name</th>
                                        <th>Course Name</th>
                                        <th>Batch Code</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Capacity</th>
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
            </div>
        </div>
    </section>
</div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->

<!-- Add Batch Modal -->
<div class="modal fade" id="addBatchModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Batch</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addBatchForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="batchName">Batch Name</label>
                        <input type="text" class="form-control" id="batchName" name="batch_name" required>
                    </div>
                    <div class="form-group">
                        <label for="course">Course</label>
                        <select class="form-control" id="course" name="course_id" required>
                            <option value="">Select Course</option>
                            <!-- Dynamic course options will be loaded here -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="startDate">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="endDate">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stable">Stable</label>
                        <input type="text" class="form-control" id="stable" name="stable" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Batch Modal -->
<div class="modal fade" id="editBatchModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Batch</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editBatchForm">
                <div class="modal-body">
                    <input type="hidden" id="editBatchId" name="batch_id">
                    <div class="form-group">
                        <label for="editBatchName">Batch Name</label>
                        <input type="text" class="form-control" id="editBatchName" name="batch_name" required>
                    </div>
                    <div class="form-group">
                        <label for="editCourse">Course</label>
                        <select class="form-control" id="editCourse" name="course_id" required>
                            <option value="">Select Course</option>
                            <!-- Dynamic course options will be loaded here -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editStartDate">Start Date</label>
                        <input type="date" class="form-control" id="editStartDate" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="editEndDate">End Date</label>
                        <input type="date" class="form-control" id="editEndDate" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Status</label>
                        <select class="form-control" id="editStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editStable">Stable</label>
                        <input type="text" class="form-control" id="editStable" name="stable" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Batch Modal -->
<div class="modal fade" id="viewBatchModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Batch Details</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Batch Name:</strong> <span id="batchName"></span></p>
                <p><strong>Center Name:</strong> <span id="centerName"></span></p>
                <p><strong>Course Name:</strong> <span id="courseName"></span></p>
                <p><strong>Start Date:</strong> <span id="startDate"></span></p>
                <p><strong>End Date:</strong> <span id="endDate"></span></p>
                <p><strong>Capacity:</strong> <span id="capacity"></span></p>
                <p><strong>Status:</strong> <span id="status"></span></p>
                <p><strong>Stable:</strong> <span id="stable"></span></p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/js.php'; ?>
<script>
$(function () {
    // Initialize DataTable
    var table = $('#batchesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'inc/ajax/batches_ajax.php',
            type: 'GET',
            data: function (d) { d.action = 'list'; },
            dataSrc: function (json) { return json.data || []; }
        },
        columns: [
            { data: 'batch_id' },
            { data: 'center_name' },
            { data: 'course_name' },
            { data: 'batch_code' },
            { data: 'start_date' },
            { data: 'end_date' },
            { data: 'capacity' },
            { data: 'status', render: function(data) {
                return '<span class="badge badge-' + (data === 'ongoing' ? 'success' : 'secondary') + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
            } },
            { data: null, orderable: false, searchable: false, render: function(data, type, row) {
                return '<div class="btn-group btn-group-sm">' +
                    '<button type="button" class="btn btn-info view-batch-btn" data-id="' + row.batch_id + '"><i class="fas fa-eye"></i></button>' +
                    '<button type="button" class="btn btn-primary edit-batch-btn" data-id="' + row.batch_id + '"><i class="fas fa-edit"></i></button>' +
                    '<button type="button" class="btn btn-danger delete-batch-btn" data-id="' + row.batch_id + '"><i class="fas fa-trash"></i></button>' +
                    '</div>';
            } }
        ],
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        order: [[0, 'asc']]
    });

    // Add error handling for DataTable
    $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
        toastr.error('Error loading data: ' + thrownError);
    });

    // Load courses dynamically
    $.ajax({
        url: 'inc/ajax/courses_ajax.php',
        type: 'POST',
        data: { action: 'read' },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                var courseSelect = $('#course');
                courseSelect.empty().append('<option value="">Select Course</option>');
                response.data.forEach(function(course) {
                    courseSelect.append('<option value="' + course.course_id + '">' + course.course_name + '</option>');
                });
            } else {
                toastr.error('Failed to load courses.');
            }
        },
        error: function() {
            toastr.error('Error loading courses.');
        }
    });

    // Add Batch
    $('#addBatchForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var isValid = true;

        // Validate required fields
        $form.find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
                toastr.error($(this).prev('label').text() + ' is required.');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            return false;
        }

        // Submit form via AJAX
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: $form.serialize() + '&action=add',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#addBatchModal').modal('hide');
                    toastr.success(response.message || 'Batch added successfully');
                    $('#addBatchModal').one('hidden.bs.modal', function() {
                        $('#batchesTable').DataTable().ajax.reload(null, false);
                    });
                    $form[0].reset();
                } else {
                    toastr.error(response.message || 'Error adding batch');
                }
            },
            error: function() {
                toastr.error('An error occurred while adding the batch. Please try again.');
            }
        });
    });

    // Edit Batch: open modal and populate data
    $(document).on('click', '.edit-batch-btn', function() {
        var batchId = $(this).data('id');
        if (!batchId) {
            toastr.error('Batch ID is missing.');
            return;
        }

        // Fetch batch details
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: { action: 'get', batch_id: batchId },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    var batch = response.data;
                    $('#editBatchModal #editBatchId').val(batch.batch_id);
                    $('#editBatchModal #editBatchName').val(batch.batch_name || '');
                    $('#editBatchModal #editCourse').val(batch.course_id || '').trigger('change');
                    $('#editBatchModal #editStartDate').val(batch.start_date || '');
                    $('#editBatchModal #editEndDate').val(batch.end_date || '');
                    $('#editBatchModal #editStatus').val(batch.status || '');
                    $('#editBatchModal #editStable').val(batch.stable || '');
                    $('#editBatchModal').modal('show');
                } else {
                    toastr.error(response.message || 'Failed to fetch batch details.');
                }
            },
            error: function() {
                toastr.error('Error fetching batch details.');
            }
        });
    });

    // View Batch: open modal and populate data
    $(document).on('click', '.view-batch-btn', function() {
        var batchId = $(this).data('id');
        if (!batchId) {
            toastr.error('Batch ID is missing.');
            return;
        }

        // Fetch batch details
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: { action: 'get', batch_id: batchId },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    var batch = response.data;
                    $('#viewBatchModal #batchName').text(batch.batch_name);
                    $('#viewBatchModal #centerName').text(batch.center_name);
                    $('#viewBatchModal #courseName').text(batch.course_name);
                    $('#viewBatchModal #startDate').text(batch.start_date);
                    $('#viewBatchModal #endDate').text(batch.end_date);
                    $('#viewBatchModal #capacity').text(batch.capacity);
                    $('#viewBatchModal #status').text(batch.status);
                    $('#viewBatchModal #stable').text(batch.stable);
                    $('#viewBatchModal').modal('show');
                } else {
                    toastr.error('Failed to fetch batch details.');
                }
            },
            error: function() {
                toastr.error('Error fetching batch details.');
            }
        });
    });

    // Delete Batch: handle delete button click
    $(document).on('click', '.delete-batch-btn', function() {
        var batchId = $(this).data('id');
        if (!batchId) {
            toastr.error('Batch ID is missing.');
            return;
        }

        if (!confirm('Are you sure you want to delete this batch?')) {
            return;
        }

        // Send AJAX request to delete the batch
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: { action: 'delete', batch_id: batchId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Batch deleted successfully');
                    $('#batchesTable').DataTable().ajax.reload(null, false);
                } else {
                    toastr.error(response.message || 'Failed to delete batch');
                }
            },
            error: function() {
                toastr.error('An error occurred while deleting the batch. Please try again.');
            }
        });
    });

    // Reset forms on modal close
    $('#addBatchModal').on('hidden.bs.modal', function () {
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
