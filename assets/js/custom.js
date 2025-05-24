/**
 * Custom JavaScript
 * This file intentionally kept minimal to preserve default DataTables functionality
 */

// --- Course Modal Partner/Center/Scheme/Sector Cascade ---
function loadCoursePartners(selectedId, isEdit) {
  $.ajax({
    url: 'inc/ajax/training_partners_ajax.php',
    type: 'POST',
    data: { action: 'list' },
    dataType: 'json',
    success: function(res) {
      var $partner = isEdit ? $('#edit_partner_id') : $('#partner_id');
      $partner.empty().append('<option value="">Select Training Partner</option>');
      if(res.data && res.data.length) {
        $.each(res.data, function(i, p) {
          $partner.append(`<option value="${p.partner_id}"${selectedId==p.partner_id?' selected':''}>${p.partner_name}</option>`);
        });
      }
    }
  });
}
function loadCourseCenters(partnerId, selectedId, isEdit) {
  var $center = isEdit ? $('#edit_center_id') : $('#center_id');
  if (!partnerId) {
    $center.empty().append('<option value="">Select Training Center</option>');
    return;
  }
  $.ajax({
    url: 'inc/ajax/training-centers.php',
    type: 'POST',
    data: { action: 'list', partner_id: partnerId },
    dataType: 'json',
    success: function(res) {
      $center.empty().append('<option value="">Select Training Center</option>');
      if(res.data && res.data.length) {
        $.each(res.data, function(i, c) {
          $center.append(`<option value="${c.center_id}"${selectedId==c.center_id?' selected':''}>${c.center_name}</option>`);
        });
      }
    }
  });
}
function loadCourseSchemes(centerId, selectedId, isEdit) {
  var $scheme = isEdit ? $('#edit_scheme_id') : $('#scheme_id');
  if (!centerId) {
    $scheme.empty().append('<option value="">Select Scheme</option>');
    return;
  }
  $.ajax({
    url: 'inc/ajax/schemes_ajax.php',
    type: 'POST',
    data: { action: 'list', center_id: centerId },
    dataType: 'json',
    success: function(res) {
      $scheme.empty().append('<option value="">Select Scheme</option>');
      if(res.data && res.data.length) {
        $.each(res.data, function(i, s) {
          if(s.center_id == centerId && s.status === 'active') {
            $scheme.append(`<option value="${s.scheme_id}"${selectedId==s.scheme_id?' selected':''}>${s.scheme_name}</option>`);
          }
        });
      }
    }
  });
}
function loadCourseSectors(schemeId, selectedId, isEdit) {
  var $sector = isEdit ? $('#edit_sector_id') : $('#sector_id');
  if (!schemeId) {
    $sector.empty().append('<option value="">Select Sector</option>');
    return;
  }
  $.ajax({
    url: 'inc/ajax/sectors_ajax.php',
    type: 'POST',
    data: { action: 'list', scheme_id: schemeId },
    dataType: 'json',
    success: function(res) {
      $sector.empty().append('<option value="">Select Sector</option>');
      if(res.data && res.data.length) {
        $.each(res.data, function(i, s) {
          $sector.append(`<option value="${s.sector_id}"${selectedId==s.sector_id?' selected':''}>${s.sector_name}</option>`);
        });
      }
    }
  });
}

