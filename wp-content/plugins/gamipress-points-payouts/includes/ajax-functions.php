<?php
/**
 * Ajax_Functions
 *
 * @package GamiPress\Points_Payouts\Ajax_Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function to process the points payout
 *
 * @since 1.0.0
 */
function gamipress_points_payouts_ajax_process_points_payout() {

    global $wpdb;

    $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

    // Security check
    if ( ! wp_verify_nonce( $nonce, 'gamipress_points_payouts_form' ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-points-payouts' ) );
    }

    // Check the user ID
    $user_id = get_current_user_id();

    if( $user_id === 0 ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-points-payouts' ) );
    }

    /* ----------------------------
     * Form processing
     ---------------------------- */

    // Check points type and its conversion
    $points_types = gamipress_get_points_types();
    $points_type = isset( $_POST['points_type'] ) ? $_POST['points_type'] : '';

    if( ! isset( $points_types[$points_type] ) ) {
        wp_send_json_error( __( 'Invalid points type.', 'gamipress-points-payouts' ) );
    }

    $enabled = (bool) gamipress_get_post_meta( $points_types[$points_type]['ID'], '_gamipress_points_payouts_enable', true );

    // Skip points type if hasn't enabled for points payouts
    if( ! $enabled ) {
        wp_send_json_error( __( 'Invalid points type.', 'gamipress-points-payouts' ) );
    }

    $points_type_object = $points_types[$points_type];

    $amount = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;

    // Check if amount is correct
    if( $amount <= 0 ) {
        wp_send_json_error( __( 'Invalid amount.', 'gamipress-points-payouts' ) );
    }

    // Check if amount doesn't exceeds limits
    $min = (int) gamipress_get_post_meta( $points_types[$points_type]['ID'], '_gamipress_points_payouts_min_amount', true );
    $max = (int) gamipress_get_post_meta( $points_types[$points_type]['ID'], '_gamipress_points_payouts_max_amount', true );

    if( $min !== 0 && $amount < $min ) {
        wp_send_json_error( __( 'Invalid amount.', 'gamipress-points-payouts' ) );
    }

    if( $max !== 0 && $amount > $max ) {
        wp_send_json_error( __( 'Invalid amount.', 'gamipress-points-payouts' ) );
    }

    $user_points = gamipress_get_user_points( $user_id, $points_type );

    // Check if user has the amount he wants to withdrawal
    if( $amount > $user_points ) {
        wp_send_json_error( sprintf( __( 'Insufficient %s to withdrawal.', 'gamipress-points-payouts' ), $points_type_object['plural_name'] ) );
    }

    $money = gamipress_points_payouts_convert_to_money( $amount, $points_type );

    $payment_method = isset( $_POST['payment_method'] ) ? $_POST['payment_method'] : '';

    if( ! empty( $payment_method ) ) {
        gamipress_update_user_meta( $user_id, '_gamipress_points_payouts_payment_method', $payment_method );
    }

    /* ----------------------------
     * Everything done, so process it!
     ---------------------------- */

    // Deduct points to user
    gamipress_deduct_points_to_user( $user_id, $amount, $points_type, array(
        'reason' => __( '{user} has withdrawn {points} {points_type}', 'gamipress-points-payouts' )
    ) );

    // Lets to create the points payout
    $ct_table = ct_setup_table( 'gamipress_points_payouts' );

    $points_payout = array(
        'user_id'       => $user_id,
        'points'        => $amount,
        'points_type'   => $points_type,
        'money'         => $money,
        'status'        => 'pending',
        'date'          => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
    );

    $points_payout_id = $ct_table->db->insert( $points_payout );

    // Store the given points payout id
    $points_payout['points_payout_id'] = $points_payout_id;

    // Setup vars for coming filters
    $points_payout_link = gamipress_points_payouts_get_points_payout_details_link( $points_payout_id );

    // Send email about the new points payout
    gamipress_points_payouts_send_payout_request_email( (object) $points_payout );

    /* ----------------------------
     * Response processing
     ---------------------------- */

    $response = array(
        'success'       => true,
        'message'       => '',
        'redirect'      => $points_payout_link ? true : false,
        'redirect_url'  => $points_payout_link,
    );

    // Update message
    $response['message'] = __( 'Your withdrawal has been made successfully and is waiting for approval.', 'gamipress-points-payouts' );

    // Just add the "Redirecting ..." part if points payout link is set
    if( $points_payout_link ) {
        $response['message'] .= ' ' . __( 'Redirecting to withdrawal details ...', 'gamipress-points-payouts' );
    }

    /**
     * Let other functions process the points payout and get their response
     *
     * @since 1.0.0
     *
     * @param array     $response       Processing response
     * @param array     $points_payout  Points payout data array
     *
     * @return array    $response       Response
     */
    $response = apply_filters( "gamipress_points_payouts_process_points_payout_response", $response, $points_payout );

    if( $response['success'] === true ) {
        wp_send_json_success( $response );
    } else {
        wp_send_json_error( $response );
    }

}
add_action( 'wp_ajax_gamipress_points_payouts_process_points_payout', 'gamipress_points_payouts_ajax_process_points_payout' );