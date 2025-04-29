<?php
// Define BASEPATH constant
define('BASEPATH', true);

// Start session and include required files
session_start();
require_once 'config.php';
require_once 'crud_functions.php';
$sectors = Sector::getAll();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Set page title
$pageTitle = 'Sectors';

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
                    <h1>Sectors</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Sectors</li>
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
                            <h3 class="card-title">Sectors List</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#sectorModal">
                                    <i class="fas fa-plus"></i> Add New Sector
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="sectorsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Sector ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach ($sectors as $sector): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($sector['sector_id']) ?></td>
                                        <td><?= htmlspecialchars($sector['sector_name']) ?></td>
                                        <td><?= htmlspecialchars($sector['description']) ?></td>
                                        <td><span class="badge badge-<?= $sector['status'] === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($sector['status']) ?></span></td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm view-sector-btn" data-sector-id="<?= $sector['sector_id'] ?>"><i class="fas fa-eye"></i></button>
                                            <button type="button" class="btn btn-primary btn-sm edit-sector-btn" data-sector-id="<?= $sector['sector_id'] ?>"><i class="fas fa-edit"></i></button>
                                            <button type="button" class="btn btn-danger btn-sm delete-sector-btn" data-sector-id="<?= $sector['sector_id'] ?>"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
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

<!-- Add Sector Modal -->
<div class="modal fade" id="sectorModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Sector</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addSectorForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sectorName">Sector Name</label>
                                <input type="text" class="form-control" id="sectorName" name="sector_name" placeholder="Enter sector name" required>
                            </div>
                            <div class="form-group">
                                <label for="sectorCode">Sector Code</label>
                                <input type="text" class="form-control" id="sectorCode" name="sector_code" placeholder="Enter sector code" required>
                            </div>
                            <div class="form-group">
                                <label for="sectorType">Sector Type</label>
                                <select class="form-control select2" id="sectorType" name="sector_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Technical">Technical</option>
                                    <option value="Non-Technical">Non-Technical</option>
                                    <option value="Vocational">Vocational</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter sector description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="jobRoles">Job Roles</label>
                                <textarea class="form-control" id="jobRoles" name="job_roles" rows="3" placeholder="Enter potential job roles" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Documents</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="sectorDoc" name="sector_document">
                                    <label class="custom-file-label" for="sectorDoc">Sector Document</label>
                                </div>
                                <div class="custom-file mt-2">
                                    <input type="file" class="custom-file-input" id="curriculumDoc" name="curriculum_document">
                                    <label class="custom-file-label" for="curriculumDoc">Curriculum Document</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Sector</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Sector Modal (dynamic fields) -->
<div class="modal fade" id="viewSectorModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">View Sector Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group"><label>Sector ID</label><p data-field="sector_id"></p></div>
                        <div class="form-group"><label>Sector Name</label><p data-field="sector_name"></p></div>
                        <div class="form-group"><label>Sector Code</label><p data-field="sector_code"></p></div>
                        <div class="form-group"><label>Sector Type</label><p data-field="sector_type"></p></div>
                        <div class="form-group"><label>Description</label><p data-field="description"></p></div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"><label>Job Roles</label><p data-field="job_roles"></p></div>
                        <div class="form-group"><label>Status</label><p data-field="status"></p></div>
                        <div class="form-group"><label>Sector Document</label><p data-field="sector_document"></p></div>
                        <div class="form-group"><label>Curriculum Document</label><p data-field="curriculum_document"></p></div>
                        <div class="form-group"><label>Created At</label><p data-field="created_at"></p></div>
                        <div class="form-group"><label>Updated At</label><p data-field="updated_at"></p></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Sector Modal -->
<div class="modal fade" id="editSectorModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Sector</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editSectorForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editSectorName">Sector Name</label>
                                <input type="text" class="form-control" id="editSectorName" name="sector_name" required>
                            </div>
                            <div class="form-group">
                                <label for="editSectorCode">Sector Code</label>
                                <input type="text" class="form-control" id="editSectorCode" name="sector_code" required>
                            </div>
                            <div class="form-group">
                                <label for="editSectorType">Sector Type</label>
                                <select class="form-control select2" id="editSectorType" name="sector_type" required>
                                    <option value="Technical">Technical</option>
                                    <option value="Non-Technical">Non-Technical</option>
                                    <option value="Vocational">Vocational</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editDescription">Description</label>
                                <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="editJobRoles">Job Roles</label>
                                <textarea class="form-control" id="editJobRoles" name="job_roles" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Documents</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="editSectorDoc" name="sector_document">
                                    <label class="custom-file-label" for="editSectorDoc">Sector Document</label>
                                </div>
                                <div class="custom-file mt-2">
                                    <input type="file" class="custom-file-input" id="editCurriculumDoc" name="curriculum_document">
                                    <label class="custom-file-label" for="editCurriculumDoc">Curriculum Document</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Sector</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Sector Modal -->
