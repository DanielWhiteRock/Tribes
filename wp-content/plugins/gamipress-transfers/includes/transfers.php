<?php
/**
 * Transfers Functions
 *
 * @package     GamiPress\Transfers\Transfers_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the registered transfer statuses
 *
 * @since  1.0.0
 *
 * @return array Array of transfer statuses
 */
function gamipress_transfers_get_transfer_statuses() {

    return apply_filters( 'gamipress_transfers_get_transfer_statuses', array(
        'processing' => __( 'Processing', 'gamipress-transfers' ),
        'pending' => __( 'Pending', 'gamipress-transfers' ),
        'complete' => __( 'Complete', 'gamipress-transfers' ),
        'cancelled' => __( 'Cancelled', 'gamipress-transfers' ),
        'failed' => __( 'Failed', 'gamipress-transfers' ),
        'refunded' => __( 'Refunded', 'gamipress-transfers' ),
    ) );

}

/**
 * Get the next transfer number
 *
 * @since  1.0.0
 *
 * @return integer
 */
function gamipress_transfers_get_transfer_next_transfer_number() {

    global $wpdb;

    $ct_table = ct_setup_table( 'gamipress_transfers' );

    $number = $wpdb->get_var( "SELECT p.number FROM {$ct_table->db->table_name} AS p ORDER BY p.number DESC LIMIT 1" );

    ct_reset_setup_table();

    return absint( $number ) + 1;

}

/**
 * Update the transfer status
 *
 * @since  1.0.0
 *
 * @param integer   $transfer_id    The transfer ID
 * @param string    $new_status     The transfer new status
 *
 * @return bool                     True if status changes successfully, false if not
 */
function gamipress_transfers_update_transfer_status( $transfer_id, $new_status ) {

    // Check if new status is registered
    $transfer_statuses = gamipress_transfers_get_transfer_statuses();
    $transfer_statuses = array_keys( $transfer_statuses );

    if( ! in_array( $new_status, $transfer_statuses ) ) {
        return false;
    }

    // Setup the CT Table
    $ct_table = ct_setup_table( 'gamipress_transfers' );

    // Check the object
    $transfer = ct_get_object( $transfer_id );

    if( ! $transfer ) {
        return false;
    }


    // Prevent set the same status
    if( $transfer->status === $new_status ) {
        return false;
    }

    $old_status = $transfer->status;

    // Update the transfer status
    $ct_table->db->update(
        array( 'status' => $new_status ),
        array( 'transfer_id' => $transfer_id )
    );

    // Fire the transfer status transition hooks
    gamipress_transfers_transition_transfer_status( $new_status, $old_status, $transfer );

    return true;

}

/**
 * Fires hooks related to the transfer status
 *
 * @since  1.0.0
 *
 * @param string    $new_status     The transfer new status
 * @param string    $old_status     The transfer old status
 * @param object    $transfer       The transfer object
 *
 * @return bool                     True if status changes successfully, false if not
 */
function gamipress_transfers_transition_transfer_status( $new_status, $old_status, $transfer ) {

    // Trigger a common transition action to hook any change
    do_action( 'gamipress_transfers_transition_transfer_status', $new_status, $old_status, $transfer );

    // Trigger a specific transition action to hook a desired change
    do_action( "gamipress_transfers_{$old_status}_to_{$new_status}", $transfer );

    if( $new_status === 'complete' && $old_status !== 'complete' ) {

        // Trigger a new transfer hook
        do_action( 'gamipress_transfers_complete_transfer', $transfer );

    }

}

/**
 * Transfer the transfer items to the user
 *
 * @since  1.0.0
 *
 * @param string    $new_status     The transfer new status
 * @param string    $old_status     The transfer old status
 * @param object    $transfer        The transfer object
 */
function gamipress_transfers_transfer_status_change( $new_status, $old_status, $transfer ) {

    // Revoke items on pending
    if( $new_status === 'pending' && $old_status !== 'pending' ) {
        gamipress_transfers_revoke_items_to_user( $transfer );
    }

    // Award on complete
    if( $new_status === 'complete' && $old_status !== 'complete' ) {

        // If transfer is marked directly as complete, revoke the items to the user
        if( $old_status !== 'pending' ) {
            gamipress_transfers_revoke_items_to_user( $transfer );
        }

        gamipress_transfers_award_items_to_recipient( $transfer );
    }

    // Restore items on refund
    if( $new_status === 'refunded' && $old_status !== 'refunded' ) {
        gamipress_transfers_restore_items_transferred( $transfer );
    }

}
add_action( 'gamipress_transfers_transition_transfer_status', 'gamipress_transfers_transfer_status_change', 10, 3 );

