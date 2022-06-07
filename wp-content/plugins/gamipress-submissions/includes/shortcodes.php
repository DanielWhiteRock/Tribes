<?php
/**
 * Shortcodes
 *
 * @package GamiPress\Submissions\Shortcodes
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Shortcodes
require_once GAMIPRESS_SUBMISSIONS_DIR . 'includes/shortcodes/gamipress_submission.php';

/**
 * Adds the "submissions" parameter to [gamipress_achievement]
 *
 * @since 1.0.0
 *
 * @param array $fields
 *
 * @return mixed
 */
function gamipress_submissions_achievement_shortcode_fields( $fields ) {

    $fields['submissions'] = array(
        'name'        => __( 'Show Submissions', 'gamipress-submissions' ),
        'description' => __( 'Display a submission form if achievement has submissions enabled.', 'gamipress-submissions' ),
        'type' 	=> 'checkbox',
        'classes' => 'gamipress-switch',
        'default' => 'yes'
    );

    return $fields;

}
add_filter( 'gamipress_gamipress_achievement_shortcode_fields', 'gamipress_submissions_achievement_shortcode_fields' );

/**
 * Adds the "submissions" parameter to [gamipress_achievement] defaults
 *
 * @since 1.0.0
 *
 * @param array $defaults
 *
 * @return array
 */
function gamipress_submissions_achievement_shortcode_defaults( $defaults ) {

    $defaults['submissions'] = 'yes';

    return $defaults;

}
add_filter( 'gamipress_achievement_shortcode_defaults', 'gamipress_submissions_achievement_shortcode_defaults' );

/**
 * Adds the "submissions" parameter to [gamipress_rank]
 *
 * @since 1.0.4
 *
 * @param array $fields
 *
 * @return array
 */
function gamipress_submissions_rank_shortcode_fields( $fields ) {

    $fields['submissions'] = array(
        'name'        => __( 'Show Submissions', 'gamipress-submissions' ),
        'description' => __( 'Display a submission form if rank has submissions enabled.', 'gamipress-submissions' ),
        'type' 	=> 'checkbox',
        'classes' => 'gamipress-switch',
        'default' => 'yes'
    );

    return $fields;

}
add_filter( 'gamipress_gamipress_rank_shortcode_fields', 'gamipress_submissions_rank_shortcode_fields' );

/**
 * Adds the "submissions" parameter to [gamipress_rank] defaults
 *
 * @since 1.0.0
 *
 * @param array $defaults
 *
 * @return array
 */
function gamipress_submissions_rank_shortcode_defaults( $defaults ) {

    $defaults['submissions'] = 'yes';

    return $defaults;

}
add_filter( 'gamipress_rank_shortcode_defaults', 'gamipress_submissions_rank_shortcode_defaults' );