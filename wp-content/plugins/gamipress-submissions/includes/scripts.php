<?php
/**
 * Scripts
 *
 * @package     GamiPress\Submissions\Scripts
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
function gamipress_submissions_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Scripts
    wp_register_script( 'gamipress-submissions-js', GAMIPRESS_SUBMISSIONS_URL . 'assets/js/gamipress-submissions' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_SUBMISSIONS_VER, true );

}
add_action( 'init', 'gamipress_submissions_register_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_submissions_enqueue_scripts( $hook = null ) {

    // Localize scripts
    wp_localize_script( 'gamipress-submissions-js', 'gamipress_submissions', array(
        'ajaxurl'       => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
        'nonce'         => gamipress_get_nonce(),
        'notes_error'   => __( 'Please, fill the form', 'gamipress-submissions' )
    ) );

    // Scripts
    wp_enqueue_script( 'gamipress-submissions-js' );

}
add_action( 'wp_enqueue_scripts', 'gamipress_submissions_enqueue_scripts', 100 );

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_submissions_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Scripts
    wp_register_script( 'gamipress-submissions-admin-js', GAMIPRESS_SUBMISSIONS_URL . 'assets/js/gamipress-submissions-admin' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_SUBMISSIONS_VER, true );

}
add_action( 'admin_init', 'gamipress_submissions_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_submissions_admin_enqueue_scripts( $hook ) {

    // Localize scripts
    wp_localize_script( 'gamipress-submissions-admin-js', 'gamipress_submissions_admin', array(
        'nonce'         => gamipress_get_admin_nonce(),
    ) );

    // Scripts
    wp_enqueue_script( 'gamipress-submissions-admin-js' );

}
add_action( 'admin_enqueue_scripts', 'gamipress_submissions_admin_enqueue_scripts', 100 );