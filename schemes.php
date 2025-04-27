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
$pageTitle = 'Schemes';

// Include header
require_once 'includes/header.php';

// Include sidebar
require_once 'includes/sidebar.php';
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Schemes</h1>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Schemes List -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Schemes List</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSchemeModal">
                <i class="fas fa-plus"></i> Add New Scheme
              </button>
            </div>
          </div>
          <div class="card-body">
            <table id="schemesTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Scheme ID</th>
                  <th>Name</th>
                  <th>Type</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Target Beneficiaries</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>S001</td>
                  <td>PMKVY 4.0</td>
                  <td>Government</td>
                  <td>01/01/2024</td>
                  <td>31/12/2024</td>
                  <td>1000</td>
                  <td><span class="badge badge-success">Active</span></td>
                  <td>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewSchemeModal">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editSchemeModal">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteSchemeModal">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>S002</td>
                  <td>DDU-GKY</td>
                  <td>Government</td>
                  <td>01/01/2024</td>
                  <td>31/12/2025</td>
                  <td>500</td>
                  <td><span class="badge badge-success">Active</span></td>
                  <td>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewSchemeModal">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editSchemeModal">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteSchemeModal">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->

  <!-- Add Scheme Modal -->
  <div class="modal fade" id="addSchemeModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Add New Scheme</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="schemeName">Scheme Name</label>
                  <input type="text" class="form-control" id="schemeName" placeholder="Enter scheme name" required>
                </div>
                <div class="form-group">
                  <label for="schemeType">Scheme Type</label>
                  <select class="form-control select2" id="schemeType" required>
                    <option value="">Select Type</option>
                    <option value="Government">Government</option>
                    <option value="Private">Private</option>
                    <option value="Corporate">Corporate</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="startDate">Start Date</label>
                  <input type="date" class="form-control" id="startDate" required>
                </div>
                <div class="form-group">
                  <label for="endDate">End Date</label>
                  <input type="date" class="form-control" id="endDate" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="targetBeneficiaries">Target Beneficiaries</label>
                  <input type="number" class="form-control" id="targetBeneficiaries" placeholder="Enter target number" required>
                </div>
                <div class="form-group">
                  <label for="eligibility">Eligibility Criteria</label>
                  <textarea class="form-control" id="eligibility" rows="3" placeholder="Enter eligibility criteria" required></textarea>
                </div>
                <div class="form-group">
                  <label for="benefits">Benefits</label>
                  <textarea class="form-control" id="benefits" rows="3" placeholder="Enter scheme benefits" required></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea class="form-control" id="description" rows="3" placeholder="Enter scheme description"></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Documents</label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="schemeDoc">
                    <label class="custom-file-label" for="schemeDoc">Scheme Document</label>
                  </div>
                  <div class="custom-file mt-2">
                    <input type="file" class="custom-file-input" id="guidelinesDoc">
                    <label class="custom-file-label" for="guidelinesDoc">Guidelines Document</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Scheme</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Scheme Modal -->
  <div class="modal fade" id="viewSchemeModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">View Scheme Details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Scheme ID</label>
                <p>S001</p>
              </div>
              <div class="form-group">
                <label>Scheme Name</label>
                <p>PMKVY 4.0</p>
              </div>
              <div class="form-group">
                <label>Scheme Type</label>
                <p>Government</p>
              </div>
              <div class="form-group">
                <label>Start Date</label>
                <p>01/01/2024</p>
              </div>
              <div class="form-group">
                <label>End Date</label>
                <p>31/12/2024</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Target Beneficiaries</label>
                <p>1000</p>
              </div>
              <div class="form-group">
                <label>Eligibility Criteria</label>
                <p>Age: 18-35 years, Education: 10th pass</p>
              </div>
              <div class="form-group">
                <label>Benefits</label>
                <p>Free training, Certification, Placement assistance</p>
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
                      <td>C002</td>
                      <td>Digital Marketing</td>
                      <td>2 months</td>
                      <td>₹12,000</td>
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

  <!-- Edit Scheme Modal -->
  <div class="modal fade" id="editSchemeModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Scheme</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editSchemeName">Scheme Name</label>
                  <input type="text" class="form-control" id="editSchemeName" value="PMKVY 4.0" required>
                </div>
                <div class="form-group">
                  <label for="editSchemeType">Scheme Type</label>
                  <select class="form-control select2" id="editSchemeType" required>
                    <option value="Government" selected>Government</option>
                    <option value="Private">Private</option>
                    <option value="Corporate">Corporate</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="editStartDate">Start Date</label>
                  <input type="date" class="form-control" id="editStartDate" value="2024-01-01" required>
                </div>
                <div class="form-group">
                  <label for="editEndDate">End Date</label>
                  <input type="date" class="form-control" id="editEndDate" value="2024-12-31" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editTargetBeneficiaries">Target Beneficiaries</label>
                  <input type="number" class="form-control" id="editTargetBeneficiaries" value="1000" required>
                </div>
                <div class="form-group">
                  <label for="editEligibility">Eligibility Criteria</label>
                  <textarea class="form-control" id="editEligibility" rows="3" required>Age: 18-35 years, Education: 10th pass</textarea>
                </div>
                <div class="form-group">
                  <label for="editBenefits">Benefits</label>
                  <textarea class="form-control" id="editBenefits" rows="3" required>Free training, Certification, Placement assistance</textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="editDescription">Description</label>
                  <textarea class="form-control" id="editDescription" rows="3">Pradhan Mantri Kaushal Vikas Yojana 4.0</textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Documents</label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="editSchemeDoc">
                    <label class="custom-file-label" for="editSchemeDoc">Scheme Document</label>
                  </div>
                  <div class="custom-file mt-2">
                    <input type="file" class="custom-file-input" id="editGuidelinesDoc">
                    <label class="custom-file-label" for="editGuidelinesDoc">Guidelines Document</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update Scheme</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete Scheme Modal -->
  <div class="modal fade" id="deleteSchemeModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Delete Scheme</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this scheme? This action cannot be undone.</p>
          <p><strong>Scheme:</strong> PMKVY 4.0</p>
          <p><strong>Associated Courses:</strong> 2</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger">Delete Scheme</button>
        </div>
      </div>
    </div>
  </div>

<?php include 'includes/js.php'; ?>

<script>
  $(function () {
    // Initialize DataTable
    $('#schemesTable').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true
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
