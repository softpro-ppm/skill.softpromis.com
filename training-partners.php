<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define BASEPATH constant if not already defined
if (!defined('BASEPATH')) {
    define('BASEPATH', true);
}

require_once 'inc/auth_check.php';
require_once 'config.php';
require_once 'inc/functions.php';

// Debug session data
error_log("Session data in training-partners.php: " . print_r($_SESSION, true));
error_log("User role in training-partners.php: " . ($_SESSION['user']['role'] ?? 'not set'));

// Check if user has admin privileges
if (!hasRole('admin')) {
    error_log("Access denied to training-partners.php - User role: " . ($_SESSION['user']['role'] ?? 'not set'));
    setFlashMessage('error', 'You do not have permission to access this page.');
    header('Location: dashboard.php');
    exit;
}

error_log("Access granted to training-partners.php - User role: " . ($_SESSION['user']['role'] ?? 'not set'));

$pageTitle = 'Training Partners';
$currentPage = 'training-partners';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Softpro Skill Solutions - Training Partners</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php" role="button">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </nav>

  <!-- Main Sidebar Container -->
  <?php require_once 'includes/sidebar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Training Partners</h1>
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
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="contact_person">Contact Person</label>
                  <input type="text" class="form-control" id="contact_person" name="contact_person" placeholder="Enter contact person name" required>
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address" required>
                </div>
                <div class="form-group">
                  <label for="phone">Phone</label>
                  <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter phone number" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="address">Address</label>
                  <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter full address" required></textarea>
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
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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
</body>
</html> 
