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
                                        <th>Batch Name</th>
                                        <th>Course</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
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
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Batch</button>
                </div>
            </form>
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
            { data: 'batch_name' },
            { data: 'course_name' },
            { data: 'start_date' },
            { data: 'end_date' },
            { data: 'status', render: function (data) { return '<span class="badge badge-' + (data === 'active' ? 'success' : 'secondary') + '">' + (data.charAt(0).toUpperCase() + data.slice(1)) + '</span>'; } },
            { data: null, orderable: false, searchable: false, render: function (data, type, row) {
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

    // Add Batch
    $('#addBatchForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var isValid = true;
        $form.find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        if (!isValid) return false;
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: $form.serialize() + '&action=add',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#addBatchModal').modal('hide');
                    toastr.success(response.message || 'Batch added successfully');
                    $('#addBatchModal').one('hidden.bs.modal', function() {
                        table.ajax.reload(null, false);
                    });
                    setTimeout(function() {
                        $form[0].reset();
                        $form.find('.is-invalid').removeClass('is-invalid');
                    }, 500);
                } else {
                    toastr.error(response.message || 'Error adding batch');
                }
            },
            error: function() {
                toastr.error('Error adding batch');
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
