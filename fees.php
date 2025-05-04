<?php
// Define BASEPATH constant for footer include
if (!defined('BASEPATH')) define('BASEPATH', true);

session_start();
require_once 'config.php';
require_once 'crud_functions.php';

$pageTitle = 'Fees';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Fees</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Fees</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Fees List</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" id="addFeeBtn">
                                    <i class="fas fa-plus"></i> Add New Fee
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="feesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                        <th>Payment Mode</th>
                                        <th>Transaction ID</th>
                                        <th>Status</th>
                                        <th>Receipt No</th>
                                        <th>Notes</th>
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
            </div>
        </div>
    </section>
</div>
<!-- Add/Edit Fee Modal -->
<div class="modal fade" id="feeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="feeModalTitle">Add New Fee</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="feeForm">
                <div class="modal-body">
                    <input type="hidden" id="fee_id" name="fee_id">
                    <div class="form-group">
                        <label for="student_id">Student</label>
                        <select class="form-control" id="student_id" name="student_id" required>
                            <option value="">Select Student</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="enrollment_id">Enrollment ID</label>
                        <select class="form-control" id="enrollment_id" name="enrollment_id" required>
                            <option value="">Select Enrollment</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="payment_date">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date">
                    </div>
                    <div class="form-group">
                        <label for="payment_mode">Payment Mode</label>
                        <input type="text" class="form-control" id="payment_mode" name="payment_mode">
                    </div>
                    <div class="form-group">
                        <label for="transaction_id">Transaction ID</label>
                        <input type="text" class="form-control" id="transaction_id" name="transaction_id">
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="receipt_no">Receipt No</label>
                        <input type="text" class="form-control" id="receipt_no" name="receipt_no">
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveFeeBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/js.php'; ?>
<script src="assets/js/fees.js"></script>
</body>
</html>
