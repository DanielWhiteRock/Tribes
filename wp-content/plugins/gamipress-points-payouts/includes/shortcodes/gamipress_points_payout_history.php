<?php
/**
 * GamiPress Points Payouts Points Payout History Shortcode
 *
 * @package     GamiPress\Points_Payouts\Shortcodes\Shortcode\GamiPress_Points_Payout_History
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_points_payout_history] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_points_payouts_register_points_payout_history_shortcode() {

    gamipress_register_shortcode( 'gamipress_points_payout_history', array(
        'name'              => __( 'Points Payout History', 'gamipress-points-payouts' ),
        'description'       => __( 'Render the points payout history of the current logged in user or the provided user.', 'gamipress-points-payouts' ),
        'output_callback'   => 'gamipress_points_payouts_points_payout_history_shortcode',
        'icon'              => 'star-filled',
        'fields'            => array(
            'current_user' => array(
                'name'        => __( 'Current User', 'gamipress-points-payouts' ),
                'description' => __( 'Show the points payouts history of the current logged in user.', 'gamipress-points-payouts' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' => 'yes'
            ),
            'user_id' => array(
                'name'        => __( 'User', 'gamipress-points-payouts' ),
                'description' => __( 'Show the points payouts history of a specific user.', 'gamipress-points-payouts' ),
                'type'        => 'select',
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users',
                'classes' 	  => 'gamipress-user-selector',
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_points_payouts_register_points_payout_history_shortcode' );

/**
 * Points Payout History Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_points_payouts_points_payout_history_shortcode( $atts = array() ) {

    global $gamipress_points_payouts_template_args;

    $atts = shortcode_atts( array(
        'current_user'      => 'yes',
        'user_id'     	    => '0',
    ), $atts, 'gamipress_points_payout_history' );

    // Force to set current user as user ID
    if( $atts['current_user'] === 'yes' ) {
        $atts['user_id'] = get_current_user_id();
    } else if( absint( $atts['user_id'] ) === 0 ) {
        $atts['user_id'] = get_current_user_id();
    }

    if( $atts['user_id'] === 0 )
        return '';

    $gamipress_points_payouts_template_args = $atts;

    // Check if single points payout details
    if( isset( $_GET['points_payout_id'] ) ) {

        // Setup CT Table
        ct_setup_table( 'gamipress_points_payouts' );
        $points_payout = ct_get_object( $_GET['points_payout_id'] );

        // Check if points payout exists
        if( ! $points_payout ) {
            return '';
        }

        // Check if user is assigned to this points payout
        if( absint( $points_payout->user_id ) !== absint( $atts['user_id'] ) )
            return '';

        $gamipress_points_payouts_template_args['points_payout_id'] = $_GET['points_payout_id'];

        // Enqueue assets
        gamipress_points_payouts_enqueue_scripts();

        ob_start();
        gamipress_get_template_part( 'points-payout-details' );
        $output = ob_get_clean();

        // Return our rendered achievement
        return $output;

    } else {

        // Enqueue assets
        gamipress_points_payouts_enqueue_scripts();

        ob_start();
        gamipress_get_template_part( 'points-payout-history' );
        $output = ob_get_clean();

        // Return our rendered achievement
        return $output;
    }

}
add_shortcode( 'gamipress_points_payout_history', 'gamipress_points_payouts_points_payout_history_shortcode' );
