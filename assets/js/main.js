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
}); 