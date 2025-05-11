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
                                <button type="button" class="btn btn-primary" id="addBatchBtn">
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
                                        <th>Batch Name</th>
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

<!-- Add/Edit Batch Modal -->
<div class="modal fade" id="batchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="batchModalTitle">Add New Batch</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="batchForm">
                <div class="modal-body">
                    <input type="hidden" id="batch_id" name="batch_id">
                    <div class="form-group">
                        <label for="batch_name">Batch Name</label>
                        <input type="text" class="form-control" id="batch_name" name="batch_name" required>
                    </div>
                    <div class="form-group">
                        <label for="batch_code">Batch Code</label>
                        <input type="text" class="form-control" id="batch_code" name="batch_code" required>
                    </div>
                
                    <div class="form-group">
                        <label for="center_id">Center</label>
                        <select class="form-control" id="center_id" name="center_id" required>
                            <option value="">Select Center</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="course_id">Course</label>
                        <select class="form-control" id="course_id" name="course_id" required>
                            <option value="">Select Course</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label for="capacity">Capacity</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBatchBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/js.php'; ?>
<script src="assets/js/batches.js"></script>
</body>
</html>
