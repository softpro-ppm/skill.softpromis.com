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
        case 'add':
            $response = array('status' => false, 'message' => '');
            try {
                // Create uploads directory if it doesn't exist
                $upload_dir = 'uploads/partners/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Sanitize input data
                $partner_name = mysqli_real_escape_string($conn, $_POST['partner_name']);
                $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                $phone = mysqli_real_escape_string($conn, $_POST['phone']);
                $address = mysqli_real_escape_string($conn, $_POST['address']);
                $website = mysqli_real_escape_string($conn, $_POST['website']);
                $status = mysqli_real_escape_string($conn, $_POST['status']);
                $stable = mysqli_real_escape_string($conn, $_POST['stable']);

                // Handle file uploads
                $registration_doc = null;
                $agreement_doc = null;

                // Process registration document
                if(isset($_FILES['registration_doc']) && $_FILES['registration_doc']['error'] == 0) {
                    $file_info = pathinfo($_FILES['registration_doc']['name']);
                    $registration_doc = uniqid() . '_reg.' . $file_info['extension'];
                    $target_file = $upload_dir . $registration_doc;
                    
                    if(!move_uploaded_file($_FILES['registration_doc']['tmp_name'], $target_file)) {
                        throw new Exception("Error uploading registration document");
                    }
                }

                // Process agreement document
                if(isset($_FILES['agreement_doc']) && $_FILES['agreement_doc']['error'] == 0) {
                    $file_info = pathinfo($_FILES['agreement_doc']['name']);
                    $agreement_doc = uniqid() . '_agr.' . $file_info['extension'];
                    $target_file = $upload_dir . $agreement_doc;
                    
                    if(!move_uploaded_file($_FILES['agreement_doc']['tmp_name'], $target_file)) {
                        throw new Exception("Error uploading agreement document");
                    }
                }

                // Prepare the insert query
                $query = "INSERT INTO training_partners (partner_name, contact_person, email, phone, address, website, 
                         registration_doc, agreement_doc, status, stable, created_at, updated_at) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
                
                $stmt = $conn->prepare($query);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }

                $stmt->bind_param("ssssssssss", $partner_name, $contact_person, $email, $phone, $address, $website,
                                $registration_doc, $agreement_doc, $status, $stable);
                
                if($stmt->execute()) {
                    $response['status'] = true;
                    $response['message'] = 'Partner added successfully';
                    $response['partner_id'] = $conn->insert_id;
                } else {
                    // If insert fails, delete uploaded files
                    if($registration_doc && file_exists($upload_dir . $registration_doc)) {
                        unlink($upload_dir . $registration_doc);
                    }
                    if($agreement_doc && file_exists($upload_dir . $agreement_doc)) {
                        unlink($upload_dir . $agreement_doc);
                    }
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                $stmt->close();
            } catch (Exception $e) {
                $response['message'] = 'Error adding partner: ' . $e->getMessage();
            }
            echo json_encode($response);
            exit;
            break;

        case 'list':
            try {
                $sql = "SELECT tp.*, COUNT(tc.center_id) as center_count 
                        FROM training_partners tp 
                        LEFT JOIN training_centers tc ON tp.partner_id = tc.partner_id 
                        GROUP BY tp.partner_id 
                        ORDER BY tp.partner_id DESC";
                $result = $conn->query($sql);
                
                if (!$result) {
                    throw new Exception($conn->error);
                }
                
                $data = array();
                while($row = $result->fetch_assoc()) {
                    // Prepare the row data
                    $data[] = array(
                        "partner_id" => $row['partner_id'],
                        "partner_name" => htmlspecialchars($row['partner_name']),
                        "contact_person" => htmlspecialchars($row['contact_person']),
                        "email" => htmlspecialchars($row['email']),
                        "phone" => htmlspecialchars($row['phone']),
                        "center_count" => $row['center_count'],
                        "status" => $row['status'],
                        "stable" => $row['stable'],
                        "actions" => '<button type="button" class="btn btn-primary btn-sm view-btn mr-1" data-id="' . $row['partner_id'] . '">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm edit-btn mr-1" data-id="' . $row['partner_id'] . '">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                        data-id="' . $row['partner_id'] . '" 
                                        data-name="' . htmlspecialchars($row['partner_name']) . '"
                                        data-centers="' . $row['center_count'] . '">
                                        <i class="fas fa-trash"></i>
                                    </button>'
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

        case 'edit':
            $response = array('status' => false, 'message' => '');
            if(isset($_POST['partner_id'])) {
                try {
                    $upload_dir = 'uploads/partners/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                $partner_id = mysqli_real_escape_string($conn, $_POST['partner_id']);
                $partner_name = mysqli_real_escape_string($conn, $_POST['partner_name']);
                $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                $phone = mysqli_real_escape_string($conn, $_POST['phone']);
                $address = mysqli_real_escape_string($conn, $_POST['address']);
                    $website = mysqli_real_escape_string($conn, $_POST['website']);
                $status = mysqli_real_escape_string($conn, $_POST['status']);
                $stable = mysqli_real_escape_string($conn, $_POST['stable']);

                    // Get current document filenames
                    $current_docs_query = "SELECT registration_doc, agreement_doc FROM training_partners WHERE partner_id = ?";
                    $stmt = $conn->prepare($current_docs_query);
                    $stmt->bind_param("i", $partner_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $current_docs = $result->fetch_assoc();
                    $stmt->close();

                    $registration_doc = $current_docs['registration_doc'];
                    $agreement_doc = $current_docs['agreement_doc'];

                    // Process registration document
                    if(isset($_FILES['registration_doc']) && $_FILES['registration_doc']['error'] == 0) {
                        // Delete old file if exists
                        if($registration_doc && file_exists($upload_dir . $registration_doc)) {
                            unlink($upload_dir . $registration_doc);
                        }
                        
                        $file_info = pathinfo($_FILES['registration_doc']['name']);
                        $registration_doc = uniqid() . '_reg.' . $file_info['extension'];
                        $target_file = $upload_dir . $registration_doc;
                        
                        if(!move_uploaded_file($_FILES['registration_doc']['tmp_name'], $target_file)) {
                            throw new Exception("Error uploading registration document");
                        }
                    }

                    // Process agreement document
                    if(isset($_FILES['agreement_doc']) && $_FILES['agreement_doc']['error'] == 0) {
                        // Delete old file if exists
                        if($agreement_doc && file_exists($upload_dir . $agreement_doc)) {
                            unlink($upload_dir . $agreement_doc);
                        }
                        
                        $file_info = pathinfo($_FILES['agreement_doc']['name']);
                        $agreement_doc = uniqid() . '_agr.' . $file_info['extension'];
                        $target_file = $upload_dir . $agreement_doc;
                        
                        if(!move_uploaded_file($_FILES['agreement_doc']['tmp_name'], $target_file)) {
                            throw new Exception("Error uploading agreement document");
                        }
                    }

                $query = "UPDATE training_partners SET 
                         partner_name = ?, 
                         contact_person = ?, 
                         email = ?, 
                         phone = ?, 
                         address = ?, 
                             website = ?,
                             registration_doc = ?,
                             agreement_doc = ?,
                         status = ?, 
                         stable = ?,
                         updated_at = CURRENT_TIMESTAMP 
                         WHERE partner_id = ?";
                
                $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssssssssssi", $partner_name, $contact_person, $email, $phone, $address, 
                                    $website, $registration_doc, $agreement_doc, $status, $stable, $partner_id);
                
                if($stmt->execute()) {
                    $response['status'] = true;
                    $response['message'] = 'Partner updated successfully';
                } else {
                        throw new Exception("Error updating partner");
                    }
                    $stmt->close();
                } catch (Exception $e) {
                    $response['message'] = 'Error updating partner: ' . $e->getMessage();
                }
            }
            echo json_encode($response);
            exit;
            break;

        case 'delete':
            $response = array('status' => false, 'message' => '');
            if(isset($_POST['partner_id'])) {
                $partner_id = mysqli_real_escape_string($conn, $_POST['partner_id']);
                
                // First check if partner has any centers
                $check_query = "SELECT COUNT(*) as count FROM training_centers WHERE partner_id = ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->bind_param("i", $partner_id);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                $row = $result->fetch_assoc();
                
                if($row['count'] > 0) {
                    $response['message'] = 'Cannot delete partner with associated training centers';
                } else {
                    $query = "DELETE FROM training_partners WHERE partner_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $partner_id);
                    
                    if($stmt->execute()) {
                        $response['status'] = true;
                        $response['message'] = 'Partner deleted successfully';
                    } else {
                        $response['message'] = 'Error deleting partner';
                    }
                    $stmt->close();
                }
                $check_stmt->close();
            }
            echo json_encode($response);
            exit;
            break;

        case 'get_partner':
            $response = array('status' => false, 'message' => '', 'data' => null);
            if(isset($_POST['partner_id'])) {
                $partner_id = mysqli_real_escape_string($conn, $_POST['partner_id']);
                
                $query = "SELECT * FROM training_partners WHERE partner_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $partner_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if($row = $result->fetch_assoc()) {
                    $response['status'] = true;
                    $response['data'] = $row;
                } else {
                    $response['message'] = 'Partner not found';
                }
                $stmt->close();
            }
            echo json_encode($response);
            exit;
            break;
    }
}

