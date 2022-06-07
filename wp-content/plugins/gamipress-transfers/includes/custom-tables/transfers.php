<?php
/**
 * Transfers
 *
 * @package     GamiPress\Transfers\Custom_Tables\Transfers
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Parse query args for transfers
 *
 * @since  1.0.0
 *
 * @param string $where
 * @param CT_Query $ct_query
 *
 * @return string
 */
function gamipress_transfers_transfers_query_where( $where, $ct_query ) {

    global $ct_table;

    if( $ct_table->name !== 'gamipress_transfers' )
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

    // Recipient ID
    if( isset( $ct_query->query_vars['recipient_id'] ) && absint( $ct_query->query_vars['recipient_id'] ) !== 0 ) {

        $recipient_id = $ct_query->query_vars['recipient_id'];

        // Setup recipient ID operator
        $recipient_id_operator = 'AND';

        // Searching a recipient ID equal than user ID means that is looking for sent and received transfers (because an user can't transfer himself)
        // So, let's to force recipient operator to OR
        if( $user_id === $recipient_id )
            $recipient_id_operator = 'OR';

        if( is_array( $recipient_id ) ) {
            $recipient_id = implode( ", ", $recipient_id );

            $where .= " {$recipient_id_operator} {$table_name}.recipient_id IN ( {$recipient_id} )";
        } else {
            $where .= " {$recipient_id_operator} {$table_name}.recipient_id = {$recipient_id}";
        }
    }

    // Transfer Key
    if( isset( $ct_query->query_vars['transfer_key'] ) && absint( $ct_query->query_vars['transfer_key'] ) !== 0 ) {

        $transfer_key = $ct_query->query_vars['transfer_key'];

        if( is_array( $transfer_key ) ) {
            $transfer_key = implode( "', '", $transfer_key );

            $where .= " AND {$table_name}.transfer_key IN ( '{$transfer_key}' )";
        } else {
            $where .= " AND {$table_name}.transfer_key = '{$transfer_key}''";
        }
    }

    return $where;
}
add_filter( 'ct_query_where', 'gamipress_transfers_transfers_query_where', 10, 2 );

/**
 * Define the search fields for transfers
 *
 * @since 1.0.0
 *
 * @param array $search_fields
 *
 * @return array
 */
function gamipress_transfers_search_fields( $search_fields ) {

    $search_fields[] = 'number';
    $search_fields[] = 'status';
    $search_fields[] = 'transfer_key';
    $search_fields[] = 'user_ip';

    return $search_fields;

}
add_filter( 'ct_query_gamipress_transfers_search_fields', 'gamipress_transfers_search_fields' );

/**
 * Columns for transfers list view
 *
 * @since  1.0.0
 *
 * @param array $columns
 *
 * @return array
 */
function gamipress_transfers_manage_transfers_columns( $columns = array() ) {

    $columns['transfer']    = __( 'Transfer', 'gamipress-transfers' );
    $columns['user']        = __( 'From', 'gamipress-transfers' );
    $columns['recipient']   = __( 'To', 'gamipress-transfers' );
    $columns['date']        = __( 'Date', 'gamipress-transfers' );
    $columns['status']      = __( 'Status', 'gamipress-transfers' );

    return $columns;
}
add_filter( 'manage_gamipress_transfers_columns', 'gamipress_transfers_manage_transfers_columns' );

/**
 * Sortable columns for transfers list view
 *
 * @since 1.0.0
 *
 * @param array $sortable_columns
 *
 * @return array
 */
function gamipress_transfers_manage_transfers_sortable_columns( $sortable_columns ) {

    $sortable_columns['transfer']   = array( 'number', false );
    $sortable_columns['user']       = array( 'user_id', false );
    $sortable_columns['recipient']  = array( 'recipient_id', false );
    $sortable_columns['date']       = array( 'date', true );
    $sortable_columns['status']     = array( 'status', false );

    return $sortable_columns;

}
add_filter( 'manage_gamipress_transfers_sortable_columns', 'gamipress_transfers_manage_transfers_sortable_columns' );

