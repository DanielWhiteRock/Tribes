<?php
/**
 * Ajax Functions
 *
 * @package     GamiPress\Restrict_Unlock\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function to check and unlock a post by expending an amount of points
 *
 * @since 1.0.0
 *
 * @return void
 */
function gamipress_restrict_unlock_ajax_access_with_points() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_restrict_unlock', 'nonce' );

    $post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;

    $post = get_post( $post_id );

    // Return if post not exists
    if( ! $post )
        wp_send_json_error( __( 'Post not found.', 'gamipress-restrict-unlock' ) );

    $user_id = get_current_user_id();

    // Guest not supported yet (basically because they has not points)
    if( $user_id === 0 )
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-restrict-unlock' ) );

    // Return if user already has got access to this post
    if( gamipress_restrict_unlock_user_has_unlocked_post( $post_id, $user_id ) )
        wp_send_json_error( __( 'You already unlocked this.', 'gamipress-restrict-unlock' ) );

    // Check if post is unlocked by expending points or is allowed access by expending points
    if(gamipress_restrict_unlock_get_meta( $post_id, 'unlock_by' ) !== 'expend-points'
        && gamipress_restrict_unlock_get_meta( $post_id, 'access_with_points' ) !== 'on' ) {
        wp_send_json_error( __( 'You are not allowed to unlock this.', 'gamipress-restrict-unlock' ) );
    }

    $points = absint( gamipress_restrict_unlock_get_meta( $post_id, 'points_to_access' ) );

    // Return if no points configured
    if( $points === 0 )
        wp_send_json_error( __( 'You are not allowed to unlock this.', 'gamipress-restrict-unlock' ) );

    // Setup points type
    $points_types = gamipress_get_points_types();
    $points_type = gamipress_restrict_unlock_get_meta( $post_id, 'points_type_to_access' );

    // Default points label
    $points_label = __( 'Points', 'gamipress-restrict-unlock' );

    // Points type label
    if( isset( $points_types[$points_type] ) )
        $points_label = $points_types[$points_type]['plural_name'];

    // Setup user points
    $user_points = gamipress_get_user_points( $user_id, $points_type );

    if( $user_points < $points ) {

        $message = sprintf( __( 'Insufficient %s.', 'gamipress-restrict-unlock' ), $points_label );

        /**
         * Available filter to override the insufficient points text when unlock a restricted post with points
         *
         * @since   1.0.5
         *
         * @param string    $message        The insufficient points message
         * @param int       $post_id        The post ID
         * @param int       $user_id        The current logged in user ID
         * @param int       $points         The required amount of points
         * @param string    $points_type    The required amount points type
         */
        $message = apply_filters( 'gamipress_restrict_unlock_insufficient_points_message', $message, $post_id, $user_id, $points, $points_type );

        wp_send_json_error( $message );
    }

    // Deduct points to user
    gamipress_deduct_points_to_user( $user_id, $points, $points_type, array(
        'log_type' => 'points_expend',
        'reason' => sprintf( __( '{user} expended {points} {points_type} to get access to earn "%s" for a new total of {total_points} {points_type}', 'gamipress-restrict-unlock' ), $post->post_title )
    ) );

    // Register the unlock unlock on logs
    gamipress_restrict_unlock_log_unlock( $post_id, $user_id );

    $congratulations = sprintf( __( 'Congratulations! You got access to earn %s, redirecting...', 'gamipress-restrict-unlock' ), $post->post_title );

    // Filter to change congratulations message
    $congratulations = apply_filters( 'gamipress_restrict_unlock_post_accessed_with_points_congratulations', $congratulations, $post_id, $user_id, $points, $points_type );

    /**
     * Post unlocked with points action
     *
     * @since 1.0.0
     *
     * @param int       $post_id 	    The post unlocked ID
     * @param int       $user_id 	    The user ID
     * @param int       $points 	    The amount of points expended
     * @param string    $points_type    The points type of the amount of points expended
     */
    do_action( 'gamipress_restrict_unlock_post_accessed_with_points', $post_id, $user_id, $points, $points_type );

    $response = array(
        'message' => $congratulations,
        'redirect' => get_permalink( $post_id ),
    );

    // Filter to change response
    $response = apply_filters( 'gamipress_restrict_unlock_post_accessed_with_points_response', $response, $post_id, $user_id, $points, $points_type );

    wp_send_json_success( $response );

}
add_action( 'wp_ajax_gamipress_restrict_unlock_access_with_points', 'gamipress_restrict_unlock_ajax_access_with_points' );