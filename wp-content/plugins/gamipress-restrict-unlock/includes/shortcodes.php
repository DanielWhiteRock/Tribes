<?php
/**
 * Shortcodes
 *
 * @package     GamiPress\Restrict_Unlock\Shortcodes
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Adds the "restrict_unlock" parameter to [gamipress_achievement]
 *
 * @since 1.0.0
 *
 * @param array $fields
 *
 * @return mixed
 */
function gamipress_restrict_unlock_shortcode_fields( $fields ) {

    $fields['restrict_unlock'] = array(
        'name'        => __( 'Show Restrict Unlock', 'gamipress-restrict-unlock' ),
        'description' => __( 'Display informational text and unlock button if unlock is restricted.', 'gamipress-restrict-unlock' ),
        'type' 	=> 'checkbox',
        'classes' => 'gamipress-switch',
    );

    return $fields;

}
add_filter( 'gamipress_gamipress_achievement_shortcode_fields', 'gamipress_restrict_unlock_shortcode_fields' );
add_filter( 'gamipress_gamipress_rank_shortcode_fields', 'gamipress_restrict_unlock_shortcode_fields' );
add_filter( 'gamipress_gamipress_points_types_shortcode_fields', 'gamipress_restrict_unlock_shortcode_fields' );

/**
 * Adds the "restrict_unlock" parameter to some shortcodes defaults
 *
 * @since 1.0.0
 *
 * @param array $defaults
 *
 * @return array
 */
function gamipress_restrict_unlock_shortcode_defaults( $defaults ) {

    $defaults['restrict_unlock'] = 'no';

    return $defaults;

}
add_filter( 'gamipress_achievement_shortcode_defaults', 'gamipress_restrict_unlock_shortcode_defaults' );
add_filter( 'gamipress_rank_shortcode_defaults', 'gamipress_restrict_unlock_shortcode_defaults' );


/**
 * Adds the "restrict_unlock" parameter to [gamipress_points_types] shortcode defaults
 *
 * @since 1.0.0
 *
 * @param array  $out       The output array of shortcode attributes.
 * @param array  $pairs     The supported attributes and their defaults.
 * @param array  $atts      The user defined shortcode attributes.
 * @param string $shortcode The shortcode name.
 *
 * @return array
 */
function gamipress_restrict_unlock_points_types_shortcode_defaults( $out, $pairs, $atts, $shortcode ) {

    $out['restrict_unlock'] = ( isset( $atts['restrict_unlock'] ) ? $atts['restrict_unlock'] : 'no' );

    return $out;
}
add_filter( 'shortcode_atts_gamipress_points_types', 'gamipress_restrict_unlock_points_types_shortcode_defaults', 10, 4 );