/**
 * Columns rendering for transfers list view
 *
 * @since  1.0.0
 *
 * @param string $column_name
 * @param integer $object_id
 */
function gamipress_transfers_manage_transfers_custom_column(  $column_name, $object_id ) {

    // Setup vars
    $transfer = ct_get_object( $object_id );

    switch( $column_name ) {
        case 'transfer':
            ?>

            <strong>
                <a href="<?php echo ct_get_edit_link( 'gamipress_transfers', $transfer->transfer_id ); ?>">#<?php echo $transfer->number . ' (ID:' . $transfer->transfer_id . ')'; ?></a>
            </strong>

            <?php
            break;
        case 'user':
            $user = get_userdata( $transfer->user_id );

            if( $user ) :

                if( current_user_can('edit_users')) {
                    ?>

                    <strong><a href="<?php echo get_edit_user_link( $transfer->user_id ); ?>"><?php echo $user->display_name; ?></a></strong>
                    <br>
                    <?php echo $user->user_email; ?>

                    <?php
                } else {
                    echo $user->display_name . '<br>' . $user->user_email;
                }

            endif;
            break;
        case 'recipient':
            $recipient = get_userdata( $transfer->recipient_id );

            if( $recipient ) :

                if( current_user_can('edit_users')) {
                    ?>

                    <strong><a href="<?php echo get_edit_user_link( $transfer->recipient_id ); ?>"><?php echo $recipient->display_name; ?></a></strong>
                    <br>
                    <?php echo $recipient->user_email . ''; ?>

                    <?php
                } else {
                    echo $recipient->display_name . '<br>' . $recipient->user_email;
                }

            endif;
            break;
        case 'date':
            ?>

            <abbr title="<?php echo date( 'Y/m/d g:i:s a', strtotime( $transfer->date ) ); ?>"><?php echo date( 'Y/m/d', strtotime( $transfer->date ) ); ?></abbr>

            <?php
            break;
        case 'status':
            $statuses = gamipress_transfers_get_transfer_statuses(); ?>

            <span class="gamipress-transfers-status gamipress-transfers-status-<?php echo $transfer->status; ?>"><?php echo ( isset( $statuses[$transfer->status] ) ? $statuses[$transfer->status] : $transfer->status ); ?></span>

            <?php
            break;
    }
}
add_action( 'manage_gamipress_transfers_custom_column', 'gamipress_transfers_manage_transfers_custom_column', 10, 2 );

/**
 * Turns array of date and time into a valid mysql date on update transfer data
 *
 * @since 1.0.0
 *
 * @param array $object_data
 * @param array $original_object_data
 *
 * @return array
 */
function gamipress_transfers_insert_transfer_data( $object_data, $original_object_data ) {

    global $ct_table;

    // If not is our transfer, return
    if( $ct_table->name !== 'gamipress_transfers' ) {
        return $object_data;
    }

    // If not saved from edit screen, return
    if( ! is_array( $object_data['date'] ) ) {
        return $object_data;
    }

    // Build the full date received
    $full_date = $object_data['date']['date'] . ' ' . $object_data['date']['time'];

    // Turn it into a valid mysql date
    $object_data['date'] = date( 'Y-m-d H:i:s', CMB2_Utils::get_timestamp_from_value( $full_date, 'Y-m-d H:i:s' ) );

    return $object_data;

}
add_filter( 'ct_insert_object_data', 'gamipress_transfers_insert_transfer_data', 10, 2 );

/**
 * Fire transition transfer status hooks on save transfer
 *
 * @since  1.0.0
 *
 * @param integer   $object_id
 * @param stdClass  $object_after
 * @param stdClass  $object_before
 */
