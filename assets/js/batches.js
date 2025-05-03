$(document).ready(function() {
    // Initialize DataTable
    var batchesTable = $('#batchesTable').DataTable({
        ajax: {
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: function(d) {
                d.action = 'list';
            }
        },
        columns: [
            { data: 'batch_code' },
            { data: 'course_name' },
            { data: 'center_name' },
            { data: 'start_date' },
            { data: 'end_date' },
            { data: 'capacity' },
            { data: 'status', render: function(data) {
                let badgeClass = 'badge-primary';
                if (data === 'ongoing') badgeClass = 'badge-info';
                if (data === 'completed') badgeClass = 'badge-success';
                if (data === 'cancelled') badgeClass = 'badge-danger';
                return `<span class="badge ${badgeClass}">${data}</span>`;
            }},
            {
                data: null,
                render: function(data) {
                    return `
                        <div class="btn-group btn-group-sm">
                        <button class="btn btn-sm btn-primary edit-batch-btn" data-batch-id="${data.batch_id}"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger delete-batch-btn" data-batch-id="${data.batch_id}"><i class="fas fa-trash"></i></button>
                        </div>
                    `;
                }
            }
        ],
        responsive: true,
        order: [[0, 'desc']]
    });

    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Reset form when modal is closed
    $('#addBatchModal').on('hidden.bs.modal', function() {
        $('#addBatchForm')[0].reset();
        $('.select2').val('').trigger('change');
    });
    $('#editBatchModal').on('hidden.bs.modal', function() {
        $('#editBatchForm')[0].reset();
        $('.select2').val('').trigger('change');
    });

    // Form submission for new batch
    $('#addBatchForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize() + '&action=add';
        console.log('Form Data:', formData); // Debugging log
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Batch added successfully');
                    $('#addBatchModal').modal('hide');
                    batchesTable.ajax.reload();
                } else {
                    toastr.error(response.message || 'Error adding batch');
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });

    // Edit batch (populate modal)
    $(document).on('click', '.edit-batch-btn', function() {
        var batchId = $(this).data('batch-id');
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'GET',
            data: { action: 'get', batch_id: batchId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var b = response.data;
                    $('#editBatchId').val(b.batch_id);
                    $('#editBatchCode').val(b.batch_code);
                    $('#editCourse').val(b.course_id).trigger('change');
                    $('#editCenter').val(b.center_id).trigger('change');
                    $('#editStartDate').val(b.start_date);
                    $('#editEndDate').val(b.end_date);
                    $('#editCapacity').val(b.capacity);
                    $('#editStatus').val(b.status);
                    $('#editBatchModal').modal('show');
                } else {
                    toastr.error(response.message || 'Could not fetch batch details.');
                }
            },
            error: function() {
                toastr.error('Could not fetch batch details.');
            }
        });
    });

    // Edit form submission
    $('#editBatchForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: $(this).serialize() + '&action=edit',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Batch updated successfully');
                    $('#editBatchModal').modal('hide');
                    batchesTable.ajax.reload();
                } else {
                    toastr.error(response.message || 'Error updating batch');
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });

    // Delete batch
    $(document).on('click', '.delete-batch-btn', function() {
        var batchId = $(this).data('batch-id');
        if (confirm('Are you sure you want to delete this batch?')) {
            $.ajax({
                url: 'inc/ajax/batches_ajax.php',
                type: 'POST',
                data: { action: 'delete', batch_id: batchId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message || 'Batch deleted successfully');
                        batchesTable.ajax.reload();
                    } else {
                        toastr.error(response.message || 'Error deleting batch');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        }
    });
});