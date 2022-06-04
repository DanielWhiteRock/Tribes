<?php
/**
 * GamiPress Purchases 1.0.3 compatibility functions
 *
 * @package     GamiPress\Purchases\1.0.3
 * @since       1.0.3
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Add old shortcodes for Backward compatibility
add_shortcode( 'gamipress_points_purchase_form', 'gamipress_purchases_points_purchase_shortcode' );
add_shortcode( 'gamipress_achievement_purchase_form', 'gamipress_purchases_achievement_purchase_shortcode' );
add_shortcode( 'gamipress_rank_purchase_form', 'gamipress_purchases_rank_purchase_shortcode' );

/**
 * Inset a payment note
 *
 * @deprecated Wrong named function, use gamipress_purchases_insert_payment_note() instead
 *
 * @since  1.0.3
 *
 * @param integer   $payment_id     The payment ID
 * @param string    $title          The payment note title
 * @param string    $description    The payment note description
 * @param integer   $user_id        The user ID (-1 = GamiPress BOT, 0 = Guest)
 *
 * @return bool|integer             The payment note ID or false
 */
function gamipress_purchases_inset_payment_note( $payment_id, $title, $description, $user_id = -1 ) {
    return gamipress_purchases_insert_payment_note( $payment_id, $title, $description, $user_id = -1 );
}