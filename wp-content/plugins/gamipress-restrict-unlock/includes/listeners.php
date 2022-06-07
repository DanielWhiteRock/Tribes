<?php
/**
 * Listeners
 *
 * @package     GamiPress\Restrict_Unlock\Listeners
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Post unlocked with points listener
 *
 * @since 1.0.0
 *
 * @param int       $post_id 	    The post unlocked ID
 * @param int       $user_id 	    The user ID
 * @param int       $points 	    The amount of points expended
 * @param string    $points_type    The points type of the amount of points expended
 */
function gamipress_restrict_unlock_post_accessed_with_points_listener( $post_id, $user_id, $points, $points_type ) {

    // Unlock post by any way
    do_action( 'gamipress_restrict_unlock_unlock_post', $post_id, $user_id );

    // Unlock specific post by any way
    do_action( 'gamipress_restrict_unlock_unlock_specific_post', $post_id, $user_id );

    // Unlock post with points
    do_action( 'gamipress_restrict_unlock_unlock_post_with_points', $post_id, $user_id, $points, $points_type );

    // Unlock specific post with points
    do_action( 'gamipress_restrict_unlock_unlock_specific_post_with_points', $post_id, $user_id, $points, $points_type );

}
add_action( 'gamipress_restrict_unlock_post_accessed_with_points', 'gamipress_restrict_unlock_post_accessed_with_points_listener', 10, 4 );

/**
 * Post unlocked meeting all requirements listener
 *
 * @since 1.0.0
 *
 * @param int       $post_id 	    The post unlocked ID
 * @param int       $user_id 	    The user ID
 * @param array     $requirements 	Post configured requirements
 */
function gamipress_restrict_unlock_post_unlocked_meeting_all_requirements_listener( $post_id, $user_id, $requirements ) {

    // Unlock post by any way
    do_action( 'gamipress_restrict_unlock_unlock_post', $post_id, $user_id );

    // Unlock specific post by any way
    do_action( 'gamipress_restrict_unlock_unlock_specific_post', $post_id, $user_id );

    // Unlock post by meeting all requirements
    do_action( 'gamipress_restrict_unlock_unlock_post_by_requirements', $post_id, $user_id, $requirements );

    // Unlock specific post by meeting all requirements
    do_action( 'gamipress_restrict_unlock_unlock_specific_post_by_requirements', $post_id, $user_id, $requirements );

}
add_action( 'gamipress_restrict_unlock_post_unlocked_meeting_all_requirements', 'gamipress_restrict_unlock_post_unlocked_meeting_all_requirements_listener', 10, 3 );