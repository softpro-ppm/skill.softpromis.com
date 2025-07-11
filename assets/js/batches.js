$(document).ready(function() {
    // Initialize DataTable
    var batchesTable = $('#batchesTable').DataTable({
        ajax: {
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: { action: 'list' }
        },
        columns: [
            { data: null, render: function(data, type, row, meta) { return meta.row + 1; }, orderable: false, searchable: false }, // Ensure Sr No. always shows 1,2,3,4...
            { data: 'batch_code', render: function(data) {
                return data ? data : '-';
            } },
            { data: 'course_name', render: function(data) {
                return data ? data : '-';
            } },
            { data: 'batch_name' },
            { data: 'start_date' },
            { data: 'end_date' },
            { data: null, render: function(data, type, row) {
                // Show total students added (student_count) and total capacity
                return (row.student_count || 0) + ' / ' + (row.capacity || 0);
            } },
            { data: 'status', render: function(data) {
                let badgeClass = data === 'active' ? 'badge-success' : 'badge-secondary';
                return `<span class="badge ${badgeClass}">${data}</span>`;
            }},
            {
                data: null,
                orderable: false,
                render: function(data) {
                    return `
                        <button class="btn btn-sm btn-info view-batch-students-btn" data-batch-id="${data.batch_id}"><i class="fas fa-users"></i></button>
                        <button class="btn btn-sm btn-success register-student-btn" data-batch-id="${data.batch_id}"><i class="fas fa-user-plus"></i></button>
                        <button class="btn btn-sm btn-primary edit-batch-btn" data-batch-id="${data.batch_id}"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger delete-batch-btn" data-batch-id="${data.batch_id}"><i class="fas fa-trash"></i></button>
                    `;
                }
            }
        ],
        responsive: true,
        order: [[0, 'desc']] // Order by Sr No. descending (latest first)
    });

    // Reload table data when modal is closed or after add/edit/delete
    function reloadBatchesTable() {
        if (window.batchesTable && typeof window.batchesTable.ajax === 'object') {
            window.batchesTable.ajax.reload(null, false); // false = keep current page
        } else if ($('#batchesTable').DataTable) {
            $('#batchesTable').DataTable().ajax.reload(null, false);
        }
    }

    // Load courses for modal select
    function loadCourses(courseId) {
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: { action: 'get_centers_courses' }, // changed from 'get_courses'
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

    // Add a CodePen-inspired glassmorphic animated loader overlay to the modal
    var batchLoadingOverlay = `
    <div id="batchLoadingOverlay" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(240,245,255,0.85);backdrop-filter:blur(6px);z-index:1051;display:flex;align-items:center;justify-content:center;animation:fadeIn 0.4s;">
      <div style="background:rgba(255,255,255,0.7);border-radius:2rem;box-shadow:0 8px 32px rgba(60,60,120,0.18);padding:3rem 4rem;display:flex;flex-direction:column;align-items:center;backdrop-filter:blur(8px);">
        <svg width="80" height="80" viewBox="0 0 80 80" style="margin-bottom:2rem;">
          <defs>
            <linearGradient id="loaderGradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#007bff"/>
              <stop offset="100%" stop-color="#00c6ff"/>
            </linearGradient>
          </defs>
          <circle cx="40" cy="40" r="32" stroke="url(#loaderGradient)" stroke-width="8" fill="none" stroke-linecap="round" stroke-dasharray="180 100" stroke-dashoffset="0">
            <animateTransform attributeName="transform" type="rotate" from="0 40 40" to="360 40 40" dur="1s" repeatCount="indefinite"/>
          </circle>
        </svg>
        <div class="fw-bold text-primary" style="font-size:2rem;text-shadow:0 2px 8px #e0e7ef;letter-spacing:0.5px;">Loading Batch Data...</div>
      </div>
    </div>
    <style>
      @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>`;

    // Open modal for add
    $('#addBatchBtn').on('click', function() {
        $('#batchModalTitle').text('Add New Batch');
        $('#batchForm')[0].reset();
        $('#batch_id').val('');
        // Load partners and clear centers
        if (typeof loadPartners === 'function') {
            loadPartners();
        }
        $('#center_id').empty().append('<option value="">Select Training Center</option>');
        $('#scheme_id').empty().append('<option value="">Select Scheme</option>');
        $('#sector_id').empty().append('<option value="">Select Sector</option>');
        $('#course_id').empty().append('<option value="">Select Course</option>');
        $('#batchModal').modal('show');
    });

    // Open modal for edit
    $(document).on('click', '.edit-batch-btn', function() {
        var batchId = $(this).data('batch-id');
        // Open modal immediately and show overlay
        $('#batchModal').modal('show');
        if (!$('#batchLoadingOverlay').length) {
            $('#batchModal .modal-content').append(batchLoadingOverlay);
        } else {
            $('#batchLoadingOverlay').show();
        }
        $.ajax({
            url: 'inc/ajax/batches_ajax.php',
            type: 'POST',
            data: { action: 'get', batch_id: batchId },
            dataType: 'json',
            success: async function(res) {
                if(res.success) {
                    var b = res.data;
                    $('#batchModalTitle').text('Edit Batch');
                    $('#batch_id').val(b.batch_id);
                    $('#batch_name').val(b.batch_name);
                    $('#start_date').val(b.start_date);
                    $('#end_date').val(b.end_date);
                    $('#capacity').val(b.capacity);
                    await loadSelectOptions($('#partner_id'), 'inc/ajax/training_partners_ajax.php', { action: 'list' }, 'partner_id', 'partner_name', b.partner_id);
                    await loadSelectOptions($('#center_id'), 'inc/ajax/training-centers.php', { action: 'list', partner_id: b.partner_id }, 'center_id', 'center_name', b.center_id);
                    await loadSelectOptions($('#scheme_id'), 'inc/ajax/schemes_ajax.php', { action: 'list', center_id: b.center_id }, 'scheme_id', 'scheme_name', b.scheme_id);
                    await loadSelectOptions($('#sector_id'), 'inc/ajax/sectors_ajax.php', { action: 'list', scheme_id: b.scheme_id }, 'sector_id', 'sector_name', b.sector_id);
                    await loadSelectOptions($('#course_id'), 'inc/ajax/courses_ajax.php', { action: 'list', scheme_id: b.scheme_id, sector_id: b.sector_id }, 'course_id', 'course_name', b.course_id);
                    $('#batchLoadingOverlay').fadeOut(200);
                } else {
                    alert(res.message || 'Could not fetch batch details.');
                    $('#batchLoadingOverlay').hide();
                }
            },
            error: function() {
                $('#batchLoadingOverlay').hide();
            }
        });
    });

    // Helper: Load options and set value, returns a Promise
    function loadSelectOptions($select, ajaxUrl, ajaxData, valueKey, labelKey, selectedValue) {
      return new Promise((resolve) => {
        console.log('Loading', $select.attr('id'), 'with value', selectedValue, 'from', ajaxUrl, ajaxData);
        $.ajax({
          url: ajaxUrl,
          type: 'POST',
          data: ajaxData,
          dataType: 'json',
          success: function(res) {
            $select.empty().append('<option value="">Select</option>');
            if(res.data && res.data.length) {
              $.each(res.data, function(i, item) {
                let val = item[valueKey];
                let label = item[labelKey];
                $select.append(`<option value="${val}"${val==selectedValue?' selected':''}>${label}</option>`);
              });
              $select.val(selectedValue);
            }
            console.log('Set', $select.attr('id'), 'to', selectedValue, 'options:', $select.html());
            resolve();
          }
        });
      });
    }

    // Save (add/edit) batch
    $('#batchForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        // Remove status from formData if present
        formData = formData.replace(/&?status=[^&]*/g, '');
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
                    reloadBatchesTable();
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
                        reloadBatchesTable();
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

    // View students in batch
    $(document).on('click', '.view-batch-students-btn', function() {
        var batchId = $(this).data('batch-id');
        $('#batchStudentsError').addClass('d-none').text('');
        var $tableBody = $('#batchStudentsTable tbody');
        $tableBody.empty();
        $.ajax({
            url: 'inc/ajax/batch_students_ajax.php',
            type: 'POST',
            data: { action: 'get_students_by_batch', batch_id: batchId },
            dataType: 'json',
            success: function(res) {
                if(res.success && res.data.length) {
                    // Update the table header for Batch Students modal
                    $('#batchStudentsTable thead').html('<tr><th>#</th><th>Enrollment No</th><th>Full Name</th><th>Email</th><th>Mobile</th><th>Gender</th></tr>');
                    $.each(res.data, function(i, s) {
                        var fullName = s.full_name ? s.full_name : ((s.first_name || '') + (s.last_name ? ' ' + s.last_name : '')).trim();
                        $tableBody.append('<tr>' +
                            '<td>' + (i+1) + '</td>' +
                            '<td>' + (s.enrollment_no || '') + '</td>' +
                            '<td>' + fullName + '</td>' +
                            '<td>' + (s.email || '') + '</td>' +
                            '<td>' + (s.mobile || '') + '</td>' +
                            '<td>' + (s.gender ? s.gender.charAt(0).toUpperCase() + s.gender.slice(1) : '') + '</td>' +
                        '</tr>');
                    });
                } else {
                    $tableBody.append('<tr><td colspan="7" class="text-center">No students found in this batch.</td></tr>');
                }
                $('#batchStudentsModal').modal('show');
            },
            error: function() {
                $('#batchStudentsError').removeClass('d-none').text('Could not load students.');
                $('#batchStudentsModal').modal('show');
            }
        });
    });

    // Register New Student from batch context
    $(document).on('click', '.register-student-btn', function() {
        var batchId = $(this).data('batch-id');
        $('#registerBatchId').val(batchId);
        $('#registerStudentForm')[0].reset();
        $('#registerStudentError').addClass('d-none').text('');
        $('#registerStudentModal').modal('show');
    });

    // Handle register student form submit
    $('#registerStudentForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var $error = $('#registerStudentError');
        $error.addClass('d-none').text('');
        var valid = true;
        $form.find('[required]').each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        if (!valid) {
            $error.removeClass('d-none').text('Please fill all required fields.');
            return;
        }
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Registering...');
        var formData = new FormData($form[0]);
        formData.append('action', 'create');
        $.ajax({
            url: 'inc/ajax/students_ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#registerStudentModal').modal('hide');
                    reloadBatchesTable();
                    toastr.success(response.message || 'Student registered successfully.');
                } else {
                    $error.removeClass('d-none').text(response.message || 'Failed to register student.');
                    toastr.error(response.message || 'Failed to register student.');
                }
                $btn.prop('disabled', false).text('Register Student');
            },
            error: function () {
                $error.removeClass('d-none').text('Failed to register student.');
                toastr.error('Failed to register student.');
                $btn.prop('disabled', false).text('Register Student');
            }
        });
    });

    // Reload after closing add/edit modal
    $('#batchModal').on('hidden.bs.modal', function() {
        reloadBatchesTable();
    });

    // Reload after closing students modal (in case of changes)
    $('#batchStudentsModal').on('hidden.bs.modal', function() {
        reloadBatchesTable();
    });

    // Reset modal on close
    $('#batchModal').on('hidden.bs.modal', function() {
        $('#batchForm')[0].reset();
        $('#course_id').empty().append('<option value="">Select Course</option>');
    });
});