// ------------------------------------------------------------------
// Charts
// ------------------------------------------------------------------

(function( $ ) {

    var charts_selector = '.gamipress-frontend-reports-chart:not([data-style="inline"]):not(.gamipress-frontend-reports-loaded)';

    // Loop all charts with any style
    $(charts_selector).each(function() {
        gamipress_frontend_reports_load_chart( this );
    });

    // Block preview
    $('body').on('DOMSubtreeModified', 'div[class^="wp-block-gamipress-frontend-reports-"][class$="-chart"]', function() {

        var elements = $(charts_selector);

        if( elements.length === 0 ) return;

        elements.each(function() {
            gamipress_frontend_reports_load_chart( this );
        });
    });

})( jQuery );

/**
 * Helper function to turn an hexadecimal color into a rgba
 *
 * @since 1.0.0
 *
 * @param {string} hex
 *
 * @returns {string}
 */
function gamipress_frontend_reports_hex_to_rgba( hex ) {

    // If hex color has been passed without # then add it
    if( hex.length === 6 || hex.length === 3 ) {
        hex = '#' + hex;
    }

    // Check if is an hex color
    if( hex.startsWith('#') && /^#([A-Fa-f0-9]{3}){1,2}$/.test(hex) ) {
        var c = hex.substring(1).split('');

        // Allow short colors like #abc as #aabbcc
        if( c.length === 3 ){
            c = [c[0], c[0], c[1], c[1], c[2], c[2]];
        }

        c = '0x' + c.join('');

        return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+',1)';
    }

    return hex;
}

/**
 * Function to initialize a chart
 *
 * @since 1.0.0
 *
 * @param {Object} element
 */
function gamipress_frontend_reports_load_chart( element ) {

    var $ = $ || jQuery;

    var $this = $(element);

    // Bail if is an inline stats or if this has been loaded
    if( $this.hasClass('gamipress-frontend-reports-chart-style-inline') || $this.hasClass('gamipress-frontend-reports-loaded') )
        return;

    // Add a class to stop dom changes detection
    $this.addClass( 'gamipress-frontend-reports-loaded' );

    var type = $this.data('style');

    // Setup the chart type
    switch( type ) {
        case 'horizontal-bar':
            type = 'horizontalBar';
            break;
        case 'polar':
            type = 'polarArea';
            break;
    }

    // Inset a canvas element
    $this.prepend('<div style="position: relative;"><canvas></canvas></div>');

    // --------------------------
    // Setup global options
    // --------------------------

    // Setup option based on chart type (options are passed from php)
    var options = gamipress_frontend_reports.chart_options[type];

    // Get chart options
    var grid = gamipress_frontend_reports_hex_to_rgba( $this.data('grid') );
    var max_ticks = parseInt( $this.data('max-ticks') );
    var legend = $this.data('legend');

    options.legend.display = ( legend === 'yes' );

    switch( type ) {
        case 'line':
        case 'bar':
            // Grid color
            options.scales.xAxes[0].gridLines.color = grid;
            options.scales.yAxes[0].gridLines.color = grid;

            // Max ticks
            if( max_ticks > 0 )
                options.scales.yAxes[0].ticks.maxTicksLimit = max_ticks;
            break;
        case 'horizontalBar':
            // Grid color
            options.scales.xAxes[0].gridLines.color = grid;
            options.scales.yAxes[0].gridLines.color = grid;

            // Max ticks
            if( max_ticks > 0 )
                options.scales.xAxes[0].ticks.maxTicksLimit = max_ticks;
            break;
        case 'radar':
            // Grid color
            options.scale.gridLines.color = grid;
            options.scale.angleLines.color = grid;

            // Max ticks
            if( max_ticks > 0 )
                options.scale.ticks.maxTicksLimit = max_ticks;

            // Temporal fix for Chart.js 2.8.0 issue: https://github.com/chartjs/Chart.js/issues/6188
            options. tooltips = {
                enabled: true,
                callbacks: {
                    label: function( tooltipItem, data ) {
                        return data.datasets[tooltipItem.datasetIndex].label + ' : ' + data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                    }
                }
            };
            break;
        case 'polarArea':
            // Grid color
            options.scale.gridLines.color = grid;
            options.scale.angleLines.color = grid;

            // Max ticks
            if( max_ticks > 0 )
                options.scale.ticks.maxTicksLimit = max_ticks;
            break;
    }

    // --------------------------
    // Setup datasets
    // --------------------------

    var labels = [];
    var data = [];
    var backgrounds = [];
    var borders = [];

    $this.find('.gamipress-frontend-reports-chart-stat').each(function() {

        labels.push( $(this).find('.gamipress-frontend-reports-chart-stat-name').text() );
        data.push( parseFloat( $(this).find('.gamipress-frontend-reports-chart-stat-value').text() ) );

        // Background and border colors
        backgrounds.push( gamipress_frontend_reports_hex_to_rgba( $(this).data('background') ) );
        borders.push( gamipress_frontend_reports_hex_to_rgba( $(this).data('border') ) );

    });

    // Instance a new chart
    new Chart( $this.find('canvas'), {
        type: type,
        data: {
            labels: labels,
            datasets: [{
                label: $this.data('label'),
                data: data,
                backgroundColor: backgrounds,
                borderColor: borders,
            }]
        },
        options: options,
    });

}

