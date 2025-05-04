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
                                        <th>Fee Name</th>
                                        <th>Amount</th>
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
                        <label for="fee_name">Fee Name</label>
                        <input type="text" class="form-control" id="fee_name" name="fee_name" required>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
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
