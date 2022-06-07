<?php
/**
 * GamiPress Achievement Purchase Shortcode
 *
 * @package     GamiPress\Purchases\Shortcodes\Shortcode\GamiPress_Achievement_Purchase
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_achievement_purchase] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_purchases_register_achievement_purchase_shortcode() {

    // Setup the gateways
    $gateways_options = array();

    foreach( gamipress_purchases_get_active_gateways() as $gateway_id => $gateway ) {
        $gateways_options[$gateway_id] = $gateway;
    }

    // Setup the achievement fields
    $achievement_fields = GamiPress()->shortcodes['gamipress_achievement']->fields;

    unset( $achievement_fields['id'] );

    gamipress_register_shortcode( 'gamipress_achievement_purchase', array(
        'name'              => __( 'Achievement Purchase Form', 'gamipress-purchases' ),
        'description'       => __( 'Render an achievement purchase form.', 'gamipress-purchases' ),
        'output_callback'   => 'gamipress_purchases_achievement_purchase_shortcode',
        'icon'              => 'cart',
        'group'             => 'purchases',
        'tabs' => array(
            'form' => array(
                'icon' => 'dashicons-feedback',
                'title' => __( 'Form', 'gamipress' ),
                'fields' => array(
                    'id',
                    'gateways',
                    'acceptance',
                    'acceptance_text',
                    'button_text',
                ),
            ),
            'achievement' => array(
                'icon' => 'dashicons-awards',
                'title' => __( 'Achievement', 'gamipress' ),
                'fields' => array_keys( $achievement_fields ),
            ),
        ),
        'fields'      => array_merge( array(
            'id' => array(
                'name'        => __( 'Achievement ID', 'gamipress-purchases' ),
                'description' => __( 'The ID of the achievement to be purchased.', 'gamipress-purchases' ),
                'type'        => 'select',
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  gamipress_get_achievement_types_slugs() ),
                    'data-placeholder' => __( 'Select an achievement', 'gamipress-purchases' ),
                ),
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_posts'
            ),

            // Gateways

            'gateways' => array(
                'name'        => __( 'Payment Gateways', 'gamipress-purchases' ),
                'description' => __( 'Choose the payment methods customer can use.', 'gamipress-purchases' ),
                'type' 	      => 'advanced_select',
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

        ), $achievement_fields ),
    ) );

}
add_action( 'init', 'gamipress_purchases_register_achievement_purchase_shortcode' );

/**
 * Achievement Purchase Form Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_purchases_achievement_purchase_shortcode( $atts = array() ) {

    global $gamipress_purchases_template_args;

    // Get the shortcode attributes
    $atts = shortcode_atts( array_merge( array(
        'id'                => '0',
        'gateways'	  		=> 'all',
        'acceptance'	    => 'yes',
        'acceptance_text'	=> __( 'I consent to the collection of data provided above', 'gamipress-purchases' ),
        'button_text' 	    => __( 'Purchase', 'gamipress-purchases' ),
    ), gamipress_achievement_shortcode_defaults() ), $atts, 'gamipress_achievement_purchase' );

    $gamipress_purchases_template_args = $atts;

    // Return if achievement id not specified
    if ( empty( $atts['id'] ) )
        return '';

    // Setup the achievement
    $achievement = gamipress_get_post( $atts['id'] );

    if( ! $achievement ) {

        return gamipress_purchases_notify_form_error( __( 'Achievement not exists.', 'gamipress-purchases' ) );

    }

    // Setup user id
    $user_id = get_current_user_id();

    if( $user_id === 0 ) {
        return sprintf( __( 'You need to <a href="%s">log in</a> to purchase this.', 'gamipress-purchases' ), wp_login_url( get_permalink() ) );
    }

    // Setup achievement template args
    $gamipress_purchases_template_args['template_args'] = array();

    $achievement_fields = array_keys( GamiPress()->shortcodes['gamipress_achievement']->fields );

    foreach( $achievement_fields as $achievement_field ) {

        if( isset( $atts[$achievement_field] ) ) {
            $gamipress_purchases_template_args['template_args'][$achievement_field] = $atts[$achievement_field];
        }

    }

    // Check if achievement is allowed to be purchased
    $allow_purchase = (bool) gamipress_get_post_meta( $atts['id'], '_gamipress_purchases_allow_purchase', true );

    if( ! $allow_purchase ) {
        return gamipress_purchases_notify_form_error( __( 'Achievement has not configured to allow customers to purchase it.', 'gamipress-purchases' ) );
    }

    // Check if user has exceed the maximum earnings the achievement
    $user_exceed_earnings = gamipress_achievement_user_exceeded_max_earnings( $user_id, $atts['id'] );

    if( $user_exceed_earnings ) {
        return '';
    }

    // Check if user has a pending payment with this item
    $pending_payment = gamipress_purchases_user_get_item_pending_to_pay( $user_id, $atts['id'] );

    if( $pending_payment !== false ) {

        // let know to the template that user has a pending purchase
        $gamipress_purchases_template_args['pending_purchase'] = true;

        $gamipress_purchases_template_args['purchase_details_link'] =  gamipress_purchases_get_purchase_details_link( $pending_payment );
    }

    // Check if achievement has correctly configured the price
    $price = gamipress_purchases_convert_to_float( gamipress_get_post_meta( $atts['id'], '_gamipress_purchases_price', true ) );

    if( $price === 0 ) {
        return gamipress_purchases_notify_form_error( __( 'Achievement has not a price configured.', 'gamipress-purchases' ) );
    }

    // Setup gateways
    if( $atts['gateways'] === 'all' ) {
        $gateways = gamipress_purchases_get_active_gateways();
    } else {
        $active_gateways = gamipress_purchases_get_active_gateways();
        $selected_gateways = explode( ',', $atts['gateways'] );
        $gateways = array();

        foreach( $selected_gateways as $selected_gateway ) {
            if( ! isset($active_gateways[$selected_gateway]) ) {
                continue;
            }

            $gateways[$selected_gateway] = $active_gateways[$selected_gateway];
        }
    }

    if( empty( $gateways ) ) {

        return gamipress_purchases_notify_form_error( __( 'There is no gateways selected.', 'gamipress-purchases' ) );

    }

    $gamipress_purchases_template_args['gateways'] = $gateways;

    // Setup the form vars
    $gamipress_purchases_template_args['purchase_key'] = gamipress_purchases_generate_purchase_key();
    $gamipress_purchases_template_args['form_id'] = 'gamipress-purchases-form-' . esc_attr( $gamipress_purchases_template_args['purchase_key'] );

    // Setup the form totals
    $user_details = gamipress_purchases_get_user_billing_details( $user_id );

    $tax_rate = gamipress_purchases_get_tax_rate( $user_details['country'], $user_details['state'], $user_details['postcode'] );
    $tax = $tax_rate * 100;
    $tax_amount = $price * $tax_rate;

    $gamipress_purchases_template_args['subtotal'] = $price;
    $gamipress_purchases_template_args['tax'] = $tax;
    $gamipress_purchases_template_args['tax_amount'] = $tax_amount;
    $gamipress_purchases_template_args['total'] = $price + $tax_amount;

    // Enqueue assets
    gamipress_purchases_enqueue_scripts();

    ob_start();
    gamipress_get_template_part( 'achievement-purchase-form', $achievement->post_type );
    $output = ob_get_clean();

    // Return our rendered achievement purchase form
    return $output;
}
