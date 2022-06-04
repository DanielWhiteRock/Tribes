<?php
/**
 * Admin
 *
 * @package     GamiPress\Transfers\Admin
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_TRANSFERS_DIR . 'includes/admin/settings.php';

/**
 * Add GamiPress Transfers admin bar menu
 *
 * @since 1.0.2
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function gamipress_transfers_admin_bar_menu( $wp_admin_bar ) {

    // - Transfer History
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-transfers',
        'title'  => __( 'Transfer History', 'gamipress-transfers' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress_transfers' )
    ) );

}
add_action( 'admin_bar_menu', 'gamipress_transfers_admin_bar_menu', 150 );


/**
 * GamiPress Transfers Licensing meta box
 *
 * @since  1.0.0
 *
 * @param $meta_boxes
 *
 * @return mixed
 */
function gamipress_transfers_licenses_meta_boxes( $meta_boxes ) {

    $meta_boxes['gamipress-transfers-license'] = array(
        'title' => __( 'GamiPress Transfers', 'gamipress-transfers' ),
        'fields' => array(
            'gamipress_transfers_license' => array(
                'name' => __( 'License', 'gamipress-transfers' ),
                'type' => 'edd_license',
                'file' => GAMIPRESS_TRANSFERS_FILE,
                'item_name' => 'Transfers',
            ),
        )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_transfers_licenses_meta_boxes' );

/**
 * GamiPress Transfers automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_transfers_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress-transfers'] = __( 'Transfers', 'gamipress-transfers' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_transfers_automatic_updates' );