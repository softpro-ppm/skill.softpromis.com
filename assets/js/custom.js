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

// Add a CodePen-inspired glassmorphic animated loader overlay for Edit Course modal
var courseLoadingOverlay = `
<div id="courseLoadingOverlay" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(240,245,255,0.85);backdrop-filter:blur(6px);z-index:1051;display:flex;align-items:center;justify-content:center;animation:fadeIn 0.4s;">
  <div style="background:rgba(255,255,255,0.7);border-radius:2rem;box-shadow:0 8px 32px rgba(60,60,120,0.18);padding:3rem 4rem;display:flex;flex-direction:column;align-items:center;backdrop-filter:blur(8px);">
    <svg width="80" height="80" viewBox="0 0 80 80" style="margin-bottom:2rem;">
      <defs>
        <linearGradient id="loaderGradientCourse" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#007bff"/>
          <stop offset="100%" stop-color="#00c6ff"/>
        </linearGradient>
      </defs>
      <circle cx="40" cy="40" r="32" stroke="url(#loaderGradientCourse)" stroke-width="8" fill="none" stroke-linecap="round" stroke-dasharray="180 100" stroke-dashoffset="0">
        <animateTransform attributeName="transform" type="rotate" from="0 40 40" to="360 40 40" dur="1s" repeatCount="indefinite"/>
      </circle>
    </svg>
    <div class="fw-bold text-primary" style="font-size:2rem;text-shadow:0 2px 8px #e0e7ef;letter-spacing:0.5px;">Loading Course Data...</div>
  </div>
</div>
<style>@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }</style>`;

// Show loader on Edit Course modal open
$(document).on('click', '.edit-course-btn', function () {
  if (!$('#courseLoadingOverlay').length) {
    $('#editCourseModal .modal-content').append(courseLoadingOverlay);
  } else {
    $('#courseLoadingOverlay').show();
  }
});
// Hide loader after all select fields are loaded (call this at the end of setEditCourseFields)
function hideCourseLoader() {
  $('#courseLoadingOverlay').fadeOut(200);
}
// In setEditCourseFields, call hideCourseLoader() after all selects are set
var originalSetEditCourseFields = window.setEditCourseFields;
window.setEditCourseFields = async function(course) {
  if (typeof originalSetEditCourseFields === 'function') {
    await originalSetEditCourseFields(course);
    hideCourseLoader();
  }
};