/**
 * Revoke the transfer items to the user
 *
 * @since  1.0.0
 *
 * @param object    $transfer        The transfer object
 */
function gamipress_transfers_revoke_items_to_user( $transfer ) {

    $user_id = absint( $transfer->user_id );

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

    $transfer_items = gamipress_transfers_get_transfer_items( $transfer->transfer_id );

    // Loop all items to check item types assigned
    foreach( $transfer_items as $transfer_item ) {

        // Skip if not item assigned
        if( absint( $transfer_item->post_id ) === 0 ) {
            continue;
        }

        $post_type = gamipress_get_post_type( $transfer_item->post_id );

        // Skip if can not get the type of this item
        if( ! $post_type ) {
            continue;
        }

        // Setup table on each loop for the usage of ct_get_object_meta() and ct_update_object_meta()
        ct_setup_table( 'gamipress_transfer_items' );

        $revoked = (bool) ct_get_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_revoked', true );

        // Skip already revoked items
        if( $revoked ) {
            continue;
        }

        if( $post_type === 'points-type' && in_array( $transfer_item->post_type, $points_types_slugs ) ) {
            // Is a points

            // Add a mark to meet that this transfer item has been awarded
            ct_update_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_revoked', '1' );

            $quantity = absint( $transfer_item->quantity );

            // Perform the transfer between users (deduct to user and award to recipient)
            gamipress_deduct_points_to_user( $user_id, $quantity, $transfer_item->post_type );

            // Setup vars for the transfer note
            $points_type = $points_types[$transfer_item->post_type];
            $user = get_userdata( $user_id );

            // Add an informative note to let user know that points has been transferred
            gamipress_transfers_insert_transfer_note( $transfer->transfer_id,
                sprintf( __( '%s revoked', 'gamipress-transfers' ), $points_type['plural_name'] ),
                sprintf( __( '%d %s has been revoked to %s account', 'gamipress-transfers' ),
                    // X points
                    $quantity, _n( $points_type['singular_name'], $points_type['plural_name'], $quantity ),
                    // User link
                    $user->display_name . ' (' . $user->user_email . ')',
                )
            );

        } else if( in_array( $post_type, $achievement_types_slugs ) ) {
            // Is an achievement

            // Add a mark to meet that this transfer item has been awarded
            ct_update_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_revoked', '1' );

            // Perform the transfer between users (revoke to user and award to recipient)
            gamipress_revoke_achievement_to_user( $transfer_item->post_id, $user_id );

            // Setup vars for the transfer note
            $achievement_type = $achievement_types[$transfer_item->post_type];
            $achievement_title = get_post_field( 'post_title', $transfer_item->post_id );
            $user = get_userdata( $user_id );

            // Add an informative note to let user know that points has been transferred
            gamipress_transfers_insert_transfer_note( $transfer->transfer_id,
                sprintf( __( '%s revoked', 'gamipress-transfers' ), $achievement_type['singular_name'] ),
                sprintf( __( '%s %s has been revoked from %s account', 'gamipress-transfers' ),
                    // Achievement Achievement_Title
                    $achievement_type['singular_name'], $achievement_title,
                    // User link
                    $user->display_name . ' (' . $user->user_email . ')',
                )
            );

        } else if( in_array( $post_type, $rank_types_slugs ) ) {
            // Is a rank

            // Add a mark to meet that this transfer item has been awarded
            ct_update_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_revoked', '1' );

            // Perform the transfer between users (revoke to user and award to recipient)
            gamipress_revoke_rank_to_user( $transfer_item->post_id, $user_id );

            // Setup vars for the transfer note
            $rank_type = $rank_types[$transfer_item->post_type];
            $rank_title = get_post_field( 'post_title', $transfer_item->post_id );
            $user = get_userdata( $user_id );

            // Add an informative note to let user know that points has been transferred
            gamipress_transfers_insert_transfer_note( $transfer->transfer_id,
                sprintf( __( '%s revoked', 'gamipress-transfers' ), $rank_type['singular_name'] ),
                sprintf( __( '%s %s has been revoked from %s account', 'gamipress-transfers' ),
                    // Achievement Achievement_Title
                    $rank_type['singular_name'], $rank_title,
                    // User link
                    $user->display_name . ' (' . $user->user_email . ')',
                )
            );

        }

    }

}

