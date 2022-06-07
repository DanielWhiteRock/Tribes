<?php
/**
 * Ajax Functions
 *
 * @package GamiPress\Social_Share\Ajax_Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * AJAX handler where user share a post
 *
 * @since 1.0.0
 */
function gamipress_social_share_url_shared_ajax_handler() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_social_share', 'nonce' );

    // Check parameters given
    if( ! isset( $_POST['url'] ) || ! isset( $_POST['social_network'] ) )
        wp_send_json_error( array( 'message' => __( 'Missing parameters', 'gamipress-social-share' ), ) );

    // Request vars
    $url = $_POST['url'];
    $user_id = get_current_user_id();
    $social_network = $_POST['social_network'];

    // Trigger URL share events
    do_action( 'gamipress_social_share_url_on_any_network', $url, $user_id, $social_network );
    do_action( 'gamipress_social_share_specific_url_on_any_network', $url, $user_id, $social_network );
    do_action( 'gamipress_social_share_url_on_specific_network', $url, $user_id, $social_network );
    do_action( 'gamipress_social_share_specific_url_on_specific_network', $url, $user_id, $social_network );

    // Try to get the post shared
    $post_id = url_to_postid( $url );

    if( $post_id !== 0 ) {

        // Trigger post share events
        do_action( 'gamipress_social_share_share_on_any_network', $post_id, $user_id, $social_network, $url );
        do_action( 'gamipress_social_share_share_specific_on_any_network', $post_id, $user_id, $social_network, $url );
        do_action( 'gamipress_social_share_share_on_specific_network', $post_id, $user_id, $social_network, $url );
        do_action( 'gamipress_social_share_share_specific_on_specific_network', $post_id, $user_id, $social_network, $url );

        $post_author = absint( get_post_field( 'post_author', $post_id ) );

        // Check if there is a post author and he is not sharing his own post (will be awarded for share, but not the author awards)
        if( $post_author && $post_author !== $user_id ) {

            // Trigger author events
            do_action( 'gamipress_social_share_get_share_on_any_network', $post_id, $post_author, $social_network, $user_id, $url );
            do_action( 'gamipress_social_share_get_share_specific_on_any_network', $post_id, $post_author, $social_network, $user_id, $url );
            do_action( 'gamipress_social_share_get_share_on_specific_network', $post_id, $post_author, $social_network, $user_id, $url );
            do_action( 'gamipress_social_share_get_share_specific_on_specific_network', $post_author, $user_id, $social_network, $user_id, $url );

        }

    }

    wp_send_json_success( array( 'message' => __( 'Share successful', 'gamipress-social-share' ), ) );
}
add_action( 'wp_ajax_gamipress_social_share_url_shared',  'gamipress_social_share_url_shared_ajax_handler' );
add_action( 'wp_ajax_nopriv_gamipress_social_share_url_shared',  'gamipress_social_share_url_shared_ajax_handler' );