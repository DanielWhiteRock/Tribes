<?php
/**
 * GamiPress Points Payout Shortcode
 *
 * @package     GamiPress\Points_Payouts\Shortcodes\Shortcode\GamiPress_Points_payout
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_points_payout] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_points_payouts_register_points_payout_shortcode() {

    gamipress_register_shortcode( 'gamipress_points_payout', array(
        'name'              => __( 'Points Payout', 'gamipress-points-payout' ),
        'description'       => __( 'Render a points payout form.', 'gamipress-points-payout' ),
        'output_callback'   => 'gamipress_points_payouts_points_payout_shortcode',
        'icon'              => 'star-filled',
        'fields'            => array(
            'points_type' => array(
                'name'          => __( 'Points Type(s)', 'gamipress-points-payout' ),
                'description'   => __( 'The points type(s) to withdrawal.', 'gamipress-points-payout' ),
                'type' 	        => 'advanced_select',
                'multiple' 	    => true,
                'classes' 	    => 'gamipress-selector',
                'attributes' 	=> array(
                    'data-placeholder' => __( 'Default: All', 'gamipress-points-payout' ),
                ),
                'options_cb' 	=> 'gamipress_options_cb_points_types',
                'default'       => ''
            ),
            'payment_method' => array(
                'name'          => __( 'Show payment method input', 'gamipress-points-payout' ),
                'description'   => __( 'Check this option to show the payment method input.', 'gamipress-points-payout' ),
                'type' 	        => 'checkbox',
                'classes' 	    => 'gamipress-switch',
            ),
            'button_text' => array(
                'name'        => __( 'Button Text', 'gamipress-points-payout' ),
                'description' => __( 'Form button text.', 'gamipress-points-payout' ),
                'type' 	=> 'text',
                'default' => __( 'Withdrawal Points', 'gamipress-points-payout' )
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_points_payouts_register_points_payout_shortcode' );

/**
 * Points Payout Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_points_payouts_points_payout_shortcode( $atts = array() ) {

    global $gamipress_points_payouts_template_args;

    $prefix = '_gamipress_points_payouts_';

    // Get the shortcode attributes
    $atts = shortcode_atts( array(

        'points_type'       => '',
        'payment_method'    => '',
        'button_text'       => __( 'Withdrawal Points', 'gamipress-points-payout' ),

    ), $atts, 'gamipress_points_payout' );

    // Single type check to use dynamic template
    $is_single_type = false;
    $points_types = explode( ',', $atts['points_type'] );

    if( empty( $atts['points_type'] ) || $atts['points_type'] === 'all' || in_array( 'all', $points_types ) ) {
        $points_types = gamipress_get_points_types_slugs();
    }

    $valid_points_types = array();
    $points_types_objects = array();

    // Only allow points types with points payouts enabled
    foreach( $points_types as $i => $points_type ) {

        $points_type_obj = gamipress_get_points_type( $points_type );

        // Skip if not is a valid points type
        if( ! $points_type_obj ) {
            continue;
        }

        $enabled = (bool) gamipress_get_post_meta( $points_type_obj['ID'], $prefix . 'enable', true );

        // Skip points type if hasn't enabled for points payouts
        if( ! $enabled ) {
            continue;
        }

        // Register this points type as a valid points type
        $valid_points_types[] = $points_type;
        $points_types_objects[$points_type] = array(
            'conversion' => gamipress_points_payouts_get_conversion( $points_type ),
            'min_amount' => (int) gamipress_get_post_meta( $points_type_obj['ID'], $prefix . 'min_amount', true ),
            'max_amount' => (int) gamipress_get_post_meta( $points_type_obj['ID'], $prefix . 'max_amount', true ),
        );

    }

    $points_types = $valid_points_types;

    if ( empty( $points_types ) ) {
        return gamipress_shortcode_error( __( 'The type(s) provided haven\'t enabled the option to allow points payouts.', 'gamipress-points-payout' ), 'gamipress_points_payout' );
    }

    if ( count( $points_types ) === 1 ) {
        $is_single_type = true;
    }

    // Setup user id
    $user_id = get_current_user_id();

    if( $user_id === 0 ) {
        return sprintf( __( 'You need to <a href="%s">log in</a> to withdrawal points.', 'gamipress-points-payout' ), wp_login_url( get_permalink() ) );
    }

    $gamipress_points_payouts_template_args = $atts;
    $gamipress_points_payouts_template_args['points_types'] = $points_types;
    $gamipress_points_payouts_template_args['points_types_objects'] = $points_types_objects;
    $gamipress_points_payouts_template_args['form_id'] = gamipress_points_payouts_generate_form_id();

    // Enqueue assets
    gamipress_points_payouts_enqueue_scripts();

    ob_start();
    gamipress_get_template_part( 'points-payout-form', ( $is_single_type ? $points_types[0] : null ) );
    $output = ob_get_clean();

    // Return our rendered form
    return $output;
}

/**
 * Generate an unique form ID
 *
 * @since  1.0.0
 *
 * @return string
 */
function gamipress_points_payouts_generate_form_id() {

    global $gamipress_points_payouts_shortcode_ids;

    if( ! is_array( $gamipress_points_payouts_shortcode_ids ) ) {
        $gamipress_points_payouts_shortcode_ids = array();
    }

    $id_pattern = 'gamipress-points-payouts-form-';
    $index = 1;

    // First ID
    $id = $id_pattern . $index;

    while( in_array( $id, $gamipress_points_payouts_shortcode_ids ) ) {

        $index++;

        $id = $id_pattern . $index;
    }

    $gamipress_points_payouts_shortcode_ids[] = $id;

    return $id;

}
