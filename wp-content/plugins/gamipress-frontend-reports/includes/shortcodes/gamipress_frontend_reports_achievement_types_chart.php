<?php
/**
 * GamiPress Frontend Reports Achievement Types Chart Shortcode
 *
 * @package     GamiPress\Frontend_Reports\Shortcodes\Shortcode\GamiPress_Frontend_Reports_Achievement_Types_Chart
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_frontend_reports_achievement_types_chart] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_register_frontend_reports_achievement_types_chart_shortcode() {

    gamipress_register_shortcode( 'gamipress_frontend_reports_achievement_types_chart', array(
        'name'              => __( 'Achievement Types Chart', 'gamipress-frontend-reports' ),
        'description'       => __( 'Render a chart with user achievements earned of desired achievement type(s).', 'gamipress-frontend-reports' ),
        'icon'              => 'awards',
        'group'             => 'frontend_reports',
        'output_callback'   => 'gamipress_frontend_reports_achievement_types_chart_shortcode',
        'tabs' => array(
            'general' => array(
                'icon' => 'dashicons-admin-generic',
                'title' => __( 'General', 'gamipress-frontend-reports' ),
                'fields' => array(
                    'type',
                    'amount',
                    'current_user',
                    'user_id',
                ),
            ),
            'style' => array(
                'icon' => 'dashicons-admin-appearance',
                'title' => __( 'Style', 'gamipress-frontend-reports' ),
                'fields' => array(
                    'style',
                    'background',
                    'border',
                    'grid',
                    'max_ticks',
                    'legend',
                ),
            ),
        ),
        'fields'            => array(
            'type' => array(
                'name'          => __( 'Achievement Type(s)', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Choose the achievement type(s).', 'gamipress-frontend-reports' ),
                'type'          => 'advanced_select',
                'multiple'      => true,
                'classes' 	    => 'gamipress-selector',
                'attributes' 	=> array(
                    'data-placeholder' => __( 'Default: All', 'gamipress-frontend-reports' ),
                ),
                'options_cb'    => 'gamipress_options_cb_achievement_types',
                'default'       => 'all',
            ),
            'current_user' => array(
                'name'          => __( 'Current User', 'gamipress-frontend-reports' ),
                'description'   => __( 'Show achievements earned of the current logged in user.', 'gamipress-frontend-reports' ),
                'type' 		    => 'checkbox',
                'classes' 	    => 'gamipress-switch',
                'default' 	    => 'yes',
            ),
            'user_id' => array(
                'name'          => __( 'User', 'gamipress-frontend-reports' ),
                'description'   => __( 'Show achievements earned of a specific user.', 'gamipress-frontend-reports' ),
                'type'          => 'select',
                'default'       => '',
                'options_cb'    => 'gamipress_options_cb_users',
                'classes'       => 'gamipress-user-selector',
                'attributes'    => array(
                    'data-placeholder' => __( 'Select an user', 'gamipress-frontend-reports' ),
                )
            ),

            // Style

            'style' => array(
                'name'          => __( 'Style', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Choose how to render the chart.', 'gamipress-frontend-reports' ),
                'type'          => 'select',
                'options'       => gamipress_frontend_reports_get_chart_styles(),
                'default'       => 'inline'
            ),
            'legend' => array(
                'name'          => __( 'Chart legend', 'gamipress-frontend-reports' ),
                'description'   => __( 'Show chart legend.', 'gamipress-frontend-reports' ),
                'type' 		    => 'checkbox',
                'classes' 	    => 'gamipress-switch',
            ),
            'background' => array(
                'name'          => __( 'Background Color', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Chart statistics background color.', 'gamipress-frontend-reports' ),
                'shortcode_desc'    => __( 'Single or comma-separated list of colors to use as chart statistics background color. Accepts colors in format hex, rgb and rgba.', 'gamipress-frontend-reports' ),
                'type'          => 'colorpicker',
                'options'       => array( 'alpha' => true ),
                'default'       => gamipress_frontend_reports_get_chart_colors( 0.5 ),
                'repeatable' => true,
                'text'     => array(
                    'add_row_text' => __( 'Add Color', 'gamipress-frontend-reports' ),
                ),
            ),
            'border' => array(
                'name'          => __( 'Border Color', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Chart statistics border color.', 'gamipress-frontend-reports' ),
                'shortcode_desc'    => __( 'Single or comma-separated list of colors to use as chart statistics border color. Accepts colors in format hex, rgb and rgba.', 'gamipress-frontend-reports' ),
                'type'          => 'colorpicker',
                'options'       => array( 'alpha' => true ),
                'default'       => gamipress_frontend_reports_get_chart_colors(),
                'repeatable' => true,
                'text'     => array(
                    'add_row_text' => __( 'Add Color', 'gamipress-frontend-reports' ),
                ),
            ),
            'grid' => array(
                'name'          => __( 'Grid Lines Color', 'gamipress-frontend-reports' ),
                'desc'          => __( 'The grid lines color. Accepts colors in format hex, rgb and rgba.', 'gamipress-frontend-reports' ),
                'type'          => 'colorpicker',
                'options'       => array( 'alpha' => true ),
                'default'        => 'rgba(0,0,0,0.1)'
            ),
            'max_ticks' => array(
                'name'          => __( 'Maximum Number of Ticks', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Set the maximum number of ticks. Leave blank to auto-calculate them.', 'gamipress-frontend-reports' ),
                'type'          => 'text',
                'attributes'    => array(
                    'type' => 'number',
                    'step' => '1',
                ),
                'default'       => ''
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_register_frontend_reports_achievement_types_chart_shortcode' );

/**
 * Frontend Reports Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array    $atts       Shortcode attributes.
 * @param  string   $content    Shortcode content.
 *
 * @return string 	            HTML markup.
 */
