<?php
/**
 * Filters
 *
 * @package     GamiPress\Restrict_Unlock\Filters
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Append informational text and unlock button on single posts (achievements and ranks)
 *
 * @since  1.0.0
 *
 * @param int $post_id
 *
 * @return void
 */
function gamipress_restrict_unlock_single_content( $post_id ) {

    gamipress_restrict_unlock_maybe_render_markup( $post_id );

}
add_action( 'gamipress_after_single_achievement', 'gamipress_restrict_unlock_single_content' );
add_action( 'gamipress_after_single_rank', 'gamipress_restrict_unlock_single_content' );

/**
 * Append informational text and unlock button on shortcodes (check if restrict_unlock attribute is set to yes)
 *
 * @since  1.0.0
 *
 * @param int   $post_id
 * @param array $template_args Template received arguments
 *
 * @return void
 */
function gamipress_restrict_unlock_shortcode_content( $post_id, $template_args ) {

    // Initialize restrict_unlock attribute
    if( ! isset( $template_args['restrict_unlock'] ) )
        $template_args['restrict_unlock'] = 'no';

    if( $template_args['restrict_unlock'] === 'yes' )
        gamipress_restrict_unlock_maybe_render_markup( $post_id );
}
add_action( 'gamipress_after_render_achievement', 'gamipress_restrict_unlock_shortcode_content', 10, 2 );
add_action( 'gamipress_after_render_rank', 'gamipress_restrict_unlock_shortcode_content', 10, 2 );

/**
 * Append informational text and unlock button on points types shortcode
 *
 * @since  1.0.0
 *
 * @param string  $points_type      Points type slug
 * @param array   $points_awards    Array of points awards
 * @param array   $points_deducts   Array of points deducts
 * @param array   $points_types     Array of points types to be rendered
 * @param array   $template_args    Template received arguments
 *
 * @return void
 */
function gamipress_restrict_unlock_points_types_shortcode_content( $points_type, $points_awards, $points_deducts, $points_types, $template_args ) {

    // Get the points type ID
    $post_id = gamipress_get_points_type_id( $points_type );

    // Initialize restrict_unlock attribute
    if( ! isset( $template_args['restrict_unlock'] ) )
        $template_args['restrict_unlock'] = 'no';

    if( $template_args['restrict_unlock'] === 'yes' )
        gamipress_restrict_unlock_maybe_render_markup( $post_id );

}
add_action( 'gamipress_after_render_points_type', 'gamipress_restrict_unlock_points_types_shortcode_content', 10, 5 );
