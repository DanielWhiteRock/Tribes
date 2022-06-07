<?php
/**
 * Scripts
 *
 * @package     GamiPress\Frontend_Reports\Scripts
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register frontend scripts
 *
 * @return      void
 * @since       1.0.0
 */
function gamipress_frontend_reports_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

    // Libraries
    wp_register_style('gamipress-frontend-reports-chart-js-css', GAMIPRESS_FRONTEND_REPORTS_URL . 'assets/libs/Chart.js/Chart.min.css', array(), GAMIPRESS_FRONTEND_REPORTS_VER, 'all');
    wp_register_script('gamipress-frontend-reports-chart-js-js', GAMIPRESS_FRONTEND_REPORTS_URL . 'assets/libs/Chart.js/Chart.min.js', array('jquery'), GAMIPRESS_FRONTEND_REPORTS_VER, true);

    // Stylesheets
    wp_register_style('gamipress-frontend-reports-css', GAMIPRESS_FRONTEND_REPORTS_URL . 'assets/css/gamipress-frontend-reports' . $suffix . '.css', array(), GAMIPRESS_FRONTEND_REPORTS_VER, 'all');

    // Scripts
    wp_register_script('gamipress-frontend-reports-js', GAMIPRESS_FRONTEND_REPORTS_URL . 'assets/js/gamipress-frontend-reports' . $suffix . '.js', array('jquery', 'gamipress-frontend-reports-chart-js-js'), GAMIPRESS_FRONTEND_REPORTS_VER, true);

}
add_action( 'init', 'gamipress_frontend_reports_register_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @return      void
 * @since       1.0.0
 */
function gamipress_frontend_reports_enqueue_scripts( $hook = null ) {

    // Enqueue libraries
    wp_enqueue_style('gamipress-frontend-reports-chart-js-css');
    wp_enqueue_script('gamipress-frontend-reports-chart-js-js');

    // Localize scripts
    wp_localize_script('gamipress-frontend-reports-js', 'gamipress_frontend_reports', array(
        'ajaxurl'       => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
        'nonce'         => wp_create_nonce( 'gamipress_frontend_reports' ),
        'chart_options' => gamipress_frontend_reports_get_chart_options()
    ));

    // Enqueue assets
    wp_enqueue_style('gamipress-frontend-reports-css');
    wp_enqueue_script('gamipress-frontend-reports-js');

}
add_action( 'wp_enqueue_scripts', 'gamipress_frontend_reports_enqueue_scripts', 100 );

/**
 * Register admin scripts
 *
 * @return      void
 * @since       1.0.0
 */
function gamipress_frontend_reports_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

    // Stylesheets
    wp_register_style('gamipress-frontend-reports-admin-css', GAMIPRESS_FRONTEND_REPORTS_URL . 'assets/css/gamipress-frontend-reports-admin' . $suffix . '.css', array(), GAMIPRESS_FRONTEND_REPORTS_VER, 'all');

    // Scripts
    wp_register_script('gamipress-frontend-reports-admin-widgets-js', GAMIPRESS_FRONTEND_REPORTS_URL . 'assets/js/gamipress-frontend-reports-admin-widgets' . $suffix . '.js', array('jquery', 'gamipress-select2-js'), GAMIPRESS_FRONTEND_REPORTS_VER, true);
    wp_register_script('gamipress-frontend-reports-shortcode-editor-js', GAMIPRESS_FRONTEND_REPORTS_URL . 'assets/js/gamipress-frontend-reports-shortcode-editor' . $suffix . '.js', array('jquery', 'gamipress-select2-js'), GAMIPRESS_FRONTEND_REPORTS_VER, true);

}
add_action( 'admin_init', 'gamipress_frontend_reports_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @return      void
 * @since       1.0.0
 */
function gamipress_frontend_reports_admin_enqueue_scripts( $hook ) {

    global $post_type;

    // Stylesheets
    wp_enqueue_style('gamipress-frontend-reports-admin-css');

    // Widgets scripts
    if ($hook === 'widgets.php') {
        wp_enqueue_script('gamipress-frontend-reports-admin-widgets-js');
    }

    // Just enqueue on add/edit views and on post types that supports editor feature and on Gamipress settings page
    if (
        (in_array($hook, array('post.php', 'page.php', 'post-new.php', 'post-edit.php'))) && post_type_supports($post_type, 'editor')
        || $hook === 'gamipress_page_gamipress_settings'
    ) {
        wp_enqueue_script('gamipress-frontend-reports-shortcode-editor-js');
    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_frontend_reports_admin_enqueue_scripts', 100 );