// Set page title
$pageTitle = 'Training Partners';

// Include header
require_once 'includes/header.php';
?>

<!-- DataTables & Extensions CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

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
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#partnerModal">
                                    <i class="fas fa-plus"></i> Add New Partner
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="partnersTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Partner ID</th>
                                        <th>Name</th>
                                        <th>Contact Person</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Centers</th>
                                        <th>Status</th>
                                        <th>Stable</th>
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
</div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->

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
            <form id="partnerForm" enctype="multipart/form-data">
                <input type="hidden" id="partner_id" name="partner_id" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="partner_name">Partner Name</label>
                                <input type="text" class="form-control" id="partner_name" name="partner_name" required>
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
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="website">Website</label>
                                <input type="url" class="form-control" id="website" name="website">
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control select2" id="status" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="pending">Pending Approval</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="stable">Stable</label>
                                <input type="text" class="form-control" id="stable" name="stable" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Documents</label>
                                <div class="custom-file mb-2">
                                    <input type="file" class="custom-file-input" id="registration_doc" name="registration_doc" accept=".pdf,.doc,.docx">
                                    <label class="custom-file-label" for="registration_doc">Registration Document</label>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="agreement_doc" name="agreement_doc" accept=".pdf,.doc,.docx">
                                    <label class="custom-file-label" for="agreement_doc">Agreement Document</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
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
                <p><strong>Partner:</strong> <span id="delete_partner_name"></span></p>
                <p><strong>Associated Centers:</strong> <span id="delete_partner_centers"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- View Partner Modal -->
