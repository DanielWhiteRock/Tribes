<?php
/**
 * Logs
 *
 * @package     GamiPress\Restrict_Unlock\Logs
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
function gamipress_restrict_unlock_logs_types( $gamipress_log_types ) {

    $gamipress_log_types['restrict_unlock'] = __( 'Restrict Unlock', 'gamipress-restrict-unlock' );

    return $gamipress_log_types;

}
add_filter( 'gamipress_logs_types', 'gamipress_restrict_unlock_logs_types' );

/**
 * Log post unlock on logs
 *
 * @since 1.0.0
 *
 * @param int $post_id
 * @param int $user_id
 *
 * @return int|false
 */
function gamipress_restrict_unlock_log_unlock( $post_id = null, $user_id = null ) {

    // Can't unlock a not existent post
    if( ! get_post( $post_id ) ) {
        return false;
    }

    // Log meta data
    $log_meta = array(
        'pattern' => sprintf( __( '{user} got access to earn "%s"', 'gamipress-restrict-unlock' ), get_post_field( 'post_title', $post_id ) ),
        'post_id' => $post_id,
    );

    // Register the award unlock on logs
    return gamipress_insert_log( 'restrict_unlock', $user_id, 'private', $log_meta );

}