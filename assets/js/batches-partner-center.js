// JS for populating center dropdown in Add/Edit Batch modal (Training Partner removed)
$(document).ready(function() {
    function loadPartners(selectedId) {
        $.ajax({
            url: 'inc/ajax/training_partners_ajax.php',
            type: 'POST',
            data: { action: 'list' },
            dataType: 'json',
            success: function(res) {
                var $partner = $('#partner_id');
                $partner.empty().append('<option value="">Select Training Partner</option>');
                if(res.data && res.data.length) {
                    $.each(res.data, function(i, p) {
                        $partner.append(`<option value="${p.partner_id}"${selectedId==p.partner_id?' selected':''}>${p.partner_name}</option>`);
                    });
                }
            }
        });
    }
    function loadCenters(partnerId, selectedId) {
        if (!partnerId) {
            $('#center_id').empty().append('<option value="">Select Training Center</option>');
            return;
        }
        $.ajax({
            url: 'inc/ajax/training-centers.php',
            type: 'POST',
            data: { action: 'list', partner_id: partnerId },
            dataType: 'json',
            success: function(res) {
                var $center = $('#center_id');
                $center.empty().append('<option value="">Select Training Center</option>');
                if(res.data && res.data.length) {
                    $.each(res.data, function(i, c) {
                        $center.append(`<option value="${c.center_id}"${selectedId==c.center_id?' selected':''}>${c.center_name}</option>`);
                    });
                }
            }
        });
    }
    // On modal open, load partners and clear centers
    $('#addBatchBtn, .edit-batch-btn').on('click', function() {
        loadPartners();
        $('#center_id').empty().append('<option value="">Select Training Center</option>');
    });
    // On partner change, load centers
    $('#partner_id').on('change', function() {
        var partnerId = $(this).val();
        loadCenters(partnerId);
        $('#center_id').trigger('change'); // To trigger scheme reload if needed
    });
    // On center change, trigger scheme reload
    $('#center_id').on('change', function() {
        $('#scheme_id').empty().append('<option value="">Select Scheme</option>');
        $('#scheme_id').trigger('change');
    });
});
