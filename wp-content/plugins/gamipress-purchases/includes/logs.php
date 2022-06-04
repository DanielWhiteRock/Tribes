<?php
/**
 * Payments
 *
 * @package     GamiPress\Purchases\Logs
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin log types
 *
 * @since 1.0.0
 *
 * @param array $gamipress_log_types
 *
 * @return array
 */
function gamipress_purchases_logs_types( $gamipress_log_types ) {

    $gamipress_log_types['gateway_error'] = __( 'Gateway Error', 'gamipress-purchases' );

    return $gamipress_log_types;
}
add_filter( 'gamipress_logs_types', 'gamipress_purchases_logs_types' );

/**
 * Helper function to register gateway errors on logs
 *
 * @since 1.0.0
 *
 * @param string $title
 * @param string $description
 */
function gamipress_purchases_log_gateway_error( $title, $description ) {

    // Setup logs table
    ct_setup_table( 'gamipress_logs' );

    // Store log entry
    $log_id = ct_insert_object( array(
        'title'	        => $title,
        'type' 	        => 'gateway_error',
        'trigger_type' 	=> '',
        'access'	    => 'private',
        'user_id'	    => get_current_user_id(),
        'date'	        => date( 'Y-m-d H:i:s' ),
    ) );

    if( $log_id )
        ct_update_object_meta( $log_id, '_gamipress_description', $description );

    ct_reset_setup_table();

}

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
function gamipress_purchases_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_purchases_new_purchase':
            // Add the payment ID
            $log_meta['payment_id'] = $args[0];
            break;
        case 'gamipress_purchases_new_points_purchase':
            // Add the payment ID, points type ID and points purchased amount
            $log_meta['payment_id'] = $args[0];
            $log_meta['points_type_id'] = $args[3];
            $log_meta['points_amount'] = $args[4];
            break;
        case 'gamipress_purchases_new_achievement_purchase':
        case 'gamipress_purchases_new_specific_achievement_purchase':
            // Add the payment ID and the achievement ID
            $log_meta['payment_id'] = $args[0];
            $log_meta['achievement_id'] = $args[3];
            break;
        case 'gamipress_purchases_new_rank_purchase':
        case 'gamipress_purchases_new_specific_rank_purchase':
            // Add the payment ID and the rank ID
            $log_meta['payment_id'] = $args[0];
            $log_meta['rank_id'] = $args[3];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_purchases_log_event_trigger_meta_data', 10, 5 );

/**
 * Extra data fields
 *
 * @since 1.1.1
 *
 * @param array     $fields
 * @param int       $log_id
 * @param string    $type
 *
 * @return array
 */
function gamipress_purchases_log_extra_data_fields( $fields, $log_id, $type ) {

    $prefix = '_gamipress_';

    if( $type !== 'gateway_error' )
        return $fields;

    $fields[] = array(
        'name' 	            => __( 'Description', 'gamipress-purchases' ),
        'desc' 	            => __( 'Description attached to this log.', 'gamipress-purchases' ),
        'id'   	            => $prefix . 'description',
        'type' 	            => 'text',
    );

    return $fields;

}
add_filter( 'gamipress_log_extra_data_fields', 'gamipress_purchases_log_extra_data_fields', 10, 3 );