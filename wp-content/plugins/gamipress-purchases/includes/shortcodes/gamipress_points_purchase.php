<?php
/**
 * GamiPress Points Purchase Shortcode
 *
 * @package     GamiPress\Purchases\Shortcodes\Shortcode\GamiPress_Points_Purchase
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_points_purchase] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_purchases_register_points_purchase_shortcode() {

    // Setup the points types
    $points_types_options = array(
        '' => __( 'Default Points', 'gamipress-purchases' )
    );

    foreach( gamipress_get_points_types() as $slug => $data ) {
        $points_types_options[$slug] = $data['plural_name'];
    }

    // Setup the gateways
    $gateways_options = array();

    foreach( gamipress_purchases_get_active_gateways() as $gateway_id => $gateway ) {
        $gateways_options[$gateway_id] = $gateway;
    }

    gamipress_register_shortcode( 'gamipress_points_purchase', array(
        'name'              => __( 'Points Purchase Form', 'gamipress-purchases' ),
        'description'       => __( 'Render a points purchase form.', 'gamipress-purchases' ),
        'output_callback'   => 'gamipress_purchases_points_purchase_shortcode',
        'icon'              => 'cart',
        'group'             => 'purchases',
        'fields'      => array(
            'points_type' => array(
                'name'        => __( 'Points Type', 'gamipress-purchases' ),
                'description' => __( 'The points type to be purchased.', 'gamipress-purchases' ),
                'type' 	=> 'select',
                'options' 	=> $points_types_options,
                'default' => ''
            ),
            'form_type' => array(
                'name'        => __( 'Form Type', 'gamipress-purchases' ),
                'description' => __( 'The purchase form type.', 'gamipress-purchases' ),
                'type' 	=> 'select',
                'options' => array(
                    'fixed' => __( 'Fixed amount', 'gamipress-purchases' ),
                    'custom' => __( 'Allow user inputs the amount', 'gamipress-purchases' ),
                    'options' => __( 'Set of predefined options', 'gamipress-purchases' ),
                ),
                'default' => 'fixed'
            ),

            'amount_type' => array(
                'name'        => __( 'Amount Type', 'gamipress-purchases' ),
                'description' => __( 'The amount type to work with.', 'gamipress-purchases' ),
                'type' 	=> 'select',
                'options' => array(
                    'points' => __( 'Points', 'gamipress-purchases' ),
                    'money' => __( 'Money', 'gamipress-purchases' ),
                ),
                'default' => 'points'
            ),

            // Fixed

            'amount' => array(
                'name'        => __( 'Amount', 'gamipress-purchases' ),
                'description' => __( 'Amount user will purchase (based on the amount type).', 'gamipress-purchases' ),
                'type' 	=> 'text',
                'default' => '100'
            ),

            // Options

            'options' => array(
                'name'        => __( 'Options', 'gamipress-purchases' ),
                'description' => __( 'Options available to purchase.', 'gamipress-purchases' ),
                'type' 	=> 'text',
                'attributes' => array(
                    'type' => 'number'
                ),
                'repeatable' => true,
                'text'     => array(
                    'add_row_text' => __( 'Add Option', 'gamipress-purchases' ),
                ),
            ),
            'allow_user_input' => array(
                'name'        => __( 'Allow User Input', 'gamipress-purchases' ),
                'description' => __( 'Allow user input a custom amount.', 'gamipress-purchases' ),
                'type' 	      => 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),

            // Used for custom and allow user input options

            'initial_amount' => array(
                'name'        => __( 'Initial amount', 'gamipress-purchases' ),
                'description' => __( 'Set the initial amount.', 'gamipress-purchases' ),
                'type' 	      => 'text',
                'default' => '100'
            ),

            // Gateways

            'gateways' => array(
                'name'        => __( 'Payment Gateways', 'gamipress-purchases' ),
                'description' =>__( 'Choose the payment methods customer can use.', 'gamipress-purchases' ),
                'type' 	=> 'advanced_select',
                'multiple'    => true,
                'classes' 	        => 'gamipress-selector',
                'attributes' 	    => array(
                    'data-placeholder' => __( 'Select a gateway', 'gamipress-purchases' ),
                ),
                'options'     => $gateways_options,
                'default'     => 'all',
            ),

            // Acceptance

            'acceptance' => array(
                'name'        => __( 'Acceptance Checkbox', 'gamipress-purchases' ),
                'description' => __( 'Add a required acceptance checkbox (important to meet with GDPR).', 'gamipress-purchases' ),
                'type' 	      => 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'acceptance_text' => array(
                'name'        => __( 'Acceptance Text', 'gamipress-purchases' ),
                'description' => __( 'Text to show for the required acceptance checkbox.', 'gamipress-purchases' ),
                'type' 	      => 'textarea',
                'default' => __( 'I consent to the collection of data provided above', 'gamipress-purchases' )
            ),

            // Purchase button text

            'button_text' => array(
                'name'        => __( 'Button Text', 'gamipress-purchases' ),
                'description' => __( 'Purchase button text.', 'gamipress-purchases' ),
                'type' 	=> 'text',
                'default' => __( 'Purchase', 'gamipress-purchases' )
            ),

        ),
    ) );

}
add_action( 'init', 'gamipress_purchases_register_points_purchase_shortcode' );

/**
 * Points Purchase Form Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_purchases_points_purchase_shortcode( $atts = array() ) {

    global $gamipress_purchases_template_args;

    // Get the shortcode attributes
    $atts = shortcode_atts( array(
        'points_type'       => '',
        'form_type' 		=> 'fixed',
        'amount_type' 		=> 'points',
        'amount' 		    => '100',
        'initial_amount'    => '100',
        'options' 		    => '',
        'allow_user_input'  => 'yes',
        'gateways'	  		=> 'all',
        'acceptance'	    => 'yes',
        'acceptance_text'	=> __( 'I consent to the collection of data provided above', 'gamipress-purchases' ),
        'button_text' 		=> __( 'Purchase', 'gamipress-purchases' ),
    ), $atts, 'gamipress_points_purchase' );

    $gamipress_purchases_template_args = $atts;

    // Setup user id
    $user_id = get_current_user_id();

    if( $user_id === 0 ) {
        return sprintf( __( 'You need to <a href="%s">log in</a> to purchase this.', 'gamipress-purchases' ), wp_login_url( get_permalink() ) );
    }

    // Setup gateways
    if( $atts['gateways'] === 'all' ) {
        $gateways = gamipress_purchases_get_active_gateways();
    } else {
        $active_gateways = gamipress_purchases_get_active_gateways();
        $selected_gateways = explode( ',', $atts['gateways'] );
        $gateways = array();

        foreach( $selected_gateways as $selected_gateway ) {
            if( ! isset( $active_gateways[$selected_gateway] ) ) {
                continue;
            }

            $gateways[$selected_gateway] = $active_gateways[$selected_gateway];
        }
    }

    if( empty( $gateways ) ) {

        return gamipress_purchases_notify_form_error( __( 'There is no gateways selected.', 'gamipress-purchases' ) );

    }

    $gamipress_purchases_template_args['gateways'] = $gateways;

    // Setup points types
    $points_types = gamipress_get_points_types();

    if( ! isset( $points_types[$atts['points_type']] ) ) {

        return gamipress_purchases_notify_form_error( __( 'The points type provided is not a registered points type.', 'gamipress-purchases' ) );

    }

    // Setup buy conversion rate
    $points_type = $points_types[$atts['points_type']];
    $conversion = gamipress_get_post_meta( $points_type['ID'], '_gamipress_purchases_conversion' );

    if( empty( $conversion) ) {

        // TODO: Add link to the points type edit screen
        return gamipress_purchases_notify_form_error( __( 'The points type provided has not configured the conversion rate.', 'gamipress-purchases' ) );

    }

    $gamipress_purchases_template_args['points_type_object'] = $points_type;
    $gamipress_purchases_template_args['conversion'] = $conversion;
    $gamipress_purchases_template_args['currency_position'] = gamipress_purchases_get_option( 'currency_position', 'before' );
    $amount = 0;
    $points_total = 0;
    $subtotal = 0;

    // Setup options
    if( $atts['form_type'] === 'fixed' ) {

        $atts['amount'] = floatval( $atts['amount'] );

        if( $atts['amount_type'] === 'points' ) {
            $fixed_money = gamipress_purchases_convert_to_money( $atts['amount'], $atts['points_type'] );
            $fixed_points = $atts['amount'];

            $amount = $fixed_points;
        } else {
            $fixed_money = $atts['amount'];
            $fixed_points = gamipress_purchases_convert_to_points( $atts['amount'], $atts['points_type'] );

            $amount = $fixed_money;
        }

        $gamipress_purchases_template_args['fixed_money'] = $fixed_money;
        $gamipress_purchases_template_args['fixed_points'] = $fixed_points;
        $gamipress_purchases_template_args['fixed_money_label'] = gamipress_purchases_format_price( $fixed_money );
        $gamipress_purchases_template_args['fixed_points_label'] = $fixed_points . ' ' . _n( $points_type['singular_name'], $points_type['plural_name'], $fixed_points );

        $points_total = $fixed_points;
        $subtotal = $fixed_money;
    } else if( $atts['form_type'] === 'custom' ) {

        if( $atts['amount_type'] === 'points' ) {
            $custom_money = gamipress_purchases_convert_to_money( $atts['initial_amount'], $atts['points_type'] );
            $custom_points = $atts['initial_amount'];
            $gamipress_purchases_template_args['preview'] = gamipress_purchases_format_price( $custom_money );

            $amount = $custom_points;
        } else {
            $custom_money = $atts['initial_amount'];
            $custom_points = gamipress_purchases_convert_to_points( $atts['initial_amount'], $atts['points_type'] );
            $gamipress_purchases_template_args['preview'] = $custom_points . ' ' . _n( $points_type['singular_name'], $points_type['plural_name'], $custom_points );

            $amount = $custom_money;
        }

        $gamipress_purchases_template_args['custom_money'] = $custom_money;
        $gamipress_purchases_template_args['custom_points'] = $custom_points;

        $points_total = $custom_points;
        $subtotal = $custom_money;
    } else if( $atts['form_type'] === 'options' ) {

        // Explode the comma separated options
        $options = explode( ',', $atts['options'] );

        if( empty( $options ) ) {

            return gamipress_purchases_notify_form_error( __( 'There is no purchase options.', 'gamipress-purchases' ) );

        }

        // Format the given options
        foreach( $options as $index => $option ) {

            $new_option = array(
              'label' => '',
              'value' => $option,
            );

            if( $atts['amount_type'] === 'points' ) {

                $new_option['label'] = $option . ' ' . _n( $points_type['singular_name'], $points_type['plural_name'], $option );

            } else {

                $new_option['label'] = gamipress_purchases_format_price( $option );

            }

            // Initialize form values based on first option
            if( $index === 0 ) {

                if( $atts['amount_type'] === 'points' ) {

                    $subtotal = gamipress_purchases_convert_to_money( $option, $atts['points_type'] );

                } else {

                    $subtotal = $option;

                }
            }

            $options[$index] = $new_option;

        }

        $gamipress_purchases_template_args['options'] = $options;
    }

    // Setup the form totals
    $user_details = gamipress_purchases_get_user_billing_details( $user_id );

    $tax_rate = gamipress_purchases_get_tax_rate( $user_details['country'], $user_details['state'], $user_details['postcode'] );
    $tax = $tax_rate * 100;
    $tax_amount = $subtotal * $tax_rate;

    // Setup the form vars
    $gamipress_purchases_template_args['purchase_key'] = gamipress_purchases_generate_purchase_key();
    $gamipress_purchases_template_args['form_id'] = 'gamipress-purchases-form-' . esc_attr( $gamipress_purchases_template_args['purchase_key'] );

    // Points and money amounts
    $gamipress_purchases_template_args['amount'] = $amount;
    $gamipress_purchases_template_args['points_total'] = $points_total;

    // Setup form totals
    $gamipress_purchases_template_args['subtotal'] = $subtotal;
    $gamipress_purchases_template_args['tax'] = $tax;
    $gamipress_purchases_template_args['tax_amount'] = $tax_amount;
    $gamipress_purchases_template_args['total'] = $subtotal + $tax_amount;

    // Enqueue assets
    gamipress_purchases_enqueue_scripts();

    ob_start();
        gamipress_get_template_part( 'points-purchase-form', $atts['points_type'] );
    $output = ob_get_clean();

    // Return our rendered achievement
    return $output;
}
