/**
 * Custom JavaScript for Skill SoftPromis
 * Basic enhancements for DataTables
 */

$(document).ready(function() {
  // Simple initialization for any additional DataTables features
  // This is intentionally minimal to preserve standard DataTables functionality
  $('.table').each(function() {
    if ($.fn.DataTable.isDataTable(this)) {
      // Do nothing if already initialized
    } else {
      // Initialize with standard options
      $(this).DataTable({
        "responsive": true
      });
    }
  });
  
  // Initialize custom file input for file uploads
  if (typeof bsCustomFileInput !== 'undefined') {
    bsCustomFileInput.init();
  }
  
  // Initialize Select2 for enhanced dropdowns
  if (typeof $.fn.select2 !== 'undefined') {
    $('.select2').select2({
      theme: 'bootstrap4'
    });
  }
}); 