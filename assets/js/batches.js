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
            { 
                data: 'enrolled_students',
                defaultContent: '0'
            },
            { data: 'trainer_name', defaultContent: '-' },
            { 
                data: 'status',
                render: function(data) {
                    let badgeClass = 'badge-primary';
                    if (data === 'ongoing') badgeClass = 'badge-info';
                    if (data === 'completed') badgeClass = 'badge-success';
                    if (data === 'cancelled') badgeClass = 'badge-danger';
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            {
                data: null,
                render: function(data) {
                    return `
                        <button class="btn btn-sm btn-info view-batch" data-id="${data.batch_id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-primary edit-batch" data-id="${data.batch_id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-batch" data-id="${data.batch_id}">
                            <i class="fas fa-trash"></i>
                        </button>
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

    // Initialize DateTimePicker
    $('#startDate, #endDate').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    // Reset form when modal is closed
    $('#addBatchModal').on('hidden.bs.modal', function() {
        $('#addBatchForm')[0].reset();
        $('.select2').val('').trigger('change');
    });

    // Form submission for new batch
    $('#addBatchForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: {
                action: 'create',
                batch_code: $('#batchCode').val(),
                course_id: $('#course').val(),
                center_id: $('#trainingCenter').val(),
                trainer_id: $('#trainer').val(),
                start_date: $('#startDate input').val(),
                end_date: $('#endDate input').val(),
                capacity: $('#capacity').val(),
                schedule: $('#schedule').val(),
                remarks: $('#remarks').val()
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                    $('#addBatchModal').modal('hide');
                    batchesTable.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    });

    // View batch details
    $('#batchesTable').on('click', '.view-batch', function() {
        var batchId = $(this).data('id');
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: {
                action: 'get',
                batch_id: batchId
            },
            success: function(response) {
                if (response.success) {
                    var batch = response.data;
                    $('#viewBatchCode').text(batch.batch_code);
                    $('#viewCourse').text(batch.course_name);
                    $('#viewCenter').text(batch.center_name);
                    $('#viewTrainer').text(batch.trainer_name || '-');
                    $('#viewStartDate').text(batch.start_date);
                    $('#viewEndDate').text(batch.end_date);
                    $('#viewSchedule').text(batch.schedule);
                    $('#viewStatus').html(`<span class="badge badge-${getBadgeClass(batch.status)}">${batch.status}</span>`);
                    
                    // Load enrolled students
                    loadEnrolledStudents(batchId);
                    
                    $('#viewBatchModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            }
        });
    });

    // Load enrolled students
    function loadEnrolledStudents(batchId) {
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: {
                action: 'get_students',
                batch_id: batchId
            },
            success: function(response) {
                if (response.success) {
                    var html = '';
                    response.data.forEach(function(student) {
                        html += `<tr>
                            <td>${student.enrollment_no}</td>
                            <td>${student.first_name} ${student.last_name}</td>
                            <td>${student.email}</td>
                            <td>${student.mobile}</td>
                            <td><span class="badge badge-${student.fee_status === 'paid' ? 'success' : 'warning'}">${student.fee_status}</span></td>
                            <td><span class="badge badge-${student.status === 'active' ? 'success' : 'secondary'}">${student.status}</span></td>
                        </tr>`;
                    });
                    $('#enrolledStudentsTable tbody').html(html || '<tr><td colspan="6" class="text-center">No students enrolled</td></tr>');
                }
            }
        });
    }

    // Edit batch
    $('#batchesTable').on('click', '.edit-batch', function() {
        var batchId = $(this).data('id');
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: {
                action: 'get',
                batch_id: batchId
            },
            success: function(response) {
                if (response.success) {
                    var batch = response.data;
                    $('#editBatchId').val(batch.batch_id);
                    $('#editBatchCode').val(batch.batch_code);
                    $('#editCourse').val(batch.course_id).trigger('change');
                    $('#editTrainingCenter').val(batch.center_id).trigger('change');
                    $('#editTrainer').val(batch.trainer_id).trigger('change');
                    $('#editStartDate input').val(batch.start_date);
                    $('#editEndDate input').val(batch.end_date);
                    $('#editCapacity').val(batch.capacity);
                    $('#editSchedule').val(batch.schedule);
                    $('#editRemarks').val(batch.remarks);
                    $('#editStatus').val(batch.status);
                    $('#editBatchModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            }
        });
    });

    // Delete batch
    $('#batchesTable').on('click', '.delete-batch', function() {
        var batchId = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'inc/ajax/batches_ajax.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        batch_id: batchId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
                            batchesTable.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    }
                });
            }
        });
    });

    // Edit form submission
    $('#editBatchForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: {
                action: 'update',
                batch_id: $('#editBatchId').val(),
                batch_code: $('#editBatchCode').val(),
                course_id: $('#editCourse').val(),
                center_id: $('#editTrainingCenter').val(),
                trainer_id: $('#editTrainer').val(),
                start_date: $('#editStartDate input').val(),
                end_date: $('#editEndDate input').val(),
                capacity: $('#editCapacity').val(),
                schedule: $('#editSchedule').val(),
                remarks: $('#editRemarks').val(),
                status: $('#editStatus').val()
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                    $('#editBatchModal').modal('hide');
                    batchesTable.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    });

    // Reset edit form when modal is closed
    $('#editBatchModal').on('hidden.bs.modal', function() {
        $('#editBatchForm')[0].reset();
        $('.select2').val('').trigger('change');
    });

    // Helper function for status badge class
    function getBadgeClass(status) {
        switch(status) {
            case 'ongoing': return 'info';
            case 'completed': return 'success';
            case 'cancelled': return 'danger';
            default: return 'primary';
        }
    }
});