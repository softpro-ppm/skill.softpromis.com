// JS for populating scheme, sector, and course dropdowns in Add/Edit Batch modal
$(document).ready(function() {
    function loadSchemes(selectedId) {
        var centerId = $('#center_id').val();
        if (!centerId) {
            var $scheme = $('#scheme_id');
            $scheme.empty().append('<option value="">Select Scheme</option>');
            return;
        }
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: { action: 'list', center_id: centerId },
            dataType: 'json',
            success: function(res) {
                var $scheme = $('#scheme_id');
                $scheme.empty();
                if(res.data && res.data.length) {
                    $scheme.append('<option value="">Select Scheme</option>');
                    $.each(res.data, function(i, s) {
                        if(s.center_id == centerId && s.status === 'active') {
                            $scheme.append(`<option value="${s.scheme_id}"${selectedId==s.scheme_id?' selected':''}>${s.scheme_name}</option>`);
                        }
                    });
                }
                if($scheme.children('option').length === 0) {
                    $scheme.append('<option value="">No schemes found for this center</option>');
                }
            }
        });
    }
    function loadSectors(schemeId, selectedId) {
        var centerId = $('#center_id').val();
        $.ajax({
            url: 'inc/ajax/sectors_ajax.php',
            type: 'POST',
            data: { action: 'list', scheme_id: schemeId, center_id: centerId },
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
    // Helper: Show 'Processing...' in a select
    function showProcessing($select, text) {
        $select.empty().append(`<option>${text||'Processing...'}</option>`);
    }
    // On modal open, reset all selects
    $('#addBatchBtn, .edit-batch-btn').on('click', function() {
        loadSchemes();
        $('#sector_id').empty().append('<option value="">Select Sector</option>');
        $('#course_id').empty().append('<option value="">Select Course</option>');
    });
    // On center change, load schemes and reset children
    $('#center_id').on('change', function() {
        showProcessing($('#scheme_id'));
        showProcessing($('#sector_id'));
        showProcessing($('#course_id'));
        loadSchemes();
    });
    // On scheme change, load sectors and reset course
    $('#scheme_id').on('change', function() {
        showProcessing($('#sector_id'));
        showProcessing($('#course_id'));
        var schemeId = $(this).val();
        loadSectors(schemeId);
    });
    // On sector change, load courses
    $('#sector_id').on('change', function() {
        showProcessing($('#course_id'));
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
