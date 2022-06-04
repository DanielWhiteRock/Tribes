<?php
/**
 * Payments Exporters
 *
 * @package     GamiPress\Purchases\Privacy\Exporters\Payments
 * @since       1.0.2
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register exporter for user payments.
 *
 * @since 1.0.2
 *
 * @param array $exporters
 *
 * @return array
 */
function gamipress_purchases_privacy_register_payments_exporters( $exporters ) {

    $exporters[] = array(
        'exporter_friendly_name'    => __( 'Orders', 'gamipress-purchases' ),
        'callback'                  => 'gamipress_purchases_privacy_payments_exporter',
    );

    return $exporters;

}
add_filter( 'wp_privacy_personal_data_exporters', 'gamipress_purchases_privacy_register_payments_exporters' );

/**
 * Exporter for user payments.
 *
 * @since 1.0.2
 *
 * @param string    $email_address
 * @param int       $page
 *
 * @return array
 */
function gamipress_purchases_privacy_payments_exporter( $email_address, $page = 1 ) {

    global $wpdb;

    // Setup query vars
    $payments = GamiPress()->db->payments;

    // Important: keep always SELECT *, %d is user ID, and limit/offset will added automatically
    $query = "SELECT * FROM {$payments} WHERE user_id = %d";
    $count_query = str_replace( "SELECT *", "SELECT COUNT(*)", $query );

    // Setup vars
    $export_items   = array();
    $limit = 500;
    $offset = $page - 1;
    $done = false;

    $user = get_user_by( 'email', $email_address );

    if ( $user && $user->ID ) {

        // Get user payments
        $user_payments = $wpdb->get_results( $wpdb->prepare(
            $query . " LIMIT {$offset}, {$limit}",
            $user->ID
        ) );

        if( is_array( $user_payments ) ) {

            foreach( $user_payments as $user_payment ) {

                // Add the user payment to the exported items array
                $export_items[] = array(
                    'group_id'    => 'gamipress-payments',
                    'group_label' => __( 'Orders', 'gamipress-purchases' ),
                    'item_id'     => "gamipress-payments-{$user_payment->payment_id}",
                    'data'        => gamipress_purchases_privacy_get_payment_data( $user_payment ),
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
 * Function to retrieve payment data.
 *
 * @since 1.0.2
 *
 * @param stdClass $payment
 *
 * @return array
 */
function gamipress_purchases_privacy_get_payment_data( $payment ) {

    // Prefix for meta data
    $prefix = '_gamipress_purchases_';

    // Setup CT table
    ct_setup_table( 'gamipress_payments' );

    $data = array();

    // Payment number

    $data['number'] = array(
        'name' => __( 'Order Number', 'gamipress-purchases' ),
        'value' => $payment->number,
    );

    // Payment number

    $data['date'] = array(
        'name' => __( 'Order Date', 'gamipress-purchases' ),
        'value' => $payment->date,
    );

    // Payment status

    $payment_statuses = gamipress_purchases_get_payment_statuses();

    $data['status'] = array(
        'name' => __( 'Order Status', 'gamipress-purchases' ),
        'value' => isset( $payment_statuses[$payment->status] ) ? $payment_statuses[$payment->status] : $payment->status,
    );

    // Payment items

    $data['items'] = array(
        'name' => __( 'Order Items', 'gamipress-purchases' ),
        'value' => gamipress_purchases_privacy_get_payment_items_details( $payment ),
    );

    // Payment total

    $data['total'] = array(
        'name' => __( 'Order Total', 'gamipress-purchases' ),
        'value' => gamipress_purchases_format_price( $payment->total ),
    );

    // User details

    $data['user-details'] = array(
        'name' => __( 'User Details', 'gamipress-purchases' ),
        'value' => gamipress_purchases_privacy_get_payment_user_details( $payment ),
    );

    // User IP

    $data['user-ip'] = array(
        'name' => __( 'IP Address', 'gamipress-purchases' ),
        'value' => $payment->user_ip,
    );

    /**
     * User payment to export
     *
     * @param array     $data           The user payments data to export
     * @param int       $user_id        The user ID
     * @param stdClass  $payment        The payment object
     */
    return apply_filters( 'gamipress_purchases_privacy_get_payment_data', $data, $payment->user_id, $payment );

}

/**
 * Function to retrieve payment items details.
 *
 * @since 1.0.2
 *
 * @param stdClass $payment
 *
 * @return string
 */
function gamipress_purchases_privacy_get_payment_items_details( $payment ) {

    $items_details = '';

    $payment_items = gamipress_purchases_get_payment_items( $payment->payment_id );

    foreach( $payment_items as $payment_item ) {

        $item_details = $payment_item->description . ' x' . $payment_item->quantity;

        /**
         * Single payment item details to export
         *
         * @param string    $item_details   The payment's item details data to export
         * @param int       $user_id        The user ID
         * @param stdClass  $payment        The payment object
         */
        $item_details = apply_filters( 'gamipress_purchases_privacy_get_payment_item_details', $item_details, $payment->user_id, $payment );

        $items_details .= $item_details . "\n";

    }

    /**
     * Payment items details to export
     *
     * @param string    $items_details  The payment's items details data to export
     * @param int       $user_id        The user ID
     * @param stdClass  $payment        The payment object
     */
    return apply_filters( 'gamipress_purchases_privacy_get_payment_items_details', $items_details, $payment->user_id, $payment );

}

/**
 * Function to retrieve payment's user details data.
 *
 * @since 1.0.2
 *
 * @param stdClass $payment
 *
 * @return string
 */
function gamipress_purchases_privacy_get_payment_user_details( $payment ) {

    $countries = gamipress_purchases_get_countries();

    $user_details = __( 'First Name:', 'gamipress-purchases' ) . ' ' . $payment->first_name  . "\n"
    . __( 'Last Name:', 'gamipress-purchases' ) . ' ' . $payment->last_name . "\n"
    . __( 'Email:', 'gamipress-purchases' ) . ' ' . $payment->email . "\n"
    . __( 'Address Line 1:', 'gamipress-purchases' ) . ' ' . $payment->address_1 . "\n"
    . __( 'Address Line 2:', 'gamipress-purchases' ) . ' ' . $payment->address_2 . "\n"
    . __( 'City:', 'gamipress-purchases' ) . ' ' . $payment->city . "\n"
    . __( 'Postcode / ZIP:', 'gamipress-purchases' ) . ' ' . $payment->postcode . "\n"
    . __( 'Country:', 'gamipress-purchases' ) . ' ' . ( isset( $countries[$payment->country] ) ? $countries[$payment->country] : $payment->country ) . "\n"
    . __( 'State / County:', 'gamipress-purchases' ) . ' ' . $payment->state;

    /**
     * Payment user details to export
     *
     * @param string    $user_details   The payment's user details data to export
     * @param int       $user_id        The user ID
     * @param stdClass  $payment        The payment object
     */
    return apply_filters( 'gamipress_purchases_privacy_get_payment_user_details', $user_details, $payment->user_id, $payment );

}