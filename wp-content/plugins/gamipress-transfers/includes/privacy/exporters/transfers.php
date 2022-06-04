<?php
/**
 * Transfers Exporters
 *
 * @package     GamiPress\Transfers\Privacy\Exporters\Transfers
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register exporter for user transfers.
 *
 * @since 1.0.0
 *
 * @param array $exporters
 *
 * @return array
 */
function gamipress_transfers_privacy_register_transfers_exporters( $exporters ) {

    $exporters[] = array(
        'exporter_friendly_name'    => __( 'Transfers', 'gamipress-transfers' ),
        'callback'                  => 'gamipress_transfers_privacy_transfers_exporter',
    );

    return $exporters;

}
add_filter( 'wp_privacy_personal_data_exporters', 'gamipress_transfers_privacy_register_transfers_exporters' );

/**
 * Exporter for user transfers.
 *
 * @since 1.0.0
 *
 * @param string    $email_address
 * @param int       $page
 *
 * @return array
 */
function gamipress_transfers_privacy_transfers_exporter( $email_address, $page = 1 ) {

    global $wpdb;

    // Setup query vars
    $transfers = GamiPress()->db->transfers;

    // Important: keep always SELECT *, %d is user ID, and limit/offset will added automatically
    $query = "SELECT * FROM {$transfers} WHERE user_id = %d";
    $count_query = str_replace( "SELECT *", "SELECT COUNT(*)", $query );

    // Setup vars
    $export_items   = array();
    $limit = 500;
    $offset = $page - 1;
    $done = false;

    $user = get_user_by( 'email', $email_address );

    if ( $user && $user->ID ) {

        // Get user transfers
        $user_transfers = $wpdb->get_results( $wpdb->prepare(
            $query . " LIMIT {$offset}, {$limit}",
            $user->ID
        ) );

        if( is_array( $user_transfers ) ) {

            foreach( $user_transfers as $user_transfer ) {

                // Add the user transfer to the exported items array
                $export_items[] = array(
                    'group_id'    => 'gamipress-transfers',
                    'group_label' => __( 'Transfers', 'gamipress-transfers' ),
                    'item_id'     => "gamipress-transfers-{$user_transfer->transfer_id}",
                    'data'        => gamipress_transfers_privacy_get_transfer_data( $user_transfer ),
                );

            }

        }

        // Check remaining items
        $exported_items_count = $limit * $page;
        $items_count = absint( $wpdb->get_var( $wpdb->prepare( $count_query, $user->ID ) ) );

        // Process done!
        $done = (bool) ( $exported_items_count >= $items_count );

    }

    // Return our exported items
    return array(
        'data' => $export_items,
        'done' => $done
    );

}

/**
 * Function to retrieve transfer data.
 *
 * @since 1.0.0
 *
 * @param stdClass $transfer
 *
 * @return array
 */
function gamipress_transfers_privacy_get_transfer_data( $transfer ) {

    // Prefix for meta data
    $prefix = '_gamipress_transfers_';

    // Setup CT table
    ct_setup_table( 'gamipress_transfers' );

    $data = array();

    // Transfer number

    $data['number'] = array(
        'name' => __( 'Transfer Number', 'gamipress-transfers' ),
        'value' => $transfer->number,
    );

    // Transfer number

    $data['date'] = array(
        'name' => __( 'Transfer Date', 'gamipress-transfers' ),
        'value' => $transfer->date,
    );

    // Transfer status

    $transfer_statuses = gamipress_transfers_get_transfer_statuses();

    $data['status'] = array(
        'name' => __( 'Transfer Status', 'gamipress-transfers' ),
        'value' => isset( $transfer_statuses[$transfer->status] ) ? $transfer_statuses[$transfer->status] : $transfer->status,
    );

    // User
    $user = get_userdata( $transfer->user_id );

    $data['user'] = array(
        'name' => __( 'From', 'gamipress-transfers' ),
        'value' => $user->user_login,
    );

    // User IP

    $data['user-ip'] = array(
        'name' => __( 'IP Address', 'gamipress-transfers' ),
        'value' => $transfer->user_ip,
    );

    // Recipient

    $recipient = get_userdata( $transfer->recipient_id );

    $data['recipient'] = array(
        'name' => __( 'To', 'gamipress-transfers' ),
        'value' => $recipient->user_login,
    );

    // Transfer items

    $data['items'] = array(
        'name' => __( 'Transfer Items', 'gamipress-transfers' ),
        'value' => gamipress_transfers_privacy_get_transfer_items_details( $transfer ),
    );

    /**
     * User transfer to export
     *
     * @param array     $data           The user transfers data to export
     * @param int       $user_id        The user ID
     * @param stdClass  $transfer        The transfer object
     */
    return apply_filters( 'gamipress_transfers_privacy_get_transfer_data', $data, $transfer->user_id, $transfer );

}

/**
 * Function to retrieve transfer items details.
 *
 * @since 1.0.0
 *
 * @param stdClass $transfer
 *
 * @return string
 */
function gamipress_transfers_privacy_get_transfer_items_details( $transfer ) {

    $items_details = '';

    $transfer_items = gamipress_transfers_get_transfer_items( $transfer->transfer_id );

    foreach( $transfer_items as $transfer_item ) {

        $item_details = $transfer_item->description . ' x' . $transfer_item->quantity;

        /**
         * Single transfer item details to export
         *
         * @param string    $item_details   The transfer's item details data to export
         * @param int       $user_id        The user ID
         * @param stdClass  $transfer        The transfer object
         */
        $item_details = apply_filters( 'gamipress_transfers_privacy_get_transfer_item_details', $item_details, $transfer->user_id, $transfer );

        $items_details .= $item_details . "\n";

    }

    /**
     * Transfer items details to export
     *
     * @param string    $items_details  The transfer's items details data to export
     * @param int       $user_id        The user ID
     * @param stdClass  $transfer        The transfer object
     */
    return apply_filters( 'gamipress_transfers_privacy_get_transfer_items_details', $items_details, $transfer->user_id, $transfer );

}