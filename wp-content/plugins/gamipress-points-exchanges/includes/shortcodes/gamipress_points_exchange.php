<?php
/**
 * GamiPress Points Exchange Shortcode
 *
 * @package     GamiPress\Points_Exchanges\Shortcodes\Shortcode\GamiPress_Points_Exchange
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_points_exchange] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_points_exchanges_register_points_exchange_shortcode() {

    // Setup the points types
    $points_types_options = array();

    foreach( gamipress_get_points_types() as $slug => $data ) {
        $points_types_options[$slug] = $data['plural_name'];
    }

    gamipress_register_shortcode( 'gamipress_points_exchange', array(
        'name'              => __( 'Points Exchange Form', 'gamipress-points-exchange' ),
        'description'       => __( 'Render a points exchange form.', 'gamipress-points-exchange' ),
        'output_callback'   => 'gamipress_points_exchanges_points_exchange_shortcode',
        'icon'              => 'update',
        'fields'            => array(

            // Amount

            'allow_input_amount' => array(
                'name'        => __( 'Allow Input Amount', 'gamipress-points-exchange' ),
                'description' => __( 'Allow user input the amount to exchange.', 'gamipress-points-exchange' ),
                'type' 	      => 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),

            'amount' => array(
                'name'        => __( 'Amount', 'gamipress-points-exchange' ),
                'description' => __( 'Amount user will exchange. If user is allowed to input the amount, then this amount will be used as initial amount.', 'gamipress-points-exchange' ),
                'type' 	=> 'text',
                'default' => '100'
            ),

            // From

            'allow_input_from' => array(
                'name'        => __( 'Allow Choose Amount Points Type', 'gamipress-points-exchange' ),
                'description' => __( 'Allow user choose the points type to exchange from.', 'gamipress-points-exchange' ),
                'type' 	      => 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),

            'from' => array(
                'name'        => __( 'Amount\'s Points Type', 'gamipress-points-exchange' ),
                'description' => __( 'The points type to exchange from. If user is allowed to choose the amount\'s points type, then this type will be used as initial selected option.', 'gamipress-points-exchange' ),
                'type' 	=> 'select',
                'options' 	=> $points_types_options,
                'default' => ''
            ),

            // To

            'allow_input_to' => array(
                'name'        => __( 'Allow Choose Exchange Points Type', 'gamipress-points-exchange' ),
                'description' => __( 'Allow user choose the points type to exchange to.', 'gamipress-points-exchange' ),
                'type' 	      => 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),

            'to' => array(
                'name'        => __( 'Target Points Type', 'gamipress-points-exchange' ),
                'description' => __( 'The points type to exchange to. If user is allowed to choose the points type to exchange, then this type will be used as initial selected option.', 'gamipress-points-exchange' ),
                'type' 	=> 'select',
                'options' 	=> $points_types_options,
                'default' => ''
            ),

            // Button text

            'button_text' => array(
                'name'        => __( 'Button Text', 'gamipress-points-exchange' ),
                'description' => __( 'Exchange button text.', 'gamipress-points-exchange' ),
                'type' 	=> 'text',
                'default' => __( 'Exchange', 'gamipress-points-exchange' )
            ),

        ),
    ) );

}
add_action( 'init', 'gamipress_points_exchanges_register_points_exchange_shortcode' );

/**
 * Points Exchange Form Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_points_exchanges_points_exchange_shortcode( $atts = array() ) {

    global $gamipress_points_exchanges_template_args;

    // Get the shortcode attributes
    $atts = shortcode_atts( array(
        'allow_input_amount'    => 'yes',
        'amount'                => '100',
        'allow_input_from'      => 'yes',
        'from'                  => '',
        'allow_input_to'        => 'yes',
        'to'                    => '',
        'button_text' 		    => __( 'Exchange', 'gamipress-points-exchange' ),
    ), $atts, 'gamipress_points_exchange' );

    // Ensure int vars
    $atts['amount'] = absint( $atts['amount'] );

    // Setup user id
    $user_id = get_current_user_id();

    if( $user_id === 0 ) {
        return sprintf( __( 'You need to <a href="%s">login</a> to perform any exchange.', 'gamipress-points-exchange' ), wp_login_url( get_permalink() ) );
    }

    // Check points types
    $points_types = gamipress_get_points_types();

    // From points type check
    if( $atts['allow_input_from'] !== 'yes' ) {

        // Check if points type is registered
        if ( ! isset( $points_types[$atts['from']] ) ) {
            return gamipress_points_exchanges_notify_form_error( __( 'The amount\'s points type provided is not a registered points type.', 'gamipress-points-exchange' ) );
        }

        // Check if points type is has exchanges enabled
        if( ! gamipress_points_exchanges_is_exchanges_enabled( $atts['from'] ) ) {
            return gamipress_points_exchanges_notify_form_error( __( 'The amount\'s points type provided has not exchanges enabled.', 'gamipress-points-exchange' ) );
        }

    }

    // To points type check
    if( $atts['allow_input_to'] !== 'yes' ) {

        // Check if points type is registered
        if ( ! isset( $points_types[$atts['to']] ) ) {
            return gamipress_points_exchanges_notify_form_error( __( 'The points type to exchange provided is not a registered points type.', 'gamipress-points-exchange' ) );
        }

    }

    $gamipress_points_exchanges_template_args = $atts;

    // Setup form vars
    $gamipress_points_exchanges_template_args['form_id'] = 'gamipress-points-exchanges-form-' . gamipress_points_exchanges_generate_form_id();

    // Setup FROM vars
    $valid_from_points_type = ( isset( $points_types[$atts['from']] ) && gamipress_points_exchanges_is_exchanges_enabled( $atts['from'] ) );
    $from_points_type   = ( $valid_from_points_type ? $points_types[$atts['from']]                          : false );
    $from_balance       = ( $from_points_type       ? gamipress_get_user_points( $user_id, $atts['from'] )  : 0 );
    $from_new_balance   = ( $from_points_type       ? $from_balance - $atts['amount']                       : 0 );

    $gamipress_points_exchanges_template_args['from_points_type']   = $from_points_type;
    $gamipress_points_exchanges_template_args['from_balance']       = $from_balance;
    $gamipress_points_exchanges_template_args['from_new_balance']   = $from_new_balance;

    // Setup TO vars
    $valid_to_points_type = ( isset( $points_types[$atts['to']] ) && $valid_from_points_type &&  gamipress_points_exchanges_get_rate( $atts['from'], $atts['to'] ) );
    $to_points_type = ( $valid_to_points_type                   ? $points_types[$atts['to']]                                                            : false );
    $to_balance     = ( $to_points_type                         ? gamipress_get_user_points( $user_id, $atts['to'] )                                    : 0 );
    $to_amount      = ( $to_points_type                         ? gamipress_points_exchanges_apply_rate( $atts['amount'], $atts['from'], $atts['to'] )  : 0 );
    $to_new_balance = ( $from_points_type && $to_points_type    ? $to_balance + $to_amount                                                              : 0 );

    $gamipress_points_exchanges_template_args['to_points_type'] = $to_points_type;
    $gamipress_points_exchanges_template_args['to_balance']     = $to_balance;
    $gamipress_points_exchanges_template_args['to_amount']      = $to_amount;
    $gamipress_points_exchanges_template_args['to_new_balance'] = $to_new_balance;

    // Setup the TO select options
    if( $from_points_type ) {

        $rates = gamipress_points_exchanges_get_rates( $atts['from'] );
        $to_points_types = array();

        // Loop all rates to meet available points type options
        foreach( $rates as $points_type => $rate ) {

            if( $rate !== 0.00 ) {
                $to_points_types[$points_type] = $points_types[$points_type];
            }

        }

        $gamipress_points_exchanges_template_args['to_points_types_options'] = $to_points_types;
    }

    // Setup the initial exchange rate
    $gamipress_points_exchanges_template_args['exchange_rate'] = ( $from_points_type && $to_points_type ? gamipress_points_exchanges_get_rate( $atts['from'], $atts['to'] ) : 0 );

    // Enqueue assets
    gamipress_points_exchanges_enqueue_scripts();

    ob_start();
        gamipress_get_template_part( 'points-exchange-form' );
    $output = ob_get_clean();

    // Return our rendered achievement
    return $output;
}

/**
 * Generate an ID for a form.
 *
 * @since  1.0.0
 *
 * @return string
 */
function gamipress_points_exchanges_generate_form_id() {

    global $gamipress_points_exchanges_form_ids;

    // Initialize forms IDs
    if( ! is_array( $gamipress_points_exchanges_form_ids ) ) {
        $gamipress_points_exchanges_form_ids = array();
    }

    // Setup vars
    $index = 1;
    $id = $index;

    // Loop for a non served ID
    while( in_array( $id, $gamipress_points_exchanges_form_ids ) ) {

        $index++;

        $id = $index;
    }

    // Add the form id to the already placed ids
    $gamipress_points_exchanges_form_ids[] = $id;

    return $id;

}
