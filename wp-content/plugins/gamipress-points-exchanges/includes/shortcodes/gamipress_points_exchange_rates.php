<?php
/**
 * GamiPress Points Exchange Rates Shortcode
 *
 * @package     GamiPress\Points_Exchanges\Shortcodes\Shortcode\GamiPress_Points_Exchange_Rates
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_points_exchange_rates] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_points_exchanges_register_points_exchange_rates_shortcode() {

    gamipress_register_shortcode( 'gamipress_points_exchange_rates', array(
        'name'              => __( 'Points Exchange Rates', 'gamipress-points-exchanges' ),
        'description'       => __( 'Render a table with the points exchange rates.', 'gamipress-points-exchanges' ),
        'output_callback'   => 'gamipress_points_exchanges_points_exchange_rates_shortcode',
        'icon'              => 'update',
        'fields'            => array(
            'points_type' => array(
                'name'        => __( 'Points Type(s)', 'gamipress-points-exchanges' ),
                'description' => __( 'The points types to be listed on this table.', 'gamipress-points-exchanges' )
                                . '<br>' . __( '<strong>Important:</strong> Just will be rendered points types with exchanges enabled.', 'gamipress-points-exchanges' ),
                'type'        => 'advanced_select',
                'multiple'    => true,
                'classes'     => 'gamipress-selector',
                'attributes' 	    => array(
                    'data-placeholder' => __( 'Default: All', 'gamipress-points-exchanges' ),
                ),
                'options_cb'  => 'gamipress_options_cb_points_types',
                'default'     => 'all',
            ),

        ),
    ) );

}
add_action( 'init', 'gamipress_points_exchanges_register_points_exchange_rates_shortcode' );

/**
 * Points Exchange Rates Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_points_exchanges_points_exchange_rates_shortcode( $atts = array() ) {

    global $gamipress_points_exchanges_template_args;

    // Get the shortcode attributes
    $atts = shortcode_atts( array(
        'points_type'       => 'all',
    ), $atts, 'gamipress_points_exchange_rates' );

    // Single type check to use dynamic template
    $is_single_type = false;
    $atts['points_type'] = explode( ',', $atts['points_type'] );

    if( empty( $atts['points_type'] ) || $atts['points_type'] === 'all' || in_array( 'all', $atts['points_type'] ) ) {
        $atts['points_type'] = gamipress_get_points_types_slugs();
    } else if ( count( $atts['points_type'] ) === 1 ) {
        $is_single_type = true;
    }

    $rates = gamipress_points_exchanges_get_all_exchange_rates();

    foreach( $atts['points_type'] as $index => $points_type ) {

        // Remove wrong configured points types
        if( ! isset( $rates[$points_type] ) ) {
            unset( $atts['points_type'][$index] );
        }

    }

    $gamipress_points_exchanges_template_args = $atts;

    $gamipress_points_exchanges_template_args['rates'] = $rates;

    // Enqueue assets
    gamipress_points_exchanges_enqueue_scripts();

    // Template rendering
    ob_start();
    if( $is_single_type ) {
        gamipress_get_template_part( 'points-exchange-rates', $atts['points_type'] );
    } else {
        gamipress_get_template_part( 'points-exchange-rates' );
    }
    $output = ob_get_clean();

    // Return the shortcode output
    return $output;

}
