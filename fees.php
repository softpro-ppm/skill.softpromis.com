<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Softpro Skill Solutions - Fee Management</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
  <!-- Tempus Dominus -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css">
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
        <a class="nav-link" href="index.php" role="button">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="dashboard.php" class="brand-link">
      <span class="brand-text font-weight-light">Softpro Skill Solutions</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-building"></i>
              <p>
                Training Partners
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="training-partners.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Manage Partners</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="training-centers.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Training Centers</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-graduation-cap"></i>
              <p>
                Training Programs
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="schemes.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Schemes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="sectors.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Sectors</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="courses.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Courses</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="batches.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Batches</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Students
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="students.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Manage Students</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="fees.php" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Fee Management</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>
                Reports
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="reports.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>View Reports</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>
                User Management
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="roles.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Roles & Permissions</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Fee Management</h1>
          </div>
          <div class="col-sm-6">
            <div class="float-sm-right">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPaymentModal">
                <i class="fas fa-plus"></i> Add New Payment
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Summary Cards -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>₹150,000</h3>
                <p>Total Fees Collected</p>
              </div>
              <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>₹50,000</h3>
                <p>Pending Payments</p>
              </div>
              <div class="icon">
                <i class="fas fa-clock"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>25</h3>
                <p>Students with Pending Fees</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>5</h3>
                <p>Overdue Payments</p>
              </div>
              <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment List -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Payment History</h3>
          </div>
          <div class="card-body">
            <table id="paymentsTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Receipt No</th>
                  <th>Student</th>
                  <th>Course</th>
                  <th>Amount</th>
                  <th>Payment Date</th>
                  <th>Payment Mode</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- DataTables will populate this -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Add Payment Modal -->
  <div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addPaymentModalLabel">Add New Payment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addPaymentForm">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="receiptNo">Receipt Number</label>
                  <input type="text" class="form-control" id="receiptNo" readonly>
                  <small class="form-text text-muted">Auto-generated</small>
                </div>
                <div class="form-group">
                  <label for="enrollmentId">Enrollment ID</label>
                  <select class="form-control select2" id="enrollmentId" required>
                    <option value="">Select Enrollment</option>
                    <option value="1">ENR001 - Rahul Sharma</option>
                    <option value="2">ENR002 - Priya Patel</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="amount">Payment Amount</label>
                  <input type="number" class="form-control" id="amount" required>
                </div>
                <div class="form-group">
                  <label for="paymentDate">Payment Date</label>
                  <div class="input-group date" id="paymentDate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#paymentDate" required>
                    <div class="input-group-append" data-target="#paymentDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="paymentMode">Payment Mode</label>
                  <select class="form-control" id="paymentMode" required>
                    <option value="">Select Payment Mode</option>
                    <option value="cash">Cash</option>
                    <option value="online">Online</option>
                    <option value="cheque">Cheque</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="remarks">Remarks</label>
                  <textarea class="form-control" id="remarks" rows="2"></textarea>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save Payment</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Payment Modal -->
  <div class="modal fade" id="viewPaymentModal" tabindex="-1" role="dialog" aria-labelledby="viewPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewPaymentModalLabel">View Payment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Receipt No:</strong> <span id="viewReceiptNo"></span></p>
              <p><strong>Enrollment ID:</strong> <span id="viewEnrollmentId"></span></p>
              <p><strong>Total Fee:</strong> <span id="viewTotalFee"></span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Amount Paid:</strong> <span id="viewAmount"></span></p>
              <p><strong>Payment Date:</strong> <span id="viewPaymentDate"></span></p>
              <p><strong>Payment Mode:</strong> <span id="viewPaymentMode"></span></p>
              <p><strong>Status:</strong> <span id="viewStatus"></span></p>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-md-12">
              <h6>Payment History</h6>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Receipt No</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>RCPT001</td>
                    <td>01/01/2024</td>
                    <td>₹5,000</td>
                    <td>Online</td>
                    <td><span class="badge badge-success">Paid</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="window.print()">Print Receipt</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Payment Modal -->
  <div class="modal fade" id="editPaymentModal" tabindex="-1" role="dialog" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editPaymentModalLabel">Edit Payment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="editPaymentForm">
            <input type="hidden" id="feeId">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editReceiptNo">Receipt Number</label>
                  <input type="text" class="form-control" id="editReceiptNo" readonly>
                  <small class="form-text text-muted">Auto-generated</small>
                </div>
                <div class="form-group">
                  <label for="editEnrollmentId">Enrollment ID</label>
                  <select class="form-control select2" id="editEnrollmentId" required>
                    <option value="">Select Enrollment</option>
                    <option value="1">ENR001 - Rahul Sharma</option>
                    <option value="2">ENR002 - Priya Patel</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editAmount">Payment Amount</label>
                  <input type="number" class="form-control" id="editAmount" required>
                </div>
                <div class="form-group">
                  <label for="editPaymentDate">Payment Date</label>
                  <div class="input-group date" id="editPaymentDate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#editPaymentDate" required>
                    <div class="input-group-append" data-target="#editPaymentDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="editPaymentMode">Payment Mode</label>
                  <select class="form-control" id="editPaymentMode" required>
                    <option value="">Select Payment Mode</option>
                    <option value="cash">Cash</option>
                    <option value="online">Online</option>
                    <option value="cheque">Cheque</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="editRemarks">Remarks</label>
                  <textarea class="form-control" id="editRemarks" rows="2"></textarea>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Payment Modal -->
  <div class="modal fade" id="deletePaymentModal" tabindex="-1" role="dialog" aria-labelledby="deletePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deletePaymentModalLabel">Delete Payment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this payment? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger">Delete</button>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer">
    <strong>Copyright &copy; 2024 <a href="#">Softpro Skill Solutions</a>.</strong>
    All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Tempus Dominus -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>

