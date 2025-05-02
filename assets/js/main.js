// Global AJAX Setup
$.ajaxSetup({
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    }
});

// Configure Toastr
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: 3000
};

// DataTable Default Configuration
const dataTableDefaults = {
    responsive: true,
    processing: true,
    language: {
        processing: '<i class="fas fa-spinner fa-spin fa-3x fa-fw"></i>',
        emptyTable: "No data available",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: "Showing 0 to 0 of 0 entries",
        infoFiltered: "(filtered from _MAX_ total entries)",
        lengthMenu: "Show _MENU_ entries",
        loadingRecords: "Loading...",
        search: "Search:",
        zeroRecords: "No matching records found"
    },
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
};

// Form Validation Helper
const validateForm = (formElement) => {
    const requiredFields = $(formElement).find('[required]');
    let isValid = true;
    let firstInvalidField = null;

    requiredFields.each(function() {
        if (!$(this).val()) {
            isValid = false;
            $(this).addClass('is-invalid');
            if (!firstInvalidField) {
                firstInvalidField = $(this);
            }
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    if (firstInvalidField) {
        firstInvalidField.focus();
        toastr.error('Please fill in all required fields');
    }

    return isValid;
};

// Password Strength Checker
const checkPasswordStrength = (password) => {
    let strength = 0;
    const checks = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };

    Object.values(checks).forEach(check => {
        if (check) strength++;
    });

    return {
        score: strength,
        checks: checks,
        feedback: getPasswordFeedback(strength)
    };
};

const getPasswordFeedback = (strength) => {
    switch (strength) {
        case 0:
        case 1:
            return {
                message: 'Very weak password',
                class: 'text-danger'
            };
        case 2:
            return {
                message: 'Weak password',
                class: 'text-warning'
            };
        case 3:
            return {
                message: 'Medium strength password',
                class: 'text-info'
            };
        case 4:
            return {
                message: 'Strong password',
                class: 'text-success'
            };
        case 5:
            return {
                message: 'Very strong password',
                class: 'text-success'
            };
    }
};

// AJAX Form Submit Helper
const submitFormAjax = (formElement, options = {}) => {
    const defaults = {
        beforeSubmit: () => true,
        success: (response) => {
            if (response.success) {
                toastr.success(response.message || 'Operation completed successfully');
            } else {
                toastr.error(response.message || 'An error occurred');
            }
        },
        error: (xhr) => {
            toastr.error('An error occurred while processing your request');
        },
        complete: () => {}
    };

    const settings = { ...defaults, ...options };

    $(formElement).on('submit', function(e) {
        e.preventDefault();

        if (!validateForm(this) || !settings.beforeSubmit()) {
            return false;
        }

        const formData = new FormData(this);
        const submitBtn = $(this).find('[type="submit"]');
        const originalText = submitBtn.html();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: () => {
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
            },
            success: settings.success,
            error: settings.error,
            complete: () => {
                submitBtn.html(originalText).prop('disabled', false);
                settings.complete();
            }
        });
    });
};

// Confirmation Dialog Helper
const confirmAction = (options = {}) => {
    const defaults = {
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    };

    const settings = { ...defaults, ...options };

    return Swal.fire({
        title: settings.title,
        text: settings.text,
        icon: settings.icon,
        showCancelButton: true,
        confirmButtonColor: settings.confirmButtonColor,
        cancelButtonColor: settings.cancelButtonColor,
        confirmButtonText: settings.confirmButtonText,
        cancelButtonText: settings.cancelButtonText
    });
};

