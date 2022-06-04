<?php
/**
 * Listeners
 *
 * @package     GamiPress\Purchases\Listeners
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * New purchase listener
 *
 * @param object $payment
 */
function gamipress_purchases_new_purchase( $payment ) {

    // Bail if payment is wrong
    if( ! is_object( $payment ) ) {
        return;
    }

    $payment_items = gamipress_purchases_get_payment_items( $payment->payment_id );

    // Bail if anything has been purchased
    if( empty( $payment_items ) ) {
        return;
    }

    $user_id = absint( $payment->user_id );

    // Bail if guest
    if( $user_id === 0 ) {
        return;
    }

    // Trigger new purchase action
    do_action( 'gamipress_purchases_new_purchase', $payment->payment_id, $user_id, $payment, $payment_items );

    // Get our types
    $points_types_slugs = gamipress_get_points_types_slugs();
    $achievement_types_slugs = gamipress_get_achievement_types_slugs();
    $rank_types_slugs = gamipress_get_rank_types_slugs();

    foreach( $payment_items as $payment_item ) {

        // Skip if not item assigned
        if( absint( $payment_item->post_id ) === 0 ) {
            continue;
        }

        $post_type = get_post_type( $payment_item->post_id );

        // Skip if can not get the type of this item
        if( ! $post_type ) {
            continue;
        }

        if( $post_type === 'points-type' && in_array( $payment_item->post_type, $points_types_slugs ) ) {
            // Is a points

            // Amount of points purchased
            $quantity = absint( $payment_item->quantity );

            // Trigger new points purchase action
            do_action( 'gamipress_purchases_new_points_purchase', $payment->payment_id, $user_id, $payment, absint( $payment_item->post_id ), $quantity, $payment_item, $payment_item->payment_item_id );

        } else if( in_array( $post_type, $achievement_types_slugs ) ) {
            // Is an achievement

            // Trigger new achievement purchase action
            do_action( 'gamipress_purchases_new_achievement_purchase', $payment->payment_id, $user_id, $payment, absint( $payment_item->post_id ), $payment_item, $payment_item->payment_item_id );

            // Trigger new specific achievement purchase action
            do_action( 'gamipress_purchases_new_specific_achievement_purchase', $payment->payment_id, $user_id, $payment, absint( $payment_item->post_id ), $payment_item, $payment_item->payment_item_id );

        } else if( in_array( $post_type, $rank_types_slugs ) ) {
            // Is a rank

            // Trigger new rank purchase action
            do_action( 'gamipress_purchases_new_rank_purchase', $payment->payment_id, $user_id, $payment, absint( $payment_item->post_id ), $payment_item, $payment_item->payment_item_id );

            // Trigger new specific rank purchase action
            do_action( 'gamipress_purchases_new_specific_rank_purchase', $payment->payment_id, $user_id, $payment, absint( $payment_item->post_id ), $payment_item, $payment_item->payment_item_id );

        }

    }
}
add_action( 'gamipress_purchases_complete_purchase', 'gamipress_purchases_new_purchase' );