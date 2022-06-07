<?php
/**
 * GamiPress Submission Shortcode
 *
 * @package     GamiPress\Submissions\Shortcodes\Shortcode\GamiPress_Submission
 * @since       1.1.8
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_submission] shortcode.
 *
 * @since 1.1.8
 */
function gamipress_register_submission_shortcode() {

    $allowed_types = array_merge( gamipress_get_achievement_types_slugs(), gamipress_get_rank_types_slugs() );

    gamipress_register_shortcode( 'gamipress_submission', array(
        'name'              => __( 'Submission Form', 'gamipress-submissions' ),
        'description'       => __( 'Render a submission form of a desired element.', 'gamipress-submissions' ),
        'output_callback'   => 'gamipress_submission_shortcode',
        'icon'              => 'upload',
        'fields'            => array(
            'id' => array(
                'name'              => __( 'Achievement or Rank', 'gamipress-submissions' ),
                'description'       => __( 'The achievement or rank to render the submission form.', 'gamipress-submissions' ),
                'shortcode_desc'    => __( 'The ID of the achievement or rank to render the submission form.', 'gamipress-submissions' ),
                'type'              => 'select',
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  $allowed_types ),
                    'data-placeholder' => __( 'Select an item', 'gamipress-submissions' ),
                ),
                'default'           => '',
                'options_cb'        => 'gamipress_options_cb_posts'
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_register_submission_shortcode' );

/**
 * Submissions Shortcode.
 *
 * @since  1.1.8
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_submission_shortcode( $atts = array(), $content = '' ) {

    // Setup default attrs
    $atts = shortcode_atts( array(
        'id' => '',
    ), $atts, 'gamipress_submission' );

    // Check user ID
    $user_id = get_current_user_id();

    if( $user_id === 0 ) {
        return '';
    }

    // Check post ID
    $post_id = absint( $atts['id'] );

    $post = gamipress_get_post( $post_id );

    if( ! $post ) {
        return gamipress_shortcode_error( __( 'Please, provide a valid item ID.', 'gamipress-submissions' ), 'gamipress_submission' );
    }

    $allowed_types = array_merge( gamipress_get_achievement_types_slugs(), gamipress_get_rank_types_slugs() );

    if( ! in_array( $post->post_type, $allowed_types ) ) {
        return gamipress_shortcode_error( __( 'Item ID provided is not an achievement or rank.', 'gamipress-submissions' ), 'gamipress_submission' );
    }

    $template_args = array(
        'submissions' => 'yes',
        'user_id' => $user_id,
    );

    // Render the submission form
    ob_start();
        if( in_array( $post->post_type, gamipress_get_achievement_types_slugs() ) ) {
            gamipress_submissions_achievement_submission_button( $post_id, $template_args );
        } else {
            gamipress_submissions_rank_submission_button( $post_id, $template_args );
        }
    $output = ob_get_clean();

    /**
     * Filter to override shortcode output
     *
     * @since 1.1.8
     *
     * @param string    $output     Final output
     * @param array     $atts       Shortcode attributes
     * @param string    $content    Shortcode content
     */
    return apply_filters( 'gamipress_submission_shortcode_output', $output, $atts, $content );

}
