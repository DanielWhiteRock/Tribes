<?php
/**
 * Scripts
 *
 * @package     GamiPress\Restrict_Unlock\Scripts
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
function gamipress_restrict_unlock_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-restrict-unlock-css', GAMIPRESS_RESTRICT_UNLOCK_URL . 'assets/css/gamipress-restrict-unlock' . $suffix . '.css', array( ), GAMIPRESS_RESTRICT_UNLOCK_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-restrict-unlock-js', GAMIPRESS_RESTRICT_UNLOCK_URL . 'assets/js/gamipress-restrict-unlock' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_RESTRICT_UNLOCK_VER, true );

}
add_action( 'init', 'gamipress_restrict_unlock_register_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_restrict_unlock_enqueue_scripts( $hook = null ) {

    // Stylesheets
    wp_enqueue_style( 'gamipress-restrict-unlock-css' );

    // Scripts
    wp_localize_script( 'gamipress-restrict-unlock-js', 'gamipress_restrict_unlock', array(
        'ajaxurl' => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
        'nonce' => wp_create_nonce( 'gamipress_restrict_unlock' ),
    ) );

    wp_enqueue_script( 'gamipress-restrict-unlock-js' );

}
add_action( 'wp_enqueue_scripts', 'gamipress_restrict_unlock_enqueue_scripts', 100 );

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_restrict_unlock_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-restrict-unlock-admin-css', GAMIPRESS_RESTRICT_UNLOCK_URL . 'assets/css/gamipress-restrict-unlock-admin' . $suffix . '.css', array( ), GAMIPRESS_RESTRICT_UNLOCK_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-restrict-unlock-admin-js', GAMIPRESS_RESTRICT_UNLOCK_URL . 'assets/js/gamipress-restrict-unlock-admin' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_RESTRICT_UNLOCK_VER, true );

}
add_action( 'admin_init', 'gamipress_restrict_unlock_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_restrict_unlock_admin_enqueue_scripts( $hook ) {

    // Stylesheets
    wp_enqueue_style( 'gamipress-restrict-unlock-admin-css' );

    wp_localize_script( 'gamipress-restrict-unlock-admin-js', 'gamipress_restrict_unlock', array(
        'labels' => array(
            'points-balance'        => __( 'Reach a balance of {points} {points_type}', 'gamipress-restrict-unlock' ),
            'earn-rank'             => __( 'Reach {rank}', 'gamipress-restrict-unlock' ),
            'specific-achievement'  => __( 'Unlock {achievement} {count}', 'gamipress-restrict-unlock' ),
            'any-achievement'       => __( 'Unlock any {achievement_type} {count}', 'gamipress-restrict-unlock' ),
            'all-achievements'     	=> __( 'Unlock all {achievement_type}', 'gamipress-restrict-unlock' ),
        )
    ) );

    // Scripts
    wp_enqueue_script( 'gamipress-restrict-unlock-admin-js' );

}
add_action( 'admin_enqueue_scripts', 'gamipress_restrict_unlock_admin_enqueue_scripts', 100 );