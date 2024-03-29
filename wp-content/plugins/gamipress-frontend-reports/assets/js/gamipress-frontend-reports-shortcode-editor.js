(function( $ ) {

    // Current user field
    $( '#gamipress_frontend_reports_points_current_user, '
        + '#gamipress_frontend_reports_achievements_current_user, '
        + '#gamipress_frontend_reports_ranks_current_user, '

        + '#gamipress_frontend_reports_points_chart_current_user, '
        + '#gamipress_frontend_reports_points_types_chart_current_user, '
        + '#gamipress_frontend_reports_achievement_types_chart_current_user, '
        + '#gamipress_frontend_reports_rank_types_chart_current_user, '

        + '#gamipress_frontend_reports_points_graph_current_user, '
        + '#gamipress_frontend_reports_points_types_graph_current_user, '
        + '#gamipress_frontend_reports_achievement_types_graph_current_user, '
        + '#gamipress_frontend_reports_rank_types_graph_current_user'
    ).on('change', function() {
        var target = $(this).closest('.cmb-row').next(); // User ID field

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            if( target.closest('.cmb-tabs-wrap').length ) {
                // Just show if item tab is active
                if( target.hasClass('cmb-tab-active-item') ) {
                    target.slideDown();
                }
            } else {
                target.slideDown();
            }

            target.removeClass('cmb2-tab-ignore');
        }
    });

    // --------------------------------------------------------------
    // Charts
    // --------------------------------------------------------------

    var style_field_selector =
        '#gamipress_frontend_reports_points_chart_style, '
        + '#gamipress_frontend_reports_points_types_chart_style, '
        + '#gamipress_frontend_reports_achievement_types_chart_style, '
        + '#gamipress_frontend_reports_rank_types_chart_style';

    // Style field
    $(style_field_selector).on('change', function() {

        var style = $(this).val();

        // Get the style fields
        var target = $(this).closest('.cmb2-metabox').find(
            '.cmb-row[class*="-legend"], '
            + '.cmb-row[class*="-background"], '
            + '.cmb-row[class*="-border"], '
            + '.cmb-row[class*="-grid"], '
            + '.cmb-row[class*="-max-ticks"]'
        );

        if( style !== 'inline' ) {

            if( style === 'doughnut' || style === 'pie' ) {

                // If doughnut or pie styles don't need grid and max ticks
                $(this).closest('.cmb2-metabox').find(
                    '.cmb-row[class*="-grid"], '
                    + '.cmb-row[class*="-max-ticks"]'
                ).slideUp().addClass('cmb2-tab-ignore');

                // Update target
                target = $(this).closest('.cmb2-metabox').find(
                    '.cmb-row[class*="-legend"], '
                    + '.cmb-row[class*="-background"], '
                    + '.cmb-row[class*="-border"]'
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

    $(style_field_selector).trigger('change');

    var chart_shortcodes_selector =
        '#gamipress_frontend_reports_points_chart_wrapper, '
        + '#gamipress_frontend_reports_points_types_chart_wrapper, '
        + '#gamipress_frontend_reports_achievement_types_chart_wrapper, '
        + '#gamipress_frontend_reports_rank_types_chart_wrapper';

    // Parse shortcode atts that have the style fields
    $('body').on( 'gamipress_shortcode_attributes', chart_shortcodes_selector, function( e, args ) {

        // Delete background, border, grid and max_ticks if style is inline
        if( args.attributes.style === 'inline' ) {

            delete args.attributes.legend;
            delete args.attributes.background;
            delete args.attributes.border;
            delete args.attributes.grid;
            delete args.attributes.max_ticks;

        } else if( args.attributes.style === 'doughnut' || args.attributes.style === 'pie' ) {

            delete args.attributes.grid;
            delete args.attributes.max_ticks;

        }

        // Remove blank field generated by color pickers
        delete args.attributes[''];

    } );

    // --------------------------------------------------------------
    // Graphs
    // --------------------------------------------------------------

    var period_value_field_selector =
        '#gamipress_frontend_reports_points_graph_period_value, '
        + '#gamipress_frontend_reports_points_types_graph_period_value, '
        + '#gamipress_frontend_reports_achievement_types_graph_period_value, '
        + '#gamipress_frontend_reports_rank_types_graph_period_value';

    // Period value field
    $(period_value_field_selector).on('change', function() {

        var period = $(this).val();

        // Get the start and end period fields
        var target = $(this).closest('.cmb2-metabox').find(
            '.cmb-row[class*="-period-start"], '
            + '.cmb-row[class*="-period-end"]'
        );

        if( period === 'custom' ) {

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

    $(period_value_field_selector).trigger('change');

    var graph_shortcodes_selector =
        '#gamipress_frontend_reports_points_graph_wrapper, '
        + '#gamipress_frontend_reports_points_types_graph_wrapper, '
        + '#gamipress_frontend_reports_achievement_types_graph_wrapper, '
        + '#gamipress_frontend_reports_rank_types_graph_wrapper';

    // Parse shortcode atts that have the period fields
    $('body').on( 'gamipress_shortcode_attributes', graph_shortcodes_selector, function( e, args ) {

        // Delete period_start and period_end if period_value is not custom
        if( args.attributes.period_value !== 'custom' ) {

            delete args.attributes.period_start;
            delete args.attributes.period_end;

        }

        // Remove blank field generated by color pickers
        delete args.attributes[''];

    } );

})( jQuery );