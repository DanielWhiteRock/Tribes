<?php
/**
 * Template Functions
 *
 * @package GamiPress\Social_Share\Template_Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin templates directory on GamiPress template engine
 *
 * @since 1.0.0
 *
 * @param array $file_paths
 *
 * @return array
 */
function gamipress_social_share_template_paths( $file_paths ) {

    $file_paths[] = trailingslashit( get_stylesheet_directory() ) . 'gamipress/social-share/';
    $file_paths[] = trailingslashit( get_template_directory() ) . 'gamipress/social-share/';
    $file_paths[] =  GAMIPRESS_SOCIAL_SHARE_DIR . 'templates/';

    return $file_paths;

}
add_filter( 'gamipress_template_paths', 'gamipress_social_share_template_paths' );

/**
 * Get an array of pattern tags
 *
 * @since  1.0.0

 * @return array The registered pattern tags
 */
function gamipress_social_share_get_pattern_tags() {

    return apply_filters( 'gamipress_social_share_pattern_tags', array(
        '{site_title}'      => __( 'Site name.', 'gamipress-social-share' ),
        '{title}'           => __( 'The post title.', 'gamipress-social-share' ),
        '{excerpt}'         => __( 'The post excerpt.', 'gamipress-social-share' ),
        '{content}'         => __( 'The post content.', 'gamipress-social-share' ),
        '{type}'            => __( 'The post type.', 'gamipress-social-share' ),
        '{date}'            => __( 'The post date.', 'gamipress-social-share' ),
        '{modified_date}'   => __( 'The last modified post date.', 'gamipress-social-share' ),
        '{author}'          => __( 'The post author name.', 'gamipress-social-share' ),
        '{user}'            => __( 'Current logged in user name.', 'gamipress-social-share' ),
        '{site_name}'       => __( '(Deprecated, use {site_title} instead) Site name.', 'gamipress-social-share' ),
    ) );

}

/**
 * Get a string with the desired pattern tags html markup
 *
 * @since  1.0.0
 *
 * @return string Pattern tags html markup
 */
function gamipress_social_share_get_pattern_tags_html() {

    $output = '<ul class="gamipress-pattern-tags-list gamipress-social-share-pattern-tags-list">';

    foreach( gamipress_social_share_get_pattern_tags() as $tag => $description ) {

        $attr_id = 'tag-' . str_replace( array( '{', '}', '_' ), array( '', '', '-' ), $tag );

        $output .= "<li id='{$attr_id}'><code>{$tag}</code> - {$description}</li>";
    }

    $output .= '</ul>';

    return $output;

}

/**
 * Parse pattern tags to a given pattern
 *
 * @since  1.0.0
 *
 * @param string $pattern
 *
 * @return string Parsed pattern
 */
function gamipress_social_share_parse_pattern( $pattern ) {

    $post = get_post( get_the_ID() );

    $post_author = ( $post ? get_userdata( $post->post_author ) : false );
    $user = get_userdata( get_current_user_id() );

    $pattern_replacements = array(
        '{site_title}'      => get_bloginfo( 'name' ),
        '{site_name}'       => get_bloginfo( 'name' ),  // Deprecated
        '{title}'           => ( $post ? $post->post_title : '' ),
        '{excerpt}'         => ( $post ? $post->post_excerpt : '' ),
        '{content}'         => ( $post ? $post->post_content : '' ),
        '{type}'            => ( $post ? $post->post_type : '' ),
        '{date}'            => ( $post ? $post->post_date : '' ),
        '{modified_date}'   => ( $post ? $post->post_modified : '' ),
        '{author}'          => ( $post_author ? $post_author->display_name : '' ),
        '{user}'            => ( $user ? $user->display_name : '' ),
    );

    $pattern_replacements = apply_filters( 'gamipress_social_share_parse_pattern_replacements', $pattern_replacements, $pattern );

    return apply_filters( 'gamipress_social_share_parse_pattern', str_replace( array_keys( $pattern_replacements ), $pattern_replacements, $pattern ), $pattern );

}