function gamipress_transfers_on_save_transfer( $object_id, $object_after, $object_before ) {

    // TODO: Since 1.3.6 $object_after was not properly setup, to remove in the future
    if( (bool) version_compare( GAMIPRESS_VER, '1.3.6', '<' ) ) {
        $object_after = ct_get_object( $object_id );
    }

    // If not is our transfer, return
    if( ! ( property_exists( $object_after, 'transfer_id' ) && property_exists( $object_after, 'transfer_key' ) ) ) {
        return;
    }

    // Fire transition transfer status hooks
    gamipress_transfers_transition_transfer_status( $object_after->status, $object_before->status, $object_after );

}
add_action( 'ct_object_updated', 'gamipress_transfers_on_save_transfer', 10, 3 );

/**
 * Register custom transfers meta boxes
 *
 * @since  1.0.0
 */
function gamipress_transfers_add_transfers_meta_boxes() {

    add_meta_box( 'gamipress_transfers_actions', __( 'Actions', 'gamipress-transfers' ), 'gamipress_transfers_actions_meta_box', 'gamipress_transfers', 'side', 'core' );
    remove_meta_box( 'submitdiv', 'gamipress_transfers', 'side' );

}
add_action( 'add_meta_boxes', 'gamipress_transfers_add_transfers_meta_boxes' );

/**
 * Transfer actions meta box
 *
 * @since  1.0.0
 *
 * @param stdClass  $transfer
 */
function gamipress_transfers_actions_meta_box( $transfer ) {

    global $ct_table;

    $transfer_actions = array();

    if( $transfer->status === 'complete' ) {
        $transfer_actions['refund'] = array(
            'label' => __( 'Refund transfer', 'gamipress-transfers' ),
            'icon' => 'dashicons-undo'
        );
    } else if( in_array( $transfer->status, array( 'pending', 'processing' ) ) ) {
        $transfer_actions['complete'] = array(
            'label' => __( 'Mark as complete', 'gamipress-transfers' ),
            'icon' => 'dashicons-yes'
        );
    }

    $transfer_actions = apply_filters( 'gamipress_transfers_transfer_actions', $transfer_actions, $transfer );

    ?>
    <div class="submitbox" id="submitpost" style="margin: -6px -12px -12px;">

        <div id="minor-publishing">

            <div id="misc-publishing-actions transfer-actions">

                <?php foreach( $transfer_actions as $action => $transfer_action ) :

                    // Setup action vars
                    if( isset( $transfer_action['url'] ) && ! empty( $transfer_action['url'] ) ) {
                        $url = $transfer_action['url'];
                    } else {
                        $url = add_query_arg( array( 'gamipress_transfers_transfer_action' => $action ) );
                    }

                    if( isset( $transfer_action['target'] ) && ! empty( $transfer_action['target'] ) ) {
                        $target = $transfer_action['target'];
                    } else {
                        $target = '_self';
                    } ?>

                    <div class="misc-pub-section transfer-action">

                        <?php if( isset( $transfer_action['icon'] ) ) : ?><span class="dashicons <?php echo $transfer_action['icon']; ?>"></span><?php endif; ?>

                        <a href="<?php echo $url; ?>" data-action="<?php echo $action; ?>" target="<?php echo $target; ?>">
                            <span class="action-label"><?php echo $transfer_action['label']; ?></span>
                        </a>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

        <div id="major-publishing-actions">

            <?php
            if ( current_user_can( $ct_table->cap->delete_item, $transfer->transfer_id ) ) {

                printf(
                    '<a href="%s" class="submitdelete deletion" onclick="%s" aria-label="%s">%s</a>',
                    ct_get_delete_link( $ct_table->name, $transfer->transfer_id ),
                    "return confirm('" .
                    esc_attr( __( "Are you sure you want to delete this item?\\n\\nClick \\'Cancel\\' to go back, \\'OK\\' to confirm the delete." ) ) .
                    "');",
                    esc_attr( __( 'Delete permanently' ) ),
                    __( 'Delete Permanently' )
                );

            } ?>

            <div id="publishing-action">
                <span class="spinner"></span>
                <?php submit_button( __( 'Save Changes' ), 'primary large', 'ct-save', false ); ?>
            </div>

            <div class="clear"></div>

        </div>

    </div>
    <?php
}

