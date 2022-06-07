<?php
/**
 * Gateways
 *
 * @package     GamiPress\Purchases\Gateways
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Include our gateways
require_once GAMIPRESS_PURCHASES_DIR . 'includes/gateways/bank-transfer.php';
require_once GAMIPRESS_PURCHASES_DIR . 'includes/gateways/paypal-standard.php';

/**
 * Get the registered gateways
 *
 * @since  1.0.0
 *
 * @return array Array of gateways registered
 */
function gamipress_purchases_get_gateways() {

    return apply_filters( 'gamipress_purchases_get_gateways', array() );

}

/**
 * Get the active gateways
 *
 * @since  1.0.0
 *
 * @return array Array of gateways registered
 */
function gamipress_purchases_get_active_gateways() {

    $registered_gateways = gamipress_purchases_get_gateways();
    $setup_gateways = gamipress_purchases_get_option( 'gateways', array() );

    $active_gateways = array();

    foreach( $setup_gateways as $gateway ) {

        if( ! isset( $registered_gateways[$gateway] ) ) {
            continue;
        }

        $active_gateways[$gateway] = $registered_gateways[$gateway];

    }

    return apply_filters( 'gamipress_purchases_get_active_gateways', $active_gateways );
}