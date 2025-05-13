$(document).ready(function() {
    // Initialize DataTable
    var batchesTable = $('#batchesTable').DataTable({
        ajax: {
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: { action: 'list' }
        },
        columns: [
            { data: 'batch_id' },
            { data: 'course_name', render: function(data) {
                return data ? data : '-';
            } },
            { data: 'batch_name' },
            { data: 'start_date' },
            { data: 'end_date' },
            { data: 'capacity' },
            { data: 'status', render: function(data) {
                let badgeClass = data === 'active' ? 'badge-success' : 'badge-secondary';
                return `<span class="badge ${badgeClass}">${data}</span>`;
            }},
            {
                data: null,
                orderable: false,
                render: function(data) {
                    return `
                        <button class="btn btn-sm btn-primary edit-batch-btn" data-batch-id="${data.batch_id}"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger delete-batch-btn" data-batch-id="${data.batch_id}"><i class="fas fa-trash"></i></button>
                    `;
                }
            }
        ],
        responsive: true,
        order: [[0, 'desc']]
    });

    // Load courses for modal select
    function loadCourses(courseId) {
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: { action: 'get_courses' },
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    var courseSel = $('#course_id');
                    courseSel.empty().append('<option value="">Select Course</option>');
                    $.each(res.courses, function(i, c) {
                        courseSel.append(`<option value="${c.course_id}"${courseId==c.course_id?' selected':''}>${c.course_name}</option>`);
                    });
                }
            }
        });
    }

    // Open modal for add
    $('#addBatchBtn').on('click', function() {
        $('#batchModalTitle').text('Add New Batch');
        $('#batchForm')[0].reset();
        $('#batch_id').val('');
        loadCourses();
        $('#batchModal').modal('show');
    });

    // Open modal for edit
    $(document).on('click', '.edit-batch-btn', function() {
        var batchId = $(this).data('batch-id');
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: { action: 'get', batch_id: batchId },
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    var b = res.data;
                    $('#batchModalTitle').text('Edit Batch');
                    $('#batch_id').val(b.batch_id);
                    $('#batch_name').val(b.batch_name);
                    $('#batch_code').val(b.batch_code);
                    $('#start_date').val(b.start_date);
                    $('#end_date').val(b.end_date);
                    $('#capacity').val(b.capacity);
                    $('#status').val(b.status);
                    loadCourses(b.course_id);
                    $('#batchModal').modal('show');
                } else {
                    alert(res.message || 'Could not fetch batch details.');
                }
            }
        });
    });

    // Save (add/edit) batch
    $('#batchForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var isEdit = $('#batch_id').val() !== '';
        formData += '&action=' + (isEdit ? 'edit' : 'add');
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    $('#batchModal').modal('hide');
                    batchesTable.ajax.reload();
                    toastr.success(res.message || (isEdit ? 'Batch updated successfully' : 'Batch added successfully'));
                } else {
                    toastr.error(res.message || 'Error saving batch.');
                }
            },
            error: function() {
                toastr.error('An error occurred.');
            }
        });
    });

    // Delete batch
    $(document).on('click', '.delete-batch-btn', function() {
        var batchId = $(this).data('batch-id');
        if(confirm('Are you sure you want to delete this batch?')) {
            $.ajax({
                url: 'inc/ajax/batches_ajax.php',
                type: 'POST',
                data: { action: 'delete', batch_id: batchId },
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        batchesTable.ajax.reload();
                        toastr.success(res.message || 'Batch deleted successfully');
                    } else {
                        toastr.error(res.message || 'Error deleting batch.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred.');
                }
            });
        }
    });

    // Reset modal on close
    $('#batchModal').on('hidden.bs.modal', function() {
        $('#batchForm')[0].reset();
        $('#course_id').empty().append('<option value="">Select Course</option>');
    });
});