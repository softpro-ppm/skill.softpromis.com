// Dynamically populate Training Partner and Training Center dropdowns in Edit Student modal
window.populatePartners = function(selectedPartnerId) {
    $.ajax({
        url: 'inc/ajax/training_partners_ajax.php',
        type: 'POST',
        data: { action: 'list' },
        dataType: 'json',
        success: function(res) {
            var $partner = $('#editPartner');
            $partner.empty();
            $partner.append('<option value="">Select Training Partner</option>');
            if(res.success && res.data && res.data.length) {
                $.each(res.data, function(i, partner) {
                    $partner.append('<option value="' + partner.partner_id + '"' + (partner.partner_id == selectedPartnerId ? ' selected' : '') + '>' + partner.partner_name + '</option>');
                });
            }
        }
    });
};
window.populateCenters = function(partnerId, selectedCenterId) {
    if (!partnerId) {
        $('#editCenter').empty().append('<option value="">Select Training Center</option>');
        return;
    }
    $.ajax({
        url: 'inc/ajax/training_centers_ajax.php',
        type: 'POST',
        data: { action: 'list', partner_id: partnerId },
        dataType: 'json',
        success: function(res) {
            var $center = $('#editCenter');
            $center.empty();
            $center.append('<option value="">Select Training Center</option>');
            if(res.success && res.data && res.data.length) {
                $.each(res.data, function(i, center) {
                    $center.append('<option value="' + center.center_id + '"' + (center.center_id == selectedCenterId ? ' selected' : '') + '>' + center.center_name + '</option>');
                });
            }
        }
    });
};
$('#editPartner').on('change', function() {
    var partnerId = $(this).val();
    window.populateCenters(partnerId, '');
});