// ------------------------------------------------------------------
// Graphs
// ------------------------------------------------------------------

(function( $ ) {

    var charts_selector = '.gamipress-frontend-reports-graph';

    // Loop all charts with any style
    $(charts_selector).each(function() {
        gamipress_frontend_reports_load_graph( this );
    });

    // Block preview
    $('body').on('DOMSubtreeModified', 'div[class^="wp-block-gamipress-frontend-reports-"][class$="-graph"]', function() {

        var elements = $(charts_selector);

        if( elements.length === 0 ) return;

        elements.each(function() {
            gamipress_frontend_reports_load_graph( this );
        });
    });

    // On change period
    $('body').on( 'change', 'select#period', function() {

        var $this = $(this);
        var graph = $this.closest('.gamipress-frontend-reports-graph');

        var period = $this.val();

        var target = graph.find('#period_start:not([type="hidden"]), '
            + '#period_end:not([type="hidden"]), '
            + '#range:not([type="hidden"])');

        // Toggle date fields visibility
        if( period === 'custom' ) {
            target.slideDown();
        } else {
            target.slideUp();
        }

        // Update date fields
        if( period !== 'custom' ) {

            var date = new Date();

            var from = gamipress_frontend_reports_get_date_range( period, date );

            // Update from inputs
            graph.find('#period_start').val(from.start);
            graph.find('#period_start').attr('value', from.start);

            graph.find('#period_end').val(from.end);
            graph.find('#period_end').attr('value', from.end);

            // Update range input
            switch( period ) {
                case 'this-week':
                case 'past-week':
                    graph.find('#range').val('week');
                    break;
                case 'this-month':
                case 'past-month':
                    graph.find('#range').val('month');
                    break;
                case 'this-year':
                case 'past-year':
                    graph.find('#range').val('year');
                    break;
            }

            // Refresh chart
            gamipress_frontend_reports_load_graph( graph );

        }

    });

    // On change period start and end
    $('body').on( 'change', 'input[type="date"]#period_start, input[type="date"]#period_end', function() {

        var $this = $(this);
        var graph = $this.closest('.gamipress-frontend-reports-graph');

        var period = graph.find('#period').val();

        if( period === 'custom' ) {
            gamipress_frontend_reports_load_graph( graph );
        }

    });

    // On change range
    $('body').on( 'change', 'select#range', function() {

        var $this = $(this);
        var graph = $this.closest('.gamipress-frontend-reports-graph');

        var period = graph.find('#period').val();

        if( period === 'custom' ) {
            gamipress_frontend_reports_load_graph( graph );
        }

    });

})( jQuery );

/**
 * Function to initialize a chart
 *
 * @since 1.0.0
 *
 * @param {Object} element
 */