/**
 * Transfer actions handler
 *
 * Fire hook gamipress_transfers_process_transfer_action_{$action}
 *
 * @since 1.0.0
 */
function gamipress_transfers_handle_transfer_actions() {

    if( isset( $_REQUEST['gamipress_transfers_transfer_action'] ) && isset( $_REQUEST['transfer_id'] ) ) {

        $action = $_REQUEST['gamipress_transfers_transfer_action'];
        $transfer_id = absint( $_REQUEST['transfer_id'] );

        if( $transfer_id !== 0 ) {

            /**
             * Hook gamipress_transfers_process_transfer_action_{$action}
             *
             * @since 1.0.0
             *
             * @param integer $transfer_id
             */
            do_action( "gamipress_transfers_process_transfer_action_{$action}", $transfer_id );

            // Redirect to the same URL but without the action var if action do not process a redirect
            wp_redirect( remove_query_arg( array( 'gamipress_transfers_transfer_action' ) ) );
            exit;

        }

    }

}
add_action( 'admin_init', 'gamipress_transfers_handle_transfer_actions' );

/**
 * Default data when creating a new item (similar to WP auto draft) see ct_insert_object()
 *
 * @since  1.0.0
 *
 * @param array $default_data
 *
 * @return array
 */
function gamipress_transfers_default_data( $default_data = array() ) {

    $default_data['number'] = gamipress_transfers_get_transfer_next_transfer_number();
    $default_data['status'] = 'processing';

    return $default_data;
}
add_filter( 'ct_gamipress_transfers_default_data', 'gamipress_transfers_default_data' );

/**
 * Register custom transfers CMB2 meta boxes
 *
 * @since  1.0.0
 */
