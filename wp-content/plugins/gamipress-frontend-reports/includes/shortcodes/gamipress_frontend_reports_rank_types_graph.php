<?php
/**
 * GamiPress Frontend Reports Rank Types Graph Shortcode
 *
 * @package     GamiPress\Frontend_Reports\Shortcodes\Shortcode\GamiPress_Frontend_Reports_Rank_Types_Graph
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_frontend_reports_rank_types_graph] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_register_frontend_reports_rank_types_graph_shortcode() {

    gamipress_register_shortcode( 'gamipress_frontend_reports_rank_types_graph', array(
        'name'              => __( 'Rank Types Graph', 'gamipress-frontend-reports' ),
        'description'       => __( 'Render a graph with user ranks of desired rank type(s).', 'gamipress-frontend-reports' ),
        'icon'              => 'rank',
        'group'             => 'frontend_reports',
        'output_callback'   => 'gamipress_frontend_reports_rank_types_graph_shortcode',
        'tabs' => array(
            'general' => array(
                'icon' => 'dashicons-admin-generic',
                'title' => __( 'General', 'gamipress-frontend-reports' ),
                'fields' => array(
                    'type',
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
                'name'          => __( 'Rank Type(s)', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Choose the rank type(s).', 'gamipress-frontend-reports' ),
                'type'          => 'advanced_select',
                'multiple'      => true,
                'classes' 	    => 'gamipress-selector',
                'attributes' 	=> array(
                    'data-placeholder' => __( 'Default: All', 'gamipress-frontend-reports' ),
                ),
                'options_cb'    => 'gamipress_options_cb_rank_types',
                'default'       => 'all',
            ),
            'current_user' => array(
                'name'          => __( 'Current User', 'gamipress-frontend-reports' ),
                'description'   => __( 'Show ranks earned of the current logged in user.', 'gamipress-frontend-reports' ),
                'type' 		    => 'checkbox',
                'classes' 	    => 'gamipress-switch',
                'default' 	    => 'yes',
            ),
            'user_id' => array(
                'name'          => __( 'User', 'gamipress-frontend-reports' ),
                'description'   => __( 'Show ranks earned of a specific user.', 'gamipress-frontend-reports' ),
                'type'          => 'select',
                'default'       => '',
                'options_cb'    => 'gamipress_options_cb_users',
                'classes'       => 'gamipress-user-selector',
                'attributes'    => array(
                    'data-placeholder' => __( 'Select an user', 'gamipress-frontend-reports' ),
                )
            ),

            'period' => array(
                'name'          => __( 'Show Period', 'gamipress-frontend-reports' ),
                'description'   => __( 'Display the period inputs.', 'gamipress-frontend-reports' ),
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
add_action( 'init', 'gamipress_register_frontend_reports_rank_types_graph_shortcode' );

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
function gamipress_frontend_reports_rank_types_graph_shortcode( $atts = array(), $content = '' ) {

    global $gamipress_frontend_reports_template_args;
    $shortcode = 'gamipress_frontend_reports_rank_types_graph';

    // Setup default attrs
    $atts = shortcode_atts( array(

        'type'              => 'all',
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

    $types = explode( ',', $atts['type'] );
    $is_single_type = false;

    if( $atts['type'] === 'all') {
        $types = gamipress_get_rank_types_slugs();
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

        // Check if rank type is valid
        if ( ! in_array( $atts['type'], gamipress_get_rank_types_slugs() ) )
            return gamipress_shortcode_error( __( 'The type provided isn\'t a valid registered rank type.', 'gamipress-frontend-reports' ), $shortcode );

    } else if( $atts['type'] !== 'all' ) {

        // Let's check if all types provided are wrong
        $all_types_wrong = true;

        foreach( $types as $type ) {
            if ( in_array( $type, gamipress_get_rank_types_slugs() ) )
                $all_types_wrong = false;
        }

        // just notify error if all types are wrong
        if( $all_types_wrong )
            return gamipress_shortcode_error( __( 'All types provided aren\'t valid registered rank types.', 'gamipress-frontend-reports' ), $shortcode );

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

    // Setup graph attributes (used on ajax requests)
    $atts['id'] = gamipress_frontend_reports_generate_graph_id();
    $atts['graph'] = 'rank_types';
    $atts['filters'] = array(
        'type' => $atts['type'],
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
    gamipress_get_template_part( 'graph', ( $is_single_type ? $atts['type'] : null ) );
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
    return apply_filters( 'gamipress_frontend_reports_rank_types_graph_shortcode_output', $output, $atts, $content );

}
