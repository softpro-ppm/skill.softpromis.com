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
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": ajaxUrl,
            "type": "POST",
            "data": function(d) {
                d.action = 'read';
            },
            "dataSrc": function(json) {
                if (!json || !json.success || !json.data || !json.data.data) {
                    console.error("Invalid data received:", json);
                    toastr.error(json.message || 'Failed to load data. Check console for details.');
                    return [];
                }
                return json.data.data;
            },
            "error": function(xhr, error, thrown) {
                console.error("DataTables AJAX Error:", { xhr, error, thrown });
                toastr.error('An error occurred while fetching data.');
            }
        },
        "columns": [
            { "data": "id", "title": "Center ID" },
            { "data": "name", "title": "Name" },
            { "data": null, "title": "Location", "render": function(data, type, row) {
                    return `${row.city || ''}, ${row.state || ''}`;
                }
            },
            { "data": "contact_person", "title": "Contact Person" },
            { "data": "phone", "title": "Phone" },
            { "data": "capacity", "title": "Capacity" },
            { "data": "status", "title": "Status" },
            { 
                "data": null, 
                "title": "Actions",
                "render": function(data, type, row) {
                    return `
                        <button class="btn btn-info btn-sm view-center" data-id="${row.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-primary btn-sm edit-center" data-id="${row.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-center" data-id="${row.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        "order": [[0, "desc"]],
        "pageLength": 10,
        "responsive": true
    });

    // --- Form Submission Handlers ---
    $('#addCenterForm').on('submit', function(e) {
        e.preventDefault();
        const formData = {
            action: 'create',
            name: $('#centerName').val(),
            contact_person: $('#contactPerson').val(),
            phone: $('#phone').val(),
            address: $('#address').val(),
            city: $('#city').val(),
            state: $('#state').val(),
            pincode: $('#pincode').val(),
            capacity: $('#capacity').val(),
            status: 'active'
        };

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#addCenterModal').modal('hide');
                    centersTable.ajax.reload();
                    $('#addCenterForm')[0].reset();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('An error occurred while creating the center');
            }
        });
    });

    // --- Event Handlers ---
    $(document).on('click', '.view-center', function() {
        const centerId = $(this).data('id');
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: { action: 'get', id: centerId },
            success: function(response) {
                if (response.success) {
                    const center = response.data;
                    $('#viewCenterModal .modal-body').html(`
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Center ID:</strong> ${center.id}</p>
                                <p><strong>Name:</strong> ${center.name}</p>
                                <p><strong>Contact Person:</strong> ${center.contact_person}</p>
                                <p><strong>Phone:</strong> ${center.phone}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Address:</strong> ${center.address}</p>
                                <p><strong>City:</strong> ${center.city}</p>
                                <p><strong>State:</strong> ${center.state}</p>
                                <p><strong>Pincode:</strong> ${center.pincode}</p>
                                <p><strong>Capacity:</strong> ${center.capacity}</p>
                                <p><strong>Status:</strong> ${center.status}</p>
                            </div>
                        </div>
                    `);
                    $('#viewCenterModal').modal('show');
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // --- Utility Functions ---
    function reloadTable() {
        centersTable.ajax.reload();
    }
}); 