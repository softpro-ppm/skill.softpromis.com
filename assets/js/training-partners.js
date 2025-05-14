$(document).ready(function() {
    // Initialize DataTable
    var partnersTable = $('#partnersTable').DataTable({
        "ajax": {
            "url": "inc/ajax/training_partners_ajax.php",
            "type": "POST",
            "data": { action: "list" }
        },
        "columns": [
            { "data": "partner_name" },
            { "data": "contact_person" },
            { "data": "email" },
            { "data": "phone" },
            { "data": "address" },
            { 
                "data": "status",
                "render": function(data) {
                    return `<span class="badge badge-${data === 'active' ? 'success' : 'danger'}">${data}</span>`;
                }
            },
            {
                "data": null,
                "render": function(data) {
                    return `
                        <button class="btn btn-sm btn-info edit-partner" data-id="${data.partner_id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-partner" data-id="${data.partner_id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
    });

    // Handle form submission
    $('#partnerForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', $('#partner_id').val() ? 'update' : 'add');
        // Always set status to active for new partners
        if (!$('#partner_id').val()) {
            formData.set('status', 'active');
        }

        $.ajax({
            url: 'inc/ajax/training_partners_ajax.php',
            type: 'POST',
            data: {
                action: formData.get('action'),
                partner_id: formData.get('partner_id'),
                partner_name: formData.get('partner_name'),
                contact_person: formData.get('contact_person'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                address: formData.get('address'),
                website: formData.get('website'),
                status: formData.get('status')
            },
            success: function(response) {
                if (response.success) {
                    $('#partnerModal').modal('hide');
                    $('#partnerForm')[0].reset();
                    $('#status').val('active'); // Reset status to active
                    partnersTable.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message || 'Error processing request');
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again later.');
            }
        });
    });

    // Edit Partner Button Click
    $('#partnersTable').on('click', '.edit-partner', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'inc/ajax/training_partners_ajax.php',
            type: 'POST',
            data: {
                action: 'get',
                partner_id: id
            },
            success: function(response) {
                if (response.success) {
                    const partner = response.data;
                    $('#partner_id').val(partner.partner_id);
                    $('#partner_name').val(partner.partner_name);
                    $('#contact_person').val(partner.contact_person);
                    $('#email').val(partner.email);
                    $('#phone').val(partner.phone);
                    $('#address').val(partner.address);
                    $('#website').val(partner.website);
                    $('#status').val(partner.status); // Keep existing status
                    $('#partnerModal').modal('show');
                    $('.modal-title').text('Edit Training Partner');
                } else {
                    toastr.error(response.message || 'Error fetching partner details');
                }
            },
            error: function() {
                toastr.error('Error fetching partner details');
            }
        });
    });

    // Delete Partner Button Click
    let deleteId = null;
    $('#partnersTable').on('click', '.delete-partner', function() {
        deleteId = $(this).data('id');
        $('#deleteModal').modal('show');
    });

    // Handle delete confirmation
    $('#confirmDelete').click(function() {
        if (deleteId) {
            $.ajax({
                url: 'inc/ajax/training_partners_ajax.php',
                type: 'POST',
                data: {
                    action: 'delete',
                    partner_id: deleteId
                },
                success: function(response) {
                    if (response.success) {
                        $('#deleteModal').modal('hide');
                        partnersTable.ajax.reload();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message || 'Error deleting partner');
                    }
                },
                error: function() {
                    toastr.error('Error deleting partner');
                }
            });
        }
    });

    // Reset form when modal is closed
    $('#partnerModal').on('hidden.bs.modal', function() {
        $('#partnerForm')[0].reset();
        $('#partner_id').val('');
        $('#status').val('active'); // Reset status to active
        $('.modal-title').text('Add New Training Partner');
    });

    // Configure Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 3000,
        preventDuplicates: true
    };
}); 