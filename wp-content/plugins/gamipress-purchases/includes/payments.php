<?php
/**
 * Payments
 *
 * @package     GamiPress\Purchases\Payments
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the registered payment statuses
 *
 * @since  1.0.0
 *
 * @return array Array of payment statuses
 */
function gamipress_purchases_get_payment_statuses() {

    return apply_filters( 'gamipress_purchases_get_payment_statuses', array(
        'processing' => __( 'Processing', 'gamipress-purchases' ),
        'pending' => __( 'Pending', 'gamipress-purchases' ),
        'complete' => __( 'Complete', 'gamipress-purchases' ),
        'cancelled' => __( 'Cancelled', 'gamipress-purchases' ),
        'failed' => __( 'Failed', 'gamipress-purchases' ),
        'refunded' => __( 'Refunded', 'gamipress-purchases' ),
    ) );

}

/**
 * Get an unique generated new purchase key
 *
 * @since  1.0.0
 *
 * @return string
 */
function gamipress_purchases_generate_purchase_key() {

    global $wpdb;

    $new_purchase_key = wp_generate_password( 12, false, false );

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_payments' );

    $found = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$ct_table->db->table_name} WHERE purchase_key = %s LIMIT 1", $new_purchase_key ) );

    if( $found ) {
        return gamipress_purchases_generate_purchase_key();
    }

    return $new_purchase_key;

}

/**
 * Update the payment status
 *
 * @since  1.0.0
 *
 * @param integer   $payment_id   The payment ID
 * @param string    $new_status   The payment new status
 *
 * @return bool                   Tru if status changes successfully, false if not
 */
function gamipress_purchases_update_payment_status( $payment_id, $new_status ) {

    // Check if new status is registered
    $payment_statuses = gamipress_purchases_get_payment_statuses();
    $payment_statuses = array_keys( $payment_statuses );

    if( ! in_array( $new_status, $payment_statuses ) ) {
        return false;
    }

    // Setup the CT Table
    $ct_table = ct_setup_table( 'gamipress_payments' );

    // Check the object
    $payment = ct_get_object( $payment_id );

    if( ! $payment ) {
        return false;
    }


    // Prevent set the same status
    if( $payment->status === $new_status ) {
        return false;
    }

    $old_status = $payment->status;

    // Update the payment status
    $ct_table->db->update(
        array( 'status' => $new_status ),
        array( 'payment_id' => $payment_id )
    );

    // Fire the payment status transition hooks
    gamipress_purchases_transition_payment_status( $new_status, $old_status, $payment );

}

/**
 * Fires hooks related to the payment status
 *
 * @since  1.0.0
 *
 * @param string    $new_status     The payment new status
 * @param string    $old_status     The payment old status
 * @param object    $payment        The payment object
 *
 * @return bool                   Tru if status changes successfully, false if not
 */
function gamipress_purchases_transition_payment_status( $new_status, $old_status, $payment ) {

    // Trigger a common transition action to hook any change
    do_action( 'gamipress_purchases_transition_payment_status', $new_status, $old_status, $payment );

    // Trigger a specific transition action to hook a desired change
    do_action( "gamipress_purchases_{$old_status}_to_{$new_status}", $payment );

    if( $new_status === 'complete' && $old_status !== 'complete' ) {

        // Trigger a new purchase hook
        do_action( 'gamipress_purchases_complete_purchase', $payment );

    }

}

/**
 * Award the purchased items to the user
 *
 * @since  1.0.0
 *
 * @param string    $new_status     The payment new status
 * @param string    $old_status     The payment old status
 * @param object    $payment        The payment object
 */
