<?php
/**
 * Filters
 *
 * @package GamiPress\WooCommerce\Partial_Payments\Filters
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Partial payments form
 *
 * @since 1.0.0
 */
function gamipress_wc_partial_payments_form() {

    global $gamipress_wc_partial_payments_template_args;

    // Guests not allowed
    if( ! is_user_logged_in() ) return;

    $user_id = get_current_user_id();
    $partial_payments = gamipress_wc_partial_payments_get_partial_payments();

    $points_types = array();
    $prefix = '_gamipress_wc_partial_payments_';

    // Settings
    $amount_type = gamipress_wc_partial_payments_get_option( 'amount_type', 'input' );
    $amount_step = absint( gamipress_wc_partial_payments_get_option( 'amount_step', '1' ) );

    foreach( gamipress_get_points_types() as $points_type => $data ) {

        // Skip points type that are already in use
        if( isset( $partial_payments[$points_type] ) ) continue;

        if( (bool) gamipress_get_post_meta( $data['ID'], $prefix . 'enable' ) ) {

            $data['user_points'] = gamipress_get_user_points( $user_id, $points_type );

            // Skip points types that user doesn't have any amount
            if( $data['user_points'] === 0 ) continue;

            $data['conversion'] = gamipress_get_post_meta( $data['ID'], $prefix . 'conversion' );

            // Points types without a conversion rate can't be used for partial payments
            if( empty( $data['conversion'] ) ) continue;

            $data['initial_amount']  = absint( gamipress_get_post_meta( $data['ID'], $prefix . 'initial_amount' ) );
            $data['max_amount']  = absint( gamipress_get_post_meta( $data['ID'], $prefix . 'max_amount' ) );

            /**
             * Filter to allow other plugins to decide if points type is allowed for partial payments
             *
             * @since 1.0.0
             *
             * @param bool      $allow_points_type  Whatever if points type is allowed for partial payments, by default true
             * @param string    $points_type        The points type slug
             * @param integer   $user_id            The user ID that will perform the purchase
             * @param array     $data               The points type data, with extra keys from this plugin
             *
             * @return bool                         Whatever if points type is allowed for partial payments, by default true
             */
            $allow_points_type = apply_filters( 'gamipress_wc_partial_payments_allow_points_type', true, $points_type, $user_id, $data );

            // Skip not allowed points types
            if( ! $allow_points_type ) continue;

            // Turn amount type into a valid input type value
            switch( $amount_type ) {
                case 'fixed':
                    $field_type = 'hidden';
                    break;
                case 'slider':
                    $field_type = 'range';
                    break;
                default:
                    $field_type = 'number';
                    break;
            }

            // Setup field vars (for the points field)
            $data['field_type'] = $field_type;
            $data['field_placeholder'] = 0;
            $data['field_step'] = $amount_step;
            $data['field_min'] = 0;
            $data['field_max'] = ( $data['max_amount'] === 0 ? $data['user_points'] : $data['max_amount'] );
            $data['field_value'] = $data['initial_amount'];

            $points_types[$points_type] = $data;
        }

    }

    // Bail if none points type is setup to be used for partial payments
    if( empty( $points_types ) ) {
        return;
    }

    /**
     * Filter to allow other plugins to decide if is allowed partial payments with this full setup
     *
     * @since 1.0.0
     *
     * @param bool      $allow_partial_payments Whatever if is allowed partial payments, by default true
     * @param array     $points_types           Array of the points types allowed, with extra keys from this plugin
     * @param integer   $user_id                The user ID that will perform the purchase
     *
     * @return bool                             Whatever if is allowed partial payments, by default true
     */
    $allow_partial_payments = apply_filters( 'gamipress_wc_partial_payments_allow_partial_payments', true, $points_types, $user_id );

    // Bail if not allowed partial payments
    if( ! $allow_partial_payments ) {
        return;
    }

    $gamipress_wc_partial_payments_template_args = array();

    // Pass the settings to the template
    $gamipress_wc_partial_payments_template_args['amount_type'] = $amount_type;
    $gamipress_wc_partial_payments_template_args['amount_step'] = $amount_step;

    // Setup vars
    $initial_points_type = array_keys( $points_types )[0];
    $initial_points_type_data = $points_types[$initial_points_type];
    $gamipress_wc_partial_payments_template_args['points_types'] = $points_types;
    $gamipress_wc_partial_payments_template_args['initial_points_type'] = $initial_points_type;
    $gamipress_wc_partial_payments_template_args['initial_points_type_data'] = $initial_points_type_data;

    // Points preview vars
    $points_preview_html = '<span class="gamipress-wc-partial-payments-preview-points">' . gamipress_format_amount( $initial_points_type_data['initial_amount'], $initial_points_type ) . '</span>';
    $points_type_preview_label = '<span class="gamipress-wc-partial-payments-preview-points-type">' . $initial_points_type_data['plural_name'] . '</span>';
    // Add the points label to the points amount
    $points_preview_html .= ' ' . $points_type_preview_label;

    $gamipress_wc_partial_payments_template_args['points_preview'] = $points_preview_html;

    // Setup money preview
    $preview_money      = gamipress_wc_partial_payments_convert_to_money( $initial_points_type_data['initial_amount'], $initial_points_type );
    $decimals           = wc_get_price_decimals();
    $decimal_separator  = wc_get_price_decimal_separator();
    $thousand_separator = wc_get_price_thousand_separator();
    $preview_money      = number_format( $preview_money, $decimals, $decimal_separator, $thousand_separator );

    $money_preview_html = '<span class="gamipress-wc-partial-payments-preview-money">' . $preview_money . '</span>';
    // Add the currency to the money amount
    $money_preview_html = sprintf( get_woocommerce_price_format(), '<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol() . '</span>', $money_preview_html );

    $gamipress_wc_partial_payments_template_args['money_preview'] = $money_preview_html;

    gamipress_get_template_part( 'wc-partial-payments-checkout' );

}
add_action( 'woocommerce_before_checkout_form', 'gamipress_wc_partial_payments_form', 11 );

/**
 * Add new fees based on partial payments applied
 *
 * @since 1.0.0
 *
 * @param WC_Cart $cart
 */
function gamipress_wc_partial_payments_calculate_fees( $cart ) {

    $partial_payments = gamipress_wc_partial_payments_get_partial_payments();

    // Loop all applied partial payments to add them as a fee
    foreach( $partial_payments as $points_type => $data ) {
        WC()->cart->fees_api()->add_fee(
            array(
                'name'          => sprintf( __( 'Discount using %s', 'gamipress-wc-partial-payments' ), gamipress_format_points( $data['points'], $points_type ) ),
                'amount'        => -1 * $data['money'],
                'taxable'       => false,
                'tax_class'     => '',
                'points_type'   => $points_type,
                'points'        => $data['points'],
                'money'         => $data['money'],
            )
        );
    }

}
add_action( 'woocommerce_cart_calculate_fees', 'gamipress_wc_partial_payments_calculate_fees' );

/**
 * Filter required on WooCommerce Subscriptions to make the discount work on renewals
 *
 * @since 1.0.0
 *
 * @param bool $is_recurring
 * @param object $fee
 * @param WC_Cart $cart
 */
function gamipress_wc_partial_payments_recurring_fee( $is_recurring, $fee, $cart ) {

    if( property_exists( $fee, 'points_type' ) ) {
        $is_recurring = true;
    }

    return $is_recurring;

}
add_filter( 'woocommerce_subscriptions_is_recurring_fee', 'gamipress_wc_partial_payments_recurring_fee', 10, 3 );

/**
 * Adds the remove link to the fee
 *
 * @since 1.0.0
 *
 * @param string $cart_totals_fee_html
 * @param object $fee
 *
 * @return string
 */
function gamipress_wc_partial_payments_fee_html( $html, $fee ) {

    $points_type = ( property_exists( $fee, 'points_type' ) ? $fee->points_type : '' );
    $points_type_obj = gamipress_get_points_type( $points_type );

    // If is a fee from a partial payment, add the remove link
    if( $points_type_obj )
        $html .= ' <a href="#" class="gamipress-wc-partial-payments-remove" data-points-type="' . $points_type . '">' . __( '[Remove]', 'gamipress-wc-partial-payments' ) . '</a>';

    return $html;
}
add_filter( 'woocommerce_cart_totals_fee_html', 'gamipress_wc_partial_payments_fee_html', 10, 2 );

/**
 *  On save fee, add custom meta data
 *
 * @since 1.0.0
 *
 * @param WC_Order_Item_Fee $item
 * @param $fee_key
 * @param stdClass $fee
 * @param WC_Order $order
 */
function gamipress_wc_partial_payments_create_order_fee_item( $item, $fee_key, $fee, $order ) {

    $prefix = '_gamipress_wc_partial_payments_';

    if( property_exists( $fee, 'points_type' ) )
        $item->add_meta_data( $prefix . 'points_type', $fee->points_type, true );

    if( property_exists( $fee, 'points' ) )
        $item->add_meta_data( $prefix . 'points', $fee->points, true );

    if( property_exists( $fee, 'money' ) )
        $item->add_meta_data( $prefix . 'money', $fee->money, true );

}
add_action( 'woocommerce_checkout_create_order_fee_item', 'gamipress_wc_partial_payments_create_order_fee_item', 10, 4 );

/**
 * Clear the user fees on empty the cart
 *
 * @since 1.0.0
 */
function gamipress_wc_partial_payments_clear_partial_payments() {

    if( is_user_logged_in() ) {

        $user_id = get_current_user_id();

        delete_user_meta( $user_id, 'gamipress_wc_partial_payments' );

    }

}
add_action( 'woocommerce_cart_emptied', 'gamipress_wc_partial_payments_clear_partial_payments' );

/**
 * Deduct points to the user if user has partially paid it
 *
 * @since 1.0.0
 *
 * @param int $order_id
 */
function gamipress_wc_partial_payments_revoke_points_on_complete( $order_id ) {

    $prefix = '_gamipress_wc_partial_payments_';

    // Set a post meta to meet that points have been deducted
    update_post_meta( $order_id, $prefix . 'points_deducted', '1' );

    // TODO: Now points are deducted at the moment the user applies the discount to avoid hacks
    return;

    $order = wc_get_order( $order_id );

    $points_deducted = get_post_meta( $order_id, $prefix . 'points_deducted', true );

    // Bail if points already deducted
    if( (bool) $points_deducted ) {
        return;
    }

    // Loop all order fees
    foreach( $order->get_fees() as $fee ) {

        $points_type = $fee->get_meta( $prefix . 'points_type' );

        if( gamipress_get_points_type( $points_type ) ) {
            // Get the points amount
            $points = absint( $fee->get_meta( $prefix . 'points' ) );

            // Deduct the points to the user
            gamipress_deduct_points_to_user( $order->get_user_id(), $points, $points_type );
        }
    }

    // Set a post meta to meet that points have been deducted
    update_post_meta( $order_id, $prefix . 'points_deducted', '1' );

}
add_action( 'woocommerce_payment_complete', 'gamipress_wc_partial_payments_revoke_points_on_complete' );

/**
 * Award points to the user if user has partially paid it
 *
 * @since 1.0.0
 *
 * @param int $order_id
 */
function gamipress_wc_partial_payments_award_points_on_refund( $order_id ) {

    $prefix = '_gamipress_wc_partial_payments_';

    $order = wc_get_order( $order_id );

    $points_deducted = get_post_meta( $order_id, $prefix . 'points_deducted', true );

    // Bail if points hasn't been deducted, then there is nothing to award
    if( ! (bool) $points_deducted ) {
        return;
    }

    // Loop all order fees
    foreach( $order->get_fees() as $fee ) {

        $points_type = $fee->get_meta( $prefix . 'points_type' );

        if( gamipress_get_points_type( $points_type ) ) {
            // Get the points amount
            $points = absint( $fee->get_meta( $prefix . 'points' ) );

            // Award the points to the user
            gamipress_award_points_to_user( $order->get_user_id(), $points, $points_type );
        }
    }

    // Set a post meta to meet that points haven't been deducted
    update_post_meta( $order_id, $prefix . 'points_deducted', '' );

    // Set a post meta to meet that points have been refunded
    update_post_meta( $order_id, $prefix . 'points_refunded', '1' );

}

/**
 * Check order status changes to meet if should award or revoke points to the user
 *
 * @since 1.0.0
 *
 * @param $order_id
 * @param $from
 * @param $to
 * @param $order
 */
function gamipress_wc_partial_payments_check_order_status_change( $order_id, $from, $to, $order ) {

    if( $from !== 'completed' && $to === 'completed' )
        gamipress_wc_partial_payments_revoke_points_on_complete( $order_id );

    if( $from !== 'refunded' && $to === 'refunded' )
        gamipress_wc_partial_payments_award_points_on_refund( $order_id );

}
add_action( 'woocommerce_order_status_changed', 'gamipress_wc_partial_payments_check_order_status_change', 10, 4 );