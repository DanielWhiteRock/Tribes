<?php
/**
 * Admin
 *
 * @package     GamiPress\Restrict_Unlock\Admin
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_RESTRICT_UNLOCK_DIR . 'includes/admin/meta-boxes.php';

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
function gamipress_restrict_unlock_get_option( $option_name, $default = false ) {

    $prefix = 'gamipress_restrict_unlock_';

    return gamipress_get_option( $prefix . $option_name, $default );
}

/**
 * GamiPress Restrict Unlock Licensing meta box
 *
 * @since  1.0.0
 *
 * @param $meta_boxes
 *
 * @return mixed
 */
function gamipress_restrict_unlock_licenses_meta_boxes( $meta_boxes ) {

    $meta_boxes['gamipress-restrict-unlock-license'] = array(
        'title' => __( 'GamiPress Restrict Unlock', 'gamipress-restrict-unlock' ),
        'fields' => array(
            'gamipress_restrict_unlock_license' => array(
                'name' => __( 'License', 'gamipress-restrict-unlock' ),
                'type' => 'edd_license',
                'file' => GAMIPRESS_RESTRICT_UNLOCK_FILE,
                'item_name' => 'Restrict Unlock',
            ),
        )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_restrict_unlock_licenses_meta_boxes' );

/**
 * GamiPress Restrict Unlock automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_restrict_unlock_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress-restrict-unlock'] = __( 'Restrict Unlock', 'gamipress-restrict-unlock' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_restrict_unlock_automatic_updates' );