function gamipress_purchases_award_purchased_items( $new_status, $old_status, $payment ) {

    // Not complete yet
    if( $new_status !== 'complete' ) {
        return;
    }

    // Already completed
    if( $old_status === 'complete' ) {
        return;
    }

    $user_id = absint( $payment->user_id );

    // Guest not supported yet
    if( $user_id === 0 ) {
        return;
    }

    // Get our types
    $points_types = gamipress_get_points_types();
    $points_types_slugs = gamipress_get_points_types_slugs();
    $achievement_types = gamipress_get_achievement_types();
    $achievement_types_slugs = gamipress_get_achievement_types_slugs();
    $rank_types = gamipress_get_rank_types();
    $rank_types_slugs = gamipress_get_rank_types_slugs();

    $payment_items = gamipress_purchases_get_payment_items( $payment->payment_id );

    // Loop all items to check item types assigned
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

        // Setup table on each loop for the usage of ct_get_object_meta() and ct_update_object_meta()
        ct_setup_table( 'gamipress_payment_items' );

        $awarded = (bool) ct_get_object_meta( $payment_item->payment_item_id, '_gamipress_purchases_awarded', true );

        // Skip already awarded items
        if( $awarded ) {
            continue;
        }

        if( $post_type === 'points-type' && in_array( $payment_item->post_type, $points_types_slugs ) ) {
            // Is a points

            // Add a mark to meet that this payment item has been awarded
            ct_update_object_meta( $payment_item->payment_item_id, '_gamipress_purchases_awarded', '1' );

            $quantity = absint( $payment_item->quantity );
            $points_type = $points_types[$payment_item->post_type];

            // Award points (with support for GamiPress 1.3.7)
            if( function_exists( 'gamipress_award_points_to_user' ) ) {
                gamipress_award_points_to_user( $user_id, $quantity, $payment_item->post_type );
            } else {
                gamipress_update_user_points( $user_id, $quantity, false, null, $payment_item->post_type );
            }

            // Add an informative note to let user know that points has been awarded
            gamipress_purchases_insert_payment_note( $payment->payment_id,
                sprintf( __( '%s awarded to user', 'gamipress-purchases' ), $points_type['plural_name'] ),
                sprintf( __( '%d %s awarded to user for successfully complete the payment', 'gamipress-purchases' ), $quantity, _n( $points_type['singular_name'], $points_type['plural_name'], $quantity ) )
            );

        } else if( in_array( $post_type, $achievement_types_slugs ) ) {
            // Is an achievement

            // Add a mark to meet that this payment item has been awarded
            ct_update_object_meta( $payment_item->payment_item_id, '_gamipress_purchases_awarded', '1' );

            // Award achievement
            gamipress_award_achievement_to_user( $payment_item->post_id, $user_id );

            // Add an informative note to let user know that achievement has been awarded
            gamipress_purchases_insert_payment_note( $payment->payment_id,
                sprintf( __( '%s awarded to user', 'gamipress-purchases' ), $achievement_types[$post_type]['singular_name'] ),
                sprintf( __( '%s %s awarded to user for successfully complete the payment', 'gamipress-purchases' ),  $achievement_types[$post_type]['singular_name'], get_post_field( 'post_title', $payment_item->post_id ) )
            );

        } else if( in_array( $post_type, $rank_types_slugs ) ) {
            // Is a rank

            // Add a mark to meet that this payment item has been awarded
            ct_update_object_meta( $payment_item->payment_item_id, '_gamipress_purchases_awarded', '1' );

            // Award rank
            gamipress_update_user_rank( $user_id, $payment_item->post_id );

            // Add an informative note to let user know that rank has been awarded
            gamipress_purchases_insert_payment_note( $payment->payment_id,
                sprintf( __( '%s awarded to user', 'gamipress-purchases' ), $rank_types[$post_type]['singular_name'] ),
                sprintf( __( '%s %s awarded to user for successfully complete the payment', 'gamipress-purchases' ),  $rank_types[$post_type]['singular_name'], get_post_field( 'post_title', $payment_item->post_id ) )
            );

        }

    }

}
add_action( 'gamipress_purchases_transition_payment_status', 'gamipress_purchases_award_purchased_items', 10, 3 );

