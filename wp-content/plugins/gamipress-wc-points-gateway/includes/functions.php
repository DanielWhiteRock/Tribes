<?php
/**
 * Functions
 *
 * @package GamiPress\WooCommerce\Points_Gateway\Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the given points type slug conversion
 *
 * @since 1.0.0
 *
 * @param string $points_type
 *
 * @return int|bool
 */
function gamipress_wc_points_gateway_get_conversion_rate( $points_type = '' ) {

    global $woocommerce;

    $points_types = gamipress_get_points_types();

    if( ! isset( $points_types[$points_type] ) )
        return false;

    $gateways = $woocommerce->payment_gateways->payment_gateways();

    $conversion_rate = floatval( $gateways['gamipress_' . $points_type]->get_option( 'conversion_rate' ) );

    if( $conversion_rate <= 0 ) {
        // Default conversion is 100 points => 1$
        return 100;
    }

    return $conversion_rate;

}

/**
 * Convert an amount of money to points based on configured conversion rate
 *
 * @since 1.0.0
 *
 * @param string|float  $amount
 * @param string        $points_type
 *
 * @return float
 */
function gamipress_wc_points_gateway_convert_to_points( $amount, $points_type = '' ) {

    $amount = floatval( $amount );

    $conversion_rate = gamipress_wc_points_gateway_get_conversion_rate( $points_type );

    $converted_amount = $amount * $conversion_rate;

    /**
     * Filter the ability to round (rounding up) or not the converted amount (by default, true)
     *
     * @since 1.1.0
     *
     * @param bool          $round
     * @param string|float  $converted_amount
     * @param string|float  $amount
     * @param string        $points_type
     * @param int           $conversion_rate
     *
     * @return int|float
     */
    if( apply_filters( 'gamipress_wc_points_gateway_convert_to_points_round', true, $converted_amount, $amount, $points_type, $conversion_rate ) )
        $converted_amount = ceil( $converted_amount );

    /**
     * Filters the converted amount of money to points based on configured conversion rate
     *
     * @since 1.0.0
     *
     * @param string|float  $converted_amount
     * @param string|float  $amount
     * @param string        $points_type
     * @param int           $conversion_rate
     *
     * @return int|float
     */
    return apply_filters( 'gamipress_wc_points_gateway_convert_to_points', $converted_amount, $amount, $points_type, $conversion_rate );

}