<?php
/**
 * Ajax Functions
 *
 * @package     GamiPress\Purchases\Ajax Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function to process the purchase
 *
 * @since 1.0.0
 */
function gamipress_purchases_ajax_process_purchase() {

    $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

    // Security check
    if ( ! wp_verify_nonce( $nonce, 'gamipress_purchases_purchase_form' ) )
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-purchases' ) );

    // Check purchase form parameters
    $purchase_key = isset( $_POST['purchase_key'] ) ? $_POST['purchase_key'] : '';
    $form_id = 'gamipress-purchases-form-' . $purchase_key;

    if( empty( $purchase_key ) )
        wp_send_json_error( __( 'Invalid transaction ID.', 'gamipress-purchases' ) );

    /* ----------------------------
     * User billing details
     ---------------------------- */

    // Check current user
    $user_id = get_current_user_id();

    if( $user_id === 0 )
        wp_send_json_error( __( 'You need to log in first.', 'gamipress-purchases' ) );

    // Check user details
    $current_user_details = gamipress_purchases_get_user_billing_details( $user_id );

    $user_details = array(
        'first_name' => isset( $_POST[$form_id . '-first-name'] ) ? $_POST[$form_id . '-first-name'] : $current_user_details['first_name'],
        'last_name' => isset( $_POST[$form_id . '-last-name'] ) ? $_POST[$form_id . '-last-name'] : $current_user_details['last_name'],
        'email' => isset( $_POST[$form_id . '-email'] ) ? $_POST[$form_id . '-email'] : $current_user_details['email'],
        'address_1' => isset( $_POST[$form_id . '-address-1'] ) ? $_POST[$form_id . '-address-1'] : $current_user_details['address_1'],
        'address_2' => isset( $_POST[$form_id . '-address-2'] ) ? $_POST[$form_id . '-address-2'] : $current_user_details['address_2'],
        'city' => isset( $_POST[$form_id . '-city'] ) ? $_POST[$form_id . '-city'] : $current_user_details['city'],
        'postcode' => isset( $_POST[$form_id . '-postcode'] ) ? $_POST[$form_id . '-postcode'] : $current_user_details['postcode'],
        'country' => isset( $_POST[$form_id . '-country'] ) ? $_POST[$form_id . '-country'] : $current_user_details['country'],
        'state' => isset( $_POST[$form_id . '-state'] ) ? $_POST[$form_id . '-state'] : $current_user_details['state'],
    );

    // First name, last name and email are required
    if( empty( $user_details['first_name'] ) || empty( $user_details['last_name'] ) || empty( $user_details['email'] ) )
        wp_send_json_error( __( 'You need to fill your personal info.', 'gamipress-purchases' ) );

    // Check address fields
    if( empty( $user_details['address_1'] ) || empty( $user_details['city'] ) || empty( $user_details['postcode'] ) || empty( $user_details['country'] ) || empty( $user_details['state'] ) )
        wp_send_json_error( __( 'You need to fill your billing address.', 'gamipress-purchases' ) );

    /* ----------------------------
     * Gateways
     ---------------------------- */

    // Check payment gateway
    $gateway_id = isset( $_POST[$form_id . '-gateway'] ) ? $_POST[$form_id . '-gateway'] : '';

    $active_gateways = gamipress_purchases_get_active_gateways();

    if( ! in_array( $gateway_id, array_keys( $active_gateways ) ) )
        wp_send_json_error( __( 'Invalid payment method.', 'gamipress-purchases' ) );

    /* ----------------------------
     * Purchase type (achievement/points or rank) an total
     ---------------------------- */

    // First, we need to decide if is an achievement/points or rank purchase form
    $purchase_type = isset( $_POST['purchase_type'] ) ? $_POST['purchase_type'] : '';

    if( ! in_array( $purchase_type, array( 'points', 'achievement', 'rank' ) ) )
        wp_send_json_error( __( 'Form not well configured.', 'gamipress-purchases' ) );

    $subtotal = 0;

    // Setup vars based on purchase type
    if( $purchase_type === 'points' ) {
        // Points purchase

        // Check points type and its conversion
        $points_types = gamipress_get_points_types();
        $points_type = isset( $_POST['points_type'] ) ? $_POST['points_type'] : '';

        if( ! isset( $points_types[$points_type] ) )
            wp_send_json_error( __( 'Invalid points type.', 'gamipress-purchases' ) );

        $points_type_object = $points_types[$points_type];
        $conversion = gamipress_purchases_get_conversion( $points_type );

        if( ! $conversion )
            wp_send_json_error( __( 'Conversion was not well configured.', 'gamipress-purchases' ) );

        $points_price  = gamipress_purchases_convert_to_money( 1, $points_type );

        // Check the amount type (points could send an amount of points to convert it in money based on configs)
        $amount_type = isset( $_POST['amount_type'] ) ? $_POST['amount_type'] : '';

        if( empty( $amount_type ) ) {
            wp_send_json_error( __( 'Invalid amount type.', 'gamipress-purchases' ) );
        }

        $amount = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;

        if( $amount <= 0 )
            wp_send_json_error( __( 'Invalid amount.', 'gamipress-purchases' ) );

        // Calculate payment total based on the amount type

        if( $amount_type === 'points' ) {
            $subtotal = gamipress_purchases_convert_to_money( $amount, $points_type );
            $points = $amount;
        } else {
            $subtotal = $amount;
            $points = gamipress_purchases_convert_to_points( $amount, $points_type );
        }

        // Setup the payment item data
        $payment_item = array(
            'post_id' => $points_type_object['ID'],
            'post_type' => $points_type,
            'description' => $points_type_object['plural_name'],
            'quantity' => $points,
            'price' => $points_price,
            'total' => $subtotal,
        );

    } else if( $purchase_type === 'achievement' ) {
        // Achievement purchase

        // Check the achievement
        $achievement_id = isset( $_POST['achievement_id'] ) ? $_POST['achievement_id'] : '';
        $achievement = gamipress_get_post( $achievement_id );

        if( ! $achievement )
            wp_send_json_error( __( 'Invalid achievement.', 'gamipress-purchases' ) );

        // Check achievement type
        $achievement_types = gamipress_get_achievement_types();
        $achievement_type = $achievement->post_type;

        if( ! isset( $achievement_types[$achievement_type] ) )
            wp_send_json_error( __( 'Invalid achievement type.', 'gamipress-purchases' ) );

        // Payment total is the achievement price
        $subtotal = gamipress_get_post_meta( $achievement_id, '_gamipress_purchases_price', true );

        // Setup the payment item data
        $payment_item = array(
            'post_id' => $achievement_id,
            'post_type' => $achievement_type,
            'description' => $achievement_types[$achievement_type]['singular_name'] . ': ' . $achievement->post_title,
            'quantity' => 1,
            'price' => $subtotal,
            'total' => $subtotal,
        );

    } else if( $purchase_type === 'rank' ) {
        // Rank purchase

        // Check the rank
        $rank_id = isset( $_POST['rank_id'] ) ? $_POST['rank_id'] : '';
        $rank = gamipress_get_post( $rank_id );

        if( ! $rank )
            wp_send_json_error( __( 'Invalid rank.', 'gamipress-purchases' ) );

        // Check achievement type
        $rank_types = gamipress_get_rank_types();
        $rank_type = $rank->post_type;

        if( ! isset( $rank_types[$rank_type] ) )
            wp_send_json_error( __( 'Invalid rank type.', 'gamipress-purchases' ) );

        // Payment total is the rank price
        $subtotal = gamipress_get_post_meta( $rank_id, '_gamipress_purchases_price', true );

        // Setup the payment item data
        $payment_item = array(
            'post_id' => $rank_id,
            'post_type' => $rank_type,
            'description' => $rank_types[$rank_type]['singular_name'] . ': ' . $rank->post_title,
            'quantity' => 1,
            'price' => $subtotal,
            'total' => $subtotal,
        );

    }

    // Last check of purchase subtotal
    $subtotal = gamipress_purchases_convert_to_float( $subtotal );

    if( $subtotal <= 0 )
        wp_send_json_error( __( 'Invalid payment total.', 'gamipress-purchases' ) );

    // Calculate taxes
    $tax_rate = gamipress_purchases_get_tax_rate( $user_details['country'], $user_details['state'], $user_details['postcode'] );
    $tax = $tax_rate * 100;
    $tax_amount = $subtotal * $tax_rate;

    // The invoice total
    $total = $subtotal + $tax_amount;

    /* ----------------------------
     * Everything done, so process it!
     ---------------------------- */

    // Update user payment details
    gamipress_purchases_update_user_billing_details( $user_id, $user_details );

    // Lets to create the payment
    $ct_table = ct_setup_table( 'gamipress_payments' );

    $payment = array(

        // Payment details

        'number' => gamipress_purchases_get_payment_next_payment_number(),
        'date' => date( 'Y-m-d H:i:s' ),
        'status' => 'processing',
        'gateway' => $gateway_id,
        'purchase_key' => $purchase_key,
        'transaction_id' => '', // Transaction ID is provided by the gateway
        'subtotal' => $subtotal,
        'tax' => $tax,
        'tax_amount' => $tax_amount,
        'total' => $total,

        // User details

        'user_id' => $user_id,
        'user_ip' => gamipress_purchases_get_ip(),
        'first_name' => $user_details['first_name'],
        'last_name' => $user_details['last_name'],
        'email' => $user_details['email'],
        'address_1' => $user_details['address_1'],
        'address_2' => $user_details['address_2'],
        'city' => $user_details['city'],
        'postcode' => $user_details['postcode'],
        'country' => $user_details['country'],
        'state' => $user_details['state'],

    );

    $payment_id = $ct_table->db->insert( $payment );

    // Store the given payment id to assign it to the payment items and for hooks
    $payment['payment_id'] = $payment_id;

    // Lets to create the payment items (just one, with the amount of points)
    $ct_table = ct_setup_table( 'gamipress_payment_items' );

    $payment_item['payment_id'] = $payment_id;

    $payment_item_id = $ct_table->db->insert( $payment_item );

    $payment_item['payment_item_id'] = $payment_item_id;

    // Setup vars for gateways
    $payment_items = array( $payment_item );

    /* ----------------------------
     * Gateway processing
     ---------------------------- */

    $gateway_response = array(
        'success' => false,
        'message' => '',
        'redirect' => false,
        'redirect_url' => false,
    );

    /**
     * Let the gateway process the payment and get their response
     *
     * @since 1.0.0
     *
     * @param array     $gateway_response   Waiting gateway response
     * @param array     $payment            Payment data array
     * @param array     $payment_items      Payment items array
     *
     * @return array    $gateway_response  Gateway response
     */
    $gateway_response = apply_filters( "gamipress_purchases_{$gateway_id}_process_payment", $gateway_response, $payment, $payment_items );

    /* ----------------------------
     * That's all!
     ---------------------------- */

    if( $gateway_response['success'] === true ) {
        wp_send_json_success( $gateway_response );
    } else {
        wp_send_json_error( $gateway_response );
    }

}
add_action( 'wp_ajax_gamipress_purchases_process_purchase', 'gamipress_purchases_ajax_process_purchase' );

