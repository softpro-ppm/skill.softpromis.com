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
            { data: 'amount' },
            { data: 'student_display', defaultContent: '', title: 'Student' },
            { data: 'payment_mode' },
            { data: 'transaction_id' },
            { data: 'status', render: function(data) {
                let badgeClass = data === 'paid' ? 'badge-success' : (data === 'pending' ? 'badge-warning' : 'badge-secondary');
                return `<span class="badge ${badgeClass}">${data}</span>`;
            }},
            { data: 'receipt_no' },
            { data: 'notes' },
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

    // Helper: Load all students for dropdown
    function loadStudents(selectedStudentId) {
        $.ajax({
            url: 'inc/ajax/students_ajax.php',
            type: 'POST',
            data: { action: 'list' },
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    var studentSel = $('#student_id');
                    studentSel.empty().append('<option value="">Select Student</option>');
                    $.each(res.data, function(i, s) {
                        var label = s.full_name + ' (' + s.enrollment_no + ')';
                        studentSel.append(`<option value="${s.student_id}" data-enrollment-no="${s.enrollment_no}"${selectedStudentId==s.student_id?' selected':''}>${label}</option>`);
                    });
                }
            }
        });
    }

    // Helper: Load enrollments for a student from student_batch_enrollment
    function loadEnrollments(studentId, selectedEnrollmentId) {
        $.ajax({
            url: 'inc/ajax/students_ajax.php',
            type: 'POST',
            data: { action: 'get_enrollments_by_student', student_id: studentId },
            dataType: 'json',
            success: function(res) {
                var enrollSel = $('#enrollment_id');
                var enrollGroup = $('#enrollment_id_group');
                enrollSel.empty();
                if(res.success && res.data.length) {
                    if(res.data.length === 1) {
                        $('#enrollment_id_hidden').val(res.data[0].enrollment_id);
                        enrollGroup.hide();
                    } else {
                        enrollSel.append('<option value="">Select Enrollment</option>');
                        $.each(res.data, function(i, e) {
                            var label = e.enrollment_id;
                            if(e.batch_id) label += ' (Batch: ' + e.batch_id + ')';
                            enrollSel.append(`<option value="${e.enrollment_id}"${selectedEnrollmentId==e.enrollment_id?' selected':''}>${label}</option>`);
                        });
                        enrollGroup.show();
                        // Set hidden field to selected or first enrollment
                        if(selectedEnrollmentId) {
                            $('#enrollment_id_hidden').val(selectedEnrollmentId);
                            enrollSel.val(selectedEnrollmentId);
                        } else {
                            $('#enrollment_id_hidden').val(res.data[0].enrollment_id);
                            enrollSel.val(res.data[0].enrollment_id);
                        }
                    }
                } else {
                    enrollGroup.hide();
                    $('#enrollment_id_hidden').val('');
                }
            }
        });
    }

    // On student change, load enrollments
    $(document).on('change', '#student_id', function() {
        var studentId = $(this).val();
        loadEnrollments(studentId);
    });

    $(document).on('change', '#enrollment_id', function() {
        $('#enrollment_id_hidden').val($(this).val());
    });

    // Open modal for add
    $('#addFeeBtn').on('click', function() {
        $('#feeModalTitle').text('Add New Fee');
        $('#feeForm')[0].reset();
        $('#fee_id').val('');
        // Load students and clear enrollments
        $.ajax({
            url: 'inc/ajax/students_ajax.php',
            type: 'POST',
            data: { action: 'list' },
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    var studentSel = $('#student_id');
                    studentSel.empty().append('<option value="">Select Student</option>');
                    $.each(res.data, function(i, s) {
                        var label = s.full_name + ' (' + s.enrollment_no + ')';
                        studentSel.append(`<option value="${s.student_id}">${label}</option>`);
                    });
                    // Force the select to update (for some browsers/UI)
                    studentSel.val('').trigger('change');
                }
                // Always clear enrollments and hidden field on add
                $('#enrollment_id').empty().append('<option value="">Select Enrollment</option>');
                $('#enrollment_id_hidden').val('');
                $('#enrollment_id_group').hide();
                // Clear all other fields
                $('#amount').val('');
                $('#payment_date').val('');
                $('#payment_mode').val('');
                $('#transaction_id').val('');
                $('#status').val('pending');
                $('#receipt_no').val('');
                $('#notes').val('');
            }
        });
        var feeModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('feeModal'));
        feeModal.show();
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
                    $('#amount').val(f.amount);
                    $('#payment_date').val(f.payment_date);
                    $('#payment_mode').val(f.payment_mode);
                    $('#transaction_id').val(f.transaction_id);
                    $('#status').val(f.status);
                    $('#receipt_no').val(f.receipt_no);
                    $('#notes').val(f.notes);
                    // Load students and set selected, then load enrollments and set selected
                    $.ajax({
                        url: 'inc/ajax/students_ajax.php',
                        type: 'POST',
                        data: { action: 'list' },
                        dataType: 'json',
                        success: function(stuRes) {
                            if(stuRes.success) {
                                var studentSel = $('#student_id');
                                studentSel.empty().append('<option value="">Select Student</option>');
                                var foundStudent = null;
                                $.each(stuRes.data, function(i, s) {
                                    var label = s.full_name + ' (' + s.enrollment_no + ')';
                                    studentSel.append(`<option value="${s.student_id}"${s.student_id==f.student_id?' selected':''}>${label}</option>
                                    if(s.student_id==f.student_id) foundStudent = s.student_id;
                                });
                                if(foundStudent) {
                                    loadEnrollments(foundStudent, f.enrollment_id);
                                } else {
                                    $('#enrollment_id').empty().append('<option value="">Select Enrollment</option>');
                                }
                                // Force the select to update (for some browsers/UI)
                                $('#student_id').val(f.student_id).trigger('change');
                            }
                        }
                    });
                    var feeModal = new bootstrap.Modal(document.getElementById('feeModal'));
                    feeModal.show();
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
