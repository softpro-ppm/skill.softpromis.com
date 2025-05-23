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
            },
            error: function(xhr, status, error) {
                console.error('Error loading partners:', error);
                toastr.error('Error loading training partners');
            }
        });
    }

    function loadCenters(partnerId, selectedId) {
        if (!partnerId) {
            $('#center_id').empty().append('<option value="">Select Training Center</option>');
            return;
        }

        // Show loading state
        var $center = $('#center_id');
        $center.empty().append('<option value="">Loading centers...</option>');

        $.ajax({
            url: 'inc/ajax/training_centers_ajax.php',
            type: 'POST',
            data: { 
                action: 'read', 
                partner_id: partnerId, 
                per_page: 1000 
            },
            dataType: 'json',
            success: function(res) {
                var $center = $('#center_id');
                $center.empty().append('<option value="">Select Training Center</option>');
                var centers = res.data && res.data.data ? res.data.data : [];
                if(res.success && centers.length) {
                    $.each(centers, function(i, c) {
                        $center.append(`<option value="${c.id}"${selectedId==c.id?' selected':''}>${c.name}</option>`);
                    });
                } else {
                    $center.append('<option value="">No centers found for this partner</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading centers:', error);
                $center.empty().append('<option value="">Error loading centers</option>');
                toastr.error('Error loading training centers');
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