/**
 * Revoke the purchased items to the user on refund
 *
 * @since  1.0.0
 *
 * @param string    $new_status     The payment new status
 * @param string    $old_status     The payment old status
 * @param object    $payment        The payment object
 */
function gamipress_purchases_revoke_purchased_items( $new_status, $old_status, $payment ) {

    // Not refunded yet
    if( $new_status !== 'refunded' ) {
        return;
    }

    // Already refunded
    if( $old_status === 'refunded' ) {
        return;
    }

    $user_id = absint( $payment->user_id );

    // Guest not supported yet
    if( $user_id === 0 ) {
        return;
    }

    // Get our types
    $points_types = gamipress_get_points_types();
    $points_types_slugs = gamipress_get_points_types_slugs();
    $achievement_types = gamipress_get_achievement_types();
    $achievement_types_slugs = gamipress_get_achievement_types_slugs();
    $rank_types = gamipress_get_rank_types();
    $rank_types_slugs = gamipress_get_rank_types_slugs();

    $payment_items = gamipress_purchases_get_payment_items( $payment->payment_id );

    // Loop all items to check item types assigned
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

        // Setup table on each loop for the usage of ct_get_object_meta() and ct_update_object_meta()
        ct_setup_table( 'gamipress_payment_items' );

        $awarded = (bool) ct_get_object_meta( $payment_item->payment_item_id, '_gamipress_purchases_awarded', true );

        // Skip not awarded items
        if( ! $awarded ) {
            continue;
        }

        if( $post_type === 'points-type' && in_array( $payment_item->post_type, $points_types_slugs ) ) {
            // Is a points

            // Add a mark to meet that this payment item has been revoked
            ct_update_object_meta( $payment_item->payment_item_id, '_gamipress_purchases_awarded', '0' );

            $quantity = absint( $payment_item->quantity );
            $points_type = $points_types[$payment_item->post_type];

            $user_points = gamipress_get_user_points( $user_id, $payment_item->post_type );

            // Revoke points (with support for GamiPress 1.3.7)
            if( function_exists( 'gamipress_deduct_points_to_user' ) ) {
                gamipress_deduct_points_to_user( $user_id, $quantity, $payment_item->post_type );
            } else {
                // Passed current user as admin to allow revoke points
                gamipress_update_user_points( $user_id, $user_points - $quantity, get_current_user_id(), null, $payment_item->post_type );
            }


            // Add an informative note to let user know that points has been revoked
            gamipress_purchases_insert_payment_note( $payment->payment_id,
                sprintf( __( '%s revoked to user', 'gamipress-purchases' ), $points_type['plural_name'] ),
                sprintf( __( '%d %s revoked to user for refund the payment', 'gamipress-purchases' ), $quantity, _n( $points_type['singular_name'], $points_type['plural_name'], $quantity ) )
            );

        } else if( in_array( $post_type, $achievement_types_slugs ) ) {
            // Is an achievement

            // Add a mark to meet that this payment item has been revoked
            ct_update_object_meta( $payment_item->payment_item_id, '_gamipress_purchases_awarded', '0' );

            // Revoke achievement
            gamipress_revoke_achievement_from_user( $payment_item->post_id, $user_id );

            // Add an informative note to let user know that achievement has been revoked
            gamipress_purchases_insert_payment_note( $payment->payment_id,
                sprintf( __( '%s revoked to user', 'gamipress-purchases' ), $achievement_types[$post_type]['singular_name'] ),
                sprintf( __( '%s %s revoked to user for refund the payment', 'gamipress-purchases' ),  $achievement_types[$post_type]['singular_name'], get_post_field( 'post_title', $payment_item->post_id ) )
            );

        } else if( in_array( $post_type, $rank_types_slugs ) ) {
            // Is a rank

            // Add a mark to meet that this payment item has been revoked
            ct_update_object_meta( $payment_item->payment_item_id, '_gamipress_purchases_awarded', '0' );

            // Get the current user rank
            $user_rank_id = gamipress_get_user_rank_id( $user_id, $post_type );

            // If user is actually on this rank, move to the previous rank
            if( $user_rank_id === absint( $payment_item->post_id ) ) {
                $prev_rank_id = gamipress_get_prev_rank_id( $user_rank_id );

                if( $prev_rank_id ) {
                    // Award previous rank
                    gamipress_update_user_rank( $user_id, $prev_rank_id );
                }
            }

            // Revoke rank (from the earnings table)
            gamipress_revoke_achievement_from_user( $payment_item->post_id, $user_id );

            // Add an informative note to let user know that rank has been revoked
            gamipress_purchases_insert_payment_note( $payment->payment_id,
                sprintf( __( '%s awarded to user', 'gamipress-purchases' ), $rank_types[$post_type]['singular_name'] ),
                sprintf( __( '%s %s awarded to user for successfully complete the payment', 'gamipress-purchases' ),  $rank_types[$post_type]['singular_name'], get_post_field( 'post_title', $payment_item->post_id ) )
            );

        }

    }

}
add_action( 'gamipress_purchases_transition_payment_status', 'gamipress_purchases_revoke_purchased_items', 10, 3 );

