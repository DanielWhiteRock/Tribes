<?php
/**
 * Functions
 *
 * @package     GamiPress\Points_Exchanges\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return all points types that exchanges has been enabled
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_points_exchanges_get_exchanges_enabled_points_types() {

    $points_types = gamipress_get_points_types();
    $points_types_enabled = array();

    // Loop all registered points types
    foreach( $points_types as $points_type => $data ) {

        // Just add points types that exchanges has been enabled
        if( gamipress_points_exchanges_is_exchanges_enabled( $points_type ) ) {
            $points_types_enabled[$points_type] = $data;
        }
    }

    // Return all points types that exchanges has been enabled
    return $points_types_enabled;

}

/**
 * Check if exchanges is enabled on a specific points type
 *
 * @since 1.0.0
 *
 * @param string $points_type
 *
 * @return bool
 */
function gamipress_points_exchanges_is_exchanges_enabled( $points_type ) {

    $points_type = gamipress_get_points_type( $points_type );

    // Bail if points type is not registered
    if( ! $points_type ) {
        return false;
    }

    // Return the exchanges enabled meta value as boolean
    return (bool) gamipress_get_post_meta( $points_type['ID'], '_gamipress_points_exchanges_exchanges_enabled' );

}

/**
 * Return registered exchange rates of all points types
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_points_exchanges_get_all_exchange_rates() {

    $points_types = gamipress_get_points_types();
    $rates = array();

    // Loop all registered points types
    foreach( $points_types as $points_type => $data ) {

        $points_type_rates = gamipress_points_exchanges_get_rates( $points_type );

        // Just add points types that has any rate setup
        if( is_array( $points_type_rates ) ) {
            $rates[$points_type] = $points_type_rates;
        }
    }

    return $rates;

}

/**
 * Return registered exchange rates of a specific points type
 *
 * @since 1.0.0
 *
 * @param string $points_type
 *
 * @return array|false
 */
function gamipress_points_exchanges_get_rates( $points_type ) {

    // Bail if points type has not exchanges enabled
    if( ! gamipress_points_exchanges_is_exchanges_enabled( $points_type ) ) {
        return false;
    }

    $points_type_object = gamipress_get_points_type( $points_type );

    // Bail if points type is not registered
    if( ! $points_type_object ) {
        return false;
    }

    $rates = gamipress_get_post_meta( $points_type_object['ID'], '_gamipress_points_exchanges_rates' );

    // Bail if no rates configured
    if( empty( $rates ) ) {
        return false;
    }

    // Loop to turn all rates to float
    foreach( $rates as $rate_points_type => $rate ) {
        $rates[$rate_points_type] = floatval( $rate );
    }

    return apply_filters( 'gamipress_points_exchanges_get_rates', $rates, $points_type );

}

/**
 * Return the rate to apply from FROM points type on TO points type
 *
 * @since 1.0.0
 *
 * @param string    $from       Points type from
 * @param string    $to         Points type to
 *
 * @return int|bool
 */
function gamipress_points_exchanges_get_rate( $from, $to ) {

    $points_type_rates = gamipress_points_exchanges_get_rates( $from );

    // Bail if FROM points type has no rates
    if( ! is_array( $points_type_rates ) ) {
        return false;
    }

    // Bail if FROM points type has no rates for TO points type
    if( ! isset( $points_type_rates[$to] ) ) {
        return false;
    }

    return apply_filters( 'gamipress_points_exchanges_get_rate', $points_type_rates[$to], $from, $to );

}

/**
 * Return the applicable rate to the amount
 *
 * @since 1.0.0
 *
 * @param int       $amount     The amount of points to apply the exchange rate
 * @param string    $from       Points type from
 * @param string    $to         Points type to
 *
 * @return int|bool
 */
function gamipress_points_exchanges_apply_rate( $amount, $from, $to ) {

    $rate = gamipress_points_exchanges_get_rate( $from, $to );

    // Bail if not applicable rate
    if( ! $rate ) {
        return false;
    }

    return apply_filters( 'gamipress_points_exchanges_apply_rate', absint( $amount * $rate ), $amount, $from, $to );

}

/**
 * Return the current URL
 *
 * @since 1.0.0
 *
 * @return string
 */
function gamipress_points_exchanges_get_referrer() {

    $url = get_the_permalink();

    if( empty( $url ) ) {
        $url = ( is_ssl() ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    return $url;

}