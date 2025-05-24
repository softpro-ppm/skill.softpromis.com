// JS for populating center dropdown in Add/Edit Batch modal (Training Partner removed)
$(document).ready(function() {
    function loadCenters(selectedId) {
        $.ajax({
            url: 'inc/ajax/training_centers_ajax.php',
            type: 'POST',
            data: { action: 'list' },
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
    // On modal open, load centers
    $('#addBatchBtn, .edit-batch-btn').on('click', function() {
        loadCenters();
    });
});
