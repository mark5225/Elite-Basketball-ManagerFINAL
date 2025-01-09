jQuery(document).ready(function($) {
    // File upload handler
    function initMediaUploader(buttonClass, inputClass) {
        $(buttonClass).click(function(e) {
            e.preventDefault();
            
            const button = $(this);
            const input = button.siblings(inputClass);
            const preview = button.siblings('.image-preview');
            
            const mediaUploader = wp.media({
                title: 'Select Image',
                button: { text: 'Use this image' },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                input.val(attachment.id);
                preview.html(`<img src="${attachment.url}" style="max-width:100px; height:auto;">`);
            });
            
            mediaUploader.open();
        });
    }

    // Initialize media uploaders
    initMediaUploader('.ebm-upload-photo', '.ebm-photo-id');
    initMediaUploader('.ebm-upload-logo', '.ebm-logo-id');

    // Game Stats Form Handler
    $('#ebm-game-stats-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitButton = form.find('button[type="submit"]');
        submitButton.prop('disabled', true);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error saving stats');
                }
            },
            error: function() {
                alert('Server error occurred');
            },
            complete: function() {
                submitButton.prop('disabled', false);
            }
        });
    });

    // Delete Stats Handler
    $('.ebm-delete-stat').on('click', function() {
        if (!confirm('Are you sure you want to delete these stats?')) return;
        
        const button = $(this);
        const statId = button.data('stat-id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'ebm_delete_game_stats',
                stat_id: statId,
                nonce: ebmAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    button.closest('tr').fadeOut(400, function() {
                        $(this).remove();
                    });
                } else {
                    alert(response.data.message || 'Error deleting stats');
                }
            },
            error: function() {
                alert('Server error occurred');
            }
        });
    });

    // Edit Stats Handler
    $('.ebm-edit-stat').on('click', function() {
        const statId = $(this).data('stat-id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'ebm_get_game_stats',
                stat_id: statId,
                nonce: ebmAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    populateStatsForm(response.data);
                } else {
                    alert(response.data.message || 'Error loading stats');
                }
            },
            error: function() {
                alert('Server error occurred');
            }
        });
    });

    function populateStatsForm(data) {
        const form = $('#ebm-game-stats-form');
        form.find('[name="game_date"]').val(data.game_date);
        form.find('[name="opponent"]').val(data.opponent);
        form.find('[name="minutes_played"]').val(data.minutes_played);
        form.find('[name="points"]').val(data.points);
        form.find('[name="assists"]').val(data.assists);
        form.find('[name="rebounds"]').val(data.rebounds);
        form.find('[name="steals"]').val(data.steals);
        form.find('[name="blocks"]').val(data.blocks);
        form.find('[name="fg_made"]').val(data.fg_made);
        form.find('[name="fg_attempted"]').val(data.fg_attempted);
        form.find('[name="three_made"]').val(data.three_made);
        form.find('[name="three_attempted"]').val(data.three_attempted);
        form.find('[name="ft_made"]').val(data.ft_made);
        form.find('[name="ft_attempted"]').val(data.ft_attempted);
        
        form.append(`<input type="hidden" name="stat_id" value="${data.id}">`);
        form.find('button[type="submit"]').text('Update Stats');
        
        $('html, body').animate({
            scrollTop: form.offset().top - 100
        }, 500);
    }

    // Roster Management
    let rosterSortable = null;
    
    if ($('#ebm-roster-tbody').length) {
        rosterSortable = new Sortable($('#ebm-roster-tbody')[0], {
            animation: 150,
            handle: '.ebm-drag-handle',
            onEnd: function() {
                updateRosterOrder();
            }
        });
    }

    function updateRosterOrder() {
        const order = rosterSortable.toArray();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'ebm_update_roster_order',
                order: order,
                team_id: $('#post_ID').val(),
                nonce: ebmAdmin.nonce
            }
        });
    }

    // Stats Calculator
    $('.ebm-calc-fg-percentage').on('change', function() {
        const made = parseFloat($('#fg_made').val()) || 0;
        const attempted = parseFloat($('#fg_attempted').val()) || 0;
        
        if (attempted > 0) {
            const percentage = ((made / attempted) * 100).toFixed(1);
            $('#fg_percentage').text(percentage + '%');
        }
    });

    // Similar calculators for 3PT and FT percentages
    $('.ebm-calc-3pt-percentage').on('change', function() {
        const made = parseFloat($('#three_made').val()) || 0;
        const attempted = parseFloat($('#three_attempted').val()) || 0;
        
        if (attempted > 0) {
            const percentage = ((made / attempted) * 100).toFixed(1);
            $('#three_percentage').text(percentage + '%');
        }
    });

    $('.ebm-calc-ft-percentage').on('change', function() {
        const made = parseFloat($('#ft_made').val()) || 0;
        const attempted = parseFloat($('#ft_attempted').val()) || 0;
        
        if (attempted > 0) {
            const percentage = ((made / attempted) * 100).toFixed(1);
            $('#ft_percentage').text(percentage + '%');
        }
    });

    // Stats Validation
    $('#ebm-game-stats-form').on('submit', function(e) {
        const form = $(this);
        
        // Validate shooting stats
        const validateShooting = function(made, attempted, type) {
            made = parseInt(made) || 0;
            attempted = parseInt(attempted) || 0;
            
            if (made > attempted) {
                alert(`${type}: Made shots cannot be greater than attempted`);
                return false;
            }
            return true;
        };

        const fgMade = form.find('[name="fg_made"]').val();
        const fgAttempted = form.find('[name="fg_attempted"]').val();
        if (!validateShooting(fgMade, fgAttempted, 'Field Goals')) {
            e.preventDefault();
            return false;
        }

        const threeMade = form.find('[name="three_made"]').val();
        const threeAttempted = form.find('[name="three_attempted"]').val();
        if (!validateShooting(threeMade, threeAttempted, '3-Pointers')) {
            e.preventDefault();
            return false;
        }

        const ftMade = form.find('[name="ft_made"]').val();
        const ftAttempted = form.find('[name="ft_attempted"]').val();
        if (!validateShooting(ftMade, ftAttempted, 'Free Throws')) {
            e.preventDefault();
            return false;
        }

        // Validate points calculation
        const calculatedPoints = (parseInt(fgMade) * 2) + (parseInt(threeMade) * 3) + parseInt(ftMade);
        const enteredPoints = parseInt(form.find('[name="points"]').val()) || 0;
        
        if (calculatedPoints !== enteredPoints) {
            if (!confirm('Points entered does not match calculated points from shooting stats. Continue anyway?')) {
                e.preventDefault();
                return false;
            }
        }
    });
});