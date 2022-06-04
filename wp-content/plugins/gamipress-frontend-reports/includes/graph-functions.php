<?php
/**
 * Graph Functions
 *
 * @package     GamiPress\Frontend_Reports\Graph_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get a set of predefined graph colors
 *
 * @since 1.0.0
 *
 * @param float $opacity Colors opacity (min 0.01, max 1.0)
 *
 * @return array
 */
function gamipress_frontend_reports_get_graph_colors( $opacity = 1.0 ) {

    if( $opacity < 1 ) {
        $colors = array(
            'rgba(54,162,235,' . $opacity . ')',
            'rgba(255,99,132,' . $opacity . ')',
            'rgba(75,192,192,' . $opacity . ')',
            'rgba(255,205,86,' . $opacity . ')',
            'rgba(201,203,207,' . $opacity . ')'
        );
    } else {
        $colors = array(
            '#36a2eb',
            '#ff6384',
            '#4bc0c0',
            '#ffcd56',
            '#c9cbcf'
        );
    }

    /**
     * Filter the set of predefined graph colors
     *
     * @since 1.0.0
     *
     * @param mixed     $colors     Chart colors options
     * @param float     $opacity    Colors opacity (min 0.01, max 1.0)
     *
     * @return array
     */
    return apply_filters( 'gamipress_frontend_reports_graph_colors', $colors, $opacity );

}

/**
 * Gets registered time periods
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_frontend_reports_get_graph_time_periods() {

    /**
     * Filter registered time periods
     *
     * @since 1.0.0
     *
     * @param array $time_periods
     *
     * @return array
     */
    return apply_filters( 'gamipress_frontend_reports_graph_time_periods', array(
        'this-week'     => __( 'Current Week', 'gamipress' ),
        'past-week'     => __( 'Past Week', 'gamipress' ),
        'this-month'    => __( 'Current Month', 'gamipress' ),
        'past-month'    => __( 'Past Month', 'gamipress' ),
        'this-year'     => __( 'Current Year', 'gamipress' ),
        'past-year'     => __( 'Past Year', 'gamipress' ),
        'custom'        => __( 'Custom', 'gamipress' ),
    ) );

}

/**
 * Gets registered ranges
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_frontend_reports_get_graph_ranges() {

    /**
     * Filter registered ranges
     *
     * @since 1.0.0
     *
     * @param array $ranges
     *
     * @return array
     */
    return apply_filters( 'gamipress_frontend_reports_graph_ranges', array(
        'week'     => __( 'Week', 'gamipress' ),
        'month'    => __( 'Month', 'gamipress' ),
        'year'     => __( 'Year', 'gamipress' ),
    ) );

}

/**
 * Helper function to process periods setup from shortcodes or any form
 *
 * @since 1.0.0
 *
 * @param array $atts
 *
 * @return array
 */
function gamipress_frontend_reports_process_periods( $atts ) {

    // Date range (based on period given)
    if( $atts['period_value'] === 'custom' ) {
        $atts['period_start'] = gamipress_date( 'Y-m-d', $atts['period_start'] );
        $atts['period_end'] = gamipress_date( 'Y-m-d', $atts['period_end'] );
    } else {
        $date_range = gamipress_get_period_range( $atts['period_value'] );

        $atts['period_start'] = $date_range['start'];
        $atts['period_end'] = $date_range['end'];
    }

    return $atts;

}

/**
 * Generate an ID for graphs that not has one.
 *
 * @since  1.0.0
 *
 * @return string
 */
function gamipress_frontend_reports_generate_graph_id() {

    global $gamipress_frontend_reports_graph_ids;

    if( ! is_array( $gamipress_frontend_reports_graph_ids ) )
        $gamipress_frontend_reports_graph_ids = array();

    $id_pattern = 'graph-';
    $index = 1;

    // First ID
    $id = $id_pattern . $index;

    while( in_array( $id, $gamipress_frontend_reports_graph_ids ) ) {

        $index++;

        $id = $id_pattern . $index;
    }

    // Add the ID to the graph IDs list
    $gamipress_frontend_reports_graph_ids[] = $id;

    return $id;

}

/**
 * Helper function to format graph labels
 *
 * @param $labels array
 * @param string $range (week|month|year)
 *
 * @return array
 */
function gamipress_frontend_reports_format_graph_labels( $labels, $range ) {

    foreach( $labels as $key => $label ) {

        switch( $range ) {

            case 'week':
                // Jan 1
                $label = date( 'M j', strtotime( $label ) );
                break;
            case 'month':
                // For months, just show days 1, 5, 10, 15, 20, 25, {end day of month}
                $month_allowed_days = array( '1', '5', '10', '15', '20', '25' );
                $month_allowed_days[] = date( 'j', strtotime( 'last day of this month', strtotime( $label )  ) );

                // 1
                $current_label = date( 'j', strtotime( $label ) );

                if( in_array( $current_label, $month_allowed_days ) ) {
                    $label = $current_label;
                } else {
                    $label = '';
                }
                break;
            case 'year':
                // Jan
                $label = date( 'M', strtotime( $label ) );
                break;

        }

        $labels[$key] = $label;

    }

    return $labels;

}