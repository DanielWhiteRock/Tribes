<?php
/**
 * Emails
 *
 * @package GamiPress\Submissions\Emails
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * New pending submission email
 *
 * @since 1.0.0
 *
 * @param stdClass     $submission      Submission object
 */
function gamipress_submissions_send_pending_submission_email( $submission ) {

    $disable = (bool) gamipress_submissions_get_option( 'disable_pending_submission_email', '' );

    /**
     * Filter to override the disable pending submission emails setting
     *
     * @since 1.0.0
     *
     * @param bool          $disable
     * @param stdClass      $submission     Submission object
     *
     * @return bool
     */
    $disable = apply_filters( 'gamipress_submissions_disable_pending_submission_email', $disable, $submission );

    if( $disable ) {
        return;
    }

    // Setup subject and content
    $subject_default = __( 'New submission #{id}', 'gamipress-submissions' );
    $content_default = __( '{user} sent a new submission for {post_link}.', 'gamipress-submissions' )
        .  "\n" . __( 'Additional notes: {notes}', 'gamipress-submissions' );

    $subject = gamipress_submissions_get_option( 'pending_submission_subject', $subject_default );
    $content = gamipress_submissions_get_option( 'pending_submission_content', $content_default );

    /**
     * Filter to override the pending submission email subject
     *
     * @since 1.0.0
     *
     * @param string        $subject        Pending submission email subject
     * @param stdClass      $submission     Submission object
     *
     * @return string
     */
    $subject = apply_filters( 'gamipress_submissions_pending_submission_email_subject', $subject, $submission );

    /**
     * Filter to override the pending submission email content
     *
     * @since 1.0.0
     *
     * @param string        $content        Pending submission email content
     * @param stdClass      $submission     Submission object
     *
     * @return string
     */
    $content = apply_filters( 'gamipress_submissions_pending_submission_email_content', $content, $submission );

    // Parse tags
    $subject = gamipress_submissions_parse_pattern( $subject, $submission->user_id, $submission );
    $content = gamipress_submissions_parse_pattern( $content, $submission->user_id, $submission );

    $subject = do_shortcode( $subject );
    $content = do_shortcode( $content );

    // Skip if not subject or content provided
    if( empty( $subject ) || empty( $content ) ) {
        return;
    }

    $to = array( get_option( 'admin_email' ) );

    /**
     * Filter to override the pending submission email to
     *
     * @since 1.0.0
     *
     * @param array         $to             Pending submission email to
     * @param stdClass      $submission     Submission object
     *
     * @return array
     */
    $to = apply_filters( 'gamipress_submissions_pending_submission_email_to', $to, $submission );

    foreach( $to as $email ) {
        // Send the email
        gamipress_send_email( $email, $subject, $content );
    }

}