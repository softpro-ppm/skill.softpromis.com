// JS to dynamically populate Training Partner and Training Center dropdowns in Add Scheme modal
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
            url: 'inc/ajax/training_centers_ajax.php',
            type: 'POST',
            data: { action: 'list', partner_id: partnerId },
            dataType: 'json',
            success: function(res) {
                console.log('Centers AJAX response:', res); // Debug log
                var $center = $('#center_id');
                $center.empty().append('<option value="">Select Training Center</option>');
                if(res.data && res.data.length) {
                    $.each(res.data, function(i, c) {
                        $center.append(`<option value="${c.center_id}"${selectedId==c.center_id?' selected':''}>${c.center_name}</option>`);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading centers:', error);
                $('#center_id').empty().append('<option value="">Error loading centers</option>');
            }
        });
    }
    // When Add Scheme modal is shown, load partners
    $('#addSchemeModal').on('show.bs.modal', function() {
        loadPartners();
        $('#center_id').empty().append('<option value="">Select Training Center</option>');
    });
    // When partner changes, load centers
    $(document).on('change', '#partner_id', function() {
        var partnerId = $(this).val();
        loadCenters(partnerId);
    });
});