function gamipress_transfers_transfers_meta_boxes( ) {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_transfers_';

    // Transfer Data
    gamipress_add_meta_box(
        'gamipress-transfer-data',
        __( 'Transfer Data', 'gamipress-transfers' ),
        'gamipress_transfers',
        array(
            'transfer_title' => array(
                'content_cb' => 'gamipress_transfers_transfer_details',
                'type' 	=> 'html',
            ),

            // User Details

            'user_id' => array(
                'name' 	=> __( 'From', 'gamipress-transfers' ),
                'type' 	=> 'advanced_select',
                'options_cb'  => 'gamipress_options_cb_users',
                'before_field' => 'gamipress_transfers_transfers_before_user_id',
                'after_field' => 'gamipress_transfers_transfers_after_user_id',
            ),

            // Recipient Details

            'recipient_id' => array(
                'name' 	=> __( 'To', 'gamipress-transfers' ),
                'type' 	=> 'advanced_select',
                'options_cb'  => 'gamipress_options_cb_users',
                'before_field' => 'gamipress_transfers_transfers_before_recipient_id',
                'after_field' => 'gamipress_transfers_transfers_after_recipient_id',
            ),
        ),
        array(
            'priority' => 'core',
        )
    );

    // Transfer Details
    gamipress_add_meta_box(
        'gamipress-transfer-details',
        __( 'Transfer Details', 'gamipress-transfers' ),
        'gamipress_transfers',
        array(
            'number' => array(
                'name' 	=> __( 'Transfer Number', 'gamipress-transfers' ),
                'type' 	=> 'text',
            ),
            'date' => array(
                'name' 	=> __( 'Transfer Date', 'gamipress-transfers' ),
                'type' 	=> 'text_datetime_timestamp',
            ),
            'status' => array(
                'name' 	=> __( 'Transfer Status', 'gamipress-transfers' ),
                'type' 	=> 'select',
                'options' => gamipress_transfers_get_transfer_statuses()
            ),
            'transfer_key' => array(
                'name' 	=> __( 'Transfer Key', 'gamipress-transfers' ),
                'type' 	=> 'text',
            ),
            'user_ip' => array(
                'name' 	=> __( 'IP', 'gamipress-transfers' ),
                'type' 	=> 'text',
            ),
        ),
        array(
            'context' => 'side',
            'priority' => 'core',
        )
    );

    // Transfer items
    gamipress_add_meta_box(
        'gamipress-transfer-items-data',
        __( 'Transfer Items', 'gamipress-transfers' ),
        'gamipress_transfers',
        array(
            'transfer_items' => array(
                'type' 	=> 'group',
                'options'     => array(
                    'add_button'    => __( 'Add Item', 'gamipress-transfers' ),
                    'remove_button' => '<i class="dashicons dashicons-no-alt"></i>',
                ),
                'fields' => apply_filters( 'gamipress_transfer_item_fields', array(
                    'description' => array(
                        'name' 	=> __( 'Description', 'gamipress-transfers' ),
                        'type' => 'text',
                        'after_field' => 'gamipress_transfers_transfer_items_after_description',
                    ),
                    'quantity' => array(
                        'name' 	=> __( 'Quantity', 'gamipress-transfers' ),
                        'type' => 'text',
                        'attributes' => array(
                            'placeholder' => '0'
                        ),
                    ),

                    'transfer_item_id' => array(
                        'type' => 'text',
                        'attributes' => array(
                            'type' => 'hidden'
                        ),
                    ),
                    'transfer_id' => array(
                        'type' => 'text',
                        'attributes' => array(
                            'type' => 'hidden'
                        ),
                    ),
                    'post_id' => array(
                        'type' => 'text',
                        'attributes' => array(
                            'type' => 'hidden'
                        ),
                    ),
                    'post_type' => array(
                        'type' => 'text',
                        'attributes' => array(
                            'type' => 'hidden'
                        ),
                    ),
                ) ),
                'before_group' => 'gamipress_transfers_transfers_before_transfer_items',
            )
        ),
        array(
            'priority' => 'core',
        )
    );

    // Transfer notes
    gamipress_add_meta_box(
        'gamipress-transfer-notes-data',
        __( 'Transfer Notes', 'gamipress-transfers' ),
        'gamipress_transfers',
        array(
            'transfer_notes' => array(
                'content_cb' => 'gamipress_transfers_transfer_notes_table',
                'type' 	=> 'html',
            )
        )
    );

}
add_action( 'cmb2_admin_init', 'gamipress_transfers_transfers_meta_boxes' );

function gamipress_transfers_transfer_details( $field, $object_id, $object_type ) {

    $ct_object = ct_get_object( $object_id );

    ?>
    <h2><?php echo sprintf( __( 'Transfer #%s', 'gamipress-transfers' ), $ct_object->number ); ?></h2>
    <span><?php echo sprintf( __( 'Transfer ID: %s', 'gamipress-transfers' ), $ct_object->transfer_id ); ?></span>
    <?php
}

function gamipress_transfers_transfer_details_open_tag() {
    echo '<div class="gamipress-transfers-order-details">';
}

function gamipress_transfers_transfers_user_details_open_tag() {
    echo '<div class="gamipress-transfers-user-details">';
}

function gamipress_transfers_transfers_recipient_details_open_tag() {
    echo '<div class="gamipress-transfers-recipient-details">';
}

function gamipress_transfers_transfers_close_tag() {
    echo '</div>';
}

function gamipress_transfers_transfers_before_user_id( $field_args, $field ) {

    $ct_object = ct_get_object( $field->object_id );
    $user = get_userdata( $ct_object->user_id );

    $display_name = ( $user ? $user->display_name : '' );
    $user_email = ( $user ? $user->display_name : '' );

    ?>
    <h2 data-original="<?php echo $display_name; ?>"><?php echo $display_name; ?></h2>
    <div class="user-email" data-original="<?php echo $user_email; ?>"><?php echo $user_email; ?></div>
    <?php
}

