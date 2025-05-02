<?php
// Define BASEPATH constant for footer include
if (!defined('BASEPATH')) define('BASEPATH', true);

session_start();
require_once 'config.php';
require_once 'crud_functions.php';

$pageTitle = 'Fee Management';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>
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
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>

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
                <label for="addReceiptNo">Receipt Number</label>
                <input type="text" class="form-control" id="addReceiptNo" name="receipt_no" readonly>
                <small class="form-text text-muted">Auto-generated</small>
              </div>
              <div class="form-group">
                <label for="addEnrollmentId">Enrollment ID</label>
                <select class="form-control select2" id="addEnrollmentId" name="enrollment_id" required>
                  <option value="">Select Enrollment</option>
                  <!-- Dynamically populated -->
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="addAmount">Payment Amount</label>
                <input type="number" class="form-control" id="addAmount" name="amount" required>
              </div>
              <div class="form-group">
                <label for="addPaymentDate">Payment Date</label>
                <input type="date" class="form-control" id="addPaymentDate" name="payment_date" required>
              </div>
              <div class="form-group">
                <label for="addPaymentMode">Payment Mode</label>
                <select class="form-control" id="addPaymentMode" name="payment_mode" required>
                  <option value="">Select Payment Mode</option>
                  <option value="cash">Cash</option>
                  <option value="online">Online</option>
                  <option value="cheque">Cheque</option>
                </select>
              </div>
              <div class="form-group">
                <label for="addTransactionId">Transaction ID</label>
                <input type="text" class="form-control" id="addTransactionId" name="transaction_id">
              </div>
              <div class="form-group">
                <label for="addNotes">Remarks</label>
                <textarea class="form-control" id="addNotes" name="notes" rows="2"></textarea>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" form="addPaymentForm">Save Payment</button>
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
            <p><strong>Student:</strong> <span id="viewStudent"></span></p>
            <p><strong>Course:</strong> <span id="viewCourse"></span></p>
          </div>
          <div class="col-md-6">
            <p><strong>Amount Paid:</strong> <span id="viewAmount"></span></p>
            <p><strong>Payment Date:</strong> <span id="viewPaymentDate"></span></p>
            <p><strong>Payment Mode:</strong> <span id="viewPaymentMode"></span></p>
            <p><strong>Status:</strong> <span id="viewStatus"></span></p>
            <p><strong>Remarks:</strong> <span id="viewNotes"></span></p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
          <input type="hidden" id="editFeeId" name="fee_id">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="editReceiptNo">Receipt Number</label>
                <input type="text" class="form-control" id="editReceiptNo" name="receipt_no" readonly>
                <small class="form-text text-muted">Auto-generated</small>
              </div>
              <div class="form-group">
                <label for="editEnrollmentId">Enrollment ID</label>
                <select class="form-control select2" id="editEnrollmentId" name="enrollment_id" required>
                  <option value="">Select Enrollment</option>
                  <!-- Dynamically populated -->
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="editAmount">Payment Amount</label>
                <input type="number" class="form-control" id="editAmount" name="amount" required>
              </div>
              <div class="form-group">
                <label for="editPaymentDate">Payment Date</label>
                <input type="date" class="form-control" id="editPaymentDate" name="payment_date" required>
              </div>
              <div class="form-group">
                <label for="editPaymentMode">Payment Mode</label>
                <select class="form-control" id="editPaymentMode" name="payment_mode" required>
                  <option value="">Select Payment Mode</option>
                  <option value="cash">Cash</option>
                  <option value="online">Online</option>
                  <option value="cheque">Cheque</option>
                </select>
              </div>
              <div class="form-group">
                <label for="editTransactionId">Transaction ID</label>
                <input type="text" class="form-control" id="editTransactionId" name="transaction_id">
              </div>
              <div class="form-group">
                <label for="editNotes">Remarks</label>
                <textarea class="form-control" id="editNotes" name="notes" rows="2"></textarea>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" form="editPaymentForm">Save Changes</button>
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
        <button type="button" class="btn btn-danger" id="confirmDeletePayment">Delete</button>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
