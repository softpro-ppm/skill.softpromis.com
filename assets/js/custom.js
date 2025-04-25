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