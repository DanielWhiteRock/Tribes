<?php
/**
 * Points Payouts
 *
 * @package     GamiPress\Points_Payouts\Custom_Tables\Points_Payouts
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Parse query args for points payouts
 *
 * @since  1.0.0
 *
 * @param string $where
 * @param CT_Query $ct_query
 *
 * @return string
 */
function gamipress_points_payouts_points_payouts_query_where( $where, $ct_query ) {

    global $ct_table;

    if( $ct_table->name !== 'gamipress_points_payouts' )
        return $where;

    $table_name = $ct_table->db->table_name;
    $user_id = false;

    // User ID
    if( isset( $ct_query->query_vars['user_id'] ) && absint( $ct_query->query_vars['user_id'] ) !== 0 ) {

        $user_id = $ct_query->query_vars['user_id'];

        if( is_array( $user_id ) ) {
            $user_id = implode( ", ", $user_id );

            $where .= " AND {$table_name}.user_id IN ( {$user_id} )";
        } else {
            $where .= " AND {$table_name}.user_id = {$user_id}";
        }
    }

    return $where;
}
add_filter( 'ct_query_where', 'gamipress_points_payouts_points_payouts_query_where', 10, 2 );

/**
 * Columns for points payouts list view
 *
 * @since  1.0.0
 *
 * @param array $columns
 *
 * @return array
 */
function gamipress_points_payouts_manage_points_payouts_columns( $columns = array() ) {

    $columns['points_payout']   = __( 'Points Payout', 'gamipress-points-payouts' );
    $columns['user']            = __( 'User', 'gamipress-points-payouts' );
    $columns['points']          = __( 'Points', 'gamipress-points-payouts' );
    $columns['money']           = __( 'Money', 'gamipress-points-payouts' );
    $columns['payment_method']  = gamipress_points_payouts_get_option( 'payment_method_text', __( 'Payment Method', 'gamipress-points-payouts' ) );
    $columns['status']          = __( 'Status', 'gamipress-points-payouts' );
    $columns['date']            = __( 'Date', 'gamipress-points-payouts' );
    $columns['actions']         = __( 'Actions', 'gamipress-points-payouts' );

    return $columns;
}
add_filter( 'manage_gamipress_points_payouts_columns', 'gamipress_points_payouts_manage_points_payouts_columns' );

/**
 * Sortable columns for points payouts list view
 *
 * @since 1.0.0
 *
 * @param array $sortable_columns
 *
 * @return array
 */
function gamipress_points_payouts_manage_points_payouts_sortable_columns( $sortable_columns ) {

    $sortable_columns['points_payout']  = array( 'points_payout_id', false );
    $sortable_columns['user']           = array( 'user_id', false );
    $sortable_columns['points']         = array( 'points', false );
    $sortable_columns['money']          = array( 'money', false );
    $sortable_columns['status']         = array( 'status', false );
    $sortable_columns['date']           = array( 'date', true );

    return $sortable_columns;

}
add_filter( 'manage_gamipress_points_payouts_sortable_columns', 'gamipress_points_payouts_manage_points_payouts_sortable_columns' );

/**
 * Columns rendering for points payouts list view
 *
 * @since  1.0.0
 *
 * @param string $column_name
 * @param integer $object_id
 */
