<?php
/**
 * Submissions
 *
 * @package GamiPress\Submissions\Submissions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the registered submission statuses
 *
 * @since  1.0.0
 *
 * @return array Array of submission statuses
 */
function gamipress_submissions_get_submission_statuses() {

    return apply_filters( 'gamipress_submissions_get_submission_statuses', array(
        'pending'   => __( 'Pending', 'gamipress-submissions' ),
        'approved'  => __( 'Approved', 'gamipress-submissions' ),
        'rejected'  => __( 'Rejected', 'gamipress-submissions' ),
        'revoked'   => __( 'Revoked', 'gamipress-submissions' ),
    ) );

}

/**
 * Get pending submission for a specific item
 *
 * @since  1.0.0
 *
 * @param int $user_id The user ID
 * @param int $post_id The post ID
 *
 * @return stdClass|false
 */
function gamipress_submissions_get_user_pending_submission( $user_id, $post_id ) {

    global $wpdb;

    $ct_table = ct_setup_table( 'gamipress_submissions' );

    $submission = $wpdb->get_row( "SELECT *
        FROM {$ct_table->db->table_name} AS s 
        WHERE s.user_id = {$user_id}
        AND s.post_id = {$post_id}
        AND s.status = 'pending'
        LIMIT 1" );

    ct_reset_setup_table();

    if( $submission ) {
        return $submission;
    } else {
        return false;
    }

}

/**
 * Approve a submission
 *
 * @since  1.0.0
 *
 * @return bool Array of submission statuses
 */
function gamipress_submissions_approve_submission( $submission_id ) {

    // Setup the CT Table
    $ct_table = ct_setup_table( 'gamipress_submissions' );

    // Check the object
    $submission = ct_get_object( $submission_id );

    if( ! $submission ) {
        return false;
    }

    // Only can approve pending submissions
    if( $submission->status !== 'pending' ) {
        return false;
    }

    // Award item to the user
    $post = gamipress_get_post( $submission->post_id );

    if( $post ) {

        if( in_array( $post->post_type, gamipress_get_achievement_types_slugs() ) ) {
            // Award the achievement
            gamipress_award_achievement_to_user( $submission->post_id, $submission->user_id, get_current_user_id() );
        } else if( in_array( $post->post_type, gamipress_get_achievement_types_slugs() ) ) {
            // Award the rank
            gamipress_award_rank_to_user( $submission->post_id, $submission->user_id, array( 'admin_id' => get_current_user_id() ) );
        }

    }

    // Update the submission status
    $ct_table->db->update(
        array( 'status' => 'approved' ),
        array( 'submission_id' => $submission_id )
    );

    return true;

}