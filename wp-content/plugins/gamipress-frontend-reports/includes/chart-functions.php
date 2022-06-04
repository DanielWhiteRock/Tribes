<?php
/**
 * Chart Functions
 *
 * @package     GamiPress\Frontend_Reports\Chart_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the available chart styles
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_frontend_reports_get_chart_styles() {

    /**
     * Filter the default chart styles
     *
     * @since 1.0.0
     *
     * @param array     $styles        Chart styles options
     *
     * @return array
     */
    return apply_filters( 'gamipress_frontend_reports_chart_styles', array(
        'inline'            => __( 'Inline', 'gamipress-frontend-reports' ),
        'line'              => __( 'Line', 'gamipress-frontend-reports' ),
        'bar'               => __( 'Vertical Bar', 'gamipress-frontend-reports' ),
        'horizontal-bar'    => __( 'Horizontal Bar', 'gamipress-frontend-reports' ),
        'radar'             => __( 'Radar', 'gamipress-frontend-reports' ),
        'doughnut'          => __( 'Doughnut', 'gamipress-frontend-reports' ),
        'pie'               => __( 'Pie', 'gamipress-frontend-reports' ),
        'polar'             => __( 'Polar Area', 'gamipress-frontend-reports' ),
    ) );

}

/**
 * Get a set of predefined chart colors
 *
 * @since 1.0.0
 *
 * @param float $opacity Colors opacity (min 0.01, max 1.0)
 *
 * @return array
 */
function gamipress_frontend_reports_get_chart_colors( $opacity = 1.0 ) {

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
     * Filter the set of predefined chart colors
     *
     * @since 1.0.0
     *
     * @param mixed     $colors     Chart colors options
     * @param float     $opacity    Colors opacity (min 0.01, max 1.0)
     *
     * @return array
     */
    return apply_filters( 'gamipress_frontend_reports_chart_colors', $colors, $opacity );

}

/**
 * Get the chart options
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_frontend_reports_get_chart_options() {

    /**
     * Filter the default chart options
     *
     * @since 1.0.0
     *
     * @param array     $options        Chart options to be passed to javascript
     *
     * @return array
     */
    return apply_filters( 'gamipress_frontend_reports_chart_options', array(
        'line' => array(
            'scales' => array(
                'xAxes' => array(
                    array(
                        'ticks' => array(
                            'display' => true,
                            'beginAtZero' => true,
                        ),
                        'gridLines' => array(
                            'color' => 'rgba(0, 0, 0, 0.1)'
                        ),
                    )
                ),
                'yAxes' => array(
                    array(
                        'ticks' => array(
                            'display' => true,
                            'beginAtZero' => true,
                            //'maxTicksLimit' => 3, // Limit the number of ticks
                        ),
                        'gridLines' => array(
                            'color' => 'rgba(0, 0, 0, 0.1)'
                        ),
                    )
                ),
            ),
            'legend' => array(
                'display' => false
            ),
            'elements' => array(
                'rectangle' => array(
                    'borderWidth' => 3,
                ),
            ),
        ),
        'bar' => array(
            'scales' => array(
                'xAxes' => array(
                    array(
                        'ticks' => array(
                            'display' => true,
                            'beginAtZero' => true,
                        ),
                        'gridLines' => array(
                            'color' => 'rgba(0, 0, 0, 0.1)'
                        ),
                    )
                ),
                'yAxes' => array(
                    array(
                        'ticks' => array(
                            'display' => true,
                            'beginAtZero' => true,
                            //'maxTicksLimit' => 3, // Limit the number of ticks
                        ),
                        'gridLines' => array(
                            'color' => 'rgba(0, 0, 0, 0.1)'
                        ),
                    )
                ),
            ),
            'legend' => array(
                'display' => false
            ),
            'elements' => array(
                'rectangle' => array(
                    'borderWidth' => 3,
                ),
            ),
        ),
        'horizontalBar' => array(
            'scales' => array(
                'xAxes' => array(
                    array(
                        'ticks' => array(
                            'display' => true,
                            'beginAtZero' => true,
                            //'maxTicksLimit' => 3, // Limit the number of ticks
                        ),
                        'gridLines' => array(
                            'color' => 'rgba(0, 0, 0, 0.1)'
                        ),
                    )
                ),
                'yAxes' => array(
                    array(
                        'ticks' => array(
                            'display' => true,
                            'beginAtZero' => true,
                        ),
                        'gridLines' => array(
                            'color' => 'rgba(0, 0, 0, 0.1)'
                        ),
                    )
                ),
            ),
            'legend' => array(
                'display' => false
            ),
            'elements' => array(
                'rectangle' => array(
                    'borderWidth' => 3,
                ),
            ),
        ),
        'radar' => array(
            'scale' => array(
                'ticks' => array(
                    'display' => true,
                    'beginAtZero' => true,
                    //'maxTicksLimit' => 3, // Limit the number of ticks
                ),
                'gridLines' => array(
                    'color' => 'rgba(0, 0, 0, 0.1)'
                ),
                'angleLines' => array(
                    'color' => 'rgba(0, 0, 0, 0.1)'
                ),
            ),
            'legend' => array(
                'display' => false
            ),
            'elements' => array(
                'line' => array(
                    'borderWidth' => 3,
                ),
            ),
        ),
        'doughnut' => array(
            'legend' => array(
                'display' => false
            ),
        ),
        'pie' => array(
            'legend' => array(
                'display' => false
            ),
        ),
        'polarArea' => array(
            'scale' => array(
                'ticks' => array(
                    'display' => true,
                    'beginAtZero' => true,
                    //'maxTicksLimit' => 3, // Limit the number of ticks
                ),
                'gridLines' => array(
                    'color' => 'rgba(0, 0, 0, 0.1)'
                ),
                'angleLines' => array(
                    'color' => 'rgba(0, 0, 0, 0.1)'
                ),
            ),
            'legend' => array(
                'display' => false
            ),
            'elements' => array(
                'line' => array(
                    'borderWidth' => 3,
                ),
            ),
        ),
    ) );

}

