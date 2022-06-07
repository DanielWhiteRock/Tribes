<?php
/**
 * GamiPress Frontend Reports Points Shortcode
 *
 * @package     GamiPress\Frontend_Reports\Shortcodes\Shortcode\GamiPress_Frontend_Reports_Points
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_frontend_reports_points] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_register_frontend_reports_points_shortcode() {

    gamipress_register_shortcode( 'gamipress_frontend_reports_points', array(
        'name'              => __( 'Points Report', 'gamipress-frontend-reports' ),
        'description'       => __( 'Render an user points balance of a specific points type.', 'gamipress-frontend-reports' ),
        'icon'              => 'star-filled',
        'group'             => 'frontend_reports',
        'output_callback'   => 'gamipress_frontend_reports_points_shortcode',
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
                'name'          => __( 'Amount Type', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Choose the amount type to render.', 'gamipress-frontend-reports' ),
                'type'          => 'select',
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
        ),
    ) );

}
add_action( 'init', 'gamipress_register_frontend_reports_points_shortcode' );

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
function gamipress_frontend_reports_points_shortcode( $atts = array(), $content = '' ) {

    $shortcode = 'gamipress_frontend_reports_points';

    // Setup default attrs
    $atts = shortcode_atts( array(

        'type'              => '',
        'amount'            => '',
        'current_user'      => 'yes',
        'user_id'           => '0',

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

    switch( $atts['amount'] ) {
        case 'deducted':
            $output = gamipress_get_user_points_deducted( $atts['user_id'], $atts['type'] );
            break;
        case 'expended':
            $output = gamipress_get_user_points_expended( $atts['user_id'], $atts['type'] );
            break;
        case 'earned':
        default:
            $output = gamipress_get_user_points( $atts['user_id'], $atts['type'] );
            break;
    }

    /**
     * Filter to override shortcode output
     *
     * @since 1.0.0
     *
     * @param string    $output     Final output
     * @param array     $atts       Shortcode attributes
     * @param string    $content    Shortcode content
     */
    return apply_filters( 'gamipress_frontend_reports_points_shortcode_output', $output, $atts, $content );

}