function gamipress_frontend_reports_achievement_types_chart_shortcode( $atts = array(), $content = '' ) {

    global $gamipress_frontend_reports_template_args;
    $shortcode = 'gamipress_frontend_reports_achievement_types_chart';


    // Setup default attrs
    $atts = shortcode_atts( array(

        'type'              => 'all',
        'current_user'      => 'yes',
        'user_id'           => '0',
        'style'             => 'inline',
        'legend'            => 'no',
        'background'        => implode( ',', gamipress_frontend_reports_get_chart_colors( 0.5 ) ),
        'border'            => implode( ',', gamipress_frontend_reports_get_chart_colors() ),
        'grid'              => 'rgba(0,0,0,0.1)',
        'max_ticks'         => '',

    ), $atts, $shortcode );

    $types = explode( ',', $atts['type'] );
    $is_single_type = false;

    if( $atts['type'] === 'all') {
        $types = gamipress_get_achievement_types_slugs();
    } else if ( count( $types ) === 1 ) {
        $is_single_type = true;
    }

    // ---------------------------
    // Shortcode Errors
    // ---------------------------

    // Not type provided
    if( $atts['type'] === '' )
        return gamipress_shortcode_error( __( 'Please, provide the type attribute.', 'gamipress-frontend-reports' ), $shortcode );

    if( $is_single_type ) {

        // Check if achievement type is valid
        if ( ! in_array( $atts['type'], gamipress_get_achievement_types_slugs() ) )
            return gamipress_shortcode_error( __( 'The type provided isn\'t a valid registered achievement type.', 'gamipress-frontend-reports' ), $shortcode );

    } else if( $atts['type'] !== 'all' ) {

        // Let's check if all types provided are wrong
        $all_types_wrong = true;

        foreach( $types as $type ) {
            if ( in_array( $type, gamipress_get_achievement_types_slugs() ) )
                $all_types_wrong = false;
        }

        // just notify error if all types are wrong
        if( $all_types_wrong )
            return gamipress_shortcode_error( __( 'All types provided aren\'t valid registered achievement types.', 'gamipress-frontend-reports' ), $shortcode );

    }

    // Force to set current user as user ID
    if( $atts['current_user'] === 'yes' )
        $atts['user_id'] = get_current_user_id();

    // Not user ID provided
    if( $atts['current_user'] === 'no' && absint( $atts['user_id'] ) === 0 )
        return gamipress_shortcode_error( __( 'Please, provide the user_id attribute.', 'gamipress-frontend-reports' ), $shortcode );

    // Guests not supported
    if( absint( $atts['user_id'] ) === 0 ) return '';

    // ---------------------------
    // Shortcode Processing
    // ---------------------------

    // Initialize template args
    $gamipress_frontend_reports_template_args = array();

    // Ensure int values
    $atts['max_ticks'] = absint( $atts['max_ticks'] );

    $gamipress_frontend_reports_template_args = $atts;

    // Get the user stats ( name, slug, and value )
    $stats = array();

    foreach( $types as $type ) {

        $achievement_type = gamipress_get_achievement_type( $type );

        // Bail invalid achievement types
        if( ! $achievement_type ) continue;

        $user_id = absint( $atts['user_id'] );

        $stats[] = array(
            'name'  => $achievement_type['plural_name'],
            'slug'  => $type,
            'value' => count( gamipress_get_user_achievements( array( 'user_id' => $user_id, 'achievement_type' => $type ) ) )
        );

    }

    // Setup the label attribute
    $gamipress_frontend_reports_template_args['label'] = __( 'Earned', 'gamipress-frontend-reports' );

    // Bail if no stats
    if( empty( $stats ) )
        return gamipress_shortcode_error( __( 'Any type provided isn\'t a valid registered achievement type.', 'gamipress-frontend-reports' ), $shortcode );

    // Bail if stats provided don't meet the minimum required by the chart style
    if( ! gamipress_frontend_reports_stats_meets_chart_style_minimum( $stats, $atts['style'] ) )
        return gamipress_shortcode_error( __( 'You need to provide more achievement types for this chart style.', 'gamipress-frontend-reports' ), $shortcode );


    // Setup the stats template parameter
    $stats = gamipress_frontend_reports_process_stats( $stats, $shortcode, $atts );
    $gamipress_frontend_reports_template_args['stats'] = $stats;

    // Enqueue assets
    gamipress_frontend_reports_enqueue_scripts();

    // Render the chart
    ob_start();
    gamipress_get_template_part( 'chart', ( $is_single_type ? $atts['type'] : null ) );
    $output = ob_get_clean();

    /**
     * Filter to override shortcode output
     *
     * @since 1.0.0
     *
     * @param string    $output     Final output
     * @param array     $atts       Shortcode attributes
     * @param string    $content    Shortcode content
     */
    return apply_filters( 'gamipress_frontend_reports_achievement_types_chart_shortcode_output', $output, $atts, $content );

}
