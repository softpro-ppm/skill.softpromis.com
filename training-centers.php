<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    header('Location: index.php');
    exit;
}

// Include required files
require_once 'config.php';
require_once 'crud_functions.php';

// Establish database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle AJAX requests
if(isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch($_POST['action']) {
        case 'list':
            try {
                $sql = "SELECT tc.*, tp.partner_name, 
                        CONCAT(tc.address, ', ', tc.city, ', ', tc.state, ' - ', tc.pincode) as full_address 
                        FROM training_centers tc 
                        LEFT JOIN training_partners tp ON tc.partner_id = tp.partner_id 
                        ORDER BY tc.center_id DESC";
                $result = $conn->query($sql);
                
                if (!$result) {
                    throw new Exception($conn->error);
                }
                
                $data = array();
                while($row = $result->fetch_assoc()) {
                    // Prepare the row data
                    $data[] = array(
                        "center_id" => $row['center_id'],
                        "partner_name" => htmlspecialchars($row['partner_name']),
                        "center_name" => htmlspecialchars($row['center_name']),
                        "contact_person" => htmlspecialchars($row['contact_person']),
                        "email" => htmlspecialchars($row['email']),
                        "phone" => htmlspecialchars($row['phone']),
                        "full_address" => htmlspecialchars($row['full_address']),
                        "status" => $row['status'],
                        "actions" => '<div class="btn-group btn-group-sm">' +
                                    '<button type="button" class="btn btn-info view-btn" data-id="' + $row['center_id'] + '"><i class="fas fa-eye"></i></button>' +
                                    '<button type="button" class="btn btn-primary edit-btn" data-id="' + $row['center_id'] + '"><i class="fas fa-edit"></i></button>' +
                                    '<button type="button" class="btn btn-danger delete-btn" data-id="' + $row['center_id'] + '"><i class="fas fa-trash"></i></button>' +
                                    '</div>'
                    );
                }
                
                echo json_encode(array(
                    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
                    "recordsTotal" => $result->num_rows,
                    "recordsFiltered" => $result->num_rows,
                    "data" => $data
                ));
            } catch (Exception $e) {
                echo json_encode(array(
                    "error" => "Error fetching data: " . $e->getMessage(),
                    "data" => array()
                ));
            }
            exit;
            break;

        case 'add':
            $response = array('status' => false, 'message' => '');
            try {
                // Sanitize input data
                $partner_id = mysqli_real_escape_string($conn, $_POST['partner_id']);
                $center_name = mysqli_real_escape_string($conn, $_POST['center_name']);
                $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                $phone = mysqli_real_escape_string($conn, $_POST['phone']);
                $address = mysqli_real_escape_string($conn, $_POST['address']);
                $city = mysqli_real_escape_string($conn, $_POST['city']);
                $state = mysqli_real_escape_string($conn, $_POST['state']);
                $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
                $status = mysqli_real_escape_string($conn, $_POST['status']);

                // Prepare the insert query
                $query = "INSERT INTO training_centers (partner_id, center_name, contact_person, email, phone, 
                         address, city, state, pincode, status, created_at, updated_at) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
                
                $stmt = $conn->prepare($query);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }

                $stmt->bind_param("isssssssss", $partner_id, $center_name, $contact_person, $email, $phone, 
                                $address, $city, $state, $pincode, $status);
                
                if($stmt->execute()) {
                    $response['status'] = true;
                    $response['message'] = 'Training center added successfully';
                    $response['center_id'] = $conn->insert_id;
                } else {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                $stmt->close();
            } catch (Exception $e) {
                $response['message'] = 'Error adding training center: ' . $e->getMessage();
            }
            echo json_encode($response);
            exit;
            break;

        case 'edit':
            $response = array('status' => false, 'message' => '');
            if(isset($_POST['center_id'])) {
                try {
                    $center_id = mysqli_real_escape_string($conn, $_POST['center_id']);
                    $partner_id = mysqli_real_escape_string($conn, $_POST['partner_id']);
                    $center_name = mysqli_real_escape_string($conn, $_POST['center_name']);
                    $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
                    $email = mysqli_real_escape_string($conn, $_POST['email']);
                    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
                    $address = mysqli_real_escape_string($conn, $_POST['address']);
                    $city = mysqli_real_escape_string($conn, $_POST['city']);
                    $state = mysqli_real_escape_string($conn, $_POST['state']);
                    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
                    $status = mysqli_real_escape_string($conn, $_POST['status']);

                    $query = "UPDATE training_centers SET 
                             partner_id = ?,
                             center_name = ?, 
                             contact_person = ?, 
                             email = ?, 
                             phone = ?, 
                             address = ?,
                             city = ?,
                             state = ?,
                             pincode = ?,
                             status = ?, 
                             updated_at = CURRENT_TIMESTAMP 
                             WHERE center_id = ?";
                    
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("isssssssssi", $partner_id, $center_name, $contact_person, $email, 
                                    $phone, $address, $city, $state, $pincode, $status, $center_id);
                    
                    if($stmt->execute()) {
                        $response['status'] = true;
                        $response['message'] = 'Training center updated successfully';
                    } else {
                        throw new Exception("Execute failed: " . $stmt->error);
                    }
                    $stmt->close();
                } catch (Exception $e) {
                    $response['message'] = 'Error updating training center: ' . $e->getMessage();
                }
            }
            echo json_encode($response);
            exit;
            break;

        case 'delete':
            $response = array('status' => false, 'message' => '');
            if(isset($_POST['center_id'])) {
                try {
                    $center_id = mysqli_real_escape_string($conn, $_POST['center_id']);
                    
                    $query = "DELETE FROM training_centers WHERE center_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $center_id);
                    
                    if($stmt->execute()) {
                        $response['status'] = true;
                        $response['message'] = 'Training center deleted successfully';
                    } else {
                        throw new Exception("Execute failed: " . $stmt->error);
                    }
                    $stmt->close();
                } catch (Exception $e) {
                    $response['message'] = 'Error deleting training center: ' . $e->getMessage();
                }
            }
            echo json_encode($response);
            exit;
            break;

        case 'get_center':
            $response = array('status' => false, 'message' => '', 'data' => null);
            if(isset($_POST['center_id'])) {
                try {
                    $center_id = mysqli_real_escape_string($conn, $_POST['center_id']);
                    
                    $query = "SELECT * FROM training_centers WHERE center_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $center_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if($row = $result->fetch_assoc()) {
                        $response['status'] = true;
                        $response['data'] = $row;
                    } else {
                        $response['message'] = 'Training center not found';
                    }
                    $stmt->close();
                } catch (Exception $e) {
                    $response['message'] = 'Error fetching training center: ' . $e->getMessage();
                }
            }
            echo json_encode($response);
            exit;
            break;

        case 'get_partners':
            try {
                $query = "SELECT partner_id, partner_name FROM training_partners WHERE status = 'active' ORDER BY partner_name";
                $result = $conn->query($query);
                
                $partners = array();
                while($row = $result->fetch_assoc()) {
                    $partners[] = array(
                        'id' => $row['partner_id'],
                        'text' => $row['partner_name']
                    );
                }
                
                echo json_encode(array('status' => true, 'data' => $partners));
            } catch (Exception $e) {
                echo json_encode(array('status' => false, 'message' => $e->getMessage()));
            }
            exit;
            break;
    }
}

