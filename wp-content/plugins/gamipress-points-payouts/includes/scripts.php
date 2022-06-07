<?php
/**
 * Scripts
 *
 * @package     GamiPress\Points_Payouts\Scripts
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_points_payouts_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Scripts
    wp_register_script( 'gamipress-points-payouts-js', GAMIPRESS_POINTS_PAYOUTS_URL . 'assets/js/gamipress-points-payouts' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_POINTS_PAYOUTS_VER, true );

}
add_action( 'init', 'gamipress_points_payouts_register_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_points_payouts_enqueue_scripts( $hook = null ) {

    // Enqueue scripts
    if( ! wp_script_is('gamipress-points-payouts-js') ) {

        $points_types = array();
        $prefix = '_gamipress_points_payouts_';

        foreach( gamipress_get_points_types() as $points_type => $data ) {

            if( (bool) gamipress_get_post_meta( $data['ID'], $prefix . 'enable' ) ) {

                $data['conversion'] = gamipress_get_post_meta( $data['ID'], $prefix . 'conversion' );

                // Points types without a conversion rate can't be used for partial payments
                if( empty( $data['conversion'] ) ) continue;

                $data['label_position'] = gamipress_get_points_type_label_position( $points_type );

                $points_types[$points_type] = $data;
            }

        }

        // Localize scripts
        wp_localize_script( 'gamipress-points-payouts-js', 'gamipress_points_payouts', array(
            'ajaxurl'               => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
            'points_error'          => __( 'Enter a valid amount.', 'gamipress-points-payouts' ),
            'payment_method_error'  => __( 'Fill the payment method.', 'gamipress-points-payouts' ),
            'points_types'          => $points_types,
            'decimals'              => gamipress_points_payouts_get_option( 'decimals', 2 ),
            'decimal_separator'     => gamipress_points_payouts_get_option( 'decimal_separator', '.' ),
            'thousand_separator'    => gamipress_points_payouts_get_option( 'thousands_separator', ',' ),
        ) );

        wp_enqueue_script( 'gamipress-points-payouts-js' );
    }

}
//add_action( 'wp_enqueue_scripts', 'gamipress_points_payouts_enqueue_scripts', 100 );

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_points_payouts_admin_register_scripts( $hook ) {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Scripts
    wp_register_script( 'gamipress-points-payouts-admin-js', GAMIPRESS_POINTS_PAYOUTS_URL . 'assets/js/gamipress-points-payouts-admin' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_POINTS_PAYOUTS_VER, true );

}
add_action( 'admin_init', 'gamipress_points_payouts_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_points_payouts_admin_enqueue_scripts( $hook ) {

    global $post_type;

    //Scripts

    // Requirements ui script
    if ( $post_type === 'points-type' ) {
        wp_enqueue_script( 'gamipress-points-payouts-admin-js' );
    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_points_payouts_admin_enqueue_scripts', 100 );