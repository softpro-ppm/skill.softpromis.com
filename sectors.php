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
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectorModal">
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
                                        <th>Training Center</th>
                                        <th>Scheme</th>
                                        <th>Status</th>
                                        <th>Created At</th>
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

<!-- Add Sector Modal -->
<div class="modal fade" id="addSectorModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Sector</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addSectorForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="center_id">Training Center</label>
                        <select class="form-control" id="center_id" name="center_id" required>
                            <option value="">Select Training Center</option>
                            <?php foreach (TrainingCenter::getAll() as $center): ?>
                                <option value="<?= htmlspecialchars($center['center_id']) ?>"><?= htmlspecialchars($center['center_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="scheme_id">Scheme</label>
                        <select class="form-control" id="scheme_id" name="scheme_id" required>
                            <option value="">Select Scheme</option>
                            <?php foreach (Scheme::getAll() as $scheme): ?>
                                <option value="<?= htmlspecialchars($scheme['scheme_id']) ?>"><?= htmlspecialchars($scheme['scheme_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sectorName">Sector Name</label>
                        <input type="text" class="form-control" id="sectorName" name="sector_name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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
                    <button type="submit" class="btn btn-primary">Save Sector</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Sector Modal -->
<div class="modal fade" id="viewSectorModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">View Sector Details</h4>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-2"><label>Sector ID</label><p data-field="sector_id"></p></div>
                        <div class="form-group mb-2"><label>Sector Name</label><p data-field="sector_name"></p></div>
                        <div class="form-group mb-2"><label>Training Center</label><p data-field="center_name"></p></div>
                        <div class="form-group mb-2"><label>Scheme</label><p data-field="scheme_name"></p></div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2"><label>Status</label><p data-field="status"></p></div>
                        <div class="form-group mb-2"><label>Created At</label><p data-field="created_at"></p></div>
                        <div class="form-group mb-2"><label>Updated At</label><p data-field="updated_at"></p></div>
                    </div>
                </div>
                <div class="form-group mt-2"><label>Description</label><p data-field="description"></p></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Sector Modal -->
<div class="modal fade" id="editSectorModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Sector</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editSectorForm">
                <input type="hidden" id="editSectorId" name="sector_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_center_id">Training Center</label>
                        <select class="form-control" id="edit_center_id" name="center_id" required>
                            <option value="">Select Training Center</option>
                            <?php foreach (TrainingCenter::getAll() as $center): ?>
                                <option value="<?= htmlspecialchars($center['center_id']) ?>">
                                    <?= htmlspecialchars($center['center_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_scheme_id">Scheme</label>
                        <select class="form-control" id="edit_scheme_id" name="scheme_id" required>
                            <option value="">Select Scheme</option>
                            <?php foreach (Scheme::getAll() as $scheme): ?>
                                <option value="<?= htmlspecialchars($scheme['scheme_id']) ?>">
                                    <?= htmlspecialchars($scheme['scheme_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editSectorName">Sector Name</label>
                        <input type="text" class="form-control" id="editSectorName" name="sector_name" required>
                    </div>
                    <div class="form-group">
                        <label for="editDescription">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Status</label>
                        <select class="form-control" id="editStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
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
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteSectorId">
                <p>Are you sure you want to delete this sector? This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteSector">Delete Sector</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/js.php'; ?>
<script>
$(function () {
    var table = $('#sectorsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'inc/ajax/sectors_ajax.php',
            type: 'GET',
            data: function (d) { d.action = 'list'; },
            dataSrc: function (json) { return json.data || []; }
        },
        columns: [
            { data: null, render: function (data, type, row, meta) { return meta.row + 1; } },
            { data: 'sector_id' },
            { data: 'sector_name' },
            { data: 'center_name' },
            { data: 'scheme_name' },
            { data: 'status', render: function (data) { return '<span class="badge badge-' + (data === 'active' ? 'success' : 'secondary') + '">' + (data.charAt(0).toUpperCase() + data.slice(1)) + '</span>'; } },
            { data: 'created_at' },
            { data: null, orderable: false, searchable: false, render: function (data, type, row) {
                return '<div class="btn-group btn-group-sm">' +
                    '<button type="button" class="btn btn-info view-sector-btn" data-sector-id="' + row.sector_id + '"><i class="fas fa-eye"></i></button>' +
                    '<button type="button" class="btn btn-primary edit-sector-btn" data-sector-id="' + row.sector_id + '"><i class="fas fa-edit"></i></button>' +
                    '<button type="button" class="btn btn-danger delete-sector-btn" data-sector-id="' + row.sector_id + '"><i class="fas fa-trash"></i></button>' +
                    '</div>';
            } }
        ],
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        order: [[0, 'asc']]
    });

    // --- ADD ---
    $('#addSectorForm').on('submit', function(e) {
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
            url: 'inc/ajax/sectors_ajax.php',
            type: 'POST',
            data: $form.serialize() + '&action=add',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#addSectorModal').modal('hide');
                    toastr.success(response.message || 'Sector added successfully');
                    $('#addSectorModal').one('hidden.bs.modal', function() {
                        table.ajax.reload(null, false);
                    });
                    setTimeout(function() {
                        $form[0].reset();
                        $form.find('.is-invalid').removeClass('is-invalid');
                    }, 500);
                } else {
                    toastr.error(response.message || 'Error adding sector');
                }
            },
            error: function() {
                toastr.error('Error adding sector');
            }
        });
    });

    // --- VIEW ---
    $(document).on('click', '.view-sector-btn', function() {
        var sectorId = $(this).data('sector-id');
        var $modal = $('#viewSectorModal');
        $modal.find('[data-field]').text('');
        $.ajax({
            url: 'inc/ajax/sectors_ajax.php',
            type: 'GET',
            data: { action: 'get', sector_id: sectorId },
            dataType: 'json',
            success: function(response) {
                var s = response.data || {};
                $modal.find('[data-field="sector_id"]').text(s.sector_id || '');
                $modal.find('[data-field="sector_name"]').text(s.sector_name || '');
                $modal.find('[data-field="center_name"]').text(s.center_name || '');
                $modal.find('[data-field="scheme_name"]').text(s.scheme_name || '');
                $modal.find('[data-field="description"]').text(s.description || '');
                $modal.find('[data-field="status"]').html(s.status ? '<span class="badge badge-' + (s.status === 'active' ? 'success' : 'secondary') + '">' + (s.status.charAt(0).toUpperCase() + s.status.slice(1)) + '</span>' : '');
                $modal.find('[data-field="created_at"]').text(s.created_at || '');
                $modal.find('[data-field="updated_at"]').text(s.updated_at || '');
                $modal.modal('show');
            }
        });
    });

    // --- EDIT (populate modal) ---
    $(document).on('click', '.edit-sector-btn', function() {
        var sectorId = $(this).data('sector-id');
        var $modal = $('#editSectorModal');
        $modal.find('form')[0].reset();
        $modal.find('.is-invalid').removeClass('is-invalid');
        $modal.find('#editSectorId').val('');
        $modal.find('#editSectorName').val('');
        $modal.find('#editDescription').val('');
        $modal.find('#editStatus').val('active');
        $.ajax({
            url: 'inc/ajax/sectors_ajax.php',
            type: 'GET',
            data: { action: 'get', sector_id: sectorId },
            dataType: 'json',
            success: function(response) {
                var s = response.data || {};
                $modal.find('#editSectorId').val(s.sector_id || '');
                $modal.find('#editSectorName').val(s.sector_name || '');
                $modal.find('#editDescription').val(s.description || '');
                $modal.find('#editStatus').val(s.status || 'active');
                $modal.find('#edit_center_id').val(s.center_id || '');
                $modal.find('#edit_scheme_id').val(s.scheme_id || '');
                $modal.modal('show');
            }
        });
    });

    // --- EDIT (submit) ---
    $('#editSectorForm').on('submit', function(e) {
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
            url: 'inc/ajax/sectors_ajax.php',
            type: 'POST',
            data: $form.serialize() + '&action=edit',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#editSectorModal').modal('hide');
                    toastr.success(response.message || 'Sector updated successfully');
                    $('#editSectorModal').one('hidden.bs.modal', function() {
                        table.ajax.reload(null, false);
                    });
                    setTimeout(function() {
                        $form[0].reset();
                        $form.find('.is-invalid').removeClass('is-invalid');
                    }, 500);
                } else {
                    toastr.error(response.message || 'Error updating sector');
                }
            },
            error: function() {
                toastr.error('Error updating sector');
            }
        });
    });

    // --- DELETE ---
    $(document).on('click', '.delete-sector-btn', function() {
        var sectorId = $(this).data('sector-id');
        $('#deleteSectorId').val(sectorId);
        $('#deleteSectorModal').modal('show');
    });
    $('#confirmDeleteSector').on('click', function() {
        var sectorId = $('#deleteSectorId').val();
        $.ajax({
            url: 'inc/ajax/sectors_ajax.php',
            type: 'POST',
            data: { action: 'delete', sector_id: sectorId },
            dataType: 'json',
            success: function(response) {
                $('#deleteSectorModal').modal('hide');
                if(response.success) {
                    toastr.success(response.message || 'Sector deleted successfully');
                    table.ajax.reload(null, false);
                } else {
                    toastr.error(response.message || 'Error deleting sector');
                }
            },
            error: function() {
                $('#deleteSectorModal').modal('hide');
                toastr.error('Error deleting sector');
            }
        });
    });

    // --- Reset forms on modal close ---
    $('#addSectorModal, #editSectorModal').on('hidden.bs.modal', function () {
        var $form = $(this).find('form');
        if ($form.length) {
            $form[0].reset();
            $form.find('.is-invalid').removeClass('is-invalid');
        }
    });

    // Handle TC change to load assigned schemes
    $('#center_id, #edit_center_id').on('change', function() {
        var centerId = $(this).val();
        var $schemeSelect = $(this).attr('id') === 'center_id' ? $('#scheme_id') : $('#edit_scheme_id');
        
        console.log('TC changed. Selected center_id:', centerId);
        $schemeSelect.empty().append('<option value="">Select Scheme</option>');
        
        if (!centerId) {
            console.log('No center_id selected, returning');
            return;
        }
        
        console.log('Making AJAX call to fetch schemes for center_id:', centerId);
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: { 
                action: 'list_by_center',
                center_id: centerId
            },
            dataType: 'json',
            beforeSend: function() {
                console.log('Sending AJAX request...');
            },
            success: function(response) {
                console.log('Received AJAX response:', response);
                if (response.success && response.data) {
                    console.log('Found schemes:', response.data);
                    response.data.forEach(function(scheme) {
                        $schemeSelect.append(
                            $('<option></option>')
                                .val(scheme.scheme_id)
                                .text(scheme.scheme_name)
                        );
                    });
                } else {
                    console.log('No schemes found or error in response');
                    $schemeSelect.append('<option value="">No schemes assigned to this center</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', {xhr: xhr, status: status, error: error});
                $schemeSelect.append('<option value="">Error loading schemes</option>');
            }
        });
    });
});
</script>
</body>
</html>
