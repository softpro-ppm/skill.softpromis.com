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
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#sectorModal">
                                    <i class="fas fa-plus"></i> Add New Sector
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="sectorsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sector ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Total Courses</th>
                                        <th>Active Students</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>SEC001</td>
                                        <td>Information Technology</td>
                                        <td>IT and Software Development</td>
                                        <td>5</td>
                                        <td>150</td>
                                        <td><span class="badge badge-success">Active</span></td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewSectorModal">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editSectorModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteSectorModal">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>SEC002</td>
                                        <td>Digital Marketing</td>
                                        <td>Online Marketing and SEO</td>
                                        <td>3</td>
                                        <td>75</td>
                                        <td><span class="badge badge-success">Active</span></td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewSectorModal">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editSectorModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteSectorModal">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
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
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sectorName">Sector Name</label>
                                <input type="text" class="form-control" id="sectorName" placeholder="Enter sector name" required>
                            </div>
                            <div class="form-group">
                                <label for="sectorCode">Sector Code</label>
                                <input type="text" class="form-control" id="sectorCode" placeholder="Enter sector code" required>
                            </div>
                            <div class="form-group">
                                <label for="sectorType">Sector Type</label>
                                <select class="form-control select2" id="sectorType" required>
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
                                <textarea class="form-control" id="description" rows="3" placeholder="Enter sector description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="jobRoles">Job Roles</label>
                                <textarea class="form-control" id="jobRoles" rows="3" placeholder="Enter potential job roles" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Documents</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="sectorDoc">
                                    <label class="custom-file-label" for="sectorDoc">Sector Document</label>
                                </div>
                                <div class="custom-file mt-2">
                                    <input type="file" class="custom-file-input" id="curriculumDoc">
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

<!-- View Sector Modal -->
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
                        <div class="form-group">
                            <label>Sector ID</label>
                            <p>SEC001</p>
                        </div>
                        <div class="form-group">
                            <label>Sector Name</label>
                            <p>Information Technology</p>
                        </div>
                        <div class="form-group">
                            <label>Sector Type</label>
                            <p>Technical</p>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <p>IT and Software Development</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Job Roles</label>
                            <p>Software Developer, Web Developer, Database Administrator</p>
                        </div>
                        <div class="form-group">
                            <label>Total Courses</label>
                            <p>5</p>
                        </div>
                        <div class="form-group">
                            <label>Active Students</label>
                            <p>150</p>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <p><span class="badge badge-success">Active</span></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Associated Courses</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Course ID</th>
                                        <th>Name</th>
                                        <th>Duration</th>
                                        <th>Fee</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>C001</td>
                                        <td>Web Development</td>
                                        <td>3 months</td>
                                        <td>₹15,000</td>
                                        <td><span class="badge badge-success">Active</span></td>
                                    </tr>
                                    <tr>
                                        <td>C003</td>
                                        <td>Mobile App Development</td>
                                        <td>4 months</td>
                                        <td>₹18,000</td>
                                        <td><span class="badge badge-success">Active</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editSectorName">Sector Name</label>
                                <input type="text" class="form-control" id="editSectorName" value="Information Technology" required>
                            </div>
                            <div class="form-group">
                                <label for="editSectorCode">Sector Code</label>
                                <input type="text" class="form-control" id="editSectorCode" value="SEC001" required>
                            </div>
                            <div class="form-group">
                                <label for="editSectorType">Sector Type</label>
                                <select class="form-control select2" id="editSectorType" required>
                                    <option value="Technical" selected>Technical</option>
                                    <option value="Non-Technical">Non-Technical</option>
                                    <option value="Vocational">Vocational</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editDescription">Description</label>
                                <textarea class="form-control" id="editDescription" rows="3" required>IT and Software Development</textarea>
                            </div>
                            <div class="form-group">
                                <label for="editJobRoles">Job Roles</label>
                                <textarea class="form-control" id="editJobRoles" rows="3" required>Software Developer, Web Developer, Database Administrator</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Documents</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="editSectorDoc">
                                    <label class="custom-file-label" for="editSectorDoc">Sector Document</label>
                                </div>
                                <div class="custom-file mt-2">
                                    <input type="file" class="custom-file-input" id="editCurriculumDoc">
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
    // Initialize DataTable
    $('#sectorsTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Enter search term..."
        },
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });

    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Initialize custom file input
    bsCustomFileInput.init();
});
</script>
</body>
</html> 
