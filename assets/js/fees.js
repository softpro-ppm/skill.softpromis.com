$(document).ready(function() {
    // Initialize DataTable
    var feesTable = $('#feesTable').DataTable({
        ajax: {
            url: 'inc/ajax/fees_ajax.php',
            type: 'POST',
            data: { action: 'list' }
        },
        columns: [
            { data: 'fee_id' },
            { data: 'fee_name' },
            { data: 'amount' },
            { data: 'status', render: function(data) {
                let badgeClass = data === 'active' ? 'badge-success' : 'badge-secondary';
                return `<span class="badge ${badgeClass}">${data}</span>`;
            }},
            {
                data: null,
                orderable: false,
                render: function(data) {
                    return `
                        <button class="btn btn-sm btn-primary edit-fee-btn" data-fee-id="${data.fee_id}"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger delete-fee-btn" data-fee-id="${data.fee_id}"><i class="fas fa-trash"></i></button>
                    `;
                }
            }
        ],
        responsive: true,
        order: [[0, 'desc']]
    });

    // Open modal for add
    $('#addFeeBtn').on('click', function() {
        $('#feeModalTitle').text('Add New Fee');
        $('#feeForm')[0].reset();
        $('#fee_id').val('');
        $('#feeModal').modal('show');
    });

    // Open modal for edit
    $(document).on('click', '.edit-fee-btn', function() {
        var feeId = $(this).data('fee-id');
        $.ajax({
            url: 'inc/ajax/fees_ajax.php',
            type: 'POST',
            data: { action: 'get', fee_id: feeId },
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    var f = res.data;
                    $('#feeModalTitle').text('Edit Fee');
                    $('#fee_id').val(f.fee_id);
                    $('#fee_name').val(f.fee_name);
                    $('#amount').val(f.amount);
                    $('#status').val(f.status);
                    $('#feeModal').modal('show');
                } else {
                    toastr.error(res.message || 'Could not fetch fee details.');
                }
            },
            error: function() {
                toastr.error('Could not fetch fee details.');
            }
        });
    });

    // Save (add/edit) fee
    $('#feeForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var isEdit = $('#fee_id').val() !== '';
        formData += '&action=' + (isEdit ? 'edit' : 'add');
        $.ajax({
            url: 'inc/ajax/fees_ajax.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    $('#feeModal').modal('hide');
                    feesTable.ajax.reload();
                    toastr.success(res.message || (isEdit ? 'Fee updated successfully' : 'Fee added successfully'));
                } else {
                    toastr.error(res.message || 'Error saving fee.');
                }
            },
            error: function() {
                toastr.error('An error occurred.');
            }
        });
    });

    // Delete fee
    $(document).on('click', '.delete-fee-btn', function() {
        var feeId = $(this).data('fee-id');
        if(confirm('Are you sure you want to delete this fee?')) {
            $.ajax({
                url: 'inc/ajax/fees_ajax.php',
                type: 'POST',
                data: { action: 'delete', fee_id: feeId },
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        feesTable.ajax.reload();
                        toastr.success(res.message || 'Fee deleted successfully');
                    } else {
                        toastr.error(res.message || 'Error deleting fee.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred.');
                }
            });
        }
    });

    // Reset modal on close
    $('#feeModal').on('hidden.bs.modal', function() {
        $('#feeForm')[0].reset();
    });
});
