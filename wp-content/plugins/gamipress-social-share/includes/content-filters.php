<?php
/**
 * Content Filters
 *
 * @package GamiPress\Social_Share\Content_Filters
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Filter posts content to add social share buttons
 *
 * @since  1.0.0
 *
 * @param  string $content The page content
 *
 * @return string          The page content after reformat
 */
function gamipress_social_share_reformat_content( $content ) {

    if( (bool) gamipress_social_share_get_option( 'display_automatically', false )
        && is_singular( gamipress_social_share_get_option( 'post_types', array( 'post', 'page' ) ) ) ) {

        $social_share_output = gamipress_social_share_shortcode( array(
            'url' => gamipress_social_share_get_option( 'url', '' ),
            'pinterest_thumbnail' => gamipress_social_share_get_option( 'pinterest_thumbnail', '' ),
        ));

        $placement = gamipress_social_share_get_option( 'placement', 'before' );

        if( $placement === 'before' || $placement === 'both' ) {
            $content = $social_share_output . $content;
        }

        if( $placement === 'after' || $placement === 'both' ) {
            $content .= $social_share_output;
        }

    }

    return $content;

}
add_filter( 'the_content', 'gamipress_social_share_reformat_content' );