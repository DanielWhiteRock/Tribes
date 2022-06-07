<?php
/**
 * Points Checkout Total template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/wc-points-gateway/wc-points-checkout.php
 */
global $gamipress_wc_points_gateway_template_args;

// Shorthand
$a = $gamipress_wc_points_gateway_template_args;

$points_types = gamipress_get_points_types();

// Default points type
$points_types[''] = array(
    'singular_name' => __( 'Point', 'gamipress' ),
    'plural_name' => __( 'Points', 'gamipress' )
);

$points_type = $points_types[$a['points_type']];

// ----------------------------------
// User points
// ----------------------------------

$user_points_label = sprintf( __( 'Current %s:', 'gamipress-wc-points-gateway' ), $points_type['plural_name'] );

/**
 * Filter the user points label
 *
 * @since 1.0.4
 *
 * @param string    $user_points_label
 * @param string    $points_type
 * @param array     $template_args
 */
$user_points_label = apply_filters( 'gamipress_wc_points_gateway_checkout_user_points_label', $user_points_label, $a['points_type'], $a );

/**
 * Filter the user points
 *
 * @since 1.0.3
 *
 * @param int       $user_points
 * @param string    $points_type
 * @param array     $template_args
 */
$user_points = apply_filters( 'gamipress_wc_points_gateway_checkout_user_points', $a['user_points'], $a['points_type'], $a );

// ----------------------------------
// Required points
// ----------------------------------

$cart_points_label = sprintf( __( 'Required %s:', 'gamipress-wc-points-gateway' ), $points_type['plural_name'] );

/**
 * Filter the required points label
 *
 * @since 1.0.4
 *
 * @param string    $required_points_label
 * @param string    $points_type
 * @param array     $template_args
 */
$cart_points_label = apply_filters( 'gamipress_wc_points_gateway_checkout_cart_points_label', $cart_points_label, $a['points_type'], $a );

/**
 * Filter the required points
 *
 * @since 1.0.3
 *
 * @param int       $required_points
 * @param string    $points_type
 * @param array     $template_args
 */
$cart_points = apply_filters( 'gamipress_wc_points_gateway_checkout_cart_points', $a['cart_points'], $a['points_type'], $a );

// ----------------------------------
// Points after purchase
// ----------------------------------

$new_points_balance_label = sprintf( __( '%s after purchase:', 'gamipress-wc-points-gateway' ), $points_type['plural_name'] );

/**
 * Filter the points after purchase label
 *
 * @since 1.0.4
 *
 * @param string    $new_points_balance_label
 * @param string    $points_type
 * @param array     $template_args
 */
$new_points_balance_label = apply_filters( 'gamipress_wc_points_gateway_checkout_new_points_balance_label', $new_points_balance_label, $a['points_type'], $a );

/**
 * Filter the points after purchase
 *
 * @since 1.0.3
 *
 * @param string    $new_points_balance
 * @param string    $points_type
 * @param array     $template_args
 */
$new_points_balance = apply_filters( 'gamipress_wc_points_gateway_checkout_new_points_balance', $a['user_points'] - $a['cart_points'], $a['points_type'], $a );
?>


<?php do_action( 'gamipress_wc_points_gateway_checkout_before', $a ); ?>

    <tr id="payment-method-gamipress-<?php echo $a['points_type']; ?>-user-balance-wrap" class="gamipress-wc-points-gateway-wrap" style="display: none;">
        <th><?php echo esc_html( $user_points_label ); ?></th>
        <td class="gamipress-wc-points-gateway-user-balance"><?php echo $user_points; ?></td>
    </tr>

    <tr id="payment-method-gamipress-<?php echo $a['points_type']; ?>-required-balance-wrap" class="gamipress-wc-points-gateway-wrap" style="display: none;">
        <th><?php echo esc_html( $cart_points_label ); ?></th>
        <td class="gamipress-wc-points-gateway-required-balance"><?php echo $cart_points; ?></td>
    </tr>

    <tr id="payment-method-gamipress-<?php echo $a['points_type']; ?>-new-balance-wrap" class="gamipress-wc-points-gateway-wrap" style="display: none;">
        <th><?php echo esc_html( $new_points_balance_label ); ?></th>
        <td class="gamipress-wc-points-gateway-new-balance"><?php echo $new_points_balance; ?></td>
    </tr>

<?php do_action( 'gamipress_wc_points_gateway_checkout_after', $a ); ?>