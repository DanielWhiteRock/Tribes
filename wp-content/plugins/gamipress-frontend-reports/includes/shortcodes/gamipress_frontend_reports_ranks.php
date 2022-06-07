<?php
/**
 * GamiPress Frontend Reports Ranks Shortcode
 *
 * @package     GamiPress\Frontend_Reports\Shortcodes\Shortcode\GamiPress_Frontend_Reports_Ranks
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_frontend_reports_ranks] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_register_frontend_reports_ranks_shortcode() {

    gamipress_register_shortcode( 'gamipress_frontend_reports_ranks', array(
        'name'              => __( 'Ranks Report', 'gamipress-frontend-reports' ),
        'description'       => __( 'Render an user ranks of a specific rank type.', 'gamipress-frontend-reports' ),
        'icon'              => 'rank',
        'group'             => 'frontend_reports',
        'output_callback'   => 'gamipress_frontend_reports_ranks_shortcode',
        'fields'            => array(
            'type' => array(
                'name'          => __( 'Rank Type', 'gamipress-frontend-reports' ),
                'desc'          => __( 'Choose the rank type.', 'gamipress-frontend-reports' ),
                'type'          => 'advanced_select',
                'multiple'      => false,
                'option_all'    => false,
                'classes' 	    => 'gamipress-selector',
                'attributes' 	=> array(
                    'data-placeholder' => __( 'Choose a rank type', 'gamipress-frontend-reports' ),
                ),
                'options_cb'    => 'gamipress_options_cb_rank_types',
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
        ),
    ) );

}
add_action( 'init', 'gamipress_register_frontend_reports_ranks_shortcode' );

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
function gamipress_frontend_reports_ranks_shortcode( $atts = array(), $content = '' ) {

    $shortcode = 'gamipress_frontend_reports_ranks';

    // Setup default attrs
    $atts = shortcode_atts( array(

        'type'              => '',
        'current_user'      => 'yes',
        'user_id'           => '0',

    ), $atts, $shortcode );

    // ---------------------------
    // Shortcode Errors
    // ---------------------------

    // Not type provided
    if( $atts['type'] === '' )
        return gamipress_shortcode_error( __( 'Please, provide the type attribute.', 'gamipress-frontend-reports' ), $shortcode );

    $rank_type = gamipress_get_rank_type( $atts['type'] );

    if( ! $rank_type )
        return gamipress_shortcode_error( __( 'The type provided isn\'t a valid registered rank type.', 'gamipress-frontend-reports' ), $shortcode );

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

    $output = count( gamipress_get_user_achievements( array( 'user_id' => $atts['user_id'], 'achievement_type' => $atts['type'] ) ) );

    /**
     * Filter to override shortcode output
     *
     * @since 1.0.0
     *
     * @param string    $output     Final output
     * @param array     $atts       Shortcode attributes
     * @param string    $content    Shortcode content
     */
    return apply_filters( 'gamipress_frontend_reports_ranks_shortcode_output', $output, $atts, $content );

}
