// JS for populating scheme, sector, and course dropdowns in Add/Edit Batch modal
$(document).ready(function() {
    function loadSchemes(selectedId) {
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: { action: 'list' },
            dataType: 'json',
            success: function(res) {
                var $scheme = $('#scheme_id');
                $scheme.empty().append('<option value="">Select Scheme</option>');
                if(res.data && res.data.length) {
                    $.each(res.data, function(i, s) {
                        $scheme.append(`<option value="${s.scheme_id}"${selectedId==s.scheme_id?' selected':''}>${s.scheme_name}</option>`);
                    });
                }
            }
        });
    }
    function loadSectors(schemeId, selectedId) {
        $.ajax({
            url: 'inc/ajax/sectors_ajax.php',
            type: 'POST',
            data: { action: 'list', scheme_id: schemeId },
            dataType: 'json',
            success: function(res) {
                var $sector = $('#sector_id');
                $sector.empty().append('<option value="">Select Sector</option>');
                if(res.data && res.data.length) {
                    $.each(res.data, function(i, s) {
                        $sector.append(`<option value="${s.sector_id}"${selectedId==s.sector_id?' selected':''}>${s.sector_name}</option>`);
                    });
                }
            }
        });
    }
    function loadCourses(schemeId, sectorId, selectedId) {
        $.ajax({
            url: 'inc/ajax/courses_ajax.php',
            type: 'POST',
            data: { action: 'list', scheme_id: schemeId, sector_id: sectorId },
            dataType: 'json',
            success: function(res) {
                var $course = $('#course_id');
                $course.empty().append('<option value="">Select Course</option>');
                if(res.data && res.data.length) {
                    $.each(res.data, function(i, c) {
                        $course.append(`<option value="${c.course_id}"${selectedId==c.course_id?' selected':''}>${c.course_name}</option>`);
                    });
                }
            }
        });
    }
    // On modal open, load schemes
    $('#addBatchBtn, .edit-batch-btn').on('click', function() {
        loadSchemes();
        $('#sector_id').empty().append('<option value="">Select Sector</option>');
        $('#course_id').empty().append('<option value="">Select Course</option>');
    });
    // On scheme change, load sectors
    $('#scheme_id').on('change', function() {
        var schemeId = $(this).val();
        loadSectors(schemeId);
        $('#course_id').empty().append('<option value="">Select Course</option>');
    });
    // On sector change, load courses
    $('#sector_id').on('change', function() {
        var schemeId = $('#scheme_id').val();
        var sectorId = $(this).val();
        loadCourses(schemeId, sectorId);
    });
    // On modal open, clear scheme dropdown
    $('#addSectorModal').on('show.bs.modal', function() {
        $('#scheme_id').empty().append('<option value="">Select Scheme</option>');
    });
    // On training center change, load schemes
    $('#center_id').on('change', function() {
        var centerId = $(this).val();
        console.log('Selected center_id:', centerId); // Debug: show selected center_id
        if (!centerId) {
            $('#scheme_id').empty().append('<option value="">Select Scheme</option>');
            return;
        }
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: { action: 'list_by_center', center_id: centerId },
            dataType: 'json',
            beforeSend: function() {
                console.log('Requesting schemes for center_id:', centerId);
            },
            success: function(res) {
                console.log('Schemes AJAX response:', res); // Debug: show response
                var $scheme = $('#scheme_id');
                $scheme.empty().append('<option value="">Select Scheme</option>');
                if(res.success && res.data && res.data.length) {
                    $.each(res.data, function(i, s) {
                        $scheme.append(`<option value="${s.scheme_id}">${s.scheme_name}</option>`);
                    });
                } else {
                    $scheme.append('<option value="">No schemes found for this center</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading schemes:', error);
                $('#scheme_id').empty().append('<option value="">Error loading schemes</option>');
            }
        });
    });
});
