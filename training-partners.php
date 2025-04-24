<?php
require_once 'config.php';
require_once 'inc/functions.php';

startSecureSession();

// Check if user is logged in
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Check if user has admin privileges
if (!hasPermission('admin')) {
    header('Location: index.php?error=unauthorized');
    exit;
}

$pageTitle = 'Training Partners';
$currentPage = 'training-partners';
require_once 'includes/header.php';
?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Training Partners</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#partnerModal">
                                <i class="fas fa-plus"></i> Add New Partner
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="partnersTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Partner Modal -->
<div class="modal fade" id="partnerModal" tabindex="-1" role="dialog" aria-labelledby="partnerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="partnerModalLabel">Add New Partner</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="partnerForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="partnerId">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Partner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this training partner?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<!-- Page specific scripts -->
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#partnersTable').DataTable({
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: 'inc/ajax/training_partners_ajax.php',
            type: 'POST',
            data: { action: 'read' }
        },
        columns: [
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'address' },
            { 
                data: 'status',
                render: function(data) {
                    return `<span class="badge badge-${data === 'active' ? 'success' : 'danger'}">${data}</span>`;
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            {
                data: 'id',
                render: function(data) {
                    return `
                        <button class="btn btn-sm btn-info edit-partner" data-id="${data}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-partner" data-id="${data}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Handle form submission
    $('#partnerForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', $('#partnerId').val() ? 'update' : 'create');

        $.ajax({
            url: 'inc/ajax/training_partners_ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#partnerModal').modal('hide');
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again later.');
            }
        });
    });

    // Handle edit button click
    $('#partnersTable').on('click', '.edit-partner', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'inc/ajax/training_partners_ajax.php',
            type: 'POST',
            data: { action: 'get', id: id },
            success: function(response) {
                if (response.success) {
                    const partner = response.data;
                    $('#partnerId').val(partner.id);
                    $('#name').val(partner.name);
                    $('#email').val(partner.email);
                    $('#phone').val(partner.phone);
                    $('#address').val(partner.address);
                    $('#status').val(partner.status);
                    $('#partnerModalLabel').text('Edit Partner');
                    $('#partnerModal').modal('show');
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Handle delete button click
    let deleteId = null;
    $('#partnersTable').on('click', '.delete-partner', function() {
        deleteId = $(this).data('id');
        $('#deleteModal').modal('show');
    });

    // Handle delete confirmation
    $('#confirmDelete').click(function() {
        if (deleteId) {
            $.ajax({
                url: 'inc/ajax/training_partners_ajax.php',
                type: 'POST',
                data: { action: 'delete', id: deleteId },
                success: function(response) {
                    if (response.success) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });

    // Reset form when modal is closed
    $('#partnerModal').on('hidden.bs.modal', function() {
        $('#partnerForm')[0].reset();
        $('#partnerId').val('');
        $('#partnerModalLabel').text('Add New Partner');
    });
});
</script> 
