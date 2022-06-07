<?php
/**
 * GamiPress Social Share Shortcode
 *
 * @package     GamiPress\Social_Share\Shortcodes\Shortcode\GamiPress_Social_Share
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_social_share] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_register_social_share_shortcode() {
    gamipress_register_shortcode( 'gamipress_social_share', array(
        'name'              => __( 'Social Share', 'gamipress-social-share' ),
        'description'       => __( 'Render buttons to let users share the current post.', 'gamipress-social-share' ),
        'output_callback'   => 'gamipress_social_share_shortcode',
        'icon'              => 'share',
        'tabs' => array(
            'general' => array(
                'icon' => 'dashicons-admin-generic',
                'title' => __( 'General', 'gamipress-social-share' ),
                'fields' => array(
                    'title',
                    'url',
                    'alignment',
                ),
            ),
            'twitter' => array(
                'icon' => 'dashicons-twitter',
                'title' => __( 'Twitter', 'gamipress-social-share' ),
                'fields' => array(
                    'twitter',
                    'twitter_pattern',
                    'twitter_username',
                    'twitter_count_box',
                    'twitter_button_size',
                ),
            ),
            'facebook' => array(
                'icon' => 'dashicons-facebook',
                'title' => __( 'Facebook', 'gamipress-social-share' ),
                'fields' => array(
                    'facebook',
                    'facebook_action',
                    'facebook_button_layout',
                    'facebook_button_size',
                    'facebook_share',
                ),
            ),
            'linkedin' => array(
                'icon' => 'dashicons-linkedin',
                'title' => __( 'LinkedIn', 'gamipress-social-share' ),
                'fields' => array(
                    'linkedin',
                    'linkedin_counter',
                ),
            ),
            'pinterest' => array(
                'icon' => 'dashicons-pinterest',
                'title' => __( 'Pinterest', 'gamipress-social-share' ),
                'fields' => array(
                    'pinterest',
                    'pinterest_thumbnail',
                    'pinterest_round',
                    'pinterest_tall',
                    'pinterest_count',
                    'pinterest_description',
                ),
            ),
        ),
        'fields'      => array(

            'title' => array(
                'name' => __( 'Title', 'gamipress-social-share' ),
                'type' => 'text',
                'default' => gamipress_social_share_get_option( 'title', '' )
            ),
            'url' => array(
                'name' => __( 'URL to share', 'gamipress-social-share' ),
                'desc' => __( 'Leave blank to use the current page URL.', 'gamipress-social-share' ),
                'type' => 'text',
                'default' => ''
            ),
            'alignment' => array(
                'name' => __( 'Alignment', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'left' => __( 'Left', 'gamipress-social-share' ),
                    'center' => __( 'Center', 'gamipress-social-share' ),
                    'right' => __( 'Right', 'gamipress-social-share' ),
                ),
                'default' => gamipress_social_share_get_option( 'alignment', 'left' )
            ),

            // Twitter

            'twitter' => array(
                'name' => __( 'Show Twitter Button?', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'twitter_pattern' => array(
                'name' => __( 'Tweet pattern', 'gamipress-social-share' ),
                'description' => __( 'Tweet message pattern. Available tags:', 'gamipress-social-share' ) . gamipress_social_share_get_pattern_tags_html(),
                'type' => 'text',
                'default' => gamipress_social_share_get_option( 'twitter_pattern', __( '{title} on {site_title}', 'gamipress-social-share' ) )
            ),
            'twitter_username' => array(
                'name' => __( 'Username', 'gamipress-social-share' ),
                'description' => __( 'Attribution of the Tweet source. Attribution will appear at the end of the Tweet as "via @username".', 'gamipress-social-share' ),
                'type' => 'text',
                'default' => gamipress_social_share_get_option( 'twitter_username', '' )
            ),
            'twitter_count_box' => array(
                'name' => __( 'Count Box', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'vertical' =>  __( 'Vertical', 'gamipress-social-share' ),
                    'horizontal' =>  __( 'Horizontal', 'gamipress-social-share' ),
                    'none' =>  __( 'None', 'gamipress-social-share' ),
                ),
                'default' => gamipress_social_share_get_option( 'twitter_count_box', 'vertical' )
            ),
            'twitter_button_size' => array(
                'name' => __( 'Button Size', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'medium' =>  __( 'Medium', 'gamipress-social-share' ),
                    'large' =>  __( 'Large', 'gamipress-social-share' ),
                ),
                'default' => gamipress_social_share_get_option( 'twitter_button_size', 'medium' )
            ),

            // Facebook

            'facebook' => array(
                'name' => __( 'Show Facebook Button?', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'facebook_action' => array(
                'name' => __( 'Button Action', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'like'      =>  __( 'Like', 'gamipress-social-share' ),
                    'recommend' =>  __( 'Recommend', 'gamipress-social-share' ),
                    'share'     =>  __( 'Share', 'gamipress-social-share' ),
                ),
                'default' => gamipress_social_share_get_option( 'facebook_action', 'like' )
            ),
            'facebook_button_layout' => array(
                'name' => __( 'Button Layout', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'standard' =>  __( 'Standard', 'gamipress-social-share' ),
                    'box_count' =>  __( 'Box Count', 'gamipress-social-share' ),
                    'button_count' =>  __( 'Button Count', 'gamipress-social-share' ),
                    'button' =>  __( 'Button', 'gamipress-social-share' ),
                ),
                'default' => gamipress_social_share_get_option( 'facebook_button_layout', 'standard' )
            ),
            'facebook_button_size' => array(
                'name' => __( 'Button Size', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'small' =>  __( 'Small', 'gamipress-social-share' ),
                    'large' =>  __( 'Large', 'gamipress-social-share' ),
                ),
                'default' => gamipress_social_share_get_option( 'facebook_button_size', 'small' )
            ),
            'facebook_share' => array(
                'name' => __( 'Add Share Action', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
                'default_cb' => 'gamipress_social_share_facebook_share_default_cb',
            ),

            // LinkedIn

            'linkedin' => array(
                'name' => __( 'Show LinkedIn Button?', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'linkedin_counter' => array(
                'name' => __( 'Counter', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'top' =>  __( 'Top', 'gamipress-social-share' ),
                    'right' =>  __( 'Right', 'gamipress-social-share' ),
                    'none' =>  __( 'None', 'gamipress-social-share' ),
                ),
                'default' => gamipress_social_share_get_option( 'linkedin_counter', 'top' )
            ),

            // Pinterest

            'pinterest' => array(
                'name' => __( 'Show Pinterest Button?', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'pinterest_thumbnail' => array(
                'name' => __( 'Thumbnail URL', 'gamipress-social-share' ),
                'desc' => __( 'Leave blank to use the current page thumbnail.', 'gamipress-social-share' ),
                'type' => 'text',
                'default' => '',
            ),
            'pinterest_round' => array(
                'name' => __( 'Round', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
                'default_cb' => 'gamipress_social_share_pinterest_round_default_cb',
            ),
            'pinterest_tall' => array(
                'name' => __( 'Large', 'gamipress-social-share' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
                'default_cb' => 'gamipress_social_share_pinterest_tall_default_cb',
            ),
            'pinterest_count' => array(
                'name' => __( 'Show Saves Count', 'gamipress-social-share' ),
                'type' => 'select',
                'options' => array(
                    'none' =>  __( 'Not shown', 'gamipress-social-share' ),
                    'above' =>  __( 'Above the button', 'gamipress-social-share' ),
                    'beside' =>  __( 'Beside the button', 'gamipress-social-share' ),
                ),
                'default' => gamipress_social_share_get_option( 'pinterest_count', 'none' )
            ),
            'pinterest_description' => array(
                'name' => __( 'Description pattern', 'gamipress-social-share' ),
                'description' => __( 'Description pattern (maximum 500 characters). Available tags:', 'gamipress-social-share' ) . gamipress_social_share_get_pattern_tags_html(),
                'type' => 'text',
                'attributes' => array(
                    'maxlength' => 500,
                ),
                'default' => gamipress_social_share_get_option( 'pinterest_description', __( '{title} on {site_title}', 'gamipress-social-share' ) )
            ),
        ),
    ) );
}
add_action( 'init', 'gamipress_register_social_share_shortcode' );

/**
 * Social Share Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_social_share_shortcode( $atts = array() ) {

    global $gamipress_social_share_template_args;

    $atts = shortcode_atts( array(

        'title'     => gamipress_social_share_get_option( 'title', '' ),
        'url'       => '',
        'alignment' => gamipress_social_share_get_option( 'alignment', 'left' ),

        // Twitter

        'twitter' => (bool) gamipress_social_share_get_option( 'twitter', false ) ? 'yes' : 'no',
        'twitter_pattern' => gamipress_social_share_get_option( 'twitter_pattern', __( '{title} on {site_title}', 'gamipress-social-share' ) ),
        'twitter_username' => gamipress_social_share_get_option( 'twitter_username', '' ),
        'twitter_count_box' => gamipress_social_share_get_option( 'twitter_count_box', 'vertical' ),
        'twitter_button_size' => gamipress_social_share_get_option( 'twitter_button_size', 'medium' ),

        // Facebook

        'facebook' => (bool) gamipress_social_share_get_option( 'facebook', false ) ? 'yes' : 'no',
        'facebook_action' => gamipress_social_share_get_option( 'facebook_action', 'like' ),
        'facebook_button_layout' => gamipress_social_share_get_option( 'facebook_button_layout', 'standard' ),
        'facebook_button_size' => gamipress_social_share_get_option( 'facebook_button_size', 'small' ),
        'facebook_share' => (bool) gamipress_social_share_get_option( 'facebook_share', false ) ? 'yes' : 'no',

        // LinkedIn

        'linkedin' => (bool) gamipress_social_share_get_option( 'linkedin', false ) ? 'yes' : 'no',
        'linkedin_counter' => gamipress_social_share_get_option( 'linkedin_counter', 'top' ),

        // Pinterest

        'pinterest' => (bool) gamipress_social_share_get_option( 'pinterest', false ) ? 'yes' : 'no',
        'pinterest_thumbnail' => '',
        'pinterest_round' => (bool) gamipress_social_share_get_option( 'pinterest_round', false ) ? 'yes' : 'no',
        'pinterest_tall' => (bool) gamipress_social_share_get_option( 'pinterest_tall', false ) ? 'yes' : 'no',
        'pinterest_count' => gamipress_social_share_get_option( 'pinterest_count', 'none' ),
        'pinterest_description' => gamipress_social_share_get_option( 'pinterest_description', __( '{title} on {site_title}', 'gamipress-social-share' ) ),

    ), $atts, 'gamipress_social_share' );

    gamipress_social_share_enqueue_scripts();

    // Initialize template args global
    $gamipress_social_share_template_args = $atts;

    // Store the original shortcode attributes
    $gamipress_social_share_template_args['atts'] = $atts;

    // Render the share buttons
    ob_start();
        gamipress_get_template_part( 'share-buttons' );
    $output = ob_get_clean();

    // Return our rendered social share buttons
    return $output;
}

function gamipress_social_share_facebook_share_default_cb() {
    if( (bool) gamipress_social_share_get_option( 'facebook_share', false ) )
        return 'yes';
}

function gamipress_social_share_pinterest_round_default_cb() {
    if( (bool) gamipress_social_share_get_option( 'pinterest_round', false ) )
        return 'yes';
}

function gamipress_social_share_pinterest_tall_default_cb() {
    if( (bool) gamipress_social_share_get_option( 'pinterest_tall', false ) )
        return 'yes';
}