// Document ready function
$(function() {
  // Initialize select2 where needed
  $('.select2').select2();
  
  // Initialize custom file input for consistent file upload experience
  bsCustomFileInput.init();

  // Add Modal
  $('#addCourseModal').on('show.bs.modal', function() {
    loadCoursePartners();
    $('#center_id').empty().append('<option value="">Select Training Center</option>');
    $('#scheme_id').empty().append('<option value="">Select Scheme</option>');
    $('#sector_id').empty().append('<option value="">Select Sector</option>');
  });
  $('#partner_id').on('change', function() {
    var partnerId = $(this).val();
    loadCourseCenters(partnerId);
    $('#center_id').trigger('change');
  });
  $('#center_id').on('change', function() {
    var centerId = $(this).val();
    loadCourseSchemes(centerId);
    $('#scheme_id').trigger('change');
  });
  $('#scheme_id').on('change', function() {
    var schemeId = $(this).val();
    loadCourseSectors(schemeId);
  });
  // Edit Modal
  $('#editCourseModal').on('show.bs.modal', function() {
    loadCoursePartners();
    $('#edit_center_id').empty().append('<option value="">Select Training Center</option>');
    $('#edit_scheme_id').empty().append('<option value="">Select Scheme</option>');
    $('#edit_sector_id').empty().append('<option value="">Select Sector</option>');
  });
  $('#edit_partner_id').on('change', function() {
    var partnerId = $(this).val();
    loadCourseCenters(partnerId, null, true);
    $('#edit_center_id').trigger('change');
  });
  $('#edit_center_id').on('change', function() {
    var centerId = $(this).val();
    loadCourseSchemes(centerId, null, true);
    $('#edit_scheme_id').trigger('change');
  });
  $('#edit_scheme_id').on('change', function() {
    var schemeId = $(this).val();
    loadCourseSectors(schemeId, null, true);
  });
});

$(document).on('show.bs.modal', '#addAssessmentModal', function () {
  console.log('Opening Add Assessment Modal');
  console.log('Fetching enrollments...');
  // Fetch enrollment data
  $.ajax({
    url: 'inc/ajax/students_ajax.php',
    type: 'POST',
    data: { action: 'getEnrollments' },
    dataType: 'json',
    success: function (response) {
      console.log('Enrollments Response:', response);
      if (response.success) {
        const enrollmentDropdown = $('#addEnrollmentId');
        enrollmentDropdown.empty();
        enrollmentDropdown.append('<option value="">Select Enrollment</option>');
        response.data.forEach(function (enrollment) {
          enrollmentDropdown.append(
            `<option value="${enrollment.enrollment_id}">${enrollment.enrollment_no} - ${enrollment.student_name}</option>`
          );
        });
      } else {
        toastr.error('Failed to load enrollments: ' + response.message);
      }
    },
    error: function (xhr, status, error) {
      console.error('AJAX Error:', status, error);
      toastr.error('An error occurred while fetching enrollments.');
    },
  });
});

$(document).on('submit', '#addAssessmentForm', function (e) {
  e.preventDefault(); // Prevent default form submission

  const formData = $(this).serialize();

  $.ajax({
    url: 'inc/ajax/assessments_ajax.php',
    type: 'POST',
    data: { action: 'create', ...formData },
    dataType: 'json',
    success: function (response) {
      if (response.success) {
        toastr.success('Assessment added successfully!');
        $('#addAssessmentModal').modal('hide');
        $('#assessmentsTable').DataTable().ajax.reload();
      } else {
        toastr.error(response.message || 'Failed to add assessment.');
      }
    },
    error: function (xhr, status, error) {
      console.error('AJAX Error:', status, error);
      toastr.error('An error occurred while adding the assessment.');
    },
  });
});

// Helper for cascading select population in Edit Course modal
function setEditCourseFields(course) {
  // 1. Set Partner, then Center, then Scheme, then Sector, then other fields
  loadCoursePartners(course.partner_id, true);
  setTimeout(function() {
    $('#edit_partner_id').val(course.partner_id).trigger('change');
    loadCourseCenters(course.partner_id, course.center_id, true);
    setTimeout(function() {
      $('#edit_center_id').val(course.center_id).trigger('change');
      loadCourseSchemes(course.center_id, course.scheme_id, true);
      setTimeout(function() {
        $('#edit_scheme_id').val(course.scheme_id).trigger('change');
        loadCourseSectors(course.scheme_id, course.sector_id, true);
        setTimeout(function() {
          $('#edit_sector_id').val(course.sector_id).trigger('change');
          // Set all other fields
          $('#edit_course_code').val(course.course_code);
          $('#edit_course_name').val(course.course_name);
          $('#edit_duration_hours').val(course.duration_hours);
          $('#edit_fee').val(course.fee);
          $('#edit_description').val(course.description);
          $('#edit_prerequisites').val(course.prerequisites);
          $('#edit_syllabus').val(course.syllabus);
          $('#edit_status').val(course.status);
          $('#editCourseModal').data('id', course.course_id).modal('show');
        }, 300);
      }, 300);
    }, 300);
  }, 300);
}