/**
 * Get the next payment number
 *
 * @since  1.0.0
 *
 * @return integer
 */
function gamipress_purchases_get_payment_next_payment_number() {

    global $wpdb;

    $ct_table = ct_setup_table( 'gamipress_payments' );

    $number = $wpdb->get_var( "SELECT p.number FROM {$ct_table->db->table_name} AS p ORDER BY p.number DESC LIMIT 1" );

    ct_reset_setup_table();

    return absint( $number ) + 1;

}

/**
 * Get the payment items
 *
 * @since  1.0.0
 *
 * @param integer $payment_id   The payment ID
 *
 * @return array                Array of payment items
 */
function gamipress_purchases_get_payment_items( $payment_id, $output = OBJECT ) {

    ct_setup_table( 'gamipress_payment_items' );

    $ct_query = new CT_Query( array(
        'payment_id' => $payment_id,
        'order' => 'ASC'
    ) );

    $payment_items = $ct_query->get_results();

    if( $output === ARRAY_N || $output === ARRAY_A ) {

        // Turn array of objects into an array of arrays
        foreach( $payment_items as $payment_item_index => $payment_item ) {
            $payment_items[$payment_item_index] = (array) $payment_item;
        }

    }

    ct_reset_setup_table();

    return $payment_items;

}

/**
 * Get the payment id querying it by the given field and desired field value
 *
 * @since  1.0.0
 *
 * @param string $field   The field to query
 * @param string $value   The field value to filter
 *
 * @return integer        The payment ID
 */
function gamipress_purchases_get_payment_id_by( $field, $value ) {

    global $wpdb;

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_payments' );

    $payment_id = $wpdb->get_var( $wpdb->prepare( "SELECT {$ct_table->db->primary_key} FROM {$ct_table->db->table_name} WHERE {$field} = %s LIMIT 1", $value ) );

    ct_reset_setup_table();

    return absint( $payment_id );

}

/**
 * Inset a payment note
 *
 * @since  1.0.0
 *
 * @param integer   $payment_id     The payment ID
 * @param string    $title          The payment note title
 * @param string    $description    The payment note description
 * @param integer   $user_id        The user ID (-1 = GamiPress BOT, 0 = Guest)
 *
 * @return bool|integer             The payment note ID or false
 */
function gamipress_purchases_insert_payment_note( $payment_id, $title, $description, $user_id = -1 ) {

    $ct_table = ct_setup_table( 'gamipress_payment_notes' );

    $return = $ct_table->db->insert( array(
        'payment_id' => $payment_id,
        'title' => $title,
        'description' => $description,
        'user_id' => $user_id,
        'date' => date( 'Y-m-d H:i:s' ),
    ) );

    ct_reset_setup_table();

    return $return;

}