// Initialize Common Components
$(document).ready(function() {
    // Initialize Select2
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    }

    // Initialize Tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Initialize Custom File Input
    if (typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    }

    // Handle Password Toggle
    $('.toggle-password').click(function() {
        const passwordInput = $(this).closest('.input-group').find('input');
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
    });

    // Find the course update AJAX call and add error logging
    $(document).on('submit', '#editCourseForm', function(e) {
        e.preventDefault();

        if (!validateForm(this)) {
            return false;
        }

        const formData = new FormData(this);
        const submitBtn = $(this).find('[type="submit"]');
        const originalText = submitBtn.html();

        $.ajax({
            url: 'inc/ajax/courses_ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: () => {
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Operation completed successfully');
                } else {
                    toastr.error(response.message || 'An error occurred');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX error:', xhr.responseText, status, error);
                toastr.error('An error occurred while updating the course.');
            },
            complete: () => {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // DataTable for payments
    var paymentsTable = $('#paymentsTable').DataTable({
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
                if (data === 'paid') badge = 'success';
                if (data === 'pending') badge = 'warning';
                if (data === 'failed') badge = 'danger';
                return '<span class="badge badge-' + badge + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
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

    // Populate Enrollment dropdowns
    function loadEnrollments(selectId) {
        $.ajax({
            url: 'inc/ajax/fees_ajax.php',
            type: 'POST',
            data: { action: 'getAllEnrollments' },
            dataType: 'json',
            success: function(response) {
                var $select = $(selectId);
                $select.empty().append('<option value="">Select Enrollment</option>');
                if (response.success && response.data) {
                    $.each(response.data, function(_, e) {
                        $select.append('<option value="' + e.enrollment_id + '">' + e.enrollment_id + ' - ' + e.student_name + '</option>');
                    });
                }
            }
        });
    }
    loadEnrollments('#addEnrollmentId');
    loadEnrollments('#editEnrollmentId');

    // Add Payment
    $('#addPaymentForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize() + '&action=create';
        $.ajax({
            url: 'inc/ajax/fees_ajax.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Payment added successfully');
                    $('#addPaymentModal').modal('hide');
                    $('#addPaymentForm')[0].reset();
                    paymentsTable.ajax.reload();
                } else {
                    toastr.error(response.message || 'Error adding payment');
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });

    // View Payment
    $(document).on('click', '.view-payment-btn', function() {
        var feeId = $(this).data('id');
        $.ajax({
            url: 'inc/ajax/fees_ajax.php',
            type: 'POST',
            data: { action: 'get', fee_id: feeId },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    var d = response.data;
                    $('#viewReceiptNo').text(d.receipt_no || '');
                    $('#viewEnrollmentId').text(d.enrollment_id || '');
                    $('#viewStudent').text(d.student_name || '');
                    $('#viewCourse').text(d.course_name || '');
                    $('#viewAmount').text('₹' + d.amount);
                    $('#viewPaymentDate').text(d.payment_date || '');
                    $('#viewPaymentMode').text(d.payment_mode || '');
                    $('#viewStatus').text(d.status || '');
                    $('#viewNotes').text(d.notes || '');
                    $('#viewPaymentModal').modal('show');
                } else {
                    toastr.error('Could not fetch payment details.');
                }
            },
            error: function() {
                toastr.error('Could not fetch payment details.');
            }
        });
    });

    // Edit Payment (open modal and fill data)
    $(document).on('click', '.edit-payment-btn', function() {
        var feeId = $(this).data('id');
        $.ajax({
            url: 'inc/ajax/fees_ajax.php',
            type: 'POST',
            data: { action: 'get', fee_id: feeId },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    var d = response.data;
                    $('#editFeeId').val(d.fee_id);
                    $('#editReceiptNo').val(d.receipt_no);
                    $('#editEnrollmentId').val(d.enrollment_id).trigger('change');
                    $('#editAmount').val(d.amount);
                    $('#editPaymentDate').val(d.payment_date);
                    $('#editPaymentMode').val(d.payment_mode);
                    $('#editTransactionId').val(d.transaction_id);
                    $('#editNotes').val(d.notes);
                    $('#editPaymentModal').modal('show');
                } else {
                    toastr.error('Could not fetch payment details.');
                }
            },
            error: function() {
                toastr.error('Could not fetch payment details.');
            }
        });
    });

    // Edit Payment (submit)
    $('#editPaymentForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize() + '&action=update';
        $.ajax({
            url: 'inc/ajax/fees_ajax.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Payment updated successfully');
                    $('#editPaymentModal').modal('hide');
                    paymentsTable.ajax.reload();
                } else {
                    toastr.error(response.message || 'Error updating payment');
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });

    // Delete Payment (open modal)
    var deleteFeeId = null;
    $(document).on('click', '.delete-payment-btn', function() {
        deleteFeeId = $(this).data('id');
        $('#deletePaymentModal').modal('show');
    });

    // Confirm Delete
    $('#confirmDeletePayment').on('click', function() {
        if (!deleteFeeId) return;
        $.ajax({
            url: 'inc/ajax/fees_ajax.php',
            type: 'POST',
            data: { action: 'delete', fee_id: deleteFeeId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Payment deleted successfully');
                    $('#deletePaymentModal').modal('hide');
                    paymentsTable.ajax.reload();
                } else {
                    toastr.error(response.message || 'Error deleting payment');
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });

    // Reset forms on modal close
    $('#addPaymentModal, #editPaymentModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('.is-invalid').removeClass('is-invalid');
    });
}); 