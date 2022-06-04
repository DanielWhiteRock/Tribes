<?php
/**
 * Admin
 *
 * @package     GamiPress\Reports\Admin
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_REPORTS_DIR . 'includes/admin/pages/reports.php';
require_once GAMIPRESS_REPORTS_DIR . 'includes/admin/pages/dashboard-reports.php';
require_once GAMIPRESS_REPORTS_DIR . 'includes/admin/pages/points-reports.php';
require_once GAMIPRESS_REPORTS_DIR . 'includes/admin/pages/achievements-reports.php';
require_once GAMIPRESS_REPORTS_DIR . 'includes/admin/pages/ranks-reports.php';

/**
 * Create reports menus
 *
 * @since 1.0.0
 */
function gamipress_reports_admin_menu() {

    // Bail if GamiPress is active network wide and we are not in main site
    if( gamipress_is_network_wide_active() && ! is_main_site() ) {
        return;
    }

    // GamiPress Reports menu
    add_menu_page( __( 'GamiPress Reports', 'gamipress-reports' ), __( 'GamiPress Reports', 'gamipress-reports' ), gamipress_get_manager_capability(), 'gamipress_reports_dashboard', null, 'dashicons-chart-area', '55.1' );

}
add_action( 'admin_menu', 'gamipress_reports_admin_menu' );

/**
 * Shortcut function to get plugin options
 *
 * @since  1.0.0
 *
 * @param string    $option_name
 * @param bool      $default
 *
 * @return mixed
 */
function gamipress_reports_get_option( $option_name, $default = false ) {

    $prefix = 'gamipress_reports_';

    return gamipress_get_option( $prefix . $option_name, $default );
}

/**
 * GamiPress Reports Licensing meta box
 *
 * @since  1.0.0
 *
 * @param $meta_boxes
 *
 * @return mixed
 */
function gamipress_reports_licenses_meta_boxes( $meta_boxes ) {

    $meta_boxes['gamipress-reports-license'] = array(
        'title' => __( 'GamiPress Reports', 'gamipress-reports' ),
        'fields' => array(
            'gamipress_reports_license' => array(
                'name' => __( 'License', 'gamipress-reports' ),
                'type' => 'edd_license',
                'file' => GAMIPRESS_REPORTS_FILE,
                'item_name' => 'Reports',
            ),
        )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_reports_licenses_meta_boxes' );

/**
 * GamiPress Reports automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_reports_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress-reports'] = __( 'Reports', 'gamipress-reports' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_reports_automatic_updates' );