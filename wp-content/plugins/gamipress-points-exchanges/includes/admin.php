<?php
/**
 * Admin
 *
 * @package     GamiPress\Points_Exchanges\Admin
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/admin/meta-boxes.php';

/**
 * GamiPress Points Exchanges Licensing meta box
 *
 * @since  1.0.0
 *
 * @param $meta_boxes
 *
 * @return mixed
 */
function gamipress_points_exchanges_licenses_meta_boxes( $meta_boxes ) {

    $meta_boxes['gamipress-points-exchanges-license'] = array(
        'title' => __( 'GamiPress Points Exchanges', 'gamipress-points-exchanges' ),
        'fields' => array(
            'gamipress_points_exchanges_license' => array(
                'name' => __( 'License', 'gamipress-points-exchanges' ),
                'type' => 'edd_license',
                'file' => GAMIPRESS_POINTS_EXCHANGES_FILE,
                'item_name' => 'Points Exchanges',
            ),
        )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_points_exchanges_licenses_meta_boxes' );

/**
 * GamiPress Points Exchanges automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_points_exchanges_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress-points-exchanges'] = __( 'Points Exchanges', 'gamipress-points-exchanges' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_points_exchanges_automatic_updates' );