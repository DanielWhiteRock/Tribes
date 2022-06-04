<?php
/**
 * Settings
 *
 * @package     GamiPress\Transfers\Admin\Settings
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

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
function gamipress_transfers_get_option( $option_name, $default = false ) {

    $prefix = 'gamipress_transfers_';

    return gamipress_get_option( $prefix . $option_name, $default );
}

/**
 * GamiPress Transfers Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_transfers_settings_meta_boxes( $meta_boxes ) {

    $prefix = 'gamipress_transfers_';

    // Page Options
    $pages = get_posts( array(
        'post_type' => 'page',
        'numberposts' => -1
    ) );

    $pages_options = array();

    foreach( $pages as $page ) {
        $pages_options[$page->ID] = $page->post_title;
    }

    $meta_boxes['gamipress-transfers-settings'] = array(
        'title' => gamipress_dashicon( 'transfer' ) . __( 'Transfers', 'gamipress-transfers' ),
        'fields' => apply_filters( 'gamipress_transfers_settings_fields', array(
            $prefix . 'transfer_history_page' => array(
                'name' => __( 'Transfer History Page', 'gamipress-transfers' ),
                'desc' => __( 'Page to show a complete transfer history for the current user, including each transfer details. The [gamipress_transfer_history] shortcode should be on this page.', 'gamipress-transfers' ),
                'type' => 'select',
                'options' => $pages_options,
            ),
            $prefix . 'pending_transfers' => array(
                'name' => __( 'Keep Transfers Pending', 'gamipress-transfers' ),
                'desc' => __( 'Check this option to keep all new transfer as pending. The intervention of an administrator will be necessary in order to mark them as complete.', 'gamipress-transfers' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            $prefix . 'recipient_display' => array(
                'name' => __( 'Recipient Display', 'gamipress-transfers' ),
                'desc' => __( 'Setup the recipient display on the transfer form recipient selector. Available tags:', 'gamipress-transfers' )
                    . ' ' . gamipress_transfers_get_recipient_pattern_tags_html(),
                'type' => 'text',
                'default' => '{user} ({user_email})'
            ),
            $prefix . 'recipient_roles' => array(
                'name'          => __( 'Roles', 'gamipress-transfers' ),
                'desc'          => __( 'Set the user roles that will appear in the recipients autocomplete. If none role is selected, by default all users can be selected as recipient.', 'gamipress-transfers' ),
                'type'          => 'multicheck',
                'options_cb'    => 'gamipress_transfers_get_roles_options',
                'classes'       => 'gamipress-switch',
            ),
        ) ),
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_addons_meta_boxes', 'gamipress_transfers_settings_meta_boxes' );

// Callback to retrieve user roles as select options
function gamipress_transfers_get_roles_options() {

    $options = array();

    $editable_roles = array_reverse( get_editable_roles() );

    foreach ( $editable_roles as $role => $details ) {

        $options[$role] = translate_user_role( $details['name'] );

    }

    return array_reverse( $options );
}