function gamipress_transfers_transfers_after_user_id( $field_args, $field ) {

    $ct_object = ct_get_object( $field->object_id );

    ?>
    <div class="gamipress-transfer-user-select-actions" style="display: none;">
        <a href="#" class="save-user-select button" class="button"><?php _e( 'Change', 'gamipress-transfers' ); ?></a>
        <a href="#" class="cancel-user-select"><?php _e( 'Cancel', 'gamipress-transfers' ); ?></a>
    </div>
    <div class="gamipress-transfer-user-actions">
        <a href="<?php echo get_edit_user_link( $ct_object->user_id ); ?>" id="gamipress-transfers-view-user"><?php _e( 'View Profile', 'gamipress-transfers' ); ?></a>
        <span> | </span>
        <a href="#" id="gamipress-transfers-change-user"><?php _e( 'Change', 'gamipress-transfers' ); ?></a>
    </div>
    <?php
}

function gamipress_transfers_transfers_before_recipient_id( $field_args, $field ) {

    $ct_object = ct_get_object( $field->object_id );
    $user = get_userdata( $ct_object->recipient_id );

    $display_name = ( $user ? $user->display_name : '' );
    $user_email = ( $user ? $user->display_name : '' );

    ?>
    <h2 data-original="<?php echo $display_name; ?>"><?php echo $display_name; ?></h2>
    <div class="user-email" data-original="<?php echo $user_email; ?>"><?php echo $user_email; ?></div>
    <?php
}

function gamipress_transfers_transfers_after_recipient_id( $field_args, $field ) {

    $ct_object = ct_get_object( $field->object_id );

    ?>
    <div class="gamipress-transfer-recipient-select-actions" style="display: none;">
        <a href="#" class="save-user-select button" class="button"><?php _e( 'Change', 'gamipress-transfers' ); ?></a>
        <a href="#" class="cancel-user-select"><?php _e( 'Cancel', 'gamipress-transfers' ); ?></a>
    </div>
    <div class="gamipress-transfer-recipient-actions">
        <a href="<?php echo get_edit_user_link( $ct_object->recipient_id ); ?>" id="gamipress-transfers-view-recipient"><?php _e( 'View Profile', 'gamipress-transfers' ); ?></a>
        <span> | </span>
        <a href="#" id="gamipress-transfers-change-recipient"><?php _e( 'Change', 'gamipress-transfers' ); ?></a>
    </div>
    <?php
}

function gamipress_transfers_transfer_items_after_description( $field_args, $field ) {
    $points_types = gamipress_get_points_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();

    $index = $field->group->index;
    $group_value = array();

    if( isset( $field->group->value[$index] ) ) {
        $group_value = $field->group->value[$index];
    }

    $post_id = ( isset( $group_value['post_id'] ) ? absint( $group_value['post_id'] ) : 0 );
    $post_type = ( isset( $group_value['post_type'] ) ? $group_value['post_type'] : '' ); ?>

    <div class="gamipress-transfers-transfer-items-assignment">
        <div class="gamipress-transfers-transfer-items-assignment-text"></div>
        <div class="gamipress-transfers-transfer-items-assignment-fields" style="display: none;">

            <select class="gamipress-transfers-transfer-items-assignment-post-type">
                <?php if( ! empty( $points_types ) ) : ?>
                    <optgroup label="<?php echo __( 'Points Types', 'gamipress-transfers' ); ?>">
                        <?php foreach( $points_types as $slug => $data ) : ?>
                            <option value="<?php echo $slug; ?>"><?php echo $data['plural_name']; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

                <?php if( ! empty( $achievement_types ) ) : ?>
                    <optgroup label="<?php echo __( 'Achievement Types', 'gamipress-transfers' ); ?>">
                        <?php foreach( $achievement_types as $slug => $data ) : ?>
                            <option value="<?php echo $slug; ?>"><?php echo $data['singular_name']; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

                <?php if( ! empty( $rank_types ) ) : ?>
                    <optgroup label="<?php echo __( 'Rank Types', 'gamipress-transfers' ); ?>">
                        <?php foreach( $rank_types as $slug => $data ) : ?>
                            <option value="<?php echo $slug; ?>"><?php echo $data['singular_name']; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>
            </select>

            <span class="spinner" style="float: none;"></span>

            <select class="gamipress-transfers-transfer-items-assignment-post-id" <?php if( $post_id === 0 || in_array( $post_type, array_keys( $points_types ) ) ) : ?>style="display: none;"<?php endif; ?>>
                <?php if( $post_id !== 0 ) : ?>
                    <option value="<?php echo $post_id; ?>"><?php echo get_post_field( 'post_title', $post_id ); ?></option>
                <?php endif; ?>
            </select>

            <div class="gamipress-transfers-transfer-items-assignment-actions">
                <a href="#" class="save-assignment button" class="button"><?php _e( 'Save', 'gamipress-transfers' ); ?></a>
                <a href="#" class="cancel-assignment"><?php _e( 'Cancel', 'gamipress-transfers' ); ?></a>
            </div>
        </div>
    </div>

    <?php

}

