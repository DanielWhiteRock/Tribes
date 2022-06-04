<?php
/**
 * Shortcodes
 *
 * @package GamiPress\Social_Share\Shortcodes
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// GamiPress Social Share Shortcode
require_once GAMIPRESS_SOCIAL_SHARE_DIR . 'includes/shortcodes/gamipress_social_share.php';

/**
 * Helper function to get specific network atts
 *
 * @since 1.0.0
 *
 * @param string    $social_network
 * @param array     $atts
 *
 * @return array
 */
function gamipress_social_share_get_network_atts( $social_network, $atts ) {

    // Initialize network atts
    $network_atts = array();

    // Turn social network args into unique ones: twitter_count_box => count_box
    foreach( $atts as $key => $value ) {
        if( strpos( $key, $social_network . '_' ) !== false ) {
            $new_key = str_replace( $social_network . '_', '', $key );

            $network_atts[$new_key] = $value;
        }
    }

    return $network_atts;

}