/**
 * Setup an array of color list given
 *
 * @since 1.0.0
 *
 * @param string $colors_list A color list like: "rgba(0,0,0,0),rgb(0,0,0),#000,transparent"
 *
 * @return array
 */
function gamipress_frontend_reports_split_colors( $colors_list ) {

    // For "#000" colors change ",#" by "-#"
    $colors_list = str_replace( ',#', '-#', $colors_list );
    // For "rgb()" and "rgba()" colors change ",r" by "-r"
    $colors_list = str_replace( ',r', '-r', $colors_list );
    // For "transparent" color change ",t" by "-t"
    $colors_list = str_replace( ',t', '-t', $colors_list );

    // Split colors by -
    return explode( '-', $colors_list );

}


/**
 * Setup an array of stats to being used in charts
 *
 * @since 1.0.0
 *
 * @param array     $stats      Array of stats to being passed to the chart template
 * @param string    $shortcode  Shortcode that processed the stats
 * @param array     $atts       Shortcode attributes
 *
 * @return array
 */
function gamipress_frontend_reports_process_stats( $stats, $shortcode, $atts ) {

    // Setup the multiples backgrounds and border colors
    $backgrounds = gamipress_frontend_reports_split_colors( $atts['background'] );
    $borders = gamipress_frontend_reports_split_colors( $atts['border'] );

    // Setup the background and border colors
    foreach( $stats as $i => $stat ) {

        if( ! isset( $stat['background'] ) ) {

            $background = ( isset( $backgrounds[$i] ) ? $backgrounds[$i] : $backgrounds[0] );

            /**
             * Filter the stat background color
             *
             * @since 1.0.0
             *
             * @param string    $background     The stat background color
             * @param int       $user_id        The user ID
             * @param string    $style          The chart style
             * @param array     $stat           Array with the stat setup ( name, slug, and value )
             * @param string    $shortcode      Shortcode that processed the stats
             * @param array     $atts           Shortcode attributes
             *
             * @return string
             */
            $background = apply_filters( 'gamipress_frontend_reports_stat_background', $background, $atts['user_id'], $atts['style'], $stat, $shortcode, $atts );

            $stat['background'] = $background;
        }

        if( ! isset( $stat['border'] ) ) {

            $border = ( isset( $borders[$i] ) ? $borders[$i] : $borders[0] );

            /**
             * Filter the stat border color
             *
             * @since 1.0.0
             *
             * @param string    $border         The stat border color
             * @param int       $user_id        The user ID
             * @param string    $style          The chart style
             * @param array     $stat           Array with the stat setup ( name, slug, and value )
             * @param string    $shortcode      Shortcode that processed the stats
             * @param array     $atts           Shortcode attributes
             *
             * @return string
             */
            $border = apply_filters( 'gamipress_frontend_reports_stat_border', $border, $atts['user_id'], $atts['style'], $stat, $shortcode, $atts );

            $stat['border'] = $border;
        }

        $stats[$i] = $stat;
    }

    // Return an array with all stats information (name, slug, base, increment, value, background and border)
    /**
     * Filter the shortcode stats
     *
     * @since 1.0.0
     *
     * @param array     $stats          Array with the stats setup
     * @param string    $shortcode      Shortcode that processed the stats
     * @param array     $atts           Shortcode attributes
     *
     * @return array
     */
    return  apply_filters( 'gamipress_frontend_reports_stats', $stats, $shortcode, $atts );

}

/**
 * Get the minimum allowed stats for all chart styles
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_frontend_reports_get_chart_style_minimum_stats() {

    /**
     * Filter the minimum allowed stats for all chart styles
     *
     * @since 1.0.0
     *
     * @param array     $styles        Array with chart style as key and minimum stats as value
     *
     * @return array
     */
    return apply_filters( 'gamipress_frontend_reports_chart_style_minimum_stats', array(
        'inline'            => 1,
        'line'              => 1,
        'bar'               => 1,
        'horizontal-bar'    => 1,
        'radar'             => 3,
        'doughnut'          => 2,
        'pie'               => 2,
        'polar'             => 2,
    ) );

}

/**
 * Check if stats given meet the minimum allowed for a chart style
 *
 * @since 1.0.0
 *
 * @param array     $stats        Stats
 * @param string    $style        Chart style
 *
 * @return bool
 */
function gamipress_frontend_reports_stats_meets_chart_style_minimum( $stats, $style ) {

    $minimums = gamipress_frontend_reports_get_chart_style_minimum_stats();

    return ( isset( $minimums[$style] ) && count( $stats ) >= $minimums[$style] );

}