function gamipress_frontend_reports_load_graph( element ) {

    var $ = $ || jQuery;

    var $this = $(element);

    // Bail if graph is loading
    if( $this.hasClass('gamipress-frontend-reports-loading') )
        return;

    // Setup vars
    var graph_id = $this.attr('id');

    // Get period form fields
    var period = $this.find('#period').val();
    var period_start = $this.find('#period_start').val();
    var period_end = $this.find('#period_end').val();
    var range = $this.find('#range').val();

    // Get graph current period values
    var graph_period = $this.data('period');
    var graph_period_start = $this.data('period-start');
    var graph_period_end = $this.data('period-end');
    var graph_range = $this.data('range');

    // Bail if there isn't any changes has changed
    if( period === graph_period
        && period_start === graph_period_start
        && period_end === graph_period_end
        && range === graph_range ) {
        return;
    }

    // Show loading spinner
    $this.find('.gamipress-spinner').show();

    // Disable inputs
    $this.find('select, input[type="date"]').prop( 'disabled', true );

    // Add a class to stop dom changes detection
    $this.addClass( 'gamipress-frontend-reports-loading' );

    // Get graph options
    var grid = gamipress_frontend_reports_hex_to_rgba( $this.data('grid') );
    var max_ticks = parseInt( $this.data('max-ticks') );
    var legend = $this.data('legend');

    var options = {
        scales: {
            yAxes: [{
                gridLines: {
                    color: grid
                },
                ticks: {
                    maxTicksLimit: max_ticks
                }
            }],
            xAxes: [{
                gridLines: {
                    color: grid
                }
            }]
        },
        legend: {
            display:  ( legend === 'yes' )
        },
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
    };

    // Ajax data
    var data = {
        action: 'gamipress_frontend_reports_graph',
        nonce: gamipress_frontend_reports.nonce,
        graph: $this.data('graph'),
        current_user: $this.data('current-user'),
        user_id: $this.data('user-id'),
        grid: $this.data('grid'),
        max_ticks: $this.data('max-ticks'),
        legend: $this.data('legend'),
        background: $this.data('background'),
        border: $this.data('border'),
        period: period,
        period_start: period_start,
        period_end: period_end,
        range: range,
    };

    $this.find('.gamipress-frontend-reports-graph-filter').each(function() {
        data[$(this).attr('id')] = $(this).val();
    });

    $.ajax({
        url: gamipress_frontend_reports.ajaxurl,
        method: 'POST',
        data: data,
        dataType: 'json',
        success: function( response ) {

            // Initialize our global object
            if( window.gamipress_frontend_reports_graphs === undefined ) {
                window.gamipress_frontend_reports_graphs = {};
            }

            // Inset a canvas element
            if( ! $this.find('canvas').length )
                $this.append('<div style="position: relative;"><canvas></canvas></div>');

            if( window.gamipress_frontend_reports_graphs[graph_id] === undefined ) {

                // Create the chart instance
                window.gamipress_frontend_reports_graphs[graph_id] = new Chart( $this.find('canvas')[0].getContext('2d'), {
                    type: 'line',
                    data: response.data,
                    options: options
                });

            } else {

                // Update the graph
                window.gamipress_frontend_reports_graphs[graph_id].data = response.data;

                window.gamipress_frontend_reports_graphs[graph_id].update();

            }

            // Update graph data
            $this.data('period', period);
            $this.data('period-start', period_start);
            $this.data('period-end', period_end);
            $this.data('range', range);

            // Hide loading spinner
            $this.find('.gamipress-spinner').hide();

            // Enable inputs
            $this.find('select, input[type="date"]').prop( 'disabled', false );

            // Remove loading class
            $this.removeClass( 'gamipress-frontend-reports-loading' );
        }
    });

}

/**
 * Helper function to get a period date range
 *
 * @since 1.0.0
 *
 * @param {string} period
 * @param {Date} date
 *
 * @returns {string}
 */
function gamipress_frontend_reports_get_date_range( period, date ) {

    var start, end;

    switch( period ) {
        case 'past-week':
            date.setDate( date.getDate() - 7 );
            break;
        case 'past-month':
            date.setMonth( date.getMonth() - 1 );
            break;
        case 'past-year':
            date.setFullYear( date.getFullYear() - 1 );
            break;
    }

    switch( period ) {
        case 'this-week':
        case 'past-week':
            var first = date.getDate() - date.getDay() + 1; // First day is the day of the month - the day of the week
            var last = first + 6; // Last day is the first day + 6

            var first_date = new Date( date.setDate(first) );
            var last_date = new Date( date.setDate(last) );

            var first_month = gamipress_frontend_reports_format_month( first_date.getMonth() );
            var last_month = gamipress_frontend_reports_format_month( last_date.getMonth() );

            start = first_date.getFullYear() + '-' + first_month + '-' + ( '0' + first_date.getDate() ).slice(-2);
            end = last_date.getFullYear() + '-' + last_month + '-' + ( '0' + last_date.getDate() ).slice(-2);
            break;
        case 'this-month':
        case 'past-month':
            start = date.getFullYear() + '-' + gamipress_frontend_reports_format_month( date.getMonth() ) + '-01';

            date.setMonth( date.getMonth() + 1 );
            date.setDate( 0 );
            end = date.getFullYear() + '-' + gamipress_frontend_reports_format_month( date.getMonth() ) + '-' + ( '0' + date.getDate() ).slice(-2);
            break;
        case 'this-year':
        case 'past-year':
            start = date.getFullYear() + '-01-01';
            end = date.getFullYear() + '-12-31';
            break;
    }

    return {
        start: start,
        end: end,
    };

}

/**
 * Helper function to format a javascript date
 *
 * @since 1.0.0
 *
 * @param {number} month
 *
 * @returns {string}
 */
function gamipress_frontend_reports_format_month( month ) {
    return ( '0' + ( month + 1 ) ).slice( -2 );
}