function gamipress_points_payouts_manage_points_payouts_custom_column(  $column_name, $object_id ) {

    // Setup vars
    $points_payout = ct_get_object( $object_id );

    switch( $column_name ) {
        case 'points_payout':
            ?>

            <strong>
                <a href="<?php echo ct_get_edit_link( 'gamipress_points_payouts', $points_payout->points_payout_id ); ?>">#<?php echo $points_payout->points_payout_id; ?></a>
            </strong>

            <?php
            break;
        case 'user':
            $user = get_userdata( $points_payout->user_id );

            if( $user ) :

                if( current_user_can('edit_users')) {
                    ?>

                    <strong><a href="<?php echo get_edit_user_link( $points_payout->user_id ); ?>"><?php echo $user->display_name; ?></a></strong>
                    <br>
                    <?php echo $user->user_email; ?>

                    <?php
                } else {
                    echo $user->display_name . '<br>' . $user->user_email;
                }

            endif;
            break;
        case 'points':
            echo gamipress_format_points( $points_payout->points, $points_payout->points_type );
            break;
        case 'money':
            echo gamipress_points_payouts_format_price( $points_payout->money );
            break;
        case 'payment_method':
            echo gamipress_get_user_meta( $points_payout->user_id, '_gamipress_points_payouts_payment_method', true );
            break;
        case 'status':
            $statuses = gamipress_points_payouts_get_points_payout_statuses(); ?>

            <span class="gamipress-points-payouts-status gamipress-points-payouts-status-<?php echo $points_payout->status; ?>"><?php echo ( isset( $statuses[$points_payout->status] ) ? $statuses[$points_payout->status] : $points_payout->status ); ?></span>

            <?php
            break;
        case 'date':
            ?>

            <abbr title="<?php echo date( 'Y/m/d g:i:s a', strtotime( $points_payout->date ) ); ?>"><?php echo date( 'Y/m/d', strtotime( $points_payout->date ) ); ?></abbr>

            <?php
            break;
        case 'actions':
            $url = add_query_arg( array( 'points_payout_id' => $object_id ) );

            if( $points_payout->status === 'pending' ) :
                $pay_url = add_query_arg( array( 'gamipress_points_payouts_action' => 'pay' ), $url );
                $reject_url = add_query_arg( array( 'gamipress_points_payouts_action' => 'reject' ), $url ); ?>

                <a href="<?php echo $pay_url; ?>"><?php _e( 'Mark as paid', 'gamipress-points-payouts' ); ?></a>
                |
                <a href="<?php echo $reject_url; ?>" style="color:#a00;"><?php _e( 'Reject', 'gamipress-points-payouts' ); ?></a>

            <?php elseif ( $points_payout->status === 'paid' ) :
                $refund_url = add_query_arg( array( 'gamipress_points_payouts_action' => 'refund' ), $url ); ?>

                <a href="<?php echo $refund_url; ?>"><?php _e( 'Refund', 'gamipress-points-payouts' ); ?></a>

            <?php endif;
            break;
    }
}
add_action( 'manage_gamipress_points_payouts_custom_column', 'gamipress_points_payouts_manage_points_payouts_custom_column', 10, 2 );

/**
 * Points payout actions handler
 *
 * Fire hook gamipress_points_payouts_process_points_payout_action_{$action}
 *
 * @since 1.0.0
 */
function gamipress_points_payouts_handle_points_payout_actions() {

    if( isset( $_REQUEST['gamipress_points_payouts_action'] ) && isset( $_REQUEST['points_payout_id'] ) ) {

        $action = $_REQUEST['gamipress_points_payouts_action'];
        $points_payout_id = absint( $_REQUEST['points_payout_id'] );

        if( $points_payout_id !== 0 ) {

            /**
             * Hook gamipress_points_payouts_process_points_payout_action_{$action}
             *
             * @since 1.0.0
             *
             * @param integer $points_payout_id
             */
            do_action( "gamipress_points_payouts_process_points_payout_action_{$action}", $points_payout_id );

            // Redirect to the same URL but without the action var if action do not process a redirect
            wp_redirect( remove_query_arg( array( 'gamipress_points_payouts_action' ) ) );
            exit;

        }

    }

}
add_action( 'admin_init', 'gamipress_points_payouts_handle_points_payout_actions' );

/**
 * Pay points payout action
 *
 * @since 1.0.0
 *
 * @param integer $points_payout_id
 */
function gamipress_points_payouts_process_pay_action( $points_payout_id ) {

    // Setup the CT Table
    $ct_table = ct_setup_table( 'gamipress_points_payouts' );

    // Check the object
    $points_payout = ct_get_object( $points_payout_id );

    if( ! $points_payout ) {
        return;
    }

    // Only can pay pending payouts
    if( $points_payout->status !== 'pending' ) {
        return;
    }

    // Update the points payout status
    $ct_table->db->update(
        array( 'status' => 'paid' ),
        array( 'points_payout_id' => $points_payout_id )
    );

    $redirect = add_query_arg( array( 'message' => 'points_payout_paid' ) );

    // Redirect to the same points payout edit screen and with the var message
    wp_redirect( $redirect );
    exit;

}
add_action( 'gamipress_points_payouts_process_points_payout_action_pay', 'gamipress_points_payouts_process_pay_action' );

/**
 * Reject points payout action
 *
 * @since 1.0.0
 *
 * @param integer $points_payout_id
 */