/**
 * Award the transfer items to the recipient
 *
 * @since  1.0.0
 *
 * @param object    $transfer        The transfer object
 */
function gamipress_transfers_award_items_to_recipient( $transfer ) {

    $user_id = absint( $transfer->user_id );

    // Guest not supported yet
    if( $user_id === 0 ) {
        return;
    }

    $recipient_id = absint( $transfer->recipient_id );

    // Guest not supported yet
    if( $recipient_id === 0 ) {
        return;
    }

    // Get our types
    $points_types = gamipress_get_points_types();
    $points_types_slugs = gamipress_get_points_types_slugs();
    $achievement_types = gamipress_get_achievement_types();
    $achievement_types_slugs = gamipress_get_achievement_types_slugs();
    $rank_types = gamipress_get_rank_types();
    $rank_types_slugs = gamipress_get_rank_types_slugs();

    $transfer_items = gamipress_transfers_get_transfer_items( $transfer->transfer_id );

    // Loop all items to check item types assigned
    foreach( $transfer_items as $transfer_item ) {

        // Skip if not item assigned
        if( absint( $transfer_item->post_id ) === 0 ) {
            continue;
        }

        $post_type = gamipress_get_post_type( $transfer_item->post_id );

        // Skip if can not get the type of this item
        if( ! $post_type ) {
            continue;
        }

        // Setup table on each loop for the usage of ct_get_object_meta() and ct_update_object_meta()
        ct_setup_table( 'gamipress_transfer_items' );

        $awarded = (bool) ct_get_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_transferred', true );

        // Skip already awarded items
        if( $awarded ) {
            continue;
        }

        if( $post_type === 'points-type' && in_array( $transfer_item->post_type, $points_types_slugs ) ) {
            // Is a points

            // Add a mark to meet that this transfer item has been awarded
            ct_update_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_transferred', '1' );

            $quantity = absint( $transfer_item->quantity );

            // Perform the transfer between users (deduct to user and award to recipient)
            gamipress_award_points_to_user( $recipient_id, $quantity, $transfer_item->post_type );

            // Setup vars for the transfer note
            $points_type = $points_types[$transfer_item->post_type];
            $user = get_userdata( $user_id );
            $recipient = get_userdata( $recipient_id );

            // Add an informative note to let user know that points has been transferred
            gamipress_transfers_insert_transfer_note( $transfer->transfer_id,
                sprintf( __( '%s transferred', 'gamipress-transfers' ), $points_type['plural_name'] ),
                sprintf( __( '%d %s has been transferred from %s account to %s account', 'gamipress-transfers' ),
                    // X points
                    $quantity, _n( $points_type['singular_name'], $points_type['plural_name'], $quantity ),
                    // User link
                    $user->display_name . ' (' . $user->user_email . ')',
                    // Recipient link
                    $recipient->display_name . ' (' . $recipient->user_email . ')'
                )
            );

        } else if( in_array( $post_type, $achievement_types_slugs ) ) {
            // Is an achievement

            // Add a mark to meet that this transfer item has been awarded
            ct_update_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_transferred', '1' );

            // Perform the transfer between users (revoke to user and award to recipient)
            gamipress_award_achievement_to_user( $transfer_item->post_id, $recipient_id );

            // Setup vars for the transfer note
            $achievement_type = $achievement_types[$transfer_item->post_type];
            $achievement_title = get_post_field( 'post_title', $transfer_item->post_id );
            $user = get_userdata( $user_id );
            $recipient = get_userdata( $recipient_id );

            // Add an informative note to let user know that points has been transferred
            gamipress_transfers_insert_transfer_note( $transfer->transfer_id,
                sprintf( __( '%s transferred', 'gamipress-transfers' ), $achievement_type['singular_name'] ),
                sprintf( __( '%s %s has been transferred from %s account to %s account', 'gamipress-transfers' ),
                    // Achievement Achievement_Title
                    $achievement_type['singular_name'], $achievement_title,
                    // User link
                    $user->display_name . ' (' . $user->user_email . ')',
                    // Recipient link
                    $recipient->display_name . ' (' . $recipient->user_email . ')'
                )
            );

        } else if( in_array( $post_type, $rank_types_slugs ) ) {
            // Is a rank

            // Add a mark to meet that this transfer item has been awarded
            ct_update_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_transferred', '1' );

            // Perform the transfer between users (revoke to user and award to recipient)
            gamipress_award_rank_to_user( $transfer_item->post_id, $recipient_id );

            // Setup vars for the transfer note
            $rank_type = $rank_types[$transfer_item->post_type];
            $rank_title = get_post_field( 'post_title', $transfer_item->post_id );
            $user = get_userdata( $user_id );
            $recipient = get_userdata( $recipient_id );

            // Add an informative note to let user know that points has been transferred
            gamipress_transfers_insert_transfer_note( $transfer->transfer_id,
                sprintf( __( '%s transferred', 'gamipress-transfers' ), $rank_type['singular_name'] ),
                sprintf( __( '%s %s has been transferred from %s account to %s account', 'gamipress-transfers' ),
                    // Achievement Achievement_Title
                    $rank_type['singular_name'], $rank_title,
                    // User link
                    $user->display_name . ' (' . $user->user_email . ')',
                    // Recipient link
                    $recipient->display_name . ' (' . $recipient->user_email . ')'
                )
            );

        }

    }

}

