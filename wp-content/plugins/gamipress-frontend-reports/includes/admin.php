<?php
/**
 * Admin
 *
 * @package     GamiPress\Frontend_Reports\Admin
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Plugin Licensing meta box
 *
 * @since  1.0.0
 *
 * @param $meta_boxes
 *
 * @return mixed
 */
function gamipress_frontend_reports_licenses_meta_boxes( $meta_boxes ) {

    $meta_boxes['gamipress-frontend-reports-license'] = array(
        'title' => __( 'GamiPress Frontend Reports', 'gamipress-frontend-reports' ),
        'fields' => array(
            'gamipress_frontend_reports_license' => array(
                'name' => __( 'License', 'gamipress-frontend-reports' ),
                'type' => 'edd_license',
                'file' => GAMIPRESS_FRONTEND_REPORTS_FILE,
                'item_name' => 'Frontend Reports',
            ),
        )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_frontend_reports_licenses_meta_boxes' );

/**
 * Plugin automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_frontend_reports_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress-frontend-reports'] = __( 'Frontend Reports', 'gamipress-frontend-reports' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_frontend_reports_automatic_updates' );