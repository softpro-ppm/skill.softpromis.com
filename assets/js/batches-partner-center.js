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
        var $center = $('#center_id');
        $center.empty().append('<option value="">Processing...</option>').prop('disabled', true);
        if (!partnerId) {
            $center.empty().append('<option value="">Select Training Center</option>').prop('disabled', true);
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
                    $center.prop('disabled', false);
                } else {
                    $center.prop('disabled', true);
                }
            }
        });
    }
    // On modal open, load partners and clear/disable centers
    $('#addBatchBtn, .edit-batch-btn').on('click', function() {
        loadPartners();
        $('#center_id').empty().append('<option value="">Select Training Center</option>').prop('disabled', true);
    });
    // On partner change, load centers and reset children
    $('#partner_id').on('change', function() {
        var partnerId = $(this).val();
        loadCenters(partnerId);
        $('#center_id').empty().append('<option value="">Select Training Center</option>').prop('disabled', true);
        $('#scheme_id').empty().append('<option value="">Select Scheme</option>').prop('disabled', true);
        $('#sector_id').empty().append('<option>Please select a scheme first</option>').prop('disabled', true);
        $('#course_id').empty().append('<option>Please select a sector first</option>').prop('disabled', true);
    });
    // On center change, trigger scheme reload (handled by batches-scheme-sector.js)
});
