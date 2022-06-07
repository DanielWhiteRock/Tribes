<?php
/**
 * Functions
 *
 * @package     GamiPress\Transfers\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get an unique generated new transfer key
 *
 * @since  1.0.0
 *
 * @return string
 */
function gamipress_transfers_generate_transfer_key() {

    global $wpdb;

    $new_transfer_key = wp_generate_password( 12, false, false );

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_transfers' );

    $found = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$ct_table->db->table_name} WHERE transfer_key = %s LIMIT 1", $new_transfer_key ) );

    if( $found ) {
        return gamipress_transfers_generate_transfer_key();
    }

    return $new_transfer_key;

}

/**
 * Return the IP address of the current visitor
 *
 * @since 1.0.0
 *
 * @return string $ip User's IP address
 */
function gamipress_transfers_get_ip() {

    $ip = '127.0.0.1';

    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        //Check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        // Check ip is pass from proxy, can include more than 1 ip, first is the public one
        $ip = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ip[0]);
    } elseif( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Fix potential CSV returned from $_SERVER variables
    $ip_array = explode( ',', $ip );
    $ip_array = array_map( 'trim', $ip_array );

    return apply_filters( 'gamipress_transfers_get_ip', $ip_array[0] );

}

/**
 * Return the recipient display (by default {display_name} ({user_email})
 *
 * @since 1.0.5
 *
 * @param WP_User $recipient Recipient WP_User object
 *
 * @return string
 */
function gamipress_transfers_display_recipient( $recipient ) {

    $pattern = gamipress_transfers_get_option( 'recipient_display', '{user} ({user_email})' );

    $recipient_display = gamipress_transfers_parse_recipient_tags( $pattern, $recipient );

    /**
     * Allow filter the recipient display
     *
     * @since 1.0.5
     *
     * @param string    $output     Recipient display (by default {display_name} ({user_email})
     * @param WP_User   $recipient  Recipient WP_User object
     *
     * @return string
     */
    return apply_filters( 'gamipress_transfers_display_recipient', $recipient_display, $recipient );
}