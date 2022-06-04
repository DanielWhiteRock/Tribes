<?php
/**
 * GamiPress Frontend Reports Points Chart Shortcode
 *
 * @package     GamiPress\Frontend_Reports\Shortcodes\Shortcode\GamiPress_Frontend_Reports_Points_Chart
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_frontend_reports_points_chart] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_register_frontend_reports_points_chart_shortcode() {

    gamipress_register_shortcode( 'gamipress_frontend_reports_points_chart', array(
        'name'              => __( 'Points Chart', 'gamipress-frontend-reports' ),
        'description'       => __( 'Render a chart with user points balances of a specific points type.', 'gamipress-frontend-reports' ),
        'icon'              => 'star-filled',
        'group'             => 'frontend_reports',
        'output_callback'   => 'gamipress_frontend_reports_points_chart_shortcode',
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
                'name'          => __( 'Points Type', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Choose the points type.', 'gamipress-frontend-reports' ),
                'type'          => 'advanced_select',
                'multiple'      => false,
                'option_all'    => false,
                'classes' 	    => 'gamipress-selector',
                'attributes' 	=> array(
                    'data-placeholder' => __( 'Choose a points type', 'gamipress-frontend-reports' ),
                ),
                'options_cb'    => 'gamipress_options_cb_points_types',
            ),
            'amount' => array(
                'name'          => __( 'Amount Type(s)', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Choose the amount type(s) to use as chart values.', 'gamipress-frontend-reports' ),
                'type'          => 'multicheck',
                'options'       => array(
                    'earned'    => __( 'Earned', 'gamipress-frontend-reports' ),
                    'deducted'  => __( 'Deducted', 'gamipress-frontend-reports' ),
                    'expended'  => __( 'Expended', 'gamipress-frontend-reports' ),
                )
            ),
            'current_user' => array(
                'name'          => __( 'Current User', 'gamipress-frontend-reports' ),
                'description'   => __( 'Show points balances of the current logged in user.', 'gamipress-frontend-reports' ),
                'type' 		    => 'checkbox',
                'classes' 	    => 'gamipress-switch',
                'default' 	    => 'yes',
            ),
            'user_id' => array(
                'name'          => __( 'User', 'gamipress-frontend-reports' ),
                'description'   => __( 'Show points balances of a specific user.', 'gamipress-frontend-reports' ),
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
                'name'              => __( 'Background Color', 'gamipress-frontend-reports' ),
                'desc'              => __( 'Chart statistics background color.', 'gamipress-frontend-reports' ),
                'shortcode_desc'    => __( 'Single or comma-separated list of colors to use as chart statistics background color. Accepts colors in format hex, rgb and rgba.', 'gamipress-frontend-reports' ),
                'type'              => 'colorpicker',
                'options'           => array( 'alpha' => true ),
                'default'           => gamipress_frontend_reports_get_chart_colors( 0.5 ),
                'repeatable'        => true,
                'text'              => array(
                    'add_row_text' => __( 'Add Color', 'gamipress-frontend-reports' ),
                ),
            ),
            'border' => array(
                'name'              => __( 'Border Color', 'gamipress-frontend-reports' ),
                'desc'              => __( 'Chart statistics border color.', 'gamipress-frontend-reports' ),
                'shortcode_desc'    => __( 'Single or comma-separated list of colors to use as chart statistics border color. Accepts colors in format hex, rgb and rgba.', 'gamipress-frontend-reports' ),
                'type'              => 'colorpicker',
                'options'           => array( 'alpha' => true ),
                'default'           => gamipress_frontend_reports_get_chart_colors(),
                'repeatable'        => true,
                'text'              => array(
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
add_action( 'init', 'gamipress_register_frontend_reports_points_chart_shortcode' );

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
function gamipress_frontend_reports_points_chart_shortcode( $atts = array(), $content = '' ) {

    global $gamipress_frontend_reports_template_args;
    $shortcode = 'gamipress_frontend_reports_points_chart';

    // Setup default attrs
    $atts = shortcode_atts( array(

        'type'              => '',
        'amount'            => '',
        'current_user'      => 'yes',
        'user_id'           => '0',
        'style'             => 'inline',
        'legend'            => 'no',
        'background'        => implode( ',', gamipress_frontend_reports_get_chart_colors( 0.5 ) ),
        'border'            => implode( ',', gamipress_frontend_reports_get_chart_colors() ),
        'grid'              => 'rgba(0,0,0,0.1)',
        'max_ticks'         => '',

    ), $atts, $shortcode );

    // ---------------------------
    // Shortcode Errors
    // ---------------------------

    // Not type provided
    if( $atts['type'] === '' )
        return gamipress_shortcode_error( __( 'Please, provide the type attribute.', 'gamipress-frontend-reports' ), $shortcode );

    $points_type = gamipress_get_points_type( $atts['type'] );

    if( ! $points_type )
        return gamipress_shortcode_error( __( 'The type provided isn\'t a valid registered points type.', 'gamipress-frontend-reports' ), $shortcode );

    $amounts = explode( ',', $atts['amount'] );

    if( empty( $amounts ) )
        return gamipress_shortcode_error( __( 'Please, provide at least one amount type.', 'gamipress-frontend-reports' ), $shortcode );

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

    $amounts_labels = array(
        'earned'    => __( 'Earned', 'gamipress-frontend-reports' ),
        'deducted'  => __( 'Deducted', 'gamipress-frontend-reports' ),
        'expended'  => __( 'Expended', 'gamipress-frontend-reports' ),
    );

    foreach( $amounts as $amount ) {

        // Skip invalid amount types
        if( ! isset( $amounts_labels[$amount] ) ) continue;

        switch( $amount ) {
            case 'deducted':
                $value = gamipress_get_user_points_deducted( $atts['user_id'], $atts['type'] );
                break;
            case 'expended':
                $value = gamipress_get_user_points_expended( $atts['user_id'], $atts['type'] );
                break;
            case 'earned':
            default:
                $value = gamipress_get_user_points( $atts['user_id'], $atts['type'] );
                break;
        }

        $stats[] = array(
            'name'  => $amounts_labels[$amount],
            'slug'  => $amount,
            'value' => $value
        );

    }

    // Setup the label attribute
    $gamipress_frontend_reports_template_args['label'] = __( 'Value', 'gamipress-frontend-reports' );

    // Bail if no stats
    if( empty( $stats ) )
        return gamipress_shortcode_error( __( 'Any amount types provided aren\'t a valid amounts.', 'gamipress-frontend-reports' ), $shortcode );

    // Bail if stats provided don't meet the minimum required by the chart style
    if( ! gamipress_frontend_reports_stats_meets_chart_style_minimum( $stats, $atts['style'] ) )
        return gamipress_shortcode_error( __( 'You need to provide more amount types for this chart style.', 'gamipress-frontend-reports' ), $shortcode );

    // Setup the stats template parameter
    $stats = gamipress_frontend_reports_process_stats( $stats, $shortcode, $atts );
    $gamipress_frontend_reports_template_args['stats'] = $stats;

    // Enqueue assets
    gamipress_frontend_reports_enqueue_scripts();

    // Render the chart
    ob_start();
    gamipress_get_template_part( 'chart', $atts['type'] );
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
    return apply_filters( 'gamipress_frontend_reports_points_chart_shortcode_output', $output, $atts, $content );

}