function gamipress_transfers_transfers_before_transfer_items( $field_args, $field ) {
    ?>
    <div class="gamipress-transfers-transfer-item-columns">
        <?php

        foreach( $field_args['fields'] as $group_field ) {

            if( $group_field['type'] === 'hidden' || empty( $group_field['name'] ) ) {
                continue;
            } ?>

            <div class="gamipress-transfers-transfer-item-col gamipress-transfers-transfer-item-col-<?php echo $group_field['id']; ?>"><?php echo $group_field['name']; ?></div>

            <?php
        }

        ?>
    </div>
    <?php

}

function gamipress_transfers_transfer_items_field_value( $value, $object_id, $args, $field ) {

    global $ct_registered_tables, $ct_table, $ct_cmb2_override;

    $original_ct_table = $ct_table;

    if( $ct_cmb2_override !== true ) {
        return $value;
    }

    $transfer_items = gamipress_transfers_get_transfer_items( $object_id, ARRAY_N );

    $ct_table = $original_ct_table;

    return $transfer_items;

}
add_filter( 'cmb2_override_transfer_items_meta_value', 'gamipress_transfers_transfer_items_field_value', 10, 4 );

function gamipress_transfers_transfer_items_field_save( $check, $args, $field_args, $field ) {

    global $ct_registered_tables, $ct_table, $ct_cmb2_override;

    if( $ct_cmb2_override !== true ) {
        return $check;
    }

    $original_ct_table = $ct_table;
    $ct_table = ct_setup_table( 'gamipress_transfer_items' );

    $transfer_items = gamipress_transfers_get_transfer_items( $args['id'], ARRAY_N );
    $received_items = $args['value'];

    foreach( $received_items as $item_index => $item_data ) {

        if( empty( $item_data['transfer_item_id'] ) ) {

            // New transfer item
            unset( $item_data['transfer_item_id'] );

            $item_data['transfer_id'] = $args['id'];


            $ct_table->db->insert( $item_data );

        } else {

            // Already existent item, so update
            $ct_table->db->update( $item_data, array(
                'transfer_item_id' => $item_data['transfer_item_id']
            ) );

        }

    }

    // Next, lets to check the removed items
    $transfer_items_ids = array_map( function( $transfer_item ) {
        return $transfer_item['transfer_item_id'];
    }, $transfer_items );

    foreach( $received_items as $item_index => $item_data ) {

        if( empty( $item_data['transfer_item_id'] ) ) {
            continue;
        }

        if( ! in_array( $item_data['transfer_item_id'], $transfer_items_ids ) ) {

            // Delete the item that has not been received
            $ct_table->db->delete( $item_data['transfer_item_id'] );

        }
    }

    $ct_table = $original_ct_table;

    return true;

}
add_filter( 'cmb2_override_transfer_items_meta_save', 'gamipress_transfers_transfer_items_field_save', 10, 4 );