/**
 * Revoke the transferred items to the user on refund
 *
 * @since  1.0.0
 *
 * @param object    $transfer        The transfer object
 */
function gamipress_transfers_restore_items_transferred( $transfer ) {

    $user_id = absint( $transfer->user_id );

    // Guest not supported yet
    if( $user_id === 0 ) {
        return;
    }

    $recipient_id = absint( $transfer->recipient_id );

    // Guest not supported yet
    if( $recipient_id === 0 ) {
        return;
    }

    // Get our types
    $points_types = gamipress_get_points_types();
    $points_types_slugs = gamipress_get_points_types_slugs();
    $achievement_types = gamipress_get_achievement_types();
    $achievement_types_slugs = gamipress_get_achievement_types_slugs();
    $rank_types = gamipress_get_rank_types();
    $rank_types_slugs = gamipress_get_rank_types_slugs();

    $transfer_items = gamipress_transfers_get_transfer_items( $transfer->transfer_id );

    // Loop all items to check item types assigned
    foreach( $transfer_items as $transfer_item ) {

        // Skip if not item assigned
        if( absint( $transfer_item->post_id ) === 0 ) {
            continue;
        }

        $post_type = gamipress_get_post_type( $transfer_item->post_id );

        // Skip if can not get the type of this item
        if( ! $post_type ) {
            continue;
        }

        // Setup table on each loop for the usage of ct_get_object_meta() and ct_update_object_meta()
        ct_setup_table( 'gamipress_transfer_items' );

        $revoked = (bool) ct_get_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_revoked', true );
        $awarded = (bool) ct_get_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_transferred', true );

        // Skip not revoked/awarded items
        if( ! $revoked && ! $awarded ) {
            continue;
        }

        if( $post_type === 'points-type' && in_array( $transfer_item->post_type, $points_types_slugs ) ) {
            // Is a points

            // Add a mark to meet that this transfer item has been revoked
            ct_update_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_revoked', '0' );
            ct_update_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_transferred', '0' );

            $quantity = absint( $transfer_item->quantity );

            // Perform the transfer refund between users (award to user and deduct to recipient)
            if( $revoked ) {
                gamipress_award_points_to_user( $user_id, $quantity, $transfer_item->post_type );
            }

            if( $awarded ) {
                gamipress_deduct_points_to_user( $recipient_id, $quantity, $transfer_item->post_type );
            }

            // Setup vars for the transfer note
            $points_type = $points_types[$transfer_item->post_type];
            $user = get_userdata( $user_id );
            $recipient = get_userdata( $recipient_id );

            // Add an informative note to let user know that points has been transferred
            gamipress_transfers_insert_transfer_note( $transfer->transfer_id,
                sprintf( __( '%s refunded', 'gamipress-transfers' ), $points_type['plural_name'] ),
                sprintf( __( '%d %s has been refunded to %s account from %s account', 'gamipress-transfers' ),
                    // X points
                    $quantity, _n( $points_type['singular_name'], $points_type['plural_name'], $quantity ),
                    // User link
                    $user->display_name . ' (' . $user->user_email . ')',
                    // Recipient link
                    $recipient->display_name . ' (' . $recipient->user_email . ')'
                )
            );

        } else if( in_array( $post_type, $achievement_types_slugs ) ) {
            // Is an achievement

            // Add a mark to meet that this transfer item has been awarded
            ct_update_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_revoked', '0' );
            ct_update_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_transferred', '0' );

            // Perform the transfer between users (award to user and revoke to recipient)
            if( $revoked ) {
                gamipress_award_achievement_to_user( $transfer_item->post_id, $user_id );
            }

            if( $awarded ) {
                gamipress_revoke_achievement_to_user( $transfer_item->post_id, $recipient_id );
            }

            // Setup vars for the transfer note
            $achievement_type = $achievement_types[$transfer_item->post_type];
            $achievement_title = get_post_field( 'post_title', $transfer_item->post_id );
            $user = get_userdata( $user_id );
            $recipient = get_userdata( $recipient_id );

            // Add an informative note to let user know that points has been transferred
            gamipress_transfers_insert_transfer_note( $transfer->transfer_id,
                sprintf( __( '%s refunded', 'gamipress-transfers' ), $achievement_type['singular_name'] ),
                sprintf( __( '%s %s has been refunded to %s account from %s account', 'gamipress-transfers' ),
                    // Achievement Achievement_Title
                    $achievement_type['singular_name'], $achievement_title,
                    // User link
                    $user->display_name . ' (' . $user->user_email . ')',
                    // Recipient link
                    $recipient->display_name . ' (' . $recipient->user_email . ')'
                )
            );

        } else if( in_array( $post_type, $rank_types_slugs ) ) {
            // Is a rank

            // Add a mark to meet that this transfer item has been awarded
            ct_update_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_revoked', '0' );
            ct_update_object_meta( $transfer_item->transfer_item_id, '_gamipress_transfers_transferred', '0' );

            // Perform the transfer between users (award to user and revoke to recipient)
            if( $revoked ) {
                gamipress_award_rank_to_user( $transfer_item->post_id, $user_id );
            }

            if( $awarded ) {
                gamipress_revoke_rank_to_user( $transfer_item->post_id, $recipient_id );
            }

            // Setup vars for the transfer note
            $rank_type = $rank_types[$transfer_item->post_type];
            $rank_title = get_post_field( 'post_title', $transfer_item->post_id );
            $user = get_userdata( $user_id );
            $recipient = get_userdata( $recipient_id );

            // Add an informative note to let user know that points has been transferred
            gamipress_transfers_insert_transfer_note( $transfer->transfer_id,
                sprintf( __( '%s refunded', 'gamipress-transfers' ), $rank_type['singular_name'] ),
                sprintf( __( '%s %s has been refunded to %s account from %s account', 'gamipress-transfers' ),
                    // Achievement Achievement_Title
                    $rank_type['singular_name'], $rank_title,
                    // User link
                    $user->display_name . ' (' . $user->user_email . ')',
                    // Recipient link
                    $recipient->display_name . ' (' . $recipient->user_email . ')'
                )
            );

        }

    }

}

