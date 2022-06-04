<?php
/**
 * GamiPress Transfers Transfer History Shortcode
 *
 * @package     GamiPress\Transfers\Shortcodes\Shortcode\GamiPress_Transfer_History
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_transfer_history] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_transfers_register_transfer_history_shortcode() {

    gamipress_register_shortcode( 'gamipress_transfer_history', array(
        'name'              => __( 'Transfer History', 'gamipress-transfers' ),
        'description'       => __( 'Render the transfer history of the current logged in user or the provided user.', 'gamipress-transfers' ),
        'output_callback'   => 'gamipress_transfers_transfer_history_shortcode',
        'icon'              => 'transfer',
        'group'             => 'transfers',
        'fields'            => array(
            'current_user' => array(
                'name'        => __( 'Current User', 'gamipress-transfers' ),
                'description' => __( 'Show the transfers history of the current logged in user.', 'gamipress-transfers' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' => 'yes'
            ),
            'user_id' => array(
                'name'        => __( 'User', 'gamipress-transfers' ),
                'description' => __( 'Show the transfers history of a specific user.', 'gamipress-transfers' ),
                'type'        => 'select',
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users',
                'classes' 	  => 'gamipress-user-selector',
            ),
            'history' => array(
                'name'        => __( 'History', 'gamipress-transfers' ),
                'description' => __( 'The history type to show transfers from.', 'gamipress-transfers' ),
                'type'        => 'select',
                'default'     => '',
                'options'     => array(
                    'sent'      => __( 'Sent', 'gamipress-transfers' ),
                    'received'  => __( 'Received', 'gamipress-transfers' ),
                    'both'      => __( 'Both', 'gamipress-transfers' ),
                ),
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_transfers_register_transfer_history_shortcode' );

/**
 * Transfer History Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_transfers_transfer_history_shortcode( $atts = array() ) {

    global $gamipress_transfers_template_args;

    $atts = shortcode_atts( array(
        'current_user'      => 'yes',
        'user_id'     	    => '0',
        'history'     	    => 'sent',
    ), $atts, 'gamipress_transfer_history' );

    // Force to set current user as user ID
    if( $atts['current_user'] === 'yes' ) {
        $atts['user_id'] = get_current_user_id();
    } else if( absint( $atts['user_id'] ) === 0 ) {
        $atts['user_id'] = get_current_user_id();
    }

    if( $atts['user_id'] === 0 )
        return '';

    $gamipress_transfers_template_args = $atts;

    // Check if single transfer details
    if( isset( $_GET['transfer_id'] ) ) {

        // Setup CT Table
        ct_setup_table( 'gamipress_transfers' );
        $transfer = ct_get_object( $_GET['transfer_id'] );

        // Check if transfer exists
        if( ! $transfer )
            return '';

        // Check if user is assigned to this transfer
        if( ! in_array( absint( $atts['user_id'] ), array( absint( $transfer->user_id ), absint( $transfer->recipient_id ) ) ) ) {
            return '';
        }

        $gamipress_transfers_template_args['transfer_id'] = $_GET['transfer_id'];

        // Enqueue assets
        gamipress_transfers_enqueue_scripts();

        ob_start();
        gamipress_get_template_part( 'transfer-details' );
        $output = ob_get_clean();

        // Return our rendered achievement
        return $output;

    } else {

        // Enqueue assets
        gamipress_transfers_enqueue_scripts();

        ob_start();
        gamipress_get_template_part( 'transfer-history' );
        $output = ob_get_clean();

        // Return our rendered achievement
        return $output;
    }

}
add_shortcode( 'gamipress_transfer_history', 'gamipress_transfers_transfer_history_shortcode' );
