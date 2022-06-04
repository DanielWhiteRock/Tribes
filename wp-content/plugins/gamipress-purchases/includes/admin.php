<?php
/**
 * Admin
 *
 * @package     GamiPress\Purchases\Admin
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_PURCHASES_DIR . 'includes/admin/meta-boxes.php';
require_once GAMIPRESS_PURCHASES_DIR . 'includes/admin/settings.php';

/**
 * Add GamiPress Purchases admin bar menu
 *
 * @since 1.1.0
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function gamipress_purchases_admin_bar_menu( $wp_admin_bar ) {

    // - Transfer History
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-payments',
        'title'  => __( 'Payment History', 'gamipress-purchases' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress_payments' )
    ) );

}
add_action( 'admin_bar_menu', 'gamipress_purchases_admin_bar_menu', 150 );

/**
 * GamiPress Purchases Licensing meta box
 *
 * @since  1.0.0
 *
 * @param $meta_boxes
 *
 * @return mixed
 */
function gamipress_purchases_licenses_meta_boxes( $meta_boxes ) {

    $meta_boxes['gamipress-purchases-license'] = array(
        'title' => __( 'GamiPress Purchases', 'gamipress-purchases' ),
        'fields' => array(
            'gamipress_purchases_license' => array(
                'name' => __( 'License', 'gamipress-purchases' ),
                'type' => 'edd_license',
                'file' => GAMIPRESS_PURCHASES_FILE,
                'item_name' => 'Purchases',
            ),
        )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_purchases_licenses_meta_boxes' );

/**
 * GamiPress Purchases automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_purchases_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress-purchases'] = __( 'Purchases', 'gamipress-purchases' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_purchases_automatic_updates' );