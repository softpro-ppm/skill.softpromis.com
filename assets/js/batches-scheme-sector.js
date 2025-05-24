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
                enableIfOptions($sector);
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
                enableIfOptions($course);
            }
        });
    }
    // Helper: Show 'Processing...' in a select
    function showProcessing($select, text) {
        $select.empty().append(`<option>${text||'Processing...'}</option>`);
        $select.prop('disabled', true);
    }
    // Helper: Enable select if options exist
    function enableIfOptions($select) {
        if ($select.children('option').length > 1) {
            $select.prop('disabled', false);
        } else {
            $select.prop('disabled', true);
        }
    }
    // On modal open, reset all selects and disable
    $('#addBatchBtn, .edit-batch-btn').on('click', function() {
        $('#scheme_id').empty().append('<option value="">Select Scheme</option>').prop('disabled', true);
        $('#sector_id').empty().append('<option>Please select a scheme first</option>').prop('disabled', true);
        $('#course_id').empty().append('<option>Please select a sector first</option>').prop('disabled', true);
    });
    // On center change, load schemes and reset children
    $('#center_id').on('change', function() {
        var centerId = $(this).val();
        if (!centerId) {
            $('#scheme_id').empty().append('<option value="">Select Scheme</option>').prop('disabled', true);
            $('#sector_id').empty().append('<option>Please select a scheme first</option>').prop('disabled', true);
            $('#course_id').empty().append('<option>Please select a sector first</option>').prop('disabled', true);
            return;
        }
        showProcessing($('#scheme_id'));
        $('#sector_id').empty().append('<option>Please select a scheme first</option>').prop('disabled', true);
        $('#course_id').empty().append('<option>Please select a sector first</option>').prop('disabled', true);
        // Only use one loader for schemes
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: { action: 'list', center_id: centerId },
            dataType: 'json',
            success: function(res) {
                var $scheme = $('#scheme_id');
                $scheme.empty().append('<option value="">Select Scheme</option>');
                if(res.data && res.data.length) {
                    $.each(res.data, function(i, s) {
                        if(s.center_id == centerId && s.status === 'active') {
                            $scheme.append(`<option value="${s.scheme_id}">${s.scheme_name}</option>`);
                        }
                    });
                    $scheme.prop('disabled', false);
                } else {
                    $scheme.prop('disabled', true);
                }
            }
        });
    });
    // On scheme change, load sectors and reset course
    $('#scheme_id').on('change', function() {
        var schemeId = $(this).val();
        if (!schemeId) {
            $('#sector_id').empty().append('<option>Please select a scheme first</option>').prop('disabled', true);
            $('#course_id').empty().append('<option>Please select a sector first</option>').prop('disabled', true);
            return;
        }
        showProcessing($('#sector_id'));
        $('#course_id').empty().append('<option>Please select a sector first</option>').prop('disabled', true);
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
                        $sector.append(`<option value="${s.sector_id}">${s.sector_name}</option>`);
                    });
                    $sector.prop('disabled', false);
                } else {
                    $sector.prop('disabled', true);
                }
            }
        });
    });
    // On sector change, load courses
    $('#sector_id').on('change', function() {
        var schemeId = $('#scheme_id').val();
        var sectorId = $(this).val();
        if (!sectorId) {
            $('#course_id').empty().append('<option>Please select a sector first</option>').prop('disabled', true);
            return;
        }
        showProcessing($('#course_id'));
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
                        $course.append(`<option value="${c.course_id}">${c.course_name}</option>`);
                    });
                    $course.prop('disabled', false);
                } else {
                    $course.prop('disabled', true);
                }
            }
        });
    });
    // Add Sector Modal: filter scheme by center
    $('#addSectorModal').on('show.bs.modal', function() {
        var mainCenter = $('#center_id').val();
        var $center = $('#parent_center_id_sector');
        var $scheme = $('#parent_scheme_id_sector');
        // Copy or load all centers
        if ($('#center_id option').length > 1) {
            $center.empty();
            $('#center_id option').each(function() {
                $center.append($(this).clone());
            });
            $center.val(mainCenter);
        } else {
            // fallback: load all centers
            $.ajax({
                url: 'inc/ajax/training-centers.php',
                type: 'POST',
                data: { action: 'list' },
                dataType: 'json',
                success: function(res) {
                    $center.empty();
                    $center.append('<option value="">Select Training Center</option>');
                    if(res.data && res.data.length) {
                        $.each(res.data, function(i, c) {
                            $center.append(`<option value="${c.center_id}">${c.center_name}</option>`);
                        });
                    }
                    $center.val(mainCenter);
                }
            });
        }
        // Always filter scheme by selected center
        var centerId = mainCenter;
        $scheme.empty().append('<option value="">Processing...</option>').prop('disabled', true);
        if (!centerId) {
            $scheme.empty().append('<option value="">Select Scheme</option>').prop('disabled', true);
            return;
        }
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: { action: 'list', center_id: centerId },
            dataType: 'json',
            success: function(res) {
                $scheme.empty().append('<option value="">Select Scheme</option>');
                if(res.data && res.data.length) {
                    $.each(res.data, function(i, s) {
                        if(s.center_id == centerId && s.status === 'active') {
                            $scheme.append(`<option value="${s.scheme_id}">${s.scheme_name}</option>`);
                        }
                    });
                    $scheme.prop('disabled', false);
                } else {
                    $scheme.prop('disabled', true);
                }
            }
        });
    });
    // When center changes in Add Sector modal, filter scheme
    $('#parent_center_id_sector').on('change', function() {
        var centerId = $(this).val();
        var $scheme = $('#parent_scheme_id_sector');
        $scheme.empty().append('<option value="">Processing...</option>').prop('disabled', true);
        if (!centerId) {
            $scheme.empty().append('<option value="">Select Scheme</option>').prop('disabled', true);
            return;
        }
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: { action: 'list', center_id: centerId },
            dataType: 'json',
            success: function(res) {
                $scheme.empty().append('<option value="">Select Scheme</option>');
                if(res.data && res.data.length) {
                    $.each(res.data, function(i, s) {
                        if(s.center_id == centerId && s.status === 'active') {
                            $scheme.append(`<option value="${s.scheme_id}">${s.scheme_name}</option>`);
                        }
                    });
                    $scheme.prop('disabled', false);
                } else {
                    $scheme.prop('disabled', true);
                }
            }
        });
    });
    // Add Course Modal: cascading center -> scheme -> sector
    $('#addCourseModal').on('show.bs.modal', function() {
        var mainCenter = $('#center_id').val();
        var mainScheme = $('#scheme_id').val();
        var mainSector = $('#sector_id').val();
        var $center = $('#parent_center_id_course');
        var $scheme = $('#parent_scheme_id_course');
        var $sector = $('#parent_sector_id_course');
        // Copy or load all centers
        if ($('#center_id option').length > 1) {
            $center.empty();
            $('#center_id option').each(function() {
                $center.append($(this).clone());
            });
            $center.val(mainCenter);
        } else {
            // fallback: load all centers
            $.ajax({
                url: 'inc/ajax/training-centers.php',
                type: 'POST',
                data: { action: 'list' },
                dataType: 'json',
                success: function(res) {
                    $center.empty();
                    $center.append('<option value="">Select Training Center</option>');
                    if(res.data && res.data.length) {
                        $.each(res.data, function(i, c) {
                            $center.append(`<option value="${c.center_id}">${c.center_name}</option>`);
                        });
                    }
                    $center.val(mainCenter);
                }
            });
        }
        // Always filter scheme by selected center
        var centerId = mainCenter;
        $scheme.empty().append('<option value="">Processing...</option>').prop('disabled', true);
        $sector.empty().append('<option value="">Select Sector</option>').prop('disabled', true);
        if (!centerId) {
            $scheme.empty().append('<option value="">Select Scheme</option>').prop('disabled', true);
            $sector.empty().append('<option value="">Select Sector</option>').prop('disabled', true);
            return;
        }
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: { action: 'list', center_id: centerId },
            dataType: 'json',
            success: function(res) {
                $scheme.empty().append('<option value="">Select Scheme</option>');
                if(res.data && res.data.length) {
                    $.each(res.data, function(i, s) {
                        if(s.center_id == centerId && s.status === 'active') {
                            $scheme.append(`<option value="${s.scheme_id}"${mainScheme==s.scheme_id?' selected':''}>${s.scheme_name}</option>`);
                        }
                    });
                    $scheme.prop('disabled', false);
                } else {
                    $scheme.prop('disabled', true);
                }
                $scheme.trigger('change');
            }
        });
    });
    // When center changes in Add Course modal, filter scheme and reset sector
    $('#parent_center_id_course').on('change', function() {
        var centerId = $(this).val();
        var $scheme = $('#parent_scheme_id_course');
        var $sector = $('#parent_sector_id_course');
        $scheme.empty().append('<option value="">Processing...</option>').prop('disabled', true);
        $sector.empty().append('<option value="">Select Sector</option>').prop('disabled', true);
        if (!centerId) {
            $scheme.empty().append('<option value="">Select Scheme</option>').prop('disabled', true);
            $sector.empty().append('<option value="">Select Sector</option>').prop('disabled', true);
            return;
        }
        $.ajax({
            url: 'inc/ajax/schemes_ajax.php',
            type: 'POST',
            data: { action: 'list', center_id: centerId },
            dataType: 'json',
            success: function(res) {
                $scheme.empty().append('<option value="">Select Scheme</option>');
                if(res.data && res.data.length) {
                    $.each(res.data, function(i, s) {
                        if(s.center_id == centerId && s.status === 'active') {
                            $scheme.append(`<option value="${s.scheme_id}">${s.scheme_name}</option>`);
                        }
                    });
                    $scheme.prop('disabled', false);
                } else {
                    $scheme.prop('disabled', true);
                }
                $scheme.trigger('change');
            }
        });
    });
    // When scheme changes in Add Course modal, filter sector
    $('#parent_scheme_id_course').on('change', function() {
        var centerId = $('#parent_center_id_course').val();
        var schemeId = $(this).val();
        var $sector = $('#parent_sector_id_course');
        $sector.empty().append('<option value="">Processing...</option>').prop('disabled', true);
        if (!schemeId) {
            $sector.empty().append('<option value="">Select Sector</option>').prop('disabled', true);
            return;
        }
        $.ajax({
            url: 'inc/ajax/sectors_ajax.php',
            type: 'POST',
            data: { action: 'list', scheme_id: schemeId, center_id: centerId },
            dataType: 'json',
            success: function(res) {
                $sector.empty().append('<option value="">Select Sector</option>');
                if(res.data && res.data.length) {
                    $.each(res.data, function(i, s) {
                        $sector.append(`<option value="${s.sector_id}">${s.sector_name}</option>`);
                    });
                    $sector.prop('disabled', false);
                } else {
                    $sector.prop('disabled', true);
                }
            }
        });
    });
});
