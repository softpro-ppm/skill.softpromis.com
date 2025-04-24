<?php
// Include authentication check
//require_once 'inc/auth_check.php';

// Include config file which already includes functions.php
//require_once 'config.php';

// Check if user has admin privileges
// if (!hasRole('Administrator')) {
//     header('Location: dashboard.php?error=' . urlencode('You do not have permission to access this page.'));
//     exit;
// }

$pageTitle = 'Training Partners';
$currentPage = 'training-partners';
require_once './includes/header.php';

<!-- Additional CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
<style>
.card {
    border-radius: 0.5rem;
}
.card-header {
    border-radius: calc(0.5rem - 1px) calc(0.5rem - 1px) 0 0 !important;
}
.modal-content {
    border-radius: 0.5rem;
    overflow: hidden;
}
.table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}
.badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
}
.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}
.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}
.modal-header .close {
    padding: 1rem;
    margin: -1rem -1rem -1rem auto;
}
</style>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-handshake mr-2"></i>
                            Manage Training Partners
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#partnerModal">
                                <i class="fas fa-plus mr-1"></i> Add New Partner
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="partnersTable" class="table table-bordered table-striped table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Partner Name</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th width="100">Status</th>
                                    <th width="100">Actions</th>
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
<div class="modal fade" id="partnerModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle mr-2"></i>
                    <span>Add New Training Partner</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="partnerForm">
                <div class="modal-body">
                    <input type="hidden" id="partner_id" name="partner_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="partner_name">
                                    <i class="fas fa-building mr-1"></i>
                                    Partner Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="partner_name" name="partner_name" required>
                            </div>
                            <div class="form-group">
                                <label for="contact_person">
                                    <i class="fas fa-user mr-1"></i>
                                    Contact Person
                                </label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person">
                            </div>
                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope mr-1"></i>
                                    Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">
                                    <i class="fas fa-phone mr-1"></i>
                                    Phone
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="form-group">
                                <label for="address">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    Address
                                </label>
                                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="status">
                                    <i class="fas fa-toggle-on mr-1"></i>
                                    Status
                                </label>
                                <select class="form-control" id="status" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save Partner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Confirm Delete
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete this training partner? This action cannot be undone.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash mr-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Required JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Page specific script -->
<script>
$(document).ready(function() {
    // Configure toastr options
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Initialize DataTable with improved styling
    const table = $('#partnersTable').DataTable({
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: false,
        pageLength: 10,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>',
            searchPlaceholder: "Search partners..."
        },
        ajax: {
            url: 'inc/ajax/training_partners_ajax.php',
            type: 'POST',
            data: { action: 'list' }
        },
        columns: [
            { 
                data: 'partner_name',
                render: function(data) {
                    return `<strong>${data}</strong>`;
                }
            },
            { data: 'contact_person' },
            { 
                data: 'email',
                render: function(data) {
                    return data ? `<a href="mailto:${data}">${data}</a>` : '';
                }
            },
            { 
                data: 'phone',
                render: function(data) {
                    return data ? `<a href="tel:${data}">${data}</a>` : '';
                }
            },
            { data: 'address' },
            { 
                data: 'status',
                render: function(data) {
                    const badgeClass = data === 'active' ? 'success' : 'danger';
                    const icon = data === 'active' ? 'check-circle' : 'times-circle';
                    return `<span class="badge badge-${badgeClass}">
                                <i class="fas fa-${icon} mr-1"></i>${data}
                            </span>`;
                }
            },
            {
                data: 'partner_id',
                render: function(data) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info edit-partner" data-id="${data}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger delete-partner" data-id="${data}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'asc']],
        drawCallback: function() {
            $('[data-toggle="tooltip"]').tooltip();
        }
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
                    $('.modal-title').text('Edit Training Partner');
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
        $('.modal-title').text('Add New Training Partner');
    });
});
</script>

<?php require_once './includes/footer.php'; ?> 