/**
 * Handle the "Mark as complete" action through the transfer edit screen
 *
 * @since 1.0.0
 *
 * @param int $transfer_id The transfer ID
 */
function gamipress_transfers_process_complete_action( $transfer_id ) {

    ct_setup_table( 'gamipress_transfers' );
    $transfer = ct_get_object( $transfer_id );

    // Return if no transfer
    if( ! $transfer ) {
        return;
    }

    // Return if transfer is not pending or processing
    if( ! in_array( $transfer->status, array( 'pending', 'processing' ) ) ) {
        return;
    }

    // Update transfer status from processing or pending to complete
    gamipress_transfers_update_transfer_status( $transfer_id, 'complete' );

    $redirect = add_query_arg( array( 'message' => 'transfer_completed' ), ct_get_edit_link( 'gamipress_transfers', $transfer->transfer_id ) );

    $user = get_userdata( get_current_user_id() );

    // Add an informative note to let user know that points has been transferred
    gamipress_transfers_insert_transfer_note( $transfer->transfer_id,
        __( 'Transfer manually completed', 'gamipress-transfers' ),
        sprintf( __( '%s has marked as completed manually the transfer.', 'gamipress-transfers' ), $user->display_name . ' (' . $user->user_email . ')' )
    );

    /**
     * Action to meet when an user manually completed a transfer
     *
     * @since 1.0.0
     *
     * @param stdClass  $transfer       The transfer object
     * @param int       $transfer_id    The transfer ID
     * @param int       $user_id        The user ID that completed the transfer
     */
    do_action( 'gamipress_transfers_transfer_manually_completed', $transfer, $transfer_id, get_current_user_id() );

    // Redirect to the same transfer edit screen and with the var message
    wp_redirect( $redirect );
    exit;

}
add_action( 'gamipress_transfers_process_transfer_action_complete', 'gamipress_transfers_process_complete_action' );