// Set page title
$pageTitle = 'Training Centers';

// Include header
require_once 'includes/header.php';
?>

<!-- DataTables & Extensions CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
<!-- Select2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<?php
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
                    <h1>Training Centers</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Training Centers</li>
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
            <h3 class="card-title">Training Centers List</h3>
            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#centerModal">
                <i class="fas fa-plus"></i> Add New Center
              </button>
            </div>
          </div>
          <div class="card-body">
            <table id="centersTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Center ID</th>
                                        <th>Partner</th>
                                        <th>Center Name</th>
                  <th>Contact Person</th>
                                        <th>Email</th>
                  <th>Phone</th>
                                        <th>Address</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
            </table>
                        </div>
                    </div>
          </div>
        </div>
      </div>
    </section>
</div>

<!-- Center Modal -->
<div class="modal fade" id="centerModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Add New Training Center</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
            <form id="centerForm">
                <input type="hidden" id="center_id" name="center_id">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                                <label for="partner_id">Training Partner</label>
                                <select class="form-control select2" id="partner_id" name="partner_id" required>
                                    <option value="">Select Partner</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="center_name">Center Name</label>
                                <input type="text" class="form-control" id="center_name" name="center_name" required>
                            </div>
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
                                <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                </div>
                <div class="form-group">
                  <label for="city">City</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                </div>
                <div class="form-group">
                  <label for="state">State</label>
                                <input type="text" class="form-control" id="state" name="state" required>
                </div>
                <div class="form-group">
                  <label for="pincode">Pincode</label>
                                <input type="text" class="form-control" id="pincode" name="pincode" required>
                </div>
                <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Center</button>
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
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
                <p>Are you sure you want to delete this training center?</p>
                <p><strong>Center:</strong> <span id="delete_center_name"></span></p>
        </div>
        <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
      </div>
    </div>
  </div>