<div class="modal fade" id="deleteSectorModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Delete Sector</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this sector? This action cannot be undone.</p>
                <p><strong>Sector:</strong> Information Technology</p>
                <p><strong>Associated Courses:</strong> 5</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Delete Sector</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/js.php'; ?>

<script>
$(function () {
    $('#sectorsTable').DataTable();
    $('.select2').select2({ theme: 'bootstrap4' });
    bsCustomFileInput.init();

    // Toast function
    function showToast(type, message) {
        var toast = $('<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">'
            + '<div class="toast-header bg-' + (type === 'success' ? 'success' : 'danger') + ' text-white">'
            + '<strong class="mr-auto">' + (type === 'success' ? 'Success' : 'Error') + '</strong>'
            + '<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">'
            + '<span aria-hidden="true">&times;</span>'
            + '</button></div>'
            + '<div class="toast-body">' + message + '</div></div>');
        $('#toast-container').append(toast);
        toast.toast('show');
        toast.on('hidden.bs.toast', function () { $(this).remove(); });
    }
    if (!$('#toast-container').length) {
        $('body').append('<div id="toast-container" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>');
    }

    // --- AJAX Add Sector ---
    $('#addSectorForm').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        var formData = new FormData(form);
        var sectorCode = $('#sectorCode').val();
        // Check for duplicate code before submitting
        $.ajax({
            url: 'inc/ajax/sectors_ajax.php',
            type: 'POST',
            data: { action: 'check_code', sector_code: sectorCode },
            dataType: 'json',
            async: false,
            success: function(checkResp) {
                if (checkResp.success && checkResp.data && checkResp.data.exists) {
                    showToast('error', 'Sector Code already exists. Please use a unique code.');
                } else {
                    // Proceed with actual form submission
                    formData.append('action', 'create');
                    $.ajax({
                        url: 'inc/ajax/sectors_ajax.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showToast('success', response.message);
                                setTimeout(function(){ location.reload(); }, 1200);
                            } else {
                                showToast('error', response.message);
                            }
                        }
                    });
                }
            }
        });
    });

    // --- AJAX View Sector ---
    $(document).on('click', '.view-sector-btn', function() {
        var sector_id = $(this).data('sector-id');
        $.post('inc/ajax/sectors_ajax.php', { action: 'get', sector_id: sector_id }, function(response) {
            if (response.success && response.data) {
                var s = response.data;
                for (const key in s) {
                    var val = s[key] || '';
                    if (key === 'sector_document' && val) {
                        val = '<a href="' + val + '" target="_blank">Download</a>';
                    }
                    if (key === 'curriculum_document' && val) {
                        val = '<a href="' + val + '" target="_blank">Download</a>';
                    }
                    $('#viewSectorModal [data-field="' + key + '"]').html(val);
                }
                $('#viewSectorModal').modal('show');
            } else {
                showToast('error', 'Could not fetch sector details.');
            }
        }, 'json');
    });

    // --- AJAX Edit Sector: populate modal ---
    $(document).on('click', '.edit-sector-btn', function() {
        var sector_id = $(this).data('sector-id');
        $.post('inc/ajax/sectors_ajax.php', { action: 'get', sector_id: sector_id }, function(response) {
            if (response.success && response.data) {
                var s = response.data;
                $('#editSectorName').val(s.sector_name);
                $('#editSectorCode').val(s.sector_code);
                $('#editSectorType').val(s.sector_type).trigger('change');
                $('#editDescription').val(s.description);
                $('#editJobRoles').val(s.job_roles);
                // Clear file inputs
                $('#editSectorDoc').val("");
                $('#editCurriculumDoc').val("");
                $('#editSectorModal').data('sector-id', sector_id);
                $('#editSectorModal').modal('show');
            } else {
                showToast('error', 'Could not fetch sector details.');
            }
        }, 'json');
    });

    // --- AJAX Edit Sector: submit ---
    $('#editSectorForm').on('submit', function(e) {
        e.preventDefault();
        var sector_id = $('#editSectorModal').data('sector-id');
        var formData = new FormData(this);
        formData.append('action', 'update');
        formData.append('sector_id', sector_id);
        $.ajax({
            url: 'inc/ajax/sectors_ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    setTimeout(function(){ location.reload(); }, 1200);
                } else {
                    showToast('error', response.message);
                }
            }
        });
    });

    // --- AJAX Delete Sector ---
    $(document).on('click', '.delete-sector-btn', function() {
        var sector_id = $(this).data('sector-id');
        if (confirm('Are you sure you want to delete this sector?')) {
            $.post('inc/ajax/sectors_ajax.php', { action: 'delete', sector_id: sector_id }, function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    setTimeout(function(){ location.reload(); }, 1200);
                } else {
                    showToast('error', response.message);
                }
            }, 'json');
        }
    });
});
</script>
</body>
</html> 