/**
 * Handle the "Refund transfer" action through the transfer edit screen
 *
 * @since 1.0.0
 *
 * @param int $transfer_id The transfer ID
 */
function gamipress_transfers_process_refund_action( $transfer_id ) {

    ct_setup_table( 'gamipress_transfers' );
    $transfer = ct_get_object( $transfer_id );

    // Return if no transfer
    if( ! $transfer ) {
        return;
    }

    // Return if transfer is not complete
    if( $transfer->status !== 'complete' ) {
        return;
    }

    // Update transfer status from complete to refunded
    gamipress_transfers_update_transfer_status( $transfer_id, 'refunded' );

    $user = get_userdata( get_current_user_id() );

    // Add an informative note to let user know that points has been transferred
    gamipress_transfers_insert_transfer_note( $transfer->transfer_id,
        __( 'Transfer manually refunded', 'gamipress-transfers' ),
        sprintf( __( '%s has refunded manually the transfer.', 'gamipress-transfers' ), $user->display_name . ' (' . $user->user_email . ')' )
    );

    $redirect = add_query_arg( array( 'message' => 'transfer_refunded' ), ct_get_edit_link( 'gamipress_transfers', $transfer->transfer_id ) );

    /**
     * Action to meet when an user manually refunded a transfer
     *
     * @since 1.0.0
     *
     * @param stdClass  $transfer       The transfer object
     * @param int       $transfer_id    The transfer ID
     * @param int       $user_id        The user ID that refunded the transfer
     */
    do_action( 'gamipress_transfers_transfer_manually_refunded', $transfer, $transfer_id, get_current_user_id() );

    // Redirect to the same transfer edit screen and with the var message
    wp_redirect( $redirect );
    exit;

}
add_action( 'gamipress_transfers_process_transfer_action_refund', 'gamipress_transfers_process_refund_action' );

/**
 * Add transfers edit screen custom messages
 *
 * @since 1.0.0
 *
 * @param array $messages
 *
 * @return array
 */
function gamipress_transfers_transfer_updated_messages( $messages ) {

    $messages['transfer_completed'] = __( 'Transfer marked as paid successfully.', 'gamipress-transfers' );
    $messages['transfer_refunded'] = __( 'Transfer refunded successfully.', 'gamipress-transfers' );

    return $messages;
}
add_filter( 'ct_table_updated_messages', 'gamipress_transfers_transfer_updated_messages' );

/**
 * Get the transfer items
 *
 * @since  1.0.0
 *
 * @param integer $transfer_id   The transfer ID
 *
 * @return array                Array of transfer items
 */
function gamipress_transfers_get_transfer_items( $transfer_id, $output = OBJECT ) {

    $cache = gamipress_get_cache( 'gamipress_transfer_items', array() );

    // If result already cached, return it
    if( isset( $cache[$transfer_id] ) ) {
        return $cache[$transfer_id];
    }

    ct_setup_table( 'gamipress_transfer_items' );

    $ct_query = new CT_Query( array(
        'transfer_id' => $transfer_id,
        'order' => 'ASC'
    ) );

    $transfer_items = $ct_query->get_results();

    if( $output === ARRAY_N || $output === ARRAY_A ) {

        // Turn array of objects into an array of arrays
        foreach( $transfer_items as $transfer_item_index => $transfer_item ) {
            $transfer_items[$transfer_item_index] = (array) $transfer_item;
        }

    }

    ct_reset_setup_table();

    // Cache results for next time
    $cache[$transfer_id] = $transfer_items;

    gamipress_set_cache( 'gamipress_transfer_items', $cache );

    return $transfer_items;

}

