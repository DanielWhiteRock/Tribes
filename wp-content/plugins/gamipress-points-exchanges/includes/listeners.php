<?php
/**
 * Listeners
 *
 * @package     GamiPress\Points_Exchanges\Listeners
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * New exchange listener
 *
 * @since 1.0.0
 *
 * @param array     $response           Processing response
 * @param int       $user_id            User that perform the exchange
 * @param int       $amount             Amount to exchange
 * @param string    $from               From points type
 * @param string    $to                 To points type
 * @param int       $exchanged_amount   Exchanged amount
 *
 * @return array    $response           Response
 */
function gamipress_points_exchanges_new_exchange_listener( $response, $user_id, $amount, $from, $to, $exchanged_amount ) {

    // Trigger new exchange event
    do_action( 'gamipress_points_exchanges_new_exchange', $user_id, $amount, $from, $to, $exchanged_amount );

    // Trigger exchange a minimum amount of points event
    do_action( 'gamipress_points_exchanges_new_points_exchange', $user_id, $amount, $from, $to, $exchanged_amount );

    // Return the response without apply any change
    return $response;

}
add_filter( 'gamipress_points_exchanges_process_exchange', 'gamipress_points_exchanges_new_exchange_listener', 10, 6 );