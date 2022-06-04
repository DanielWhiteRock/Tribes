<?php
/**
 * Triggers
 *
 * @package     GamiPress\Points_Exchanges\Triggers
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin activity triggers
 *
 * @since 1.0.0
 *
 * @param array $activity_triggers
 *
 * @return mixed
 */
function gamipress_points_exchanges_activity_triggers( $activity_triggers ) {

    $activity_triggers[__( 'Points Exchanges', 'gamipress-points-exchanges' )] = array(
        'gamipress_points_exchanges_new_exchange'           => __( 'Make a new exchange', 'gamipress-points-exchanges' ),
        'gamipress_points_exchanges_new_points_exchange'    => __( 'Exchange a minimum amount of points', 'gamipress-points-exchanges' ),
    );

    return $activity_triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_points_exchanges_activity_triggers' );

/**
 * Build custom activity trigger label
 *
 * @since 1.0.0
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_points_exchanges_activity_trigger_label( $title, $requirement_id, $requirement ) {

    // Get our types
    $points_types = gamipress_get_points_types();

    switch( $requirement['trigger_type'] ) {

        // Points type label
        case 'gamipress_points_exchanges_new_points_exchange':

            // Bail if points type not well configured
            if( ! isset( $points_types[$requirement['points_exchanges_points_type']] ) ) {
                return $title;
            }

            $points_type = $points_types[$requirement['points_exchanges_points_type']];
            $points_amount = absint( $requirement['points_exchanges_points_amount'] );

            if( $points_amount > 0 ) {
                return sprintf( __( 'Exchange a minimum of %d %s', 'gamipress-points-exchanges' ), $points_amount, $points_type['plural_name'] );
            } else {
                return sprintf( __( 'Exchange any amount of %s', 'gamipress-points-exchanges' ), $points_type['plural_name'] );
            }

            break;

    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_points_exchanges_activity_trigger_label', 10, 3 );

/**
 * Get user for a given trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          User ID.
 */
function gamipress_points_exchanges_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_points_exchanges_new_exchange':
        case 'gamipress_points_exchanges_new_points_exchange':
            $user_id = $args[0];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_points_exchanges_trigger_get_user_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.0
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_points_exchanges_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_points_exchanges_new_exchange':
        case 'gamipress_points_exchanges_new_points_exchange':
            $log_meta['from_amount'] = $args[1];
            $log_meta['from_points_type'] = $args[2];
            $log_meta['to_points_type'] = $args[3];
            $log_meta['to_amount'] = $args[4];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_points_exchanges_log_event_trigger_meta_data', 10, 5 );