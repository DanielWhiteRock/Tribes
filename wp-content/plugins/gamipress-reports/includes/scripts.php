<?php
/**
 * Scripts
 *
 * @package     GamiPress\Reports\Scripts
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_reports_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-reports-admin-css', GAMIPRESS_REPORTS_URL . 'assets/css/gamipress-reports-admin' . $suffix . '.css', array( ), GAMIPRESS_REPORTS_VER, 'all' );
    wp_register_style( 'gamipress-reports-chart-css', GAMIPRESS_REPORTS_URL . 'assets/css/gamipress-reports-chart' . $suffix . '.css', array( ), GAMIPRESS_REPORTS_VER, 'all' );
    wp_register_style( 'gamipress-reports-list-table-css', GAMIPRESS_REPORTS_URL . 'assets/css/gamipress-reports-list-table' . $suffix . '.css', array( ), GAMIPRESS_REPORTS_VER, 'all' );

    // Libraries
    wp_register_script( 'gamipress-reports-chart-js-js', GAMIPRESS_REPORTS_URL . 'assets/libs/Chart.min.js', array( 'jquery' ), GAMIPRESS_REPORTS_VER, true );

    // Scripts
    wp_register_script( 'gamipress-reports-admin-js', GAMIPRESS_REPORTS_URL . 'assets/js/gamipress-reports-admin' . $suffix . '.js', array( 'jquery', 'gamipress-reports-chart-js-js' ), GAMIPRESS_REPORTS_VER, true );
    wp_register_script( 'gamipress-reports-chart-js', GAMIPRESS_REPORTS_URL . 'assets/js/gamipress-reports-chart' . $suffix . '.js', array( 'jquery', 'gamipress-reports-chart-js-js' ), GAMIPRESS_REPORTS_VER, true );
    wp_register_script( 'gamipress-reports-list-table-js', GAMIPRESS_REPORTS_URL . 'assets/js/gamipress-reports-list-table' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_REPORTS_VER, true );

}
add_action( 'admin_init', 'gamipress_reports_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_reports_admin_enqueue_scripts( $hook ) {

    $allowed_hooks = array(
        'toplevel_page_gamipress_reports_dashboard',
        'gamipress-reports_page_gamipress_reports_points',
        'gamipress-reports_page_gamipress_reports_achievements',
        'gamipress-reports_page_gamipress_reports_ranks',
    );

    if( ! in_array( $hook, $allowed_hooks ) ) {
        return;
    }

    // Stylesheets
    wp_enqueue_style( 'gamipress-reports-admin-css' );
    wp_enqueue_style( 'gamipress-reports-chart-css' );
    wp_enqueue_style( 'gamipress-reports-list-table-css' );

    // Libraries
    wp_enqueue_script( 'gamipress-reports-chart-js-js' );

    // Localize scripts
    wp_localize_script( 'gamipress-reports-admin-js', 'gamipress_reports_admin', array(
        'nonce' => gamipress_get_admin_nonce(),
    ) );

    wp_localize_script( 'gamipress-reports-chart-js', 'gamipress_reports_chart', array(
        'nonce' => gamipress_get_admin_nonce(),
    ) );

    wp_localize_script( 'gamipress-reports-list-table-js', 'gamipress_reports_list_table', array(
        'nonce' => gamipress_get_admin_nonce(),
    ) );

    // Scripts
    wp_enqueue_script( 'gamipress-reports-admin-js' );
    wp_enqueue_script( 'gamipress-reports-chart-js' );
    wp_enqueue_script( 'gamipress-reports-list-table-js' );

}
add_action( 'admin_enqueue_scripts', 'gamipress_reports_admin_enqueue_scripts', 100 );