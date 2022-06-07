<?php
/**
 * GamiPress Purchases Purchase History Shortcode
 *
 * @package     GamiPress\Purchases\Shortcodes\Shortcode\GamiPress_Purchase_History
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_purchase_history] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_purchases_register_purchase_history_shortcode() {

    gamipress_register_shortcode( 'gamipress_purchase_history', array(
        'name'              => __( 'Purchase History', 'gamipress-purchases' ),
        'description'       => __( 'Render the purchase history of the current logged in user or the provided user.', 'gamipress-purchases' ),
        'output_callback'   => 'gamipress_purchases_purchase_history_shortcode',
        'icon'              => 'cart',
        'group'             => 'purchases',
        'fields'            => array(
            'current_user' => array(
                'name'        => __( 'Current User', 'gamipress-purchases' ),
                'description' => __( 'Show the purchases history of the current logged in user.', 'gamipress-purchases' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' => 'yes'
            ),
            'user_id' => array(
                'name'        => __( 'User', 'gamipress-purchases' ),
                'description' => __( 'Show the purchases history of a specific user.', 'gamipress-purchases' ),
                'type'        => 'select',
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users',
                'classes' 	  => 'gamipress-user-selector',
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_purchases_register_purchase_history_shortcode' );

/**
 * Purchase History Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_purchases_purchase_history_shortcode( $atts = array() ) {

    global $gamipress_purchases_template_args;

    // Setup user id
    $user_id = get_current_user_id();

    if( $user_id === 0 ) {
        return '';
    }

    $gamipress_purchases_template_args = array();

    // Check if single payment details
    if( isset( $_GET['payment_id'] ) ) {

        // Setup CT Table
        ct_setup_table( 'gamipress_payments' );
        $payment = ct_get_object( $_GET['payment_id'] );

        // Check if payment exists
        if( ! $payment ) {
            return '';
        }

        // Check if user is assigned to this payment
        if( absint( $payment->user_id ) !== absint( $user_id ) ) {
            return '';
        }

        $gamipress_purchases_template_args['payment_id'] = $_GET['payment_id'];

        // Enqueue assets
        gamipress_purchases_enqueue_scripts();

        ob_start();
        gamipress_get_template_part( 'purchase-details' );
        $output = ob_get_clean();

        // Return our rendered achievement
        return $output;

    } else {

        // Enqueue assets
        gamipress_purchases_enqueue_scripts();

        ob_start();
        gamipress_get_template_part( 'purchase-history' );
        $output = ob_get_clean();

        // Return our rendered achievement
        return $output;
    }

}
add_shortcode( 'gamipress_purchase_history', 'gamipress_purchases_purchase_history_shortcode' );