/**
 * Get the payment notes
 *
 * @since  1.0.0
 *
 * @param integer $payment_id   The payment ID
 *
 * @return array                Array of payment notes
 */
function gamipress_purchases_get_payment_notes( $payment_id, $output = OBJECT ) {

    ct_setup_table( 'gamipress_payment_notes' );

    $ct_query = new CT_Query( array(
        'payment_id' => $payment_id,
        'order' => 'DESC'
    ) );

    $payment_items = $ct_query->get_results();

    if( $output === ARRAY_N || $output === ARRAY_A ) {

        // Turn array of objects into an array of arrays
        foreach( $payment_items as $payment_item_index => $payment_item ) {
            $payment_items[$payment_item_index] = (array) $payment_item;
        }

    }

    ct_reset_setup_table();

    return $payment_items;

}

/**
 * Return all user payments
 *
 * @since  1.0.0
 *
 * @param integer   $user_id
 * @param array     $query_args
 *
 * @return array
 */
function gamipress_purchases_get_user_payments( $user_id = null, $query_args = array() ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    ct_setup_table( 'gamipress_payments' );

    $query_args['user_id'] = $user_id;

    $ct_query = new CT_Query( $query_args );

    $results = $ct_query->get_results();

    ct_reset_setup_table();

    return $results;

}

/**
 * Return user payments count
 *
 * @since  1.0.0
 *
 * @param integer $user_id
 *
 * @return integer
 */
function gamipress_purchases_get_user_payments_count( $user_id = null ) {

    global $wpdb;

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_payments' );

    $user_payments = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*)
         FROM {$ct_table->db->table_name}
         WHERE user_id = %d",
        absint( $user_id )
    ) );

    ct_reset_setup_table();

    return absint( $user_payments );

}

/**
 * Check if user has a payment with a post ID already pending to pay
 *
 * @since  1.0.0
 *
 * @param integer $user_id
 * @param integer $post_id
 *
 * @return integer|bool     If a pending payment exists return the ID, if not, return false
 */
function gamipress_purchases_user_get_item_pending_to_pay( $user_id, $post_id ) {

    global $wpdb;

    // Setup table
    $payments_table = ct_setup_table( 'gamipress_payments' );
    $payment_items_table = ct_setup_table( 'gamipress_payment_items' );

    $pending_payment = $wpdb->get_var( $wpdb->prepare(
        "SELECT p.payment_id
         FROM {$payments_table->db->table_name} AS p
         INNER JOIN {$payment_items_table->db->table_name} AS m
         ON ( p.payment_id = m.payment_id )
         WHERE p.user_id = %d
         AND ( p.status = %s OR p.status = %s )
         AND m.post_id = %d
         LIMIT 1",
        absint( $user_id ),
        'pending',
        'processing',
        absint( $post_id )
    ) );

    ct_reset_setup_table();

    return ( $pending_payment ? $pending_payment : false );

}

/**
 * Return the purchase history page link
 *
 * @since  1.0.0
 *
 * @return false|string
 */
function gamipress_purchases_get_purchase_history_link() {

    $purchase_history_page = gamipress_purchases_get_option( 'purchase_history_page', '' );

    $permalink = get_permalink( $purchase_history_page );

    return $permalink;

}

/**
 * Return the purchase details page link
 *
 * @since  1.0.0
 *
 * @param integer $payment_id
 *
 * @return false|string
 */
function gamipress_purchases_get_purchase_details_link( $payment_id ) {

    $permalink = gamipress_purchases_get_purchase_history_link();

    if( $permalink ) {
        $permalink = add_query_arg( 'payment_id', $payment_id, $permalink );
    }

    return $permalink;

}
