(function( $ ) {

    function gamipress_reports_get_query_arg(name) {
        var regex, results;
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
        results = regex.exec(location.search);
        return results === null ?
            "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    // Utility to add a class to main meta box element (.postbox)
    $('.cmb2-wrap[class*="gamipress-reports-col-"]').each(function() {
        var custom_class = $(this).attr('class').replace('cmb2-wrap', '').replace('form-table', '').trim();

        $(this).closest('.postbox').addClass(custom_class);
    });

    var loaded = {};

    // Dashboard reports
    if( $('.gamipress-reports-dashboard').length ) {
        // On click on a tab, trigger the data load
        $('body').on( 'click', '.gamipress-reports-dashboard .nav-tab.opt-tab', function(e) {

            var tab = $(this).attr('id').replace('opt-tab-', '');

            if( loaded[tab] === undefined ) {
                loaded[tab] = true;

                $.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'gamipress_reports_load_dashboard_tab',
                        nonce: gamipress_reports_admin.nonce,
                        tab: tab,
                    },
                    success: function( response ) {

                        // Counters
                        $('#opt-content-' + tab + ' .gamipress-reports-widget').each(function() {
                            // Dynamically loads a widget content based on his parent id
                            // eg: for "points-circulation" will look the key "points_circulation" on given data
                            var key = $(this).parent().attr('id').replace(/-/g, '_');

                            if( response.data[key] !== undefined ) {
                                $(this).find('strong').html( response.data[key] );
                            }
                        });

                        // List tables
                        $('#opt-content-' + tab + ' .postbox[id$="-list-table"]').each(function() {
                            var key = $(this).attr('id').replace(/-/g, '_');

                            if( response.data[key] !== undefined ) {
                                $(this).find('div.inside').html( response.data[key] );
                            }
                        });
                    }
                });
            }
        } );
    }

    // Points reports
    if( $('.gamipress-reports-points').length ) {

        // On click on a tab, trigger the data load
        $('body').on( 'click', '.gamipress-reports-points .nav-tab.opt-tab', function(e) {

            var points_type = $(this).attr('id').replace('opt-tab-', '');

            if( loaded[points_type] === undefined ) {
                loaded[points_type] = true;

                $.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'gamipress_reports_load_points_tab',
                        nonce: gamipress_reports_admin.nonce,
                        points_type: points_type,
                    },
                    success: function( response ) {
                        // Counters
                        $('#' + points_type + '-circulation strong').html( response.data.circulation );
                        $('#' + points_type + '-awarded strong').html( response.data.awarded );
                        $('#' + points_type + '-deducted strong').html( response.data.deducted );
                        $('#' + points_type + '-expended strong').html( response.data.expended );

                        // List table
                        $('#' + points_type + '-list-table div').html( response.data.list_table );

                        // Charts
                        gamipress_reports_init_chart( $('#' + points_type + '-awarded-stats canvas'), response.data.awarded_chart );
                        gamipress_reports_init_chart( $('#' + points_type + '-deducted-stats canvas'), response.data.deducted_chart );
                        gamipress_reports_init_chart( $('#' + points_type + '-expended-stats canvas'), response.data.expended_chart );
                    }
                });
            }
        } );
    }

    // Achievements reports
    if( $('.gamipress-reports-achievements').length ) {

        // On click on a tab, trigger the data load
        $('body').on( 'click', '.gamipress-reports-achievements .nav-tab.opt-tab', function(e) {

            var achievement_type = $(this).attr('id').replace('opt-tab-', '');

            if( loaded[achievement_type] === undefined ) {
                loaded[achievement_type] = true;

                $.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'gamipress_reports_load_achievements_tab',
                        nonce: gamipress_reports_admin.nonce,
                        achievement_type: achievement_type,
                    },
                    success: function( response ) {

                        // Counters
                        $('#' + achievement_type + '-active strong').html( response.data.active );
                        $('#' + achievement_type + '-earned strong').html( response.data.earned );
                        $('#' + achievement_type + '-awarded strong').html( response.data.awarded );

                        // List table
                        $('#' + achievement_type + '-user-list-table div').html( response.data.user_list_table );
                        $('#' + achievement_type + '-list-table div').html( response.data.list_table );

                        // Chart
                        gamipress_reports_init_chart( $('#' + achievement_type + '-earned-stats canvas'), response.data.earned_chart );
                        gamipress_reports_init_chart( $('#' + achievement_type + '-awarded-stats canvas'), response.data.awarded_chart );
                    }
                });
            }
        } );
    }

    // Ranks reports
    if( $('.gamipress-reports-ranks').length ) {

        // On click on a tab, trigger the data load
        $('body').on( 'click', '.gamipress-reports-ranks .nav-tab.opt-tab', function(e) {

            var rank_type = $(this).attr('id').replace('opt-tab-', '');

            if( loaded[rank_type] === undefined ) {
                loaded[rank_type] = true;

                $.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'gamipress_reports_load_ranks_tab',
                        nonce: gamipress_reports_admin.nonce,
                        rank_type: rank_type,
                    },
                    success: function( response ) {

                        // Counters
                        $('#' + rank_type + '-active strong').html( response.data.active );
                        $('#' + rank_type + '-highest strong').html( response.data.highest );
                        $('#' + rank_type + '-lowest strong').html( response.data.lowest );

                        // List table
                        $('#' + rank_type + '-user-list-table div').html( response.data.user_list_table );
                        $('#' + rank_type + '-list-table div').html( response.data.list_table );

                        // Chart
                        gamipress_reports_init_chart( $('#' + rank_type + '-earned-stats canvas'), response.data.earned_chart );
                        gamipress_reports_init_chart( $('#' + rank_type + '-awarded-stats canvas'), response.data.awarded_chart );
                    }
                });
            }
        } );
    }

    // Initial load
    var tab = gamipress_reports_get_query_arg('tab').length ?  $('#' + gamipress_reports_get_query_arg('tab') ) : $('.nav-tab.opt-tab').first();

    tab.trigger('click');

})( jQuery );