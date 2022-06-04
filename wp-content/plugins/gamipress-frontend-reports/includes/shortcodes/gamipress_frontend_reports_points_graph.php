<?php
/**
 * GamiPress Frontend Reports Points Graph Shortcode
 *
 * @package     GamiPress\Frontend_Reports\Shortcodes\Shortcode\GamiPress_Frontend_Reports_Points_Graph
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_frontend_reports_points_graph] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_register_frontend_reports_points_graph_shortcode() {

    gamipress_register_shortcode( 'gamipress_frontend_reports_points_graph', array(
        'name'              => __( 'Points Graph', 'gamipress-frontend-reports' ),
        'description'       => __( 'Render a graph with user points balances of a specific points type.', 'gamipress-frontend-reports' ),
        'icon'              => 'star-filled',
        'group'             => 'frontend_reports',
        'output_callback'   => 'gamipress_frontend_reports_points_graph_shortcode',
        'tabs' => array(
            'general' => array(
                'icon' => 'dashicons-admin-generic',
                'title' => __( 'General', 'gamipress-frontend-reports' ),
                'fields' => array(
                    'type',
                    'amount',
                    'current_user',
                    'user_id',
                    'period',
                    'period_value',
                    'period_start',
                    'period_end',
                    'range',
                    'range_value',
                ),
            ),
            'style' => array(
                'icon' => 'dashicons-admin-appearance',
                'title' => __( 'Style', 'gamipress-frontend-reports' ),
                'fields' => array(
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
                'desc'          => __( 'Choose the amount type(s) to use as graph values.', 'gamipress-frontend-reports' ),
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

            'period' => array(
                'name'          => __( 'Show Periods', 'gamipress-frontend-reports' ),
                'description'   => __( 'Display the period input.', 'gamipress-frontend-reports' ),
                'type' 		    => 'checkbox',
                'classes' 	    => 'gamipress-switch',
                'default' 	    => 'yes',
            ),
            'period_value' => array(
                'name'          => __( 'Period Initial Value', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Set period initial value. If you hide the period input, user won\'t be able to change this value.', 'gamipress-frontend-reports' ),
                'type'          => 'select',
                'options'       => gamipress_frontend_reports_get_graph_time_periods(),
                'default'       => 'this-week'
            ),
            'period_start' => array(
                'name' 	        => __( 'Period Start Date', 'gamipress-frontend-reports' ),
                'desc' 	        => __( 'Period start date. Leave blank to no filter by a start date (graph will be filtered only to the end date).', 'gamipress-frontend-reports' )
                    . '<br>' . __( 'Accepts any valid PHP date format.', 'gamipress-frontend-reports' ) . ' (<a href="https://gamipress.com/docs/advanced/date-fields" target="_blank">' .  __( 'More information', 'gamipress-frontend-reports' ) .  '</a>)',
                'type'          => 'text',
            ),
            'period_end' => array(
                'name' 	        => __( 'Period End Date', 'gamipress-frontend-reports' ),
                'desc' 	        => __( 'Period end date. Leave blank to no filter by an end date (graph will be filtered from the start date to today).', 'gamipress-frontend-reports' )
                    . '<br>' . __( 'Accepts any valid PHP date format.', 'gamipress-frontend-reports' ) . ' (<a href="https://gamipress.com/docs/advanced/date-fields" target="_blank">' .  __( 'More information', 'gamipress-frontend-reports' ) .  '</a>)',
                'type'          => 'text',
            ),

            'range' => array(
                'name'          => __( 'Show Range', 'gamipress-frontend-reports' ),
                'description'   => __( 'Display the range input.', 'gamipress-frontend-reports' ),
                'type' 		    => 'checkbox',
                'classes' 	    => 'gamipress-switch',
                'default' 	    => 'yes',
            ),
            'range_value' => array(
                'name'          => __( 'Range Initial Value', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Set range initial value. If you hide the range input, user won\'t be able to change this value.', 'gamipress-frontend-reports' ),
                'type'          => 'select',
                'options'       => gamipress_frontend_reports_get_graph_ranges(),
                'default'       => 'week'
            ),

            // Style

            'legend' => array(
                'name'          => __( 'Graph legend', 'gamipress-frontend-reports' ),
                'description'   => __( 'Show graph legend.', 'gamipress-frontend-reports' ),
                'type' 		    => 'checkbox',
                'classes' 	    => 'gamipress-switch',
            ),
            'background' => array(
                'name'          => __( 'Background Color', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Graph statistics background color.', 'gamipress-frontend-reports' ),
                'shortcode_desc'    => __( 'Single or comma-separated list of colors to use as chart statistics background color. Accepts colors in format hex, rgb and rgba.', 'gamipress-frontend-reports' ),
                'type'          => 'colorpicker',
                'options'       => array( 'alpha' => true ),
                'default'       => gamipress_frontend_reports_get_graph_colors( 0.5 ),
                'repeatable' => true,
                'text'     => array(
                    'add_row_text' => __( 'Add Color', 'gamipress-frontend-reports' ),
                ),
            ),
            'border' => array(
                'name'          => __( 'Border Color', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Graph statistics border color.', 'gamipress-frontend-reports' ),
                'shortcode_desc'    => __( 'Single or comma-separated list of colors to use as chart statistics border color. Accepts colors in format hex, rgb and rgba.', 'gamipress-frontend-reports' ),
                'type'          => 'colorpicker',
                'options'       => array( 'alpha' => true ),
                'default'       => gamipress_frontend_reports_get_graph_colors(),
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
add_action( 'init', 'gamipress_register_frontend_reports_points_graph_shortcode' );

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
function gamipress_frontend_reports_points_graph_shortcode( $atts = array(), $content = '' ) {

    global $gamipress_frontend_reports_template_args;
    $shortcode = 'gamipress_frontend_reports_points_graph';

    // Setup default attrs
    $atts = shortcode_atts( array(

        'type'              => '',
        'amount'            => '',
        'current_user'      => 'yes',
        'user_id'           => '0',
        'period'            => 'yes',
        'period_value'      => 'this-week',
        'period_start'      => '',
        'period_end'        => '',
        'range'             => 'yes',
        'range_value'       => 'week',
        'legend'            => 'no',
        'background'        => implode( ',', gamipress_frontend_reports_get_graph_colors( 0.5 ) ),
        'border'            => implode( ',', gamipress_frontend_reports_get_graph_colors() ),
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

    // Setup graph attributes (used on ajax requests)
    $atts['id'] = gamipress_frontend_reports_generate_graph_id();
    $atts['graph'] = 'points';
    $atts['filters'] = array(
        'type' => $atts['type'],
        'amount' => $atts['amount'],
    );

    // Ensure int values
    $atts['max_ticks'] = absint( $atts['max_ticks'] );

    // Process period attributes
    $atts = gamipress_frontend_reports_process_periods( $atts );

    $gamipress_frontend_reports_template_args = $atts;

    // Enqueue assets
    gamipress_frontend_reports_enqueue_scripts();

    // Render the graph
    ob_start();
    gamipress_get_template_part( 'graph', $atts['type'] );
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
    return apply_filters( 'gamipress_frontend_reports_points_graph_shortcode_output', $output, $atts, $content );

}
