(function( $ ) {

    // Style field
    $('body').on('change', 'select[id^="widget-gamipress_frontend_reports"][id$="[style]"]', function() {

        var style = $(this).val();

        // Get the style fields
        var target = $(this).closest('.cmb2-wrap').find(
            '.cmb-row[class*="legend"], '
            + '.cmb-row[class*="background"], '
            + '.cmb-row[class*="border"], '
            + '.cmb-row[class*="grid"], '
            + '.cmb-row[class*="max-ticks"]'
        );

        if( style !== 'inline' ) {

            if( style === 'doughnut' || style === 'pie' ) {

                // If doughnut or pie styles don't need grid and max ticks
                $(this).closest('.cmb2-wrap').find(
                    '.cmb-row[class*="grid"], '
                    + '.cmb-row[class*="max-ticks"]'
                ).slideUp().addClass('cmb2-tab-ignore');

                // Update target
                target = $(this).closest('.cmb2-wrap').find(
                    '.cmb-row[class*="legend"], '
                    + '.cmb-row[class*="background"], '
                    + '.cmb-row[class*="border"]'
                );
            }

            if( $(this).closest('.cmb-tabs-wrap').length ) {
                // Just show if item tab is active
                if( $(this).closest('.cmb-row').hasClass('cmb-tab-active-item') ) {
                    target.slideDown();

                    // Fix display issue on repeatable fields on tabs
                    target.find('.cmb-row.cmb-repeat-row').slideDown();
                }
            } else {
                target.slideDown();
            }

            target.removeClass('cmb2-tab-ignore');
        } else {
            target.slideUp().addClass('cmb2-tab-ignore');
        }

    });

    $('select[id^="widget-gamipress_frontend_reports"][id$="[style]"]').trigger('change');

    // Period value field
    $('body').on('change', 'select[id^="widget-gamipress_frontend_reports"][id$="[period_value]"]', function() {

        var style = $(this).val();

        // Get the start and end period fields
        var target = $(this).closest('.cmb2-wrap').find(
            '.cmb-row[class*="period-start"], '
            + '.cmb-row[class*="period-end"]'
        );

        if( style === 'custom' ) {

            if( $(this).closest('.cmb-tabs-wrap').length ) {
                // Just show if item tab is active
                if( $(this).closest('.cmb-row').hasClass('cmb-tab-active-item') ) {
                    target.slideDown();
                }
            } else {
                target.slideDown();
            }

            target.removeClass('cmb2-tab-ignore');
        } else {
            target.slideUp().addClass('cmb2-tab-ignore');
        }

    });

    $('select[id^="widget-gamipress_frontend_reports"][id$="[period_value]"]').trigger('change');

    // Initialize on widgets area
    $(document).on('widget-updated widget-added', function(e, widget) {

        // Style field
        widget.find( 'select[id^="widget-gamipress_frontend_reports"][id$="[style]"]').trigger('change');

        // Period value field
        widget.find( 'select[id^="widget-gamipress_frontend_reports"][id$="[period_value]"]').trigger('change');

        // Re-init color picker fields
        widget.find( '.color-picker, .cmb2-colorpicker' ).wpColorPicker();

    });

})( jQuery );