/**
 * Inset a transfer note
 *
 * @since  1.0.0
 *
 * @param integer   $transfer_id    The transfer ID
 * @param string    $title          The transfer note title
 * @param string    $description    The transfer note description
 * @param integer   $user_id        The user ID (-1 = GamiPress BOT, 0 = Guest)
 *
 * @return bool|integer             The transfer note ID or false
 */
function gamipress_transfers_insert_transfer_note( $transfer_id, $title, $description, $user_id = -1 ) {

    $ct_table = ct_setup_table( 'gamipress_transfer_notes' );

    $return = $ct_table->db->insert( array(
        'transfer_id' => $transfer_id,
        'title' => $title,
        'description' => $description,
        'user_id' => $user_id,
        'date' => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
    ) );

    ct_reset_setup_table();

    return $return;

}

/**
 * Get the transfer notes
 *
 * @since  1.0.0
 *
 * @param integer $transfer_id  The transfer ID
 *
 * @return array                Array of transfer notes
 */
function gamipress_transfers_get_transfer_notes( $transfer_id, $output = OBJECT ) {

    ct_setup_table( 'gamipress_transfer_notes' );

    $ct_query = new CT_Query( array(
        'transfer_id' => $transfer_id,
        'order' => 'DESC'
    ) );

    $transfer_items = $ct_query->get_results();

    if( $output === ARRAY_N || $output === ARRAY_A ) {

        // Turn array of objects into an array of arrays
        foreach( $transfer_items as $transfer_item_index => $transfer_item ) {
            $transfer_items[$transfer_item_index] = (array) $transfer_item;
        }

    }

    ct_reset_setup_table();

    return $transfer_items;

}

/**
 * Get the transfer points transferred
 *
 * @since  1.0.0
 *
 * @param integer $transfer_id  The transfer ID
 *
 * @return integer              The full amount of points transferred
 */
function gamipress_transfers_get_transfer_points_amount( $transfer_id ) {

    $amount = 0;

    // Get our types
    $points_types = gamipress_get_points_types();
    $points_types_slugs = gamipress_get_points_types_slugs();

    $transfer_items = gamipress_transfers_get_transfer_items( $transfer_id );

    // Loop all items to check item types assigned
    foreach( $transfer_items as $transfer_item ) {

        // Skip if not item assigned
        if( absint( $transfer_item->post_id ) === 0 ) {
            continue;
        }

        $post_type = gamipress_get_post_type( $transfer_item->post_id );

        // Skip if can not get the type of this item
        if( ! $post_type ) {
            continue;
        }

        // Setup table on each loop for the usage of ct_get_object_meta() and ct_update_object_meta()
        ct_setup_table( 'gamipress_transfer_items' );

        if( $post_type === 'points-type' && in_array( $transfer_item->post_type, $points_types_slugs ) ) {
            // Is a points

            $amount += absint( $transfer_item->quantity );

        }

    }

    return $amount;

}

/**
 * Get the transfer items points types transferred
 *
 * @since  1.0.0
 *
 * @param integer $transfer_id   The transfer ID
 *
 * @return string|array          The points types of points transferred
 */
