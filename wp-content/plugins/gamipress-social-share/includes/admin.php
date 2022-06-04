<?php
/**
 * Admin
 *
 * @package GamiPress\Social_Share\Admin
 * @since 1.0.0
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
function gamipress_social_share_get_option( $option_name, $default = false ) {

    $prefix = 'gamipress_social_share_';

    return gamipress_get_option( $prefix . $option_name, $default );
}

/**
 * GamiPress Social Share Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_social_share_settings_meta_boxes( $meta_boxes ) {

    $prefix = 'gamipress_social_share_';

    $meta_boxes['gamipress-social-share-settings'] = array(
        'title' => gamipress_dashicon( 'share' ) . __( 'Social Share', 'gamipress-social-share' ),
        'vertical_tabs' => true,
        'tabs' => apply_filters( 'gamipress_social_share_settings_tabs', array(
            'global' => array(
                'icon' => 'dashicons-admin-site',
                'title' => __( 'Global Settings', 'gamipress-social-share' ),
                'fields' => array(
                    $prefix . 'display_automatically',
                    $prefix . 'placement',
                    $prefix . 'alignment',
                    $prefix . 'post_types',
                    $prefix . 'title',
                    $prefix . 'url',
                ),
            ),
            'twitter' => array(
                'icon' => 'dashicons-twitter',
                'title' => __( 'Twitter', 'gamipress-social-share' ),
                'fields' => array(
                    $prefix . 'twitter_title',
                    $prefix . 'twitter',
                    $prefix . 'twitter_pattern',
                    $prefix . 'twitter_username',
                    $prefix . 'twitter_count_box',
                    $prefix . 'twitter_button_size',
                ),
            ),
            'facebook' => array(
                'icon' => 'dashicons-facebook',
                'title' => __( 'Facebook', 'gamipress-social-share' ),
                'fields' => array(
                    $prefix . 'facebook_title',
                    $prefix . 'facebook',
                    $prefix . 'facebook_app_id',
                    $prefix . 'facebook_action',
                    $prefix . 'facebook_button_layout',
                    $prefix . 'facebook_button_size',
                    $prefix . 'facebook_share',
                ),
            ),
            'linkedin' => array(
                'icon' => 'dashicons-linkedin',
                'title' => __( 'LinkedIn', 'gamipress-social-share' ),
                'fields' => array(
                    $prefix . 'linkedin_title',
                    $prefix . 'linkedin',
                    $prefix . 'linkedin_counter',
                ),
            ),
            'pinterest' => array(
                'icon' => 'dashicons-pinterest',
                'title' => __( 'Pinterest', 'gamipress-social-share' ),
                'fields' => array(
                    $prefix . 'pinterest_title',
                    $prefix . 'pinterest',
                    $prefix . 'pinterest_thumbnail',
                    $prefix . 'pinterest_round',
                    $prefix . 'pinterest_tall',
                    $prefix . 'pinterest_count',
                    $prefix . 'pinterest_description',
                ),
            ),
        ) ),
        'fields' => apply_filters( 'gamipress_social_share_settings_fields', array(
            $prefix . 'display_automatically' => array(
                'name' => __( 'Display Automatically?', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'placement' => array(
                'name' => __( 'Where Display?', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'before' => __( 'Before Content', 'gamipress-social-share' ),
                    'after' => __( 'After Content', 'gamipress-social-share' ),
                    'both' => __( 'Before and After Content', 'gamipress-social-share' ),
                ),
            ),
            $prefix . 'alignment' => array(
                'name' => __( 'Alignment', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'left' => __( 'Left', 'gamipress-social-share' ),
                    'center' => __( 'Center', 'gamipress-social-share' ),
                    'right' => __( 'Right', 'gamipress-social-share' ),
                ),
                'default' => 'left'
            ),
            $prefix . 'post_types' => array(
                'name' => __( 'Post Types Where Display', 'gamipress-social-share' ),
                'type' => 'multicheck',
                'options_cb' => 'gamipress_social_share_post_types_options_cb',
                'default' => array( 'post', 'page' )
            ),
            $prefix . 'title' => array(
                'name' => __( 'Title', 'gamipress-social-share' ),
                'type' => 'text',
                'default' => ''
            ),
            $prefix . 'url' => array(
                'name' => __( 'URL to share', 'gamipress-social-share' ),
                'desc' => __( 'Leave blank to use the current page URL.', 'gamipress-social-share' ),
                'type' => 'text',
                'default' => ''
            ),

            // Twitter

            $prefix . 'twitter_title' => array(
                'name' => __( 'Twitter', 'gamipress-social-share' ),
                'desc' => __( 'Configure the default settings for the Twitter button.', 'gamipress-social-share' ),
                'type' => 'title',
            ),
            $prefix . 'twitter' => array(
                'name' => __( 'Show Twitter Button?', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'twitter_pattern' => array(
                'name' => __( 'Tweet pattern', 'gamipress-social-share' ),
                'description' => __( 'Tweet message pattern. Available tags:', 'gamipress-social-share' ) . gamipress_social_share_get_pattern_tags_html(),
                'type' => 'text',
                'default' => __( '{title} on {site_title}', 'gamipress-social-share' ),
            ),
            $prefix . 'twitter_username' => array(
                'name' => __( 'Username', 'gamipress-social-share' ),
                'description' => __( 'Attribution of the Tweet source. Attribution will appear at the end of the Tweet as "via @username".', 'gamipress-social-share' ),
                'type' => 'text',
            ),
            $prefix . 'twitter_count_box' => array(
                'name' => __( 'Count Box', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'vertical' =>  __( 'Vertical', 'gamipress-social-share' ),
                    'horizontal' =>  __( 'Horizontal', 'gamipress-social-share' ),
                    'none' =>  __( 'None', 'gamipress-social-share' ),
                ),
                'default' => 'vertical'
            ),
            $prefix . 'twitter_button_size' => array(
                'name' => __( 'Button Size', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'medium' =>  __( 'Medium', 'gamipress-social-share' ),
                    'large' =>  __( 'Large', 'gamipress-social-share' ),
                ),
                'default' => 'medium'
            ),

            // Facebook

            $prefix . 'facebook_title' => array(
                'name' => __( 'Facebook', 'gamipress-social-share' ),
                'desc' => __( 'Configure the default settings for the Facebook button.', 'gamipress-social-share' ),
                'type' => 'title',
            ),
            $prefix . 'facebook' => array(
                'name' => __( 'Show Facebook Button?', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'facebook_app_id' => array(
                'name' => __( 'App ID', 'gamipress-social-share' ),
                'desc' => __( 'Your Facebook App ID. Social Share add-on comes with a temporal App ID but is recommended to create your own.', 'gamipress-social-share' )
                    . ' <a href="https://gamipress.com/docs/gamipress-social-share/facebook-app-id/" target="_blank">' .  __( 'More information', 'gamipress-social-share' ) . '</a>',
                'type' => 'text',
            ),
            $prefix . 'facebook_action' => array(
                'name' => __( 'Button Action', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'like'      =>  __( 'Like', 'gamipress-social-share' ),
                    'recommend' =>  __( 'Recommend', 'gamipress-social-share' ),
                    'share'     =>  __( 'Share', 'gamipress-social-share' ),
                ),
                'default' => 'like'
            ),
            $prefix . 'facebook_button_layout' => array(
                'name' => __( 'Button Layout', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'standard' =>  __( 'Standard', 'gamipress-social-share' ),
                    'box_count' =>  __( 'Box Count', 'gamipress-social-share' ),
                    'button_count' =>  __( 'Button Count', 'gamipress-social-share' ),
                    'button' =>  __( 'Button', 'gamipress-social-share' ),
                ),
                'default' => 'standard'
            ),
            $prefix . 'facebook_button_size' => array(
                'name' => __( 'Button Size', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'small' =>  __( 'Small', 'gamipress-social-share' ),
                    'large' =>  __( 'Large', 'gamipress-social-share' ),
                ),
                'default' => 'small'
            ),
            $prefix . 'facebook_share' => array(
                'name' => __( 'Add Share Action', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),

            // LinkedIn

            $prefix . 'linkedin_title' => array(
                'name' => __( 'LinkedIn', 'gamipress-social-share' ),
                'desc' => __( 'Configure the default settings for the LinkedIn button.', 'gamipress-social-share' ),
                'type' => 'title',
            ),
            $prefix . 'linkedin' => array(
                'name' => __( 'Show LinkedIn Button?', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'linkedin_counter' => array(
                'name' => __( 'Counter', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'top' =>  __( 'Top', 'gamipress-social-share' ),
                    'right' =>  __( 'Right', 'gamipress-social-share' ),
                    'none' =>  __( 'None', 'gamipress-social-share' ),
                ),
                'default' => 'top'
            ),

            // Pinterest

            $prefix . 'pinterest_title' => array(
                'name' => __( 'Pinterest', 'gamipress-social-share' ),
                'desc' => __( 'Configure the default settings for the Pinterest button.', 'gamipress-social-share' ),
                'type' => 'title',
            ),
            $prefix . 'pinterest' => array(
                'name' => __( 'Show Pinterest Button?', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'pinterest_thumbnail' => array(
                'name' => __( 'Thumbnail URL', 'gamipress-social-share' ),
                'desc' => __( 'Leave blank to use the current page thumbnail.', 'gamipress-social-share' ),
                'type' => 'text',
                'default' => '',
            ),
            $prefix . 'pinterest_round' => array(
                'name' => __( 'Round', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'pinterest_tall' => array(
                'name' => __( 'Large', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'pinterest_count' => array(
                'name' => __( 'Save Count', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'none' =>  __( 'Not shown', 'gamipress-social-share' ),
                    'above' =>  __( 'Above the button', 'gamipress-social-share' ),
                    'beside' =>  __( 'Beside the button', 'gamipress-social-share' ),
                ),
                'default' => 'none'
            ),
            $prefix . 'pinterest_description' => array(
                'name' => __( 'Description pattern', 'gamipress-social-share' ),
                'description' => __( 'Description pattern (maximum 500 characters). Available tags:', 'gamipress-social-share' ) . gamipress_social_share_get_pattern_tags_html(),
                'type' => 'text',
                'attributes' => array(
                    'maxlength' => 500,
                ),
                'default' =>__( '{title} on {site_title}', 'gamipress-social-share' )
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_addons_meta_boxes', 'gamipress_social_share_settings_meta_boxes' );

/**
 * GamiPress Social Share Licensing meta box
 *
 * @since  1.0.0
 *
 * @param $meta_boxes
 *
 * @return mixed
 */
function gamipress_social_share_licenses_meta_boxes( $meta_boxes ) {

    $meta_boxes['gamipress-social-share-license'] = array(
        'title' => __( 'GamiPress Social Share', 'gamipress-social-share' ),
        'fields' => array(
            'gamipress_social_share_license' => array(
                'name' => __( 'License', 'gamipress-social-share' ),
                'type' => 'edd_license',
                'file' => GAMIPRESS_SOCIAL_SHARE_FILE,
                'item_name' => 'Social Share',
            ),
        )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_social_share_licenses_meta_boxes' );

/**
 * GamiPress Social Share automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_social_share_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress-social-share'] = __( 'Social Share', 'gamipress-social-share' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_social_share_automatic_updates' );

// Post types options cb
function gamipress_social_share_post_types_options_cb() {

    $post_types = get_post_types( array(
        'public' => true
    ), 'object' );

    $options = array();

    foreach( $post_types as $post_type => $post_type_object ) {
        $options[$post_type] = $post_type_object->label;
    }

    return $options;
}