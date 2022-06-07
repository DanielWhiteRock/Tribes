function gamipress_reports_init_chart( canvas, config ) {

    // Initialize our global object
    if( window.gamipress_reports === undefined ) {
        window.gamipress_reports = {
            charts: {}
        };
    }

    var chart_id = canvas.attr('id');
    var chart = canvas.closest('.gamipress-reports-chart');

    // Setup chart title and subtitle
    if( config.title !== undefined )
        chart.find('.gamipress-reports-chart-title strong').html( config.title );

    if( config.subtitle !== undefined )
        chart.find('.gamipress-reports-chart-title span').html( config.subtitle );

    // Setup date navigation
    if( config.prev_date !== undefined )
        chart.find('.gamipress-reports-chart-prev-date').data( 'date', config.prev_date );

    if( config.next_date !== undefined )
        chart.find('.gamipress-reports-chart-next-date').data( 'date', config.next_date );


    // Check chart counters
    if( config.counters !== undefined ) {

        var counter_keys = Object.keys( config.counters );

        for( var i = 0; i < counter_keys.length; i++ ) {
            var counter_key = counter_keys[i];

            if( config.counters[counter_key].label !== undefined )
                chart.find('.gamipress-reports-chart-counter-' + counter_key + ' .gamipress-reports-chart-counter-label').html( config.counters[counter_key].label );

            if( config.counters[counter_key].count !== undefined )
                chart.find('.gamipress-reports-chart-counter-' + counter_key + ' .gamipress-reports-chart-counter-count').html( config.counters[counter_key].count );

            if( config.counters[counter_key].difference !== undefined )
                chart.find('.gamipress-reports-chart-counter-' + counter_key + ' .gamipress-reports-chart-counter-difference').html( config.counters[counter_key].difference );
        }
    }

    if( window.gamipress_reports.charts[chart_id] === undefined ) {

        // Create the chart instance
        window.gamipress_reports.charts[chart_id] = new Chart(canvas[0].getContext('2d'), {
            type: canvas.data('type'),
            data: config.stats,
            options: {
                tooltips: {
                    callbacks: {
                        title: function (tooltip, data) {
                            tooltip = tooltip[0];

                            if( data.datasets[tooltip.datasetIndex].labels !== undefined ) {
                                return data.datasets[tooltip.datasetIndex].labels[tooltip.index]
                            }

                            return tooltip.xLabel;
                        }
                    }
                }
            }
        });

    } else {

        // Update the chart
        window.gamipress_reports.charts[chart_id].data = config.stats;

        window.gamipress_reports.charts[chart_id].update();

    }
}

(function( $ ) {

    function gamipress_reports_refresh_chart( chart ) {

        gamipress_reports_chart_add_loader( chart );

        // Setup vars
        var args = chart.data('args');

        // Turn query args into an object
        args = $.parseJSON( args.split("'").join('"') );

        $.ajax({
            url: ajaxurl,
            cache: false,
            data: {
                action: 'gamipress_reports_chart_request',
                nonce: gamipress_reports_chart.nonce,
                args: args,
                date_range: chart.find('.gamipress-reports-chart-range.active').data('range'),
                date: chart.find('input[name="date"]').val(),
            },
            success: function( response ) {

                gamipress_reports_init_chart( chart.find('canvas'), response.data );

                gamipress_reports_chart_remove_loader( chart );

            }
        });

    }

    function gamipress_reports_chart_add_loader( chart ) {
        chart.append('<div class="gamipress-reports-loader"><span class="spinner is-active gamipress-reports-spinner"></span></div>')
    }

    function gamipress_reports_chart_remove_loader( chart ) {
        chart.find('.gamipress-reports-loader').remove();
    }

    // Range click
    $('body').on( 'click', '.gamipress-reports-chart .gamipress-reports-chart-range', function(e) {

        var chart = $(this).closest('.gamipress-reports-chart');

        // Return if already running an ajax request
        if( chart.find('.gamipress-reports-loader').length ) {
            return;
        }

        // return if user clicked on active range
        if( $(this).hasClass('active') ) {
            return;
        }

        // Toggle range active class
        $(this).parent().find('.active').removeClass('active');
        $(this).addClass('active');

        gamipress_reports_refresh_chart( chart );

    });

    // Date click
    $('body').on( 'click', '.gamipress-reports-chart .gamipress-reports-chart-date', function(e) {

        var chart = $(this).closest('.gamipress-reports-chart');

        // Return if already running an ajax request
        if( chart.find('.gamipress-reports-loader').length ) {
            return;
        }

        // Update date input value
        chart.find('input[name="date"]').val( $(this).data('date') );

        gamipress_reports_refresh_chart( chart );

    });

})( jQuery );