<!-- View Training Center Modal -->
<div class="modal fade" id="viewCenterModal" tabindex="-1" role="dialog" aria-labelledby="viewCenterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCenterModalLabel">Training Center Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Center Name:</strong> <span id="view-center-name"></span></p>
                        <p><strong>Training Partner:</strong> <span id="view-partner-name"></span></p>
                        <p><strong>Contact Person:</strong> <span id="view-contact-person"></span></p>
                        <p><strong>Email:</strong> <span id="view-email"></span></p>
                        <p><strong>Phone:</strong> <span id="view-phone"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Address:</strong> <span id="view-address"></span></p>
                        <p><strong>City:</strong> <span id="view-city"></span></p>
                        <p><strong>State:</strong> <span id="view-state"></span></p>
                        <p><strong>Pincode:</strong> <span id="view-pincode"></span></p>
                        <p><strong>Status:</strong> <span id="view-status"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.form-control-static {
    padding: 7px 12px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    min-height: 35px;
}
.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}
</style>

<?php include 'includes/js.php'; ?>

<!-- DataTables & Extensions JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Add the Select2 handler script after other JS includes -->
<script src="assets/js/select2-handler.js"></script>

<script>
$(function () {
    // Initialize Select2
    Select2Handler.init('#partner_id', {
        placeholder: 'Select Partner',
        allowClear: true
    });

    // Form validation rules
    $.validator.addMethod("phoneFormat", function(value, element) {
        return this.optional(element) || /^[0-9+\-\s()]{10,15}$/.test(value);
    }, "Please enter a valid phone number");

    $.validator.addMethod("pincodeFormat", function(value, element) {
        return this.optional(element) || /^[0-9]{6}$/.test(value);
    }, "Please enter a valid 6-digit pincode");

    // Initialize form validation
    var validator = $('#centerForm').validate({
        rules: {
            partner_id: "required",
            center_name: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
            contact_person: {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            email: {
                required: true,
                email: true,
                maxlength: 100
            },
            phone: {
                required: true,
                phoneFormat: true
            },
            address: {
                required: true,
                minlength: 5,
                maxlength: 200
            },
            city: {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            state: {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            pincode: {
                required: true,
                pincodeFormat: true
            },
            status: "required"
        },
        messages: {
            partner_id: "Please select a training partner",
            center_name: {
                required: "Please enter center name",
                minlength: "Center name must be at least 3 characters long",
                maxlength: "Center name cannot exceed 100 characters"
            },
            contact_person: {
                required: "Please enter contact person name",
                minlength: "Contact person name must be at least 2 characters long",
                maxlength: "Contact person name cannot exceed 50 characters"
            },
            email: {
                required: "Please enter email address",
                email: "Please enter a valid email address",
                maxlength: "Email cannot exceed 100 characters"
            },
            phone: {
                required: "Please enter phone number",
                phoneFormat: "Please enter a valid phone number"
            },
            address: {
                required: "Please enter address",
                minlength: "Address must be at least 5 characters long",
                maxlength: "Address cannot exceed 200 characters"
            },
            city: {
                required: "Please enter city",
                minlength: "City must be at least 2 characters long",
                maxlength: "City cannot exceed 50 characters"
            },
            state: {
                required: "Please enter state",
                minlength: "State must be at least 2 characters long",
                maxlength: "State cannot exceed 50 characters"
            },
            pincode: {
                required: "Please enter pincode",
                pincodeFormat: "Please enter a valid 6-digit pincode"
            },
            status: "Please select status"
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        }
    });

    // Initialize DataTable
    var table = $('#centersTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "inc/ajax/training-centers.php",
            "type": "POST",
            "data": function(d) {
                d.action = "list";
            },
            "dataSrc": function(json) {
                if (json.error) {
                    toastr.error(json.error);
                    return [];
                }
                return json.data || [];
            }
        },
        "columns": [
            { "data": "center_id" },
            { "data": "partner_name" },
            { "data": "center_name" },
            { "data": "contact_person" },
            { "data": "email" },
            { "data": "phone" },
            { 
                "data": null,
                "render": function(data, type, row) {
                    var address = [row.address];
                    if (row.city) address.push(row.city);
                    if (row.state) address.push(row.state);
                    if (row.pincode) address.push(row.pincode);
                    return address.filter(Boolean).join(', ');
                }
            },
            { 
                "data": "status",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        var badgeClass = data === 'active' ? 'success' : 'danger';
                        return '<span class="badge badge-' + badgeClass + '">' + 
                               data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                    }
                    return data;
                }
            },
            { 
                "data": null,
                "orderable": false,
                "searchable": false,
                "render": function(data, type, row) {
                    return '<div class="btn-group btn-group-sm">' +
                           '<button type="button" class="btn btn-info view-center" data-id="' + row.center_id + '"><i class="fas fa-eye"></i></button>' +
                           '<button type="button" class="btn btn-primary edit-btn" data-id="' + row.center_id + '"><i class="fas fa-edit"></i></button>' +
                           '<button type="button" class="btn btn-danger delete-btn" data-id="' + row.center_id + '" data-name="' + row.center_name + '"><i class="fas fa-trash"></i></button>' +
                           '</div>';
                }
            }
        ],
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "order": [[0, 'desc']],
        "dom": 'Bfrtip',
        "buttons": [
            'copy', 
            'csv', 
            {
                extend: 'excel',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdf',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ],
        "pageLength": 10,
        "language": {
            "emptyTable": "No training centers found",
            "zeroRecords": "No matching records found",
            "loadingRecords": "Loading...",
            "processing": "Processing...",
            "error": "Error loading data. Please try again."
        }
    });

    // View Center
    $('#centersTable').on('click', '.view-btn', function() {
        var centerId = $(this).data('id');
        $.ajax({
            url: 'inc/ajax/training-centers.php',
            type: 'POST',
            data: {
                action: 'get_center',
                center_id: centerId
            },
            dataType: 'json',
            success: function(response) {
                if(response.status) {
                    var data = response.data;
                    
                    // Populate view modal
                    $('#view-center-name').text(data.center_name);
                    $('#view-partner-name').text(data.partner_name);
                    $('#view-contact-person').text(data.contact_person);
                    $('#view-email').text(data.email);
                    $('#view-phone').text(data.phone);
                    $('#view-address').text(data.address);
                    $('#view-city').text(data.city);
                    $('#view-state').text(data.state);
                    $('#view-pincode').text(data.pincode);
                    $('#view-status').html('<span class="badge badge-' + 
                        (data.status === 'active' ? 'success' : 'danger') + '">' +
                        data.status.charAt(0).toUpperCase() + data.status.slice(1) + '</span>');
                    
                    $('#viewCenterModal').modal('show');
                } else {
                    toastr.error(response.message || 'Error fetching center data');
                }
            },
            error: function() {
                toastr.error('Error fetching center data');
            }
        });
    });

    // Edit Center
    $('#centersTable').on('click', '.edit-btn', function() {
        var centerId = $(this).data('id');
        
        // Reset form and validation
        $('#centerForm')[0].reset();
        validator.resetForm();
        $('.is-invalid').removeClass('is-invalid');
        
        // Update modal title
        $('.modal-title').text('Edit Training Center');
        
        // Show loading state
        var submitBtn = $('#centerModal button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        
        // Load center data
        $.ajax({
            url: 'inc/ajax/training-centers.php',
            type: 'POST',
            data: {
                action: 'get_center',
                center_id: centerId
            },
            dataType: 'json',
            success: function(response) {
                if(response.status) {
                    var data = response.data;
                    
                    // Load partners first
                    $.ajax({
                        url: 'inc/ajax/training-centers.php',
                        type: 'POST',
                        data: { action: 'get_partners' },
                        success: function(partnersResponse) {
                            if(partnersResponse.status) {
                                var $partnerSelect = $('#partner_id');
                                $partnerSelect.empty().append('<option value="">Select Partner</option>');
                                
                                partnersResponse.data.forEach(function(partner) {
                                    var selected = partner.id == data.partner_id ? 'selected' : '';
                                    $partnerSelect.append('<option value="' + partner.id + '" ' + selected + '>' + partner.text + '</option>');
                                });
                                
                                // Set other form values
                                $('#center_id').val(data.center_id);
                                $('#center_name').val(data.center_name);
                                $('#contact_person').val(data.contact_person);
                                $('#email').val(data.email);
                                $('#phone').val(data.phone);
                                $('#address').val(data.address);
                                $('#city').val(data.city);
                                $('#state').val(data.state);
                                $('#pincode').val(data.pincode);
                                $('#status').val(data.status);
                                
                                // Initialize Select2 and show modal
                                $partnerSelect.trigger('change');
                                $('#centerModal').modal('show');
                            } else {
                                toastr.error(partnersResponse.message || 'Error loading partners');
                            }
                        },
                        error: function() {
                            toastr.error('Error loading partners');
                        },
                        complete: function() {
                            submitBtn.prop('disabled', false).text('Save Changes');
                        }
                    });
                } else {
                    toastr.error(response.message || 'Error fetching center data');
                }
            },
            error: function() {
                toastr.error('Error fetching center data');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Save Changes');
            }
        });
    });

    // Handle form submission
    $('#centerForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!validator.form()) {
            return false;
        }
        
        var formData = $(this).serialize();
        var isEdit = $('#center_id').val() !== '';
        var action = isEdit ? 'edit' : 'add';
        
        // Show loading state
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.text();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: 'inc/ajax/training-centers.php',
            type: 'POST',
            data: formData + '&action=' + action,
            dataType: 'json',
            success: function(response) {
                if(response.status) {
                    toastr.success(response.message);
                    $('#centerModal').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.error(response.message || 'Error saving training center');
                }
            },
            error: function() {
                toastr.error('Error saving training center');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Delete Center
    var deleteCenterId = null;
    
    $('#centersTable').on('click', '.delete-btn', function() {
        deleteCenterId = $(this).data('id');
        $('#delete_center_name').text($(this).data('name'));
        $('#deleteModal').modal('show');
    });
    
    $('#confirmDelete').on('click', function() {
        if (!deleteCenterId) return;
        
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
        
        $.ajax({
            url: 'inc/ajax/training-centers.php',
            type: 'POST',
            data: {
                action: 'delete',
                center_id: deleteCenterId
            },
            dataType: 'json',
            success: function(response) {
                if(response.status) {
                    toastr.success(response.message);
                    $('#deleteModal').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.error(response.message || 'Error deleting training center');
                }
            },
            error: function() {
                toastr.error('Error deleting training center');
            },
            complete: function() {
                btn.prop('disabled', false).text('Delete');
                deleteCenterId = null;
            }
        });
    });

    // Reset form when modal is hidden
    $('#centerModal').on('hidden.bs.modal', function() {
        $('#centerForm')[0].reset();
        $('#center_id').val('');
        validator.resetForm();
        $('.is-invalid').removeClass('is-invalid');
        Select2Handler.reset('#partner_id');
        $('.modal-title').text('Add New Training Center');
    });

    // Load partners when modal is shown for adding new center
    $('#centerModal').on('show.bs.modal', function(e) {
        if(!$('#center_id').val()) {
            Select2Handler.loadFromServer(
                '#partner_id',
                'inc/ajax/training-centers.php',
                { action: 'get_partners' },
                { placeholder: 'Select Partner' }
            );
        }
    });
});
</script>
</body>
</html>