function gamipress_points_payouts_process_reject_action( $points_payout_id ) {

    // Setup the CT Table
    $ct_table = ct_setup_table( 'gamipress_points_payouts' );

    // Check the object
    $points_payout = ct_get_object( $points_payout_id );

    if( ! $points_payout ) {
        return;
    }

    // Only can reject pending payouts
    if( $points_payout->status !== 'pending' ) {
        return;
    }

    // Restore user points
    gamipress_award_points_to_user( $points_payout->user_id, $points_payout->points, $points_payout->points_type, array(
        'reason' =>__( '{points} {points_type} refunded to {user} since withdrawal has been rejected', 'gamipress-points-payouts' )
    ) );

    // Update the points payout status
    $ct_table->db->update(
        array( 'status' => 'rejected' ),
        array( 'points_payout_id' => $points_payout_id )
    );

    $redirect = add_query_arg( array( 'message' => 'points_payout_rejected' ) );

    // Redirect to the same points payout edit screen and with the var message
    wp_redirect( $redirect );
    exit;

}
add_action( 'gamipress_points_payouts_process_points_payout_action_reject', 'gamipress_points_payouts_process_reject_action' );

/**
 * Refund points payout action
 *
 * @since 1.0.0
 *
 * @param integer $points_payout_id
 */
function gamipress_points_payouts_process_refund_action( $points_payout_id ) {

    // Setup the CT Table
    $ct_table = ct_setup_table( 'gamipress_points_payouts' );

    // Check the object
    $points_payout = ct_get_object( $points_payout_id );

    if( ! $points_payout ) {
        return;
    }

    // Only can refund paid payouts
    if( $points_payout->status !== 'paid' ) {
        return;
    }

    // Restore user points
    gamipress_award_points_to_user( $points_payout->user_id, $points_payout->points, $points_payout->points_type, array(
        'reason' =>__( '{points} {points_type} refunded to {user} since withdrawal has been refunded', 'gamipress-points-payouts' )
    ) );

    // Update the points payout status
    $ct_table->db->update(
        array( 'status' => 'refunded' ),
        array( 'points_payout_id' => $points_payout_id )
    );

    $redirect = add_query_arg( array( 'message' => 'points_payout_refunded' ) );

    // Redirect to the same points payout edit screen and with the var message
    wp_redirect( $redirect );
    exit;

}
add_action( 'gamipress_points_payouts_process_points_payout_action_refund', 'gamipress_points_payouts_process_refund_action' );

/**
 * Add points payouts edit screen custom messages
 *
 * @since 1.0.0
 *
 * @param array $messages
 *
 * @return array
 */
function gamipress_points_payouts_points_payout_updated_messages( $messages ) {

    $messages['points_payout_paid'] = __( 'Points payout marked as paid successfully.', 'gamipress-points-payouts' );
    $messages['points_payout_rejected'] = __( 'Points payout rejected successfully.', 'gamipress-points-payouts' );
    $messages['points_payout_refunded'] = __( 'Points payout refunded successfully.', 'gamipress-points-payouts' );

    return $messages;
}
add_filter( 'ct_table_updated_messages', 'gamipress_points_payouts_points_payout_updated_messages' );

/**
 * Register custom points payouts meta boxes
 *
 * @since  1.0.0
 */
function gamipress_points_payouts_add_points_payouts_meta_boxes() {

    add_meta_box( 'gamipress_points_payouts_details', __( 'Details', 'gamipress-points-payouts' ), 'gamipress_points_payouts_details_meta_box', 'gamipress_points_payouts', 'normal', 'core' );
    add_meta_box( 'gamipress_points_payouts_actions', __( 'Actions', 'gamipress-points-payouts' ), 'gamipress_points_payouts_actions_meta_box', 'gamipress_points_payouts', 'side', 'core' );
    remove_meta_box( 'submitdiv', 'gamipress_points_payouts', 'side' );

}
add_action( 'add_meta_boxes', 'gamipress_points_payouts_add_points_payouts_meta_boxes' );

/**
 * Points payout details meta box
 *
 * @since  1.0.0
 *
 * @param stdClass  $points_payout
 */
