<?php
/**
 * Scripts
 *
 * @package     GamiPress\Social_Share\Scripts
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_social_share_register_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-social-share-css', GAMIPRESS_SOCIAL_SHARE_URL . 'assets/css/gamipress-social-share' . $suffix . '.css', array( ), GAMIPRESS_SOCIAL_SHARE_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-social-share-js', GAMIPRESS_SOCIAL_SHARE_URL . 'assets/js/gamipress-social-share' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_SOCIAL_SHARE_VER, true );
}
add_action( 'init', 'gamipress_social_share_register_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_social_share_enqueue_scripts( $hook = null ) {

    wp_enqueue_style( 'gamipress-social-share-css' );

    // Prevent duplicated enqueueing
    if( ! wp_script_is( 'gamipress-social-share-js', 'enqueued' ) ) {
        wp_localize_script( 'gamipress-social-share-js', 'gamipress_social_share', array(
            'ajaxurl' => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
            'nonce' => wp_create_nonce( 'gamipress_social_share' ),
            'twitter_delay' => apply_filters( 'gamipress_social_share_twitter_event_delay', 5000 ), // Default 5 secs
            'facebook_app_id' => gamipress_social_share_get_option( 'facebook_app_id', '178579039481922' ),
        ) );

        wp_enqueue_script( 'gamipress-social-share-js' );
    }

}
add_action( 'wp_enqueue_scripts', 'gamipress_social_share_enqueue_scripts', 100 );

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_social_share_admin_register_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-social-share-admin-css', GAMIPRESS_SOCIAL_SHARE_URL . 'assets/css/gamipress-social-share-admin' . $suffix . '.css', array( ), GAMIPRESS_SOCIAL_SHARE_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-social-share-admin-js', GAMIPRESS_SOCIAL_SHARE_URL . 'assets/js/gamipress-social-share-admin' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_SOCIAL_SHARE_VER, true );
    wp_register_script( 'gamipress-social-share-admin-widgets-js', GAMIPRESS_SOCIAL_SHARE_URL . 'assets/js/gamipress-social-share-admin-widgets' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_SOCIAL_SHARE_VER, true );
    wp_register_script( 'gamipress-social-share-requirements-ui-js', GAMIPRESS_SOCIAL_SHARE_URL . 'assets/js/gamipress-social-share-requirements-ui' . $suffix . '.js', array( 'jquery', 'gamipress-requirements-ui-js' ), GAMIPRESS_SOCIAL_SHARE_VER, true );

}
add_action( 'admin_init', 'gamipress_social_share_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_social_share_admin_enqueue_scripts( $hook ) {
    global $post_type;

    // Stylesheets
    wp_enqueue_style( 'gamipress-social-share-admin-css' );

    // Scripts
    wp_enqueue_script( 'gamipress-social-share-admin-js' );

    // Requirements ui script
    if ( $post_type === 'points-type' || in_array( $post_type, gamipress_get_achievement_types_slugs() ) || in_array( $post_type, gamipress_get_rank_types_slugs() ) ) {
        wp_enqueue_script( 'gamipress-social-share-requirements-ui-js' );
    }

    // Widgets scripts
    if( $hook === 'widgets.php' ) {
        wp_enqueue_script( 'gamipress-social-share-admin-widgets-js' );
    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_social_share_admin_enqueue_scripts', 100 );