function gamipress_transfers_get_transfer_points_types( $transfer_id ) {

    $transfer_points_types = array();

    // Get our types
    $points_types = gamipress_get_points_types();
    $points_types_slugs = gamipress_get_points_types_slugs();

    $transfer_items = gamipress_transfers_get_transfer_items( $transfer_id );

    // Loop all items to check item types assigned
    foreach( $transfer_items as $transfer_item ) {

        // Skip if not item assigned
        if( absint( $transfer_item->post_id ) === 0 ) {
            continue;
        }

        $post_type = gamipress_get_post_type( $transfer_item->post_id );

        // Skip if can not get the type of this item
        if( ! $post_type ) {
            continue;
        }

        // Setup table on each loop for the usage of ct_get_object_meta() and ct_update_object_meta()
        ct_setup_table( 'gamipress_transfer_items' );

        if( $post_type === 'points-type' && in_array( $transfer_item->post_type, $points_types_slugs ) && ! in_array( $transfer_item->post_type, $transfer_points_types ) ) {
            // Is a points

            $transfer_points_types[] = $transfer_item->post_type;

        }

    }

    // Return string if just there is one points type
    if( count( $transfer_points_types ) === 1 ) {
        return $transfer_points_types[0];
    }

    return $transfer_points_types;

}

/**
 * Get the transfer id querying it by the given field and desired field value
 *
 * @since  1.0.0
 *
 * @param string $field   The field to query
 * @param string $value   The field value to filter
 *
 * @return integer        The transfer ID
 */
function gamipress_transfers_get_transfer_id_by( $field, $value ) {

    global $wpdb;

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_transfers' );

    $transfer_id = $wpdb->get_var( $wpdb->prepare( "SELECT {$ct_table->db->primary_key} FROM {$ct_table->db->table_name} WHERE {$field} = %s LIMIT 1", $value ) );

    ct_reset_setup_table();

    return absint( $transfer_id );

}

/**
 * Return all user transfers
 *
 * @since  1.0.0
 *
 * @param integer   $user_id
 * @param array     $query_args
 *
 * @return array
 */
function gamipress_transfers_get_user_transfers( $user_id = null, $query_args = array() ) {

    if( ! $user_id )
        $user_id = get_current_user_id();

    ct_setup_table( 'gamipress_transfers' );

    $query_args['user_id'] = $user_id;

    $ct_query = new CT_Query( $query_args );

    $results = $ct_query->get_results();

    ct_reset_setup_table();

    return $results;

}

/**
 * Return user transfers count
 *
 * @since  1.0.0
 *
 * @param integer $user_id
 *
 * @return integer
 */
function gamipress_transfers_get_user_transfers_count( $user_id = null ) {

    global $wpdb;

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_transfers' );

    $user_transfers = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*)
         FROM {$ct_table->db->table_name}
         WHERE user_id = %d",
        absint( $user_id )
    ) );

    ct_reset_setup_table();

    return absint( $user_transfers );

}

/**
 * Check if user has a transfer with a post ID already pending to transfer
 *
 * @since  1.0.0
 *
 * @param integer $user_id
 * @param integer $post_id
 *
 * @return integer|bool     If a pending transfer exists return the ID, if not, return false
 */
function gamipress_transfers_user_get_item_pending( $user_id, $post_id ) {

    global $wpdb;

    // Setup table
    $transfers_table = ct_setup_table( 'gamipress_transfers' );
    $transfer_items_table = ct_setup_table( 'gamipress_transfer_items' );

    $pending_transfer = $wpdb->get_var( $wpdb->prepare(
        "SELECT p.transfer_id
         FROM {$transfers_table->db->table_name} AS p
         INNER JOIN {$transfer_items_table->db->table_name} AS m
         ON ( p.transfer_id = m.transfer_id )
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

    return ( $pending_transfer ? $pending_transfer : false );

}

/**
 * Return the transfer history page link
 *
 * @since  1.0.0
 *
 * @return false|string
 */
function gamipress_transfers_get_transfer_history_link() {

    $transfer_history_page = gamipress_transfers_get_option( 'transfer_history_page', '' );

    // On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
    $blog_id = gamipress_switch_to_main_site_if_network_wide_active();

    $permalink = get_permalink( $transfer_history_page );

    // If switched to blog, return back to que current blog
    if( $blog_id !== get_current_blog_id() && is_multisite() ) {
        restore_current_blog();
    }

    return $permalink;

}

/**
 * Return the transfer details page link
 *
 * @since  1.0.0
 *
 * @param integer $transfer_id
 *
 * @return false|string
 */
function gamipress_transfers_get_transfer_details_link( $transfer_id ) {

    $permalink = gamipress_transfers_get_transfer_history_link();

    if( $permalink ) {
        $permalink = add_query_arg( 'transfer_id', $transfer_id, $permalink );
    }

    return $permalink;

}