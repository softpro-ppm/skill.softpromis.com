<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'inc/auth_check.php';
require_once 'config.php';
require_once 'inc/functions.php';

// Debug session
error_log("Session data: " . print_r($_SESSION, true));

// Check if user has admin privileges
if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'Administrator') {
    error_log("Access denied: User role is " . ($_SESSION['user']['role'] ?? 'not set'));
    header('Location: dashboard.php?error=' . urlencode('You do not have permission to access this page.'));
    exit;
}

$pageTitle = 'Training Partners';
$currentPage = 'training-partners';
require_once 'includes/header.php';
?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-handshake"></i> Training Partners
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Training Partners</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Manage Training Partners</h3>
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
                                    <th>Partner Name</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Status</th>
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
                <h5 class="modal-title" id="partnerModalLabel">Add New Training Partner</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="partnerForm">
                <div class="modal-body">
                    <input type="hidden" name="partner_id" id="partner_id">
                    <div class="form-group">
                        <label for="partner_name">Partner Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="partner_name" name="partner_name" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_person">Contact Person</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
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
            data: { action: 'list' }
        },
        columns: [
            { data: 'partner_name' },
            { data: 'contact_person' },
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
                data: 'partner_id',
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
        formData.append('action', $('#partner_id').val() ? 'update' : 'add');

        $.ajax({
            url: 'inc/ajax/training_partners_ajax.php',
            type: 'POST',
            data: Object.fromEntries(formData),
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
            data: { 
                action: 'get',
                partner_id: id
            },
            success: function(response) {
                if (response.success) {
                    const partner = response.data;
                    $('#partner_id').val(partner.partner_id);
                    $('#partner_name').val(partner.partner_name);
                    $('#contact_person').val(partner.contact_person);
                    $('#email').val(partner.email);
                    $('#phone').val(partner.phone);
                    $('#address').val(partner.address);
                    $('#status').val(partner.status);
                    $('#partnerModalLabel').text('Edit Training Partner');
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
                data: { 
                    action: 'delete',
                    partner_id: deleteId
                },
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
        $('#partner_id').val('');
        $('#partnerModalLabel').text('Add New Training Partner');
    });
});
</script> 
