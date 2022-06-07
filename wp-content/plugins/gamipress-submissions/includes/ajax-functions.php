<?php
/**
 * Ajax Functions
 *
 * @package GamiPress\Submissions\Ajax_Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function to process the submission
 *
 * @since 1.0.0
 */
function gamipress_submissions_ajax_process_submission() {

    $prefix = '_gamipress_submissions_';

    $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

    // Security check
    if ( ! wp_verify_nonce( $nonce, 'gamipress' ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-submissions' ) );
    }

    // Check the user ID
    $user_id = get_current_user_id();

    if( $user_id === 0 ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-submissions' ) );
    }

    // Check the post ID
    $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

    $post = gamipress_get_post( $post_id );

    if( ! $post ) {
        wp_send_json_error( __( 'Invalid item.', 'gamipress-submissions' ) );
    }

    // Return if submissions is not enabled
    if( ! (bool) gamipress_get_post_meta( $post_id, $prefix . 'enable', true ) ) {
        wp_send_json_error( __( 'Submissions not enabled for this item.', 'gamipress-submissions' ) );
    }

    // Check notes
    $notes = isset( $_POST['notes'] ) ? sanitize_text_field( $_POST['notes'] ) : '';

    // Check if there is a pending submission
    $submission = gamipress_submissions_get_user_pending_submission( $user_id, $post_id );

    if( $submission ) {
        wp_send_json_error( __( 'You have already perform a submission and is waiting for approval.', 'gamipress-submissions' ) );
    }


    /* ----------------------------
     * Everything done, so process it!
     ---------------------------- */

    // Lets to create the points payout
    $ct_table = ct_setup_table( 'gamipress_submissions' );

    $submission = array(
        'user_id'       => $user_id,
        'post_id'       => $post_id,
        'notes'         => $notes,
        'status'        => 'pending',
        'date'          => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
    );

    $submission_id = $ct_table->db->insert( $submission );

    // Store the given points payout id
    $submission['submission_id'] = $submission_id;

    $auto_approve = (bool) gamipress_get_post_meta( $post_id, $prefix . 'auto_approve', true );

    if( $auto_approve ) {
        // Auto-approve the submission
        gamipress_submissions_approve_submission( $submission_id );
    } else {
        // Send pending email about the new submission
        gamipress_submissions_send_pending_submission_email( (object) $submission );
    }


    /* ----------------------------
     * Response processing
     ---------------------------- */

    $response = array(
        'success'       => true,
        'message'       => '',
    );

    // Update message
    if( $auto_approve ) {
        $response['message'] = __( 'Your submission has been sent successfully.', 'gamipress-submissions' );
    } else {
        $response['message'] = __( 'Your submission has been sent successfully and is waiting for approval.', 'gamipress-submissions' );
    }

    /**
     * Let other functions process the points payout and get their response
     *
     * @since 1.0.0
     *
     * @param array     $response       Processing response
     * @param array     $submission     Submission data array
     *
     * @return array    $response       Response
     */
    $response = apply_filters( "gamipress_submissions_process_submission_response", $response, $submission );

    if( $response['success'] === true ) {
        wp_send_json_success( $response );
    } else {
        wp_send_json_error( $response );
    }

}
add_action( 'wp_ajax_gamipress_submissions_process_submission', 'gamipress_submissions_ajax_process_submission' );
