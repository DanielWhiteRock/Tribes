<?php
/**
 * Emails
 *
 * @package GamiPress\Points_Payouts\Emails
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * New points payout request email
 *
 * @since 1.0.0
 *
 * @param stdClass     $points_payout  Points payout object
 */
function gamipress_points_payouts_send_payout_request_email( $points_payout ) {

    $disable = (bool) gamipress_points_payouts_get_option( 'disable_payout_request_email', '' );

    /**
     * Filter to override the disable points payout email setting
     *
     * @since 1.0.0
     *
     * @param bool          $disable
     * @param stdClass      $points_payout  Points payout object
     *
     * @return bool
     */
    $disable = apply_filters( 'gamipress_points_payouts_disable_payout_request_email', $disable, $points_payout );

    if( $disable ) {
        return;
    }

    // Setup subject and content
    $subject_default = __( 'New points payout request #{id}', 'gamipress-points-payouts' );
    $content_default = __( '{user} requested a new points payout.', 'gamipress-points-payouts' )
        .  "\n" . __( 'Details:', 'gamipress-points-payouts' )
        .  "\n" . __( 'Points: {points} {points_label} for {money}', 'gamipress-points-payouts' )
        .  "\n" . __( 'Money: {money}', 'gamipress-points-payouts' )
        .  "\n" . __( '{payment_method_label}: {payment_method}', 'gamipress-points-payouts' );

    $subject = gamipress_points_payouts_get_option( 'payout_request_subject', $subject_default );
    $content = gamipress_points_payouts_get_option( 'payout_request_content', $content_default );

    /**
     * Filter to override the points payout request email subject
     *
     * @since 1.0.0
     *
     * @param string        $subject        Points payout request email subject
     * @param stdClass      $points_payout  Points payout object
     *
     * @return string
     */
    $subject = apply_filters( 'gamipress_points_payouts_payout_request_email_subject', $subject, $points_payout );

    /**
     * Filter to override the points payout request email content
     *
     * @since 1.0.0
     *
     * @param string        $content        Points payout request email content
     * @param stdClass      $points_payout  Points payout object
     *
     * @return string
     */
    $content = apply_filters( 'gamipress_points_payouts_payout_request_email_content', $content, $points_payout );

    // Parse tags
    $subject = gamipress_points_payouts_parse_pattern( $subject, $points_payout->user_id, $points_payout );
    $content = gamipress_points_payouts_parse_pattern( $content, $points_payout->user_id, $points_payout );

    $subject = do_shortcode( $subject );
    $content = do_shortcode( $content );

    // Skip if not subject or content provided
    if( empty( $subject ) || empty( $content ) ) {
        return;
    }

    $to = array( get_option( 'admin_email' ) );

    /**
     * Filter to override the points payout request email to
     *
     * @since 1.0.0
     *
     * @param array         $to             Points payout request email to
     * @param stdClass      $points_payout  Points payout object
     *
     * @return array
     */
    $to = apply_filters( 'gamipress_points_payouts_payout_request_email_to', $to, $points_payout );

    foreach( $to as $email ) {
        // Send the email
        gamipress_send_email( $email, $subject, $content );
    }

}