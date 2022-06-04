<?php
/**
 * Template Functions
 *
 * @package GamiPress\Submissions\Template_Functions
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
function gamipress_submissions_template_paths( $file_paths ) {

    $file_paths[] = trailingslashit( get_stylesheet_directory() ) . 'gamipress/submissions/';
    $file_paths[] = trailingslashit( get_template_directory() ) . 'gamipress/submissions/';
    $file_paths[] =  GAMIPRESS_SUBMISSIONS_DIR . 'templates/';

    return $file_paths;

}
add_filter( 'gamipress_template_paths', 'gamipress_submissions_template_paths' );

/**
 * Common user pattern tags
 *
 * @since  1.0.0

 * @return array The registered pattern tags
 */
function gamipress_submissions_get_user_pattern_tags() {

    return apply_filters( 'gamipress_submissions_user_pattern_tags', array(
        '{user}'                => __( 'User display name.', 'gamipress-submissions' ),
        '{user_email}'          => __( 'User email.', 'gamipress-submissions' ),
        '{user_first}'          => __( 'User first name.', 'gamipress-submissions' ),
        '{user_last}'           => __( 'User last name.', 'gamipress-submissions' ),
        '{user_id}'             => __( 'User ID (useful for shortcodes that user ID can be passed as attribute).', 'gamipress-submissions' ),
    ) );

}

/**
 * Parse user pattern tags to a given pattern
 *
 * @since  1.0.0
 *
 * @param string    $pattern
 * @param int       $user_id
 *
 * @return string Parsed pattern
 */
function gamipress_submissions_parse_user_pattern( $pattern, $user_id ) {

    if( absint( $user_id ) === 0 ) {
        $user_id = get_current_user_id();
    }

    $user = get_userdata( $user_id );

    $pattern_replacements = array(
        '{user}'                =>  ( $user ? $user->display_name : '' ),
        '{user_email}'          =>  ( $user ? $user->user_email : '' ),
        '{user_first}'          =>  ( $user ? $user->first_name : '' ),
        '{user_last}'           =>  ( $user ? $user->last_name : '' ),
        '{user_id}'             =>  ( $user ? $user->ID : '' ),
    );

    $pattern_replacements = apply_filters( 'gamipress_submissions_parse_user_pattern_replacements', $pattern_replacements, $pattern );

    return apply_filters( 'gamipress_submissions_parse_user_pattern', str_replace( array_keys( $pattern_replacements ), $pattern_replacements, $pattern ), $pattern );

}


/**
 * Get an array of pattern tags
 *
 * @since  1.0.0

 * @return array The registered pattern tags
 */
function gamipress_submissions_get_pattern_tags() {

    return apply_filters( 'gamipress_submissions_pattern_tags', array_merge(
        gamipress_submissions_get_user_pattern_tags(),
        array(
            '{id}'                      => __( 'The submission number.', 'gamipress-submissions' ),
            '{post_id}'                 => __( 'The ID of the achievement or rank the user sent the submission.', 'gamipress-submissions' ),
            '{post_title}'              => __( 'The title of the achievement or rank the user sent the submission.', 'gamipress-submissions' ),
            '{post_link}'               => __( 'The link of the achievement or rank the user sent the submission.', 'gamipress-submissions' ),
            '{notes}'                   => __( 'The submission notes.', 'gamipress-submissions' ),
        )
    ) );

}

/**
 * Get a string with the desired pattern tags html markup
 *
 * @since  1.0.0
 *
 * @return string Pattern tags html markup
 */
function gamipress_submissions_get_pattern_tags_html() {

    $output = ' <a href="" class="gamipress-pattern-tags-list-toggle" data-show-text="' . __( 'Show tags', 'gamipress-submissions' ) . '" data-hide-text="' . __( 'Show tags', 'gamipress-submissions' ) . '">' . __( 'Show tags', 'gamipress-submissions' ) . '</a>';
    $output .= '<ul class="gamipress-pattern-tags-list gamipress-submissions-pattern-tags-list" style="display: none;">';

    foreach( gamipress_submissions_get_pattern_tags() as $tag => $description ) {

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
 * @param int $user_id
 * @param stdClass $submission
 *
 * @return string Parsed pattern
 */
function gamipress_submissions_parse_pattern( $pattern, $user_id, $submission ) {

    // Parse user replacements
    $pattern = gamipress_submissions_parse_user_pattern( $pattern, $user_id );

    $post = gamipress_get_post( $submission->post_id );

    // Parse replacements
    $pattern_replacements = array(
        '{id}'                      => $submission->submission_id,
        '{post_id}'                 => $post->ID,
        '{post_title}'              => $post->post_title,
        '{post_link}'               => sprintf( '<a href="%s" title="%s">%s</a>', get_the_permalink( $post->ID ), $post->post_title, $post->post_title ),
        '{notes}'                   => $submission->notes,
    );

    $pattern_replacements = apply_filters( 'gamipress_submissions_parse_pattern_replacements', $pattern_replacements, $pattern );

    return apply_filters( 'gamipress_submissions_parse_pattern', str_replace( array_keys( $pattern_replacements ), $pattern_replacements, $pattern ), $pattern );

}