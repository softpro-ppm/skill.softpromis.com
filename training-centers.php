<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Training Centers</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Training Centers</li>
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
            <h3 class="card-title">Training Centers List</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCenterModal">
                <i class="fas fa-plus"></i> Add New Center
              </button>
            </div>
          </div>
          <div class="card-body">
            <table id="centersTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Center ID</th>
                  <th>Name</th>
                  <th>Location</th>
                  <th>Contact Person</th>
                  <th>Phone</th>
                  <th>Capacity</th>
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
  </div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->

  <!-- Add Center Modal -->
  <div class="modal fade" id="addCenterModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Add New Training Center</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addCenterForm">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="centerName">Center Name</label>
                  <input type="text" class="form-control" id="centerName" placeholder="Enter center name" required>
                </div>
                <div class="form-group">
                  <label for="contactPerson">Contact Person</label>
                  <input type="text" class="form-control" id="contactPerson" placeholder="Enter contact person name" required>
                </div>
                <div class="form-group">
                  <label for="phone">Phone</label>
                  <input type="tel" class="form-control" id="phone" placeholder="Enter phone number" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="address">Address</label>
                  <textarea class="form-control" id="address" rows="3" placeholder="Enter full address" required></textarea>
                </div>
                <div class="form-group">
                  <label for="city">City</label>
                  <input type="text" class="form-control" id="city" placeholder="Enter city" required>
                </div>
                <div class="form-group">
                  <label for="state">State</label>
                  <input type="text" class="form-control" id="state" placeholder="Enter state" required>
                </div>
                <div class="form-group">
                  <label for="pincode">Pincode</label>
                  <input type="text" class="form-control" id="pincode" placeholder="Enter pincode" required>
                </div>
                <div class="form-group">
                  <label for="capacity">Capacity</label>
                  <input type="number" class="form-control" id="capacity" placeholder="Enter student capacity" required>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Center</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Center Modal -->
  <div class="modal fade" id="viewCenterModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">View Training Center Details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

<?php include 'includes/js.php'; ?>
<!-- Custom JS -->
<script src="assets/js/training-centers.js"></script>
</body>
</html> 