/**
 * Ajax function to load user billing details at backend
 *
 * @since 1.0.0
 */
function gamipress_purchases_ajax_get_user_billing_details() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Security check
    if( ! current_user_can( gamipress_get_manager_capability() ) )
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-purchases' ) );

    $user_id = absint( $_REQUEST['user_id'] );

    // Get the user details to return as response
    $user_details = gamipress_purchases_get_user_billing_details( $user_id );

    wp_send_json_success( $user_details );

}
add_action( 'wp_ajax_gamipress_purchases_get_user_billing_details', 'gamipress_purchases_ajax_get_user_billing_details' );

/**
 * Ajax function to add a payment note at backend
 *
 * @since 1.0.0
 */
function gamipress_purchases_ajax_add_payment_note() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Security check
    if( ! current_user_can( gamipress_get_manager_capability() ) )
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-purchases' ) );

    // Setup vars
    $user_id = get_current_user_id();
    $payment_id = absint( $_REQUEST['payment_id'] );
    $title = sanitize_text_field( $_REQUEST['title'] );
    $description = sanitize_textarea_field( $_REQUEST['description'] );

    // Check all vars
    if( $payment_id === 0 )
        wp_send_json_error( __( 'Wrong payment ID.', 'gamipress-purchases' ) );

    if( empty( $title ) )
        wp_send_json_error( __( 'Please, fill the title.', 'gamipress-purchases' ) );

    if( empty( $description ) )
        wp_send_json_error( __( 'Please, fill the note.', 'gamipress-purchases' ) );

    // Setup the payment notes table
    $ct_table = ct_setup_table( 'gamipress_payment_notes' );

    // Insert the new payment note
    $payment_note_id = $ct_table->db->insert( array(
        'payment_id' => $payment_id,
        'title' => $title,
        'description' => $description,
        'user_id' => $user_id,
        'date' => date( 'Y-m-d H:i:s' )
    ) );

    // Get the payment note object
    $payment_note = ct_get_object( $payment_note_id );

    // Setup the payments table
    ct_setup_table( 'gamipress_payments' );

    // Get the payment object
    $payment = ct_get_object( $payment_id );

    // Get the payment note html to return as response
    ob_start();

    gamipress_purchases_admin_render_payment_note( $payment_note, $payment );

    $response = ob_get_clean();

    wp_send_json_success( $response );

}
add_action( 'wp_ajax_gamipress_purchases_add_payment_note', 'gamipress_purchases_ajax_add_payment_note' );

/**
 * Ajax function to delete a payment note at backend
 *
 * @since 1.0.0
 */
function gamipress_purchases_ajax_delete_payment_note() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Security check
    if( ! current_user_can( gamipress_get_manager_capability() ) )
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-purchases' ) );

    // Setup vars
    $payment_note_id = absint( $_REQUEST['payment_note_id'] );

    // Check all vars
    if( $payment_note_id === 0 )
        wp_send_json_error( __( 'Wrong payment note ID.', 'gamipress-purchases' ) );

    // Setup the payment notes table
    $ct_table = ct_setup_table( 'gamipress_payment_notes' );

    $result = $ct_table->db->delete( $payment_note_id );

    wp_send_json_success( __( 'Payment note deleted successfully.', 'gamipress-purchases' ) );

}
add_action( 'wp_ajax_gamipress_purchases_delete_payment_note', 'gamipress_purchases_ajax_delete_payment_note' );