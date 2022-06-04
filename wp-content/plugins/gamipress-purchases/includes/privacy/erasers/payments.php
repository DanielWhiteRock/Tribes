<?php
/**
 * Payments Erasers
 *
 * @package     GamiPress\Privacy\Erasers\Payments
 * @since       1.5.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register eraser for user payments.
 *
 * @since 1.5.0
 *
 * @param array $erasers
 *
 * @return array
 */
function gamipress_purchases_privacy_register_payments_erasers( $erasers ) {

    $erasers[] = array(
        'eraser_friendly_name'    => __( 'Orders', 'gamipress-purchases' ),
        'callback'                => 'gamipress_purchases_privacy_payments_eraser',
    );

    return $erasers;

}
add_filter( 'wp_privacy_personal_data_erasers', 'gamipress_purchases_privacy_register_payments_erasers' );

/**
 * Eraser for user payments.
 *
 * @since 1.5.0
 *
 * @param string    $email_address
 * @param int       $page
 *
 * @return array
 */
function gamipress_purchases_privacy_payments_eraser( $email_address, $page = 1 ) {

    global $wpdb;

    // Setup query vars
    $payments = GamiPress()->db->payments;
    $payment_items = GamiPress()->db->payment_items;
    $payment_notes = GamiPress()->db->payment_notes;

    // Important: keep always SELECT *, %d is user ID, and limit/offset will added automatically
    $query = "SELECT * FROM {$payments} WHERE user_id = %d";
    $count_query = str_replace( "SELECT *", "SELECT COUNT(*)", $query );

    // Setup vars
    $payment_statuses = gamipress_purchases_get_payment_statuses();
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

        // Get user payments
        $user_payments = $wpdb->get_results( $wpdb->prepare(
            $query . " LIMIT {$offset}, {$limit}",
            $user->ID
        ) );

        if( is_array( $user_payments ) ) {

            foreach( $user_payments as $payment ) {

                // First decide which action to perform
                switch ( $payment->status ) {
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

                $payment_status_label = isset( $payment_statuses[$payment->status] ) ? $payment_statuses[$payment->status] : $payment->status;

                switch( $action ) {
                    case 'none':
                    default:

                        // Inform that there is items retained
                        $response['items_retained'] = true;

                        // Let user know which order has been retained
                        $response['messages'] = sprintf( __( 'Order #%d not modified, due to status: %s.', 'gamipress-purchases' ), $payment->number, $payment_status_label );

                        break;
                    case 'erase':

                        // Delete all payment items
                        $items = gamipress_purchases_get_payment_items( $payment->payment_id );

                        foreach( $items as $item ) {
                            $wpdb->query( $wpdb->prepare( "DELETE FROM {$payment_items} WHERE payment_item_id = %d", $item->payment_item_id ) );
                        }

                        // Delete all payment notes
                        $notes = gamipress_purchases_get_payment_notes( $payment->payment_id );

                        foreach( $notes as $note ) {
                            $wpdb->query( $wpdb->prepare( "DELETE FROM {$payment_notes} WHERE payment_note_id = %d", $note->payment_note_id ) );
                        }

                        // Delete the payment
                        $wpdb->query( $wpdb->prepare( "DELETE FROM {$payments} WHERE payment_id = %d", $payment->payment_id ) );

                        // Let user know which order has been erased
                        $response['messages'] = sprintf( __( 'Order #%d with status %s successfully erased.', 'gamipress-purchases' ), $payment->number, $payment_status_label );

                        break;
                    case 'anonymize':

                        $ct_table = ct_setup_table( 'gamipress_payments' );

                        $ct_table->db->update(
                            array(
                                'user_id' => 0,                                                     // Unset user ID
                                'user_ip' => wp_privacy_anonymize_ip( $payment->user_ip ),          // Anonymize user IP
                                'first_name' => __( 'Anonymized User', 'gamipress-purchases' ),     // Anonymize first name
                                'last_name' => '',                                                  // Unset last name
                                'email' => gamipress_purchases_anonymize_email( $payment->email ),  // Anonymize email
                                'address_1' => '',                                                  // Unset address line 1
                                'address_2' => '',                                                  // Unset address line 2
                            ),
                            array( 'payment_id' => $payment->payment_id )
                        );

                        // Let user know which order has been anonymized
                        $response['messages'] = sprintf( __( 'Order #%d with status %s successfully anonymized.', 'gamipress-purchases' ), $payment->number, $payment_status_label );

                        break;
                }

            }

        }

        // Check remaining items
        $items_count = absint( $wpdb->get_var( $wpdb->prepare( $count_query, $user->ID ) ) );

        // Process done! Since all user payments has been anonymized
        $response['done'] = (bool) ( $items_count === 0 );

    }

    // Return our erased items
    return $response;

}