function gamipress_transfers_transfer_notes_table( $field, $object_id, $object_type ) {

    ct_setup_table( 'gamipress_transfers' );

    $transfer = ct_get_object( $object_id );

    $transfer_notes = gamipress_transfers_get_transfer_notes( $object_id ); ?>

    <table class="widefat fixed striped comments wp-list-table comments-box transfer-notes-list">

        <tbody id="the-comment-list" data-wp-lists="list:comment">

        <?php foreach( $transfer_notes as $transfer_note ) :

            gamipress_transfers_admin_render_transfer_note( $transfer_note, $transfer );

        endforeach; ?>

        </tbody>

    </table>

    <div id="new-transfer-note-form">
        <p class="hide-if-no-js">
            <a id="add-new-transfer-note" class="button" href="#"><?php _e( 'Add Transfer Note', 'gamipress-transfers' ) ?></a>
        </p>

        <fieldset id="new-transfer-note-fieldset" style="display: none;">

            <div id="new-transfer-note-title-wrap">
                <input type="text" id="transfer-note-title" size="50" placeholder="<?php _e( 'Title', 'gamipress-transfers' ); ?>">
            </div>

            <div id="new-transfer-note-description-wrap">
                <textarea id="transfer-note-description" placeholder="<?php _e( 'Note', 'gamipress-transfers' ); ?>"></textarea>
            </div>

            <div id="new-transfer-note-submit" class="new-transfer-note-submit">
                <p>
                    <a href="#" id="save-transfer-note" class="save button button-primary alignright"><?php _e( 'Add Transfer Note', 'gamipress-transfers' ) ?></a>
                    <a href="#" id="cancel-transfer-note" class="cancel button alignleft"><?php _e( 'Cancel', 'gamipress-transfers' ) ?></a>
                    <span class="waiting spinner"></span>
                </p>
                <br class="clear">
                <div class="notice notice-error notice-alt inline hidden">
                    <p class="error"></p>
                </div>
            </div>

        </fieldset>
    </div>

    <?php
}

/**
 * Render the given transfer note
 *
 * @since 1.0.0
 *
 * @param stdClass $transfer_note
 * @param stdClass $transfer
 */
function gamipress_transfers_admin_render_transfer_note( $transfer_note, $transfer ) {

    if( $transfer_note->user_id === '-1' ) {
        // -1 is used for system notes
        $user_name = __( 'GamiPress Bot', 'gamipress-transfers' );

    } else if( $transfer_note->user_id === '0' ) {
        // Get the user details from the transfer
        $user_name = $transfer->first_name . ' ' . $transfer->last_name;
        $user_email =  $transfer->email;
    } else {
        // Get the user details from the user profile
        $user = new WP_User( $transfer_note->user_id );

        $user_name = $user->display_name . ' (' .  $user->user_login .')';
        $user_email =$user->user_email;
    }

    ?>

    <tr id="transfer-note-<?php echo $transfer_note->transfer_note_id ?>" class="comment transfer-note byuser comment-author-admin depth-1 approved">
        <td class="author column-author">
            <strong><?php echo $user_name; ?></strong>
            <?php if( isset( $user_email ) ) : ?>
                <br>
                <a href="mailto:<?php echo $user_email; ?>"><?php echo $user_email; ?></a>
            <?php endif; ?>
        </td>
        <td class="comment column-comment has-row-actions column-primary">
            <p>
                <strong class="transfer-note-title"><?php echo $transfer_note->title; ?></strong>
                <span class="transfer-note-date"><?php echo date( 'Y/m/d H:i', strtotime( $transfer_note->date ) ); ?></span>
                <br>
                <span class="transfer-note-description"><?php echo $transfer_note->description; ?></span>
            </p>

            <div class="row-actions">
                <span class="trash"><a href="#" class="delete vim-d vim-destructive" data-transfer-note-id="<?php echo $transfer_note->transfer_note_id; ?>" aria-label="<?php _e( 'Delete this transfer note', 'gamipress-transfers' ); ?>"><?php _e( 'Delete', 'gamipress-transfers' ); ?></a></span>
            </div>
        </td>
    </tr>

    <?php
}