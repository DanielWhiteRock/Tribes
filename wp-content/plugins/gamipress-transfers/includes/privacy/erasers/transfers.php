<?php
/**
 * Transfers Erasers
 *
 * @package     GamiPress\Transfers\Privacy\Erasers\Transfers
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register eraser for user transfers.
 *
 * @since 1.0.0
 *
 * @param array $erasers
 *
 * @return array
 */
function gamipress_transfers_privacy_register_transfers_erasers( $erasers ) {

    $erasers[] = array(
        'eraser_friendly_name'    => __( 'Transfers', 'gamipress-transfers' ),
        'callback'                => 'gamipress_transfers_privacy_transfers_eraser',
    );

    return $erasers;

}
add_filter( 'wp_privacy_personal_data_erasers', 'gamipress_transfers_privacy_register_transfers_erasers' );

/**
 * Eraser for user transfers.
 *
 * @since 1.0.0
 *
 * @param string    $email_address
 * @param int       $page
 *
 * @return array
 */
function gamipress_transfers_privacy_transfers_eraser( $email_address, $page = 1 ) {

    global $wpdb;

    // Setup query vars
    $transfers = GamiPress()->db->transfers;
    $transfer_items = GamiPress()->db->transfer_items;
    $transfer_notes = GamiPress()->db->transfer_notes;

    // Important: keep always SELECT *, %d is user ID, and limit/offset will added automatically
    $query = "SELECT * FROM {$transfers} WHERE user_id = %d";
    $count_query = str_replace( "SELECT *", "SELECT COUNT(*)", $query );

    // Setup vars
    $transfer_statuses = gamipress_transfers_get_transfer_statuses();
    $limit = 500;
    $offset = $page - 1;
    $response = array(
        'items_removed'  => true,
        'items_retained' => false,
        'messages'       => array(),
        'done'           => false
    );

    $user = get_user_by( 'email', $email_address );

    if ( $user && $user->ID ) {

        // Get user transfers
        $user_transfers = $wpdb->get_results( $wpdb->prepare(
            $query . " LIMIT {$offset}, {$limit}",
            $user->ID
        ) );

        if( is_array( $user_transfers ) ) {

            foreach( $user_transfers as $transfer ) {

                // First decide which action to perform
                switch ( $transfer->status ) {
                    case 'complete':
                    case 'refunded':
                        $action = 'anonymize';
                        break;
                    case 'cancelled':
                    case 'failed':
                        $action = 'erase';
                        break;
                    case 'pending':
                    case 'processing':
                    default:
                        $action = 'none';
                        break;
                }

                $transfer_status_label = isset( $transfer_statuses[$transfer->status] ) ? $transfer_statuses[$transfer->status] : $transfer->status;

                switch( $action ) {
                    case 'none':
                    default:

                        // Inform that there is items retained
                        $response['items_retained'] = true;

                        // Let user know which transfer has been retained
                        $response['messages'] = sprintf( __( 'Transfer #%d not modified, due to status: %s.', 'gamipress-transfers' ), $transfer->number, $transfer_status_label );

                        break;
                    case 'erase':

                        // Delete all transfer items
                        $items = gamipress_transfers_get_transfer_items( $transfer->transfer_id );

                        foreach( $items as $item ) {
                            $wpdb->query( $wpdb->prepare( "DELETE FROM {$transfer_items} WHERE transfer_item_id = %d", $item->transfer_item_id ) );
                        }

                        // Delete all transfer notes
                        $notes = gamipress_transfers_get_transfer_notes( $transfer->transfer_id );

                        foreach( $notes as $note ) {
                            $wpdb->query( $wpdb->prepare( "DELETE FROM {$transfer_notes} WHERE transfer_note_id = %d", $note->transfer_note_id ) );
                        }

                        // Delete the transfer
                        $wpdb->query( $wpdb->prepare( "DELETE FROM {$transfers} WHERE transfer_id = %d", $transfer->transfer_id ) );

                        // Let user know which transfer has been erased
                        $response['messages'] = sprintf( __( 'Transfer #%d with status %s successfully erased.', 'gamipress-transfers' ), $transfer->number, $transfer_status_label );

                        break;
                    case 'anonymize':

                        $ct_table = ct_setup_table( 'gamipress_transfers' );

                        $ct_table->db->update(
                            array(
                                'user_id' => 0,                                                     // Unset user ID
                                'user_ip' => wp_privacy_anonymize_ip( $transfer->user_ip ),          // Anonymize user IP
                            ),
                            array( 'transfer_id' => $transfer->transfer_id )
                        );

                        // Let user know which transfer has been anonymized
                        $response['messages'] = sprintf( __( 'Transfer #%d with status %s successfully anonymized.', 'gamipress-transfers' ), $transfer->number, $transfer_status_label );

                        break;
                }

            }

        }

        // Check remaining items
        $items_count = absint( $wpdb->get_var( $wpdb->prepare( $count_query, $user->ID ) ) );

        // Process done! Since all user transfers has been anonymized
        $response['done'] = (bool) ( $items_count === 0 );

    }

    // Return our erased items
    return $response;

}