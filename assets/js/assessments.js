$(function () {
  // Initialize DataTable
  var table = $('#assessmentsTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
      url: 'inc/ajax/assessments_ajax.php',
      type: 'POST',
      data: { action: 'list' },
      dataSrc: function(json) { return json.data || []; }
    },
    columns: [
      { data: 'assessment_id' },
      { data: 'student_name' },
      { data: 'course_name' },
      { data: 'assessment_type' },
      { data: 'assessment_date' },
      { data: 'score' },
      { data: 'max_score' },
      { data: 'status', render: function(data) {
          var badge = 'secondary';
          if (data === 'completed') badge = 'success';
          if (data === 'pending') badge = 'warning';
          if (data === 'failed') badge = 'danger';
          return '<span class="badge badge-' + badge + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
        }
      },
      { data: null, orderable: false, searchable: false, render: function(data, type, row) {
          return '<button class="btn btn-sm btn-info view-assessment-btn" data-id="' + row.assessment_id + '"><i class="fas fa-eye"></i></button>' +
                 '<button class="btn btn-sm btn-primary edit-assessment-btn" data-id="' + row.assessment_id + '"><i class="fas fa-edit"></i></button>' +
                 '<button class="btn btn-sm btn-danger delete-assessment-btn" data-id="' + row.assessment_id + '"><i class="fas fa-trash"></i></button>';
        }
      }
    ],
    responsive: true,
    lengthChange: true,
    autoWidth: false,
    order: [[0, 'desc']]
  });

  // Helper to load all students into the dropdown
  function loadAllStudents(selectedId) {
    $.ajax({
      url: 'inc/ajax/students_ajax.php',
      type: 'POST',
      data: { action: 'list' },
      dataType: 'json',
      success: function(res) {
        var $student = $('#student_id');
        $student.empty().append('<option value="">Select Student</option>');
        if(res.success && res.data) {
          $.each(res.data, function(i, s) {
            var label = s.first_name + ' ' + s.last_name + (s.enrollment_no ? ' (' + s.enrollment_no + ')' : '');
            $student.append('<option value="' + s.student_id + '">' + label + '</option>');
          });
        }
        if(selectedId) {
          $student.val(selectedId).trigger('change');
        }
      }
    });
  }

  // Add Assessment Button
  $('#addAssessmentBtn').on('click', function () {
    $('#assessmentForm')[0].reset();
    $('#assessment_id').val('');
    $('#assessmentModalTitle').text('Add New Assessment');
    loadAllStudents();
    $('#assessmentModal').modal('show');
  });

  // Edit Assessment Button
  $(document).on('click', '.edit-assessment-btn', function () {
    var id = $(this).data('id');
    $.ajax({
      url: 'inc/ajax/assessments_ajax.php',
      type: 'POST',
      data: { action: 'get', assessment_id: id },
      dataType: 'json',
      success: function (response) {
        if (response.success && response.data) {
          var d = response.data;
          $('#assessment_id').val(d.assessment_id);
          $('#enrollment_id').val(d.enrollment_id).trigger('change');
          // Load students and set selected
          loadAllStudents(d.student_id);
          $('#course_name').val(d.course_name);
          $('#assessment_type').val(d.assessment_type);
          $('#assessment_date').val(d.assessment_date);
          $('#score').val(d.score);
          $('#max_score').val(d.max_score);
          $('#status').val(d.status);
          $('#remarks').val(d.remarks);
          $('#assessmentModalTitle').text('Edit Assessment');
          $('#assessmentModal').modal('show');
        } else {
          toastr.error('Could not fetch assessment details.');
        }
      },
      error: function () {
        toastr.error('Could not fetch assessment details.');
      }
    });
  });

  // View Assessment Button
  $(document).on('click', '.view-assessment-btn', function () {
    var id = $(this).data('id');
    $.ajax({
      url: 'inc/ajax/assessments_ajax.php',
      type: 'POST',
      data: { action: 'get', assessment_id: id },
      dataType: 'json',
      success: function (response) {
        if (response.success && response.data) {
          var d = response.data;
          var html = '<p><strong>Student:</strong> ' + d.student_name + '</p>' +
                     '<p><strong>Course:</strong> ' + d.course_name + '</p>' +
                     '<p><strong>Type:</strong> ' + d.assessment_type + '</p>' +
                     '<p><strong>Date:</strong> ' + d.assessment_date + '</p>' +
                     '<p><strong>Score:</strong> ' + d.score + '</p>' +
                     '<p><strong>Max Score:</strong> ' + d.max_score + '</p>' +
                     '<p><strong>Status:</strong> ' + d.status + '</p>' +
                     '<p><strong>Remarks:</strong> ' + (d.remarks || '') + '</p>';
          // Show in a modal (implement a view modal if needed)
          toastr.info(html, 'Assessment Details', {timeOut: 7000, extendedTimeOut: 2000});
        } else {
          toastr.error('Could not fetch assessment details.');
        }
      },
      error: function () {
        toastr.error('Could not fetch assessment details.');
      }
    });
  });

  // Delete Assessment Button
  var deleteAssessmentId = null;
  $(document).on('click', '.delete-assessment-btn', function () {
    deleteAssessmentId = $(this).data('id');
    if (confirm('Are you sure you want to delete this assessment?')) {
      $.ajax({
        url: 'inc/ajax/assessments_ajax.php',
        type: 'POST',
        data: { action: 'delete', assessment_id: deleteAssessmentId },
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            toastr.success(response.message || 'Assessment deleted successfully');
            table.ajax.reload();
          } else {
            toastr.error(response.message || 'Error deleting assessment');
          }
        },
        error: function () {
          toastr.error('Error deleting assessment');
        }
      });
    }
  });

  // Save (Add/Edit) Assessment
  $('#assessmentForm').on('submit', function (e) {
    e.preventDefault();
    var formData = $(this).serialize();
    var action = $('#assessment_id').val() ? 'update' : 'create';
    formData += '&action=' + action;
    $.ajax({
      url: 'inc/ajax/assessments_ajax.php',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          toastr.success(response.message || 'Assessment saved successfully');
          $('#assessmentModal').modal('hide');
          $('#assessmentForm')[0].reset();
          table.ajax.reload();
        } else {
          toastr.error(response.message || 'Error saving assessment');
        }
      },
      error: function () {
        toastr.error('An error occurred. Please try again.');
      }
    });
  });

  // Reset form on modal close
  $('#assessmentModal').on('hidden.bs.modal', function () {
    $('#assessmentForm')[0].reset();
    $('#assessment_id').val('');
    $('#assessmentModalTitle').text('Add New Assessment');
  });

  // Toastr options
  toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 3000,
    preventDuplicates: true
  };
});