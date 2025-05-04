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

  // Add Assessment Button
  $('#addAssessmentBtn').on('click', function () {
    $('#assessmentForm')[0].reset();
    $('#assessment_id').val('');
    $('#assessmentModalTitle').text('Add New Assessment');
    // Reset all fields to default
    $('#student_id').val('').trigger('change');
    $('#enrollment_id').empty().append('<option value="">Select Enrollment</option>');
    $('#enrollment_id_hidden').val('');
    $('#enrollment_id_group').hide();
    $('#course_name').val('');
    $('#assessment_type').val('');
    $('#assessment_date').val('');
    $('#score').val('');
    $('#max_score').val('100');
    $('#status').val('pending');
    $('#remarks').val('');
    // Load students
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
        $student.val('').trigger('change');
      }
    });
    $('#assessmentModal').modal('show');
  });

  // On student or enrollment change, update course field
  function updateCourseField(studentId, enrollmentId) {
    if (!studentId || !enrollmentId) {
      $('#course_name').val('');
      return;
    }
    $.ajax({
      url: 'inc/ajax/assessments_ajax.php',
      type: 'POST',
      data: { action: 'get_course_by_enrollment', student_id: studentId, enrollment_id: enrollmentId },
      dataType: 'json',
      success: function(res) {
        if(res.success && res.data && res.data.course_name) {
          $('#course_name').val(res.data.course_name);
        } else {
          $('#course_name').val('');
        }
      },
      error: function() {
        $('#course_name').val('');
      }
    });
  }

  // On student change, load enrollments
  $(document).on('change', '#student_id', function() {
    var studentId = $(this).val();
    $('#course_name').val('');
    if (!studentId) {
      $('#enrollment_id').empty().append('<option value="">Select Enrollment</option>');
      $('#enrollment_id_hidden').val('');
      $('#enrollment_id_group').hide();
      return;
    }
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
            enrollSel.append('<option value="' + res.data[0].enrollment_id + '">' + res.data[0].enrollment_id + '</option>');
            enrollSel.val(res.data[0].enrollment_id);
            updateCourseField(studentId, res.data[0].enrollment_id);
          } else {
            enrollSel.append('<option value="">Select Enrollment</option>');
            $.each(res.data, function(i, e) {
              enrollSel.append('<option value="' + e.enrollment_id + '">' + e.enrollment_id + '</option>');
            });
            enrollGroup.show();
          }
        } else {
          enrollGroup.hide();
          $('#enrollment_id_hidden').val('');
        }
      }
    });
  });

  $(document).on('change', '#enrollment_id', function() {
    var studentId = $('#student_id').val();
    var enrollmentId = $(this).val();
    $('#enrollment_id_hidden').val(enrollmentId);
    updateCourseField(studentId, enrollmentId);
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
          $('#assessmentModalTitle').text('Edit Assessment');
          // Load students and set selected, then load enrollments and set selected
          $.ajax({
            url: 'inc/ajax/students_ajax.php',
            type: 'POST',
            data: { action: 'list' },
            dataType: 'json',
            success: function(res) {
              var $student = $('#student_id');
              $student.empty().append('<option value="">Select Student</option>');
              var foundStudent = null;
              if(res.success && res.data) {
                $.each(res.data, function(i, s) {
                  var label = s.first_name + ' ' + s.last_name + (s.enrollment_no ? ' (' + s.enrollment_no + ')' : '');
                  $student.append('<option value="' + s.student_id + '"' + (s.student_id==d.student_id?' selected':'') + '>' + label + '</option>');
                  if(s.student_id==d.student_id) foundStudent = s.student_id;
                });
              }
              if(foundStudent) {
                // Load enrollments for this student and set selected
                $.ajax({
                  url: 'inc/ajax/students_ajax.php',
                  type: 'POST',
                  data: { action: 'get_enrollments_by_student', student_id: foundStudent },
                  dataType: 'json',
                  success: function(res2) {
                    var enrollSel = $('#enrollment_id');
                    var enrollGroup = $('#enrollment_id_group');
                    enrollSel.empty();
                    if(res2.success && res2.data.length) {
                      if(res2.data.length === 1) {
                        $('#enrollment_id_hidden').val(res2.data[0].enrollment_id);
                        enrollGroup.hide();
                        enrollSel.append('<option value="' + res2.data[0].enrollment_id + '">' + res2.data[0].enrollment_id + '</option>');
                        enrollSel.val(res2.data[0].enrollment_id);
                      } else {
                        enrollSel.append('<option value="">Select Enrollment</option>');
                        $.each(res2.data, function(i, e) {
                          enrollSel.append('<option value="' + e.enrollment_id + '"' + (e.enrollment_id==d.enrollment_id?' selected':'') + '>' + e.enrollment_id + '</option>');
                        });
                        enrollGroup.show();
                        enrollSel.val(d.enrollment_id);
                      }
                    } else {
                      enrollGroup.hide();
                      $('#enrollment_id_hidden').val('');
                    }
                  }
                });
              }
              $student.val(d.student_id).trigger('change');
            }
          });
          $('#course_name').val(d.course_name);
          $('#assessment_type').val(d.assessment_type);
          $('#assessment_date').val(d.assessment_date);
          $('#score').val(d.score);
          $('#max_score').val(d.max_score);
          $('#status').val(d.status);
          $('#remarks').val(d.remarks);
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
          var html = '<div class="row">' +
            '<div class="col-md-6">' +
            '<p><strong>Student:</strong> ' + d.student_name + '</p>' +
            '<p><strong>Course:</strong> ' + d.course_name + '</p>' +
            '<p><strong>Type:</strong> ' + d.assessment_type + '</p>' +
            '<p><strong>Date:</strong> ' + d.assessment_date + '</p>' +
            '<p><strong>Status:</strong> ' + d.status + '</p>' +
            '</div>' +
            '<div class="col-md-6">' +
            '<p><strong>Score:</strong> ' + d.score + '</p>' +
            '<p><strong>Max Score:</strong> ' + d.max_score + '</p>' +
            '<p><strong>Remarks:</strong> ' + (d.remarks || '') + '</p>' +
            '</div>' +
            '</div>';
          $('#viewAssessmentBody').html(html);
          $('#viewAssessmentModal').modal('show');
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