<script>
  $(function () {
    var table = $('#paymentsTable').DataTable({
      processing: true,
      serverSide: false,
      ajax: {
        url: 'inc/ajax/fees_ajax.php',
        type: 'POST',
        data: { action: 'list' },
        dataSrc: function(json) {
          return json.data || [];
        }
      },
      columns: [
        { data: 'receipt_no' },
        { data: 'student_name' },
        { data: 'course_name' },
        { data: 'amount', render: function(data) { return '₹' + data; } },
        { data: 'payment_date' },
        { data: 'payment_mode' },
        { data: 'status', render: function(data) {
            var badge = 'secondary';
            if (data === 'Paid') badge = 'success';
            if (data === 'Pending') badge = 'warning';
            if (data === 'Overdue') badge = 'danger';
            return '<span class="badge badge-' + badge + '">' + data + '</span>';
          }
        },
        { data: null, orderable: false, searchable: false, render: function(data, type, row) {
            return '<button class="btn btn-sm btn-info view-payment-btn" data-id="' + row.fee_id + '"><i class="fas fa-eye"></i></button>' +
                   '<button class="btn btn-sm btn-primary edit-payment-btn" data-id="' + row.fee_id + '"><i class="fas fa-edit"></i></button>' +
                   '<button class="btn btn-sm btn-danger delete-payment-btn" data-id="' + row.fee_id + '"><i class="fas fa-trash"></i></button>';
          }
        }
      ],
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      responsive: true
    });

    // Initialize Select2
    $('.select2').select2({
      theme: 'bootstrap4'
    });

    // Initialize date picker
    $('#paymentDate, #editPaymentDate').datetimepicker({
      format: 'L'
    });

    // Load course and fee details when enrollment ID is selected
    $('#enrollmentId, #editEnrollmentId').on('change', function() {
      const enrollmentId = $(this).val();
      if (enrollmentId) {
        // Fetch course and fee details based on enrollment ID
        $.ajax({
          url: 'inc/ajax/fees_ajax.php',
          type: 'POST',
          data: { action: 'getDetails', enrollment_id: enrollmentId },
          success: function(response) {
            const data = JSON.parse(response);
            $('#course').val(data.course_name);
            $('#totalFee').val('₹' + data.total_fee);
          }
        });
      } else {
        $('#course').val('');
        $('#totalFee').val('');
      }
    });
  });
</script>
</body>
</html>