function gamipress_points_payouts_details_meta_box( $points_payout ) {

    ?>
    <table class="form-table">
        <tbody>

            <tr>
                <th><?php _e( 'Points payout', 'gamipress-points-payout' ); ?></th>
                <td><?php echo '#' . $points_payout->points_payout_id; ?></td>
            </tr>

            <tr>
                <th><?php _e( 'User', 'gamipress-points-payout' ); ?></th>
                <td><?php $user = get_userdata( $points_payout->user_id );

                    if( $user ) :

                        if( current_user_can('edit_users')) {
                            ?>

                            <strong><a href="<?php echo get_edit_user_link( $points_payout->user_id ); ?>"><?php echo $user->display_name; ?></a></strong>
                            <br>
                            <?php echo $user->user_email; ?>

                            <?php
                        } else {
                            echo $user->display_name . '<br>' . $user->user_email;
                        }

                    endif; ?></td>
            </tr>

            <tr>
                <th><?php _e( 'Points', 'gamipress-points-payout' ); ?></th>
                <td><?php echo gamipress_format_points( $points_payout->points, $points_payout->points_type ); ?></td>
            </tr>

            <tr>
                <th><?php _e( 'Money', 'gamipress-points-payout' ); ?></th>
                <td><?php echo gamipress_points_payouts_format_price( $points_payout->money ); ?></td>
            </tr>

            <tr>
                <th><?php echo gamipress_points_payouts_get_option( 'payment_method_text', __( 'Payment Method', 'gamipress-points-payouts' ) ); ?></th>
                <td><?php echo gamipress_get_user_meta( $points_payout->user_id, '_gamipress_points_payouts_payment_method', true ); ?></td>
            </tr>

            <tr>
                <th><?php _e( 'Status', 'gamipress-points-payout' ); ?></th>
                <td><?php $statuses = gamipress_points_payouts_get_points_payout_statuses(); ?>
                    <span class="gamipress-points-payouts-status gamipress-points-payouts-status-<?php echo $points_payout->status; ?>"><?php echo ( isset( $statuses[$points_payout->status] ) ? $statuses[$points_payout->status] : $points_payout->status ); ?></span>
                    <?php ?></td>
            </tr>

            <tr>
                <th><?php _e( 'Date', 'gamipress-points-payout' ); ?></th>
                <td><abbr title="<?php echo date( 'Y/m/d g:i:s a', strtotime( $points_payout->date ) ); ?>"><?php echo date( 'Y/m/d', strtotime( $points_payout->date ) ); ?></abbr></td>
            </tr>

        </tbody>
    </table>
    <?php

}

/**
 * Points payout actions meta box
 *
 * @since  1.0.0
 *
 * @param stdClass  $points_payout
 */
function gamipress_points_payouts_actions_meta_box( $points_payout ) {

    global $ct_table;

    $points_payout_actions = array();

    if( $points_payout->status === 'pending' ) {
        $points_payout_actions['pay'] = array(
            'label' => __( 'Mark as paid', 'gamipress-points-payouts' ),
            'icon' => 'dashicons-yes'
        );
        $points_payout_actions['reject'] = array(
            'label' => __( 'Reject', 'gamipress-points-payouts' ),
            'icon' => 'dashicons-no'
        );
    } else if( $points_payout->status === 'paid' ) {
        $points_payout_actions['refund'] = array(
            'label' => __( 'Refund', 'gamipress-points-payouts' ),
            'icon' => 'dashicons-undo'
        );
    }

    $points_payout_actions = apply_filters( 'gamipress_points_payouts_points_payout_actions', $points_payout_actions, $points_payout );

    ?>
    <div class="submitbox" id="submitpost" style="margin: -6px -12px -12px;">

        <div id="minor-publishing">

            <div id="misc-publishing-actions points-payouts-actions" style="padding: 5px 0;">

                <?php foreach( $points_payout_actions as $action => $points_payout_action ) :

                    // Setup action vars
                    if( isset( $points_payout_action['url'] ) && ! empty( $points_payout_action['url'] ) ) {
                        $url = $points_payout_action['url'];
                    } else {
                        $url = add_query_arg( array( 'gamipress_points_payouts_action' => $action ) );
                    }

                    if( isset( $points_payout_action['target'] ) && ! empty( $points_payout_action['target'] ) ) {
                        $target = $points_payout_action['target'];
                    } else {
                        $target = '_self';
                    } ?>

                    <div class="misc-pub-section points-payout-action">

                        <?php if( isset( $points_payout_action['icon'] ) ) : ?><span class="dashicons <?php echo $points_payout_action['icon']; ?>" style="color: #82878c;"></span><?php endif; ?>

                        <a href="<?php echo $url; ?>" data-action="<?php echo $action; ?>" target="<?php echo $target; ?>">
                            <span class="action-label"><?php echo $points_payout_action['label']; ?></span>
                        </a>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

        <div id="major-publishing-actions">

            <?php
            if ( current_user_can( $ct_table->cap->delete_item, $points_payout->points_payout_id ) ) {

                printf(
                    '<a href="%s" class="submitdelete deletion" onclick="%s" aria-label="%s">%s</a>',
                    ct_get_delete_link( $ct_table->name, $points_payout->points_payout_id ),
                    "return confirm('" .
                    esc_attr( __( "Are you sure you want to delete this item?\\n\\nClick \\'Cancel\\' to go back, \\'OK\\' to confirm the delete." ) ) .
                    "');",
                    esc_attr( __( 'Delete permanently' ) ),
                    __( 'Delete Permanently' )
                );

            } ?>

            <div class="clear"></div>

        </div>

    </div>
    <?php

}