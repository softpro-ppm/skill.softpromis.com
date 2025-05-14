/**
 * Custom JavaScript
 * This file intentionally kept minimal to preserve default DataTables functionality
 */

// Document ready function
$(function() {
  // Initialize select2 where needed
  $('.select2').select2();
  
  // Initialize custom file input for consistent file upload experience
  bsCustomFileInput.init();
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