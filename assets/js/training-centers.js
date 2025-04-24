// JavaScript for Training Centers page
$(document).ready(function() {

    // --- Configuration ---
    const ajaxUrl = 'inc/ajax/training_centers_ajax.php';

    // --- Toastr Setup ---
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 3000,
        preventDuplicates: true
    };

    // --- DataTable Initialization ---
    const centersTable = $('#centersTable').DataTable({
        "processing": true, // Show processing indicator
        "serverSide": false, // For now, use client-side processing. Change to true for large datasets.
        "ajax": {
            "url": ajaxUrl,
            "type": "POST",
            "data": function(d) { // Send 'action' parameter
                d.action = 'read';
                // Add other filters here if needed (e.g., search, partner_id)
                // For server-side processing, DataTables sends parameters like draw, start, length, search[value]
            },
            "dataSrc": function(json) { // Process the response
                if (!json || !json.success || !json.data || !json.data.data) {
                     console.error("Invalid data received:", json);
                     toastr.error(json.message || 'Failed to load data. Check console for details.');
                     return []; // Return empty array on error
                }
                // For client-side processing, return the actual data array
                return json.data.data;
            },
            "error": function(xhr, error, thrown) { // Handle AJAX errors
                console.error("DataTables AJAX Error:", { xhr, error, thrown });
                toastr.error('An error occurred while fetching data.');
            }
        },
        "columns": [
            { "data": "id", "title": "Center ID" },
            { "data": "name", "title": "Name" },
            { "data": "partner_name", "title": "Partner" }, // Assuming partner_name is returned by the 'read' action
            { "data": null, "title": "Location", "render": function(data, type, row) {
                    return `${row.city || ''}, ${row.state || ''}`;
                }
            },
            { "data": "phone", "title": "Phone" },
             { "data": "capacity", "title": "Capacity" },
            {
                "data": "status",
                "title": "Status",
                "render": function(data, type, row) {
                    const badgeClass = data === 'active' ? 'badge-success' : 'badge-danger';
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            {
                "data": null,
                "title": "Actions",
                "orderable": false,
                "searchable": false,
                "render": function(data, type, row) {
                    // Added data-id attribute to each button
                    return `
                        <button type="button" class="btn btn-info btn-sm view-center" data-id="${row.id}" title="View Details">
                          <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-sm edit-center" data-id="${row.id}" title="Edit Center">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm delete-center" data-id="${row.id}" title="Delete Center">
                          <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        "paging": true,
        "lengthChange": true,
        "searching": true, // Enable DataTables native search box
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        // Optional: Add language options if needed
        // "language": {
        //     "processing": "Loading..."
        // }
    });

    // --- Helper Functions ---
    function reloadTable() {
        centersTable.ajax.reload(null, false); // Reload DataTable without resetting pagination
    }

    // --- Function to load partners into dropdown ---
    function loadPartnersDropdown(selectElementId) {
        const selectElement = $(`#${selectElementId}`);
        if (!selectElement.length) return; // Exit if element doesn't exist

        selectElement.empty().append('<option value="">Loading Partners...</option>');

        $.ajax({
            url: 'inc/ajax/training_partners_ajax.php', // URL for partners
            type: 'POST',
            data: { action: 'list_all' },
            dataType: 'json',
            success: function(response) {
                selectElement.empty().append('<option value="">Select Partner</option>'); // Add default option
                if (response.success && response.data && response.data.partners) {
                    response.data.partners.forEach(function(partner) {
                        selectElement.append($('<option></option>').attr('value', partner.id).text(partner.name));
                    });
                } else {
                    console.error('Failed to load partners:', response.message);
                    toastr.error(response.message || 'Could not load partners.');
                    selectElement.append('<option value="">Error loading partners</option>');
                }
                // Refresh Select2 if it's initialized on this element
                if (selectElement.hasClass('select2-hidden-accessible')) {
                    selectElement.trigger('change');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error loading partners:', error);
                toastr.error('An error occurred while loading partners.');
                selectElement.empty().append('<option value="">Error loading partners</option>');
            }
        });
    }

    // --- Initialize other components ---
    // Note: These might already be initialized in training-centers.php,
    // review and remove duplication if necessary.
    $('.select2').select2({
      theme: 'bootstrap4'
    });
    bsCustomFileInput.init();

    // --- Load initial data ---
    loadPartnersDropdown('partner'); // Load partners for Add modal
    // We will call loadPartnersDropdown('editPartner') when the edit modal is opened.

    // --- Event Handlers ---

    // Add Center Form Submission
    $('#addCenterForm').on('submit', function(event) {
        event.preventDefault();
        const form = this;
        const submitButton = $(form).find('button[type="submit"]');
        const originalButtonText = submitButton.html();

        // Basic client-side validation (optional, enhance as needed)
        if (!form.checkValidity()) {
            event.stopPropagation();
            form.classList.add('was-validated');
            toastr.warning('Please fill all required fields.');
            return;
        }
        form.classList.remove('was-validated'); // Remove validation classes if valid

        submitButton.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

        const formData = new FormData(form);
        formData.append('action', 'create');

        // Log formData contents (for debugging)
        // for (var pair of formData.entries()) {
        //     console.log(pair[0]+ ': ' + pair[1]);
        // }

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false, // Important for FormData
            contentType: false, // Important for FormData
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Center created successfully!');
                    $('#addCenterModal').modal('hide');
                    form.reset(); // Clear the form
                    reloadTable(); // Refresh the DataTable
                } else {
                    toastr.error(response.message || 'Failed to create center.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Add Center AJAX Error:', error);
                toastr.error('An error occurred while saving the center.');
            },
            complete: function() {
                submitButton.html(originalButtonText).prop('disabled', false);
            }
        });
    });

    // Reset form validation state when modal is closed
    $('#addCenterModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $(this).find('form').removeClass('was-validated');
        // Reset select2 if needed
         $('#partner').val(null).trigger('change');
         // Reset custom file input labels
        $(this).find('.custom-file-label').text('Choose file');
    });

    // TODO: Implement handlers for View, Edit, Delete buttons

}); 