<div class="modal fade" id="viewPartnerModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Partner Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Partner Name</label>
                            <p id="view_partner_name" class="form-control-static"></p>
                        </div>
                        <div class="form-group">
                            <label>Contact Person</label>
                            <p id="view_contact_person" class="form-control-static"></p>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <p id="view_email" class="form-control-static"></p>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <p id="view_phone" class="form-control-static"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address</label>
                            <p id="view_address" class="form-control-static"></p>
                        </div>
                        <div class="form-group">
                            <label>Website</label>
                            <p id="view_website" class="form-control-static"></p>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <p id="view_status" class="form-control-static"></p>
                        </div>
                        <div class="form-group">
                            <label>Stable</label>
                            <p id="view_stable" class="form-control-static"></p>
                        </div>
                        <div class="form-group">
                            <label>Training Centers</label>
                            <p id="view_centers" class="form-control-static"></p>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Documents</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="d-block">Registration Document</label>
                                    <p id="view_registration_doc" class="form-control-static"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="d-block">Agreement Document</label>
                                    <p id="view_agreement_doc" class="form-control-static"></p>
                                </div>
                            </div>
                        </div>
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
.document-link {
    color: #007bff;
    text-decoration: none;
}
.document-link:hover {
    text-decoration: underline;
}
.document-not-uploaded {
    color: #6c757d;
    font-style: italic;
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

<script>
$(function () {
    // Initialize DataTable with AJAX
    var table = $('#partnersTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "training-partners.php",
            "type": "POST",
            "data": function(d) {
                d.action = "list";
            },
            "error": function(xhr, error, thrown) {
                toastr.error('Error loading data. Please try again.');
                console.error('DataTables error:', error, thrown);
            },
            "dataSrc": function(json) {
                if (json.error) {
                    toastr.error(json.error);
                    return [];
                }
                return json.data;
            }
        },
        "columns": [
            { "data": "partner_id" },
            { "data": "partner_name" },
            { "data": "contact_person" },
            { "data": "email" },
            { "data": "phone" },
            { "data": "center_count" },
            { 
                "data": "status",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        var badgeClass = data === 'active' ? 'success' : 'danger';
                        return '<span class="badge badge-' + badgeClass + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                    }
                    return data;
                }
            },
            { "data": "stable", "defaultContent": "N/A" },
            { 
                "data": "actions",
                "orderable": false,
                "searchable": false
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
            "lengthMenu": "Show _MENU_ entries per page",
            "zeroRecords": "No matching records found",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "search": "Search:",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            },
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        }
    });

    // Add error handling for DataTable
    $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
        toastr.error('Error loading data: ' + thrownError);
    });

    // Handle edit button click
    $(document).on('click', '.edit-btn', function() {
        var partnerId = $(this).data('id');
        
        // Reset form
        $('#partnerForm')[0].reset();
        $('#partner_id').val(partnerId);
        
        // Get partner data
        $.ajax({
            url: 'training-partners.php',
            type: 'POST',
            data: {
                action: 'get_partner',
                partner_id: partnerId
            },
            dataType: 'json',
            success: function(response) {
                if(response.status) {
                    var data = response.data;
                    $('#partner_id').val(data.partner_id);
                    $('#partner_name').val(data.partner_name);
                    $('#contact_person').val(data.contact_person);
                    $('#email').val(data.email);
                    $('#phone').val(data.phone);
                    $('#address').val(data.address);
                    $('#website').val(data.website);
                    $('#status').val(data.status).trigger('change');
                    $('#stable').val(data.stable);
                    
                    // Update modal title
                    $('.modal-title').text('Edit Training Partner');
                    $('#partnerModal').modal('show');
                } else {
                    toastr.error(response.message || 'Error fetching partner data');
                }
            },
            error: function() {
                toastr.error('Error fetching partner data');
            }
        });
    });

    // Handle delete button click
    $(document).on('click', '.delete-btn', function() {
        var partnerId = $(this).data('id');
        var partnerName = $(this).data('name');
        var centerCount = $(this).data('centers');
        
        $('#delete_partner_name').text(partnerName);
        $('#delete_partner_centers').text(centerCount);
        $('#deleteModal').modal('show');
        
        // Store the partner ID for delete confirmation
        $('#confirmDelete').data('id', partnerId);
    });

    // Handle delete confirmation
    $('#confirmDelete').on('click', function() {
        var partnerId = $(this).data('id');
        
        $.ajax({
            url: 'training-partners.php',
            type: 'POST',
            data: {
                action: 'delete',
                partner_id: partnerId
            },
            dataType: 'json',
            success: function(response) {
                $('#deleteModal').modal('hide');
                if(response.status) {
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message || 'Error deleting partner');
                }
            },
            error: function() {
                toastr.error('Error deleting partner');
            }
        });
    });

    // Handle form submission
    $('#partnerForm').on('submit', function(e) {
        e.preventDefault();
        
        // Basic form validation
        var requiredFields = ['partner_name', 'contact_person', 'email', 'phone', 'address', 'status', 'stable'];
        var isValid = true;
        
        requiredFields.forEach(function(field) {
            var value = $('#' + field).val();
            if (!value || value.trim() === '') {
                isValid = false;
                $('#' + field).addClass('is-invalid');
                toastr.error(field.replace('_', ' ') + ' is required');
            } else {
                $('#' + field).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            return false;
        }
        
        // Create FormData object to handle file uploads
        var formData = new FormData(this);
        var partnerId = $('#partner_id').val();
        formData.append('action', partnerId ? 'edit' : 'add');
        
        // Disable submit button and show loading state
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.text();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: 'training-partners.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if(response.status) {
                    $('#partnerModal').modal('hide');
                    table.ajax.reload();
                    toastr.success(response.message);
                    
                    // Reset form
                    $('#partnerForm')[0].reset();
                    $('#partner_id').val('');
                    $('.custom-file-label').html('Choose file');
                } else {
                    toastr.error(response.message || 'Error processing request');
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Error processing request: ' + error);
                console.error('Ajax error:', error);
            },
            complete: function() {
                // Re-enable submit button and restore text
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Update filename on file selection
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Choose file');
    });

    // Reset form when modal is hidden
    $('#partnerModal').on('hidden.bs.modal', function() {
        $('#partnerForm')[0].reset();
        $('#partner_id').val('');
        $('.modal-title').text('Add New Training Partner');
        // Remove any validation classes
        $('.is-invalid').removeClass('is-invalid');
    });

    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Initialize custom file input
    bsCustomFileInput.init();

    // Handle view button click
    $(document).on('click', '.view-btn', function() {
        var partnerId = $(this).data('id');
        
        // Get partner data
        $.ajax({
            url: 'training-partners.php',
            type: 'POST',
            data: {
                action: 'get_partner',
                partner_id: partnerId
            },
            dataType: 'json',
            success: function(response) {
                if(response.status) {
                    var data = response.data;
                    
                    // Set the data in the view modal
                    $('#view_partner_name').text(data.partner_name);
                    $('#view_contact_person').text(data.contact_person);
                    $('#view_email').text(data.email);
                    $('#view_phone').text(data.phone);
                    $('#view_address').text(data.address);
                    $('#view_website').html(data.website ? '<a href="' + data.website + '" target="_blank">' + data.website + '</a>' : 'N/A');
                    $('#view_status').html('<span class="badge badge-' + (data.status === 'active' ? 'success' : 'danger') + '">' + 
                        data.status.charAt(0).toUpperCase() + data.status.slice(1) + '</span>');
                    $('#view_stable').text(data.stable || 'N/A');
                    $('#view_centers').text(data.center_count || '0');

                    // Set document information
                    if(data.registration_doc) {
                        $('#view_registration_doc').html(
                            '<a href="uploads/partners/' + data.registration_doc + '" target="_blank" class="document-link">' +
                            '<i class="fas fa-file-pdf"></i> View Document</a>'
                        );
                    } else {
                        $('#view_registration_doc').html(
                            '<span class="document-not-uploaded">No document uploaded</span>'
                        );
                    }

                    if(data.agreement_doc) {
                        $('#view_agreement_doc').html(
                            '<a href="uploads/partners/' + data.agreement_doc + '" target="_blank" class="document-link">' +
                            '<i class="fas fa-file-pdf"></i> View Document</a>'
                        );
                    } else {
                        $('#view_agreement_doc').html(
                            '<span class="document-not-uploaded">No document uploaded</span>'
                        );
                    }
                    
                    // Show the modal
                    $('#viewPartnerModal').modal('show');
                } else {
                    toastr.error(response.message || 'Error fetching partner data');
                }
            },
            error: function() {
                toastr.error('Error fetching partner data');
            }
        });
    });
});
</script>
