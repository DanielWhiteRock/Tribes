<?php
/**
 * Ajax Functions
 *
 * @package     GamiPress\Points_Exchanges\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function to process the exchange
 *
 * @since 1.0.0
 */
function gamipress_points_exchanges_ajax_process_exchange() {

    $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

    // Security check
    if ( ! wp_verify_nonce( $nonce, 'gamipress_points_exchanges_exchange_form' ) )
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-points-exchanges' ) );

    // Check form parameters
    $form_id = isset( $_POST['form_id'] ) ? $_POST['form_id'] : '';

    if( empty( $form_id ) )
        wp_send_json_error( __( 'Invalid form ID.', 'gamipress-points-exchanges' ) );

    // Check current user
    $user_id = get_current_user_id();

    if( $user_id === 0 )
        wp_send_json_error( __( 'You need to login first.', 'gamipress-points-exchanges' ) );

    // Check the amount
    $amount = isset( $_POST['amount'] ) ? absint( $_POST['amount'] ) : 0;

    if( $amount <= 0 )
        wp_send_json_error( __( 'Invalid amount.', 'gamipress-points-exchanges' ) );

    // Check the from points type
    $from = isset( $_POST['from'] ) ? $_POST['from'] : '';

    // Check if points type is has exchanges enabled
    if( ! gamipress_points_exchanges_is_exchanges_enabled( $from ) )
        wp_send_json_error( __( 'Invalid amount\'s points type.', 'gamipress-points-exchange' ) );

    $user_points = gamipress_get_user_points( $user_id, $from );

    // Check if user has the amount he wants to exchange
    if( $amount > $user_points )
        wp_send_json_error( sprintf( __( 'Insufficient %s to exchange.', 'gamipress-points-exchange' ), gamipress_get_points_type_plural( $from ) ) );

    // Check the to points type
    $to = isset( $_POST['to'] ) ? $_POST['to'] : '';
    $rates = gamipress_points_exchanges_get_rates( $from );

    if( ! isset( $rates[$to] ) )
        wp_send_json_error( __( 'Invalid points type to exchange.', 'gamipress-points-exchange' ) );

    // Check the rate to apply
    $exchanged_amount = gamipress_points_exchanges_apply_rate( $amount, $from, $to );

    if( ! $exchanged_amount )
        wp_send_json_error( __( 'Invalid points type to exchange.', 'gamipress-points-exchange' ) );

    /* ----------------------------
     * Everything done, so process it!
     ---------------------------- */

    // Deduct to the user the amount of points
    gamipress_deduct_points_to_user( $user_id, $amount, $from );

    // Award to the user the new amount
    gamipress_award_points_to_user( $user_id, $exchanged_amount, $to );

    gamipress_points_exchanges_log_points_exchange( $user_id, $amount, $from, $to, $exchanged_amount );

    $response = array(
        'success'       => true,
        'message'       => __( 'Exchange has been completed successfully.', 'gamipress-points-exchanges' )
    );

    /**
     * Let other functions process the exchange and get their response
     *
     * @since 1.0.0
     *
     * @param array     $response           Processing response
     * @param int       $user_id            User that perform the exchange
     * @param int       $amount             Amount to exchange
     * @param string    $from               From points type
     * @param string    $to                 To points type
     * @param int       $exchanged_amount   Exchanged amount
     *
     * @return array    $response           Response
     */
    $response = apply_filters( 'gamipress_points_exchanges_process_exchange', $response, $user_id, $amount, $from, $to, $exchanged_amount );

    if( $response['success'] === true )
        wp_send_json_success( $response );
    else
        wp_send_json_error( $response );
}
add_action( 'wp_ajax_gamipress_points_exchanges_process_exchange', 'gamipress_points_exchanges_ajax_process_exchange' );
