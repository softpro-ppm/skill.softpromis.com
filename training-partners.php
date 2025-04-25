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
$pageTitle = 'Training Partners';

// Include header
require_once 'includes/layouts/header.php';

// Include topbar
require_once 'includes/layouts/topbar.php';

// Include sidebar
require_once 'includes/layouts/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Training Partners</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Training Partners</li>
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
                            <h3 class="card-title">Training Partners List</h3>
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
                                        <th>Contact Person</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Partner Modal -->
<div class="modal fade" id="partnerModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Training Partner</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="partnerForm">
                <input type="hidden" id="partner_id" name="partner_id" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Partner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirm Delete</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this training partner?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php
// Define page-specific scripts
$pageScripts = <<<EOT
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#partnersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'inc/ajax/training_partners_ajax.php',
            type: 'POST',
            data: function(d) {
                d.action = 'list';
                return d;
            }
        },
        columns: [
            { data: 'contact_person' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'address' },
            { 
                data: 'status',
                render: function(data) {
                    return '<span class="badge badge-' + (data === 'active' ? 'success' : 'danger') + '">' + data + '</span>';
                }
            },
            {
                data: 'partner_id',
                render: function(data) {
                    return '<button class="btn btn-sm btn-info edit-partner" data-id="' + data + '"><i class="fas fa-edit"></i></button> ' +
                           '<button class="btn btn-sm btn-danger delete-partner" data-id="' + data + '"><i class="fas fa-trash"></i></button>';
                }
            }
        ],
        responsive: true,
        autoWidth: false
    });

    // Handle form submission
    $('#partnerForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {};
        $(this).serializeArray().forEach(function(item) {
            formData[item.name] = item.value;
        });
        formData.action = formData.partner_id ? 'update' : 'add';

        $.ajax({
            url: 'inc/ajax/training_partners_ajax.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#partnerModal').modal('hide');
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message || 'An error occurred');
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
                    $('#contact_person').val(partner.contact_person);
                    $('#email').val(partner.email);
                    $('#phone').val(partner.phone);
                    $('#address').val(partner.address);
                    $('#status').val(partner.status);
                    $('.modal-title').text('Edit Training Partner');
                    $('#partnerModal').modal('show');
                } else {
                    toastr.error(response.message || 'Failed to load partner data');
                }
            },
            error: function() {
                toastr.error('An error occurred while fetching partner data');
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
                        toastr.error(response.message || 'Failed to delete partner');
                    }
                },
                error: function() {
                    toastr.error('An error occurred while deleting the partner');
                }
            });
        }
    });

    // Reset form when modal is closed
    $('#partnerModal').on('hidden.bs.modal', function() {
        $('#partnerForm')[0].reset();
        $('#partner_id').val('');
        $('.modal-title').text('Add New Training Partner');
    });
});
</script>
EOT;

// Include footer
require_once 'includes/layouts/footer.php';
?>
