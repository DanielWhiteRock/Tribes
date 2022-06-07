<?php
/**
 * Payments
 *
 * @package     GamiPress\Points_Purchases\Custom_Tables\Payments
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Parse query args for payments
 *
 * @since  1.0.0
 *
 * @param string $where
 * @param CT_Query $ct_query
 *
 * @return string
 */
function gamipress_purchases_payments_query_where( $where, $ct_query ) {

    global $ct_table;

    if( $ct_table->name !== 'gamipress_payments' ) {
        return $where;
    }

    $table_name = $ct_table->db->table_name;

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

    // Purchase Key
    if( isset( $ct_query->query_vars['purchase_key'] ) && absint( $ct_query->query_vars['purchase_key'] ) !== 0 ) {

        $purchase_key = $ct_query->query_vars['purchase_key'];

        if( is_array( $purchase_key ) ) {
            $purchase_key = implode( "', '", $purchase_key );

            $where .= " AND {$table_name}.purchase_key IN ( '{$purchase_key}' )";
        } else {
            $where .= " AND {$table_name}.purchase_key = '{$purchase_key}''";
        }
    }

    return $where;
}
add_filter( 'ct_query_where', 'gamipress_purchases_payments_query_where', 10, 2 );

/**
 * Define the search fields for payments
 *
 * @since 1.0.0
 *
 * @param array $search_fields
 *
 * @return array
 */
function gamipress_purchases_payments_search_fields( $search_fields ) {

    $search_fields[] = 'number';
    $search_fields[] = 'status';
    $search_fields[] = 'gateway';
    $search_fields[] = 'user_ip';
    $search_fields[] = 'first_name';
    $search_fields[] = 'last_name';
    $search_fields[] = 'email';

    return $search_fields;

}
add_filter( 'ct_query_gamipress_payments_search_fields', 'gamipress_purchases_payments_search_fields' );

/**
 * Columns for payments list view
 *
 * @since  1.0.0
 *
 * @param array $columns
 *
 * @return array
 */
function gamipress_purchases_manage_payments_columns( $columns = array() ) {

    $columns['order']   = __( 'Order', 'gamipress-purchases' );
    $columns['user']    = __( 'Customer', 'gamipress-purchases' );
    $columns['date']    = __( 'Date', 'gamipress-purchases' );
    $columns['status']  = __( 'Status', 'gamipress-purchases' );
    $columns['gateway'] = __( 'Payment Method', 'gamipress-purchases' );
    $columns['total']   = __( 'Total', 'gamipress-purchases' );

    return $columns;
}
add_filter( 'manage_gamipress_payments_columns', 'gamipress_purchases_manage_payments_columns' );

/**
 * Sortable columns for payments list view
 *
 * @since 1.0.0
 *
 * @param array $sortable_columns
 *
 * @return array
 */
function gamipress_purchases_manage_payments_sortable_columns( $sortable_columns ) {

    $sortable_columns['order']      = array( 'number', false );
    $sortable_columns['user']       = array( 'user_id', false );
    $sortable_columns['date']       = array( 'date', true );
    $sortable_columns['status']     = array( 'status', false );
    $sortable_columns['gateway']    = array( 'gateway', false );
    $sortable_columns['total']      = array( 'total', false );

    return $sortable_columns;

}
add_filter( 'manage_gamipress_payments_sortable_columns', 'gamipress_purchases_manage_payments_sortable_columns' );

/**
 * Columns rendering for payments list view
 *
 * @since  1.0.0
 *
 * @param string $column_name
 * @param integer $object_id
 */
function gamipress_purchases_manage_payments_custom_column(  $column_name, $object_id ) {

    // Setup vars
    $payment = ct_get_object( $object_id );

    switch( $column_name ) {
        case 'order':
            ?>

            <strong>
                <a href="<?php echo ct_get_edit_link( 'gamipress_payments', $payment->payment_id ); ?>">#<?php echo $payment->number . ' (ID:' . $payment->payment_id . ')'; ?></a>
            </strong>

            <?php
            break;
        case 'user':
            $user = get_userdata( $payment->user_id );

            if( $user ) :

                if( current_user_can('edit_users')) {
                    ?>

                    <a href="<?php echo get_edit_user_link( $payment->user_id ); ?>"><?php echo $user->display_name . ' (' . $user->user_email . ')'; ?></a>

                    <?php
                } else {
                    echo $user->display_name . ' (' . $user->user_email . ')';
                }

            endif;
            break;
        case 'date':
            ?>

            <abbr title="<?php echo date( 'Y/m/d g:i:s a', strtotime( $payment->date ) ); ?>"><?php echo date( 'Y/m/d', strtotime( $payment->date ) ); ?></abbr>

            <?php
            break;
        case 'status':
            $statuses = gamipress_purchases_get_payment_statuses(); ?>

            <span class="gamipress-purchases-status gamipress-purchases-status-<?php echo $payment->status; ?>"><?php echo ( isset( $statuses[$payment->status] ) ? $statuses[$payment->status] : $payment->status ); ?></span>

            <?php
            break;
        case 'gateway':
            $gateways = gamipress_purchases_get_gateways(); ?>

            <span class="gamipress-purchases-gateway gamipress-purchases-gateway-<?php echo $payment->gateway; ?>"><?php echo ( isset( $gateways[$payment->gateway] ) ? $gateways[$payment->gateway] : $payment->gateway ); ?></span>

            <?php
            break;
        case 'total':
            ?>

            <span class="gamipress-purchases-total"><?php echo gamipress_purchases_format_price( $payment->total ); ?></span>

            <?php
            break;
    }
}
add_action( 'manage_gamipress_payments_custom_column', 'gamipress_purchases_manage_payments_custom_column', 10, 2 );

/**
 * Turns array of date and time into a valid mysql date on update payment data
 *
 * @since 1.0.0
 *
 * @param array $object_data
 * @param array $original_object_data
 *
 * @return array
 */
function gamipress_purchases_insert_payment_data( $object_data, $original_object_data ) {

    global $ct_table;

    // If not is our payment, return
    if( $ct_table->name !== 'gamipress_payments' ) {
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
add_filter( 'ct_insert_object_data', 'gamipress_purchases_insert_payment_data', 10, 2 );

/**
 * Fire transition payment status hooks on save payment
 *
 * @since  1.0.0
 *
 * @param integer   $object_id
 * @param stdClass  $object_after
 * @param stdClass  $object_before
 */
function gamipress_purchases_on_save_payment( $object_id, $object_after, $object_before ) {

    // TODO: Since 1.3.6 $object_after was not properly setup, to remove in the future
    if( (bool) version_compare( GAMIPRESS_VER, '1.3.6', '<' ) ) {
        $object_after = ct_get_object( $object_id );
    }

    // If not is our payment, return
    if( ! ( property_exists( $object_after, 'payment_id' ) && property_exists( $object_after, 'purchase_key' ) ) ) {
        return;
    }

    // Fire transition payment status hooks
    gamipress_purchases_transition_payment_status( $object_after->status, $object_before->status, $object_after );

}
add_action( 'ct_object_updated', 'gamipress_purchases_on_save_payment', 10, 3 );

/**
 * Register custom payments meta boxes
 *
 * @since  1.0.0
 */
function gamipress_purchases_add_payments_meta_boxes() {

    add_meta_box( 'gamipress_payments_actions', __( 'Actions', 'gamipress-purchases' ), 'gamipress_payments_actions_meta_box', 'gamipress_payments', 'side', 'core' );
    remove_meta_box( 'submitdiv', 'gamipress_payments', 'side' );

}
add_action( 'add_meta_boxes', 'gamipress_purchases_add_payments_meta_boxes' );

/**
 * Payment actions meta box
 *
 * @since  1.0.0
 *
 * @param stdClass  $payment
 */
function gamipress_payments_actions_meta_box( $payment ) {

    $payment_actions = array();

    if( $payment->status === 'complete' ) {
        $payment_actions['resend_purchase_receipt'] = array(
            'label' => __( 'Resend Purchase Receipt' ),
            'icon' => 'dashicons-email-alt'
        );
    }

    $payment_actions = apply_filters( 'gamipress_purchases_payment_actions', $payment_actions, $payment );

    ?>
    <div class="submitbox" id="submitpost" style="margin: -6px -12px -12px;">

        <div id="minor-publishing">

            <div id="misc-publishing-actions payment-actions">

                <?php foreach( $payment_actions as $action => $payment_action ) :

                    // Setup action vars
                    if( isset( $payment_action['url'] ) && ! empty( $payment_action['url'] ) ) {
                        $url = $payment_action['url'];
                    } else {
                        $url = add_query_arg( array( 'gamipress_purchases_payment_action' => $action ) );
                    }

                    if( isset( $payment_action['target'] ) && ! empty( $payment_action['target'] ) ) {
                        $target = $payment_action['target'];
                    } else {
                        $target = '_self';
                    } ?>

                    <div class="misc-pub-section payment-action">

                        <?php if( isset( $payment_action['icon'] ) ) : ?><span class="dashicons <?php echo $payment_action['icon']; ?>"></span><?php endif; ?>

                        <a href="<?php echo $url; ?>" data-action="<?php echo $action; ?>" target="<?php echo $target; ?>">
                            <span class="action-label"><?php echo $payment_action['label']; ?></span>
                        </a>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

        <div id="major-publishing-actions">

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
 * Payment actions handler
 *
 * Fire hook gamipress_purchases_process_payment_action_{$action}
 *
 * @since 1.0.0
 */
function gamipress_purchases_handle_payment_actions() {

    if( isset( $_REQUEST['gamipress_purchases_payment_action'] ) && isset( $_REQUEST['payment_id'] ) ) {

        $action = $_REQUEST['gamipress_purchases_payment_action'];
        $payment_id = absint( $_REQUEST['payment_id'] );

        if( $payment_id !== 0 ) {

            /**
             * Hook gamipress_purchases_process_payment_action_{$action}
             *
             * @since 1.0.0
             *
             * @param integer $payment_id
             */
            do_action( "gamipress_purchases_process_payment_action_{$action}", $payment_id );

            // Redirect to the same URL but without the action var if action do not process a redirect
            wp_redirect( remove_query_arg( array( 'gamipress_purchases_payment_action' ) ) );
            exit;

        }

    }

}
add_action( 'admin_init', 'gamipress_purchases_handle_payment_actions' );

/**
 * Default data when creating a new item (similar to WP auto draft) see ct_insert_object()
 *
 * @since  1.0.0
 *
 * @param array $default_data
 *
 * @return array
 */
function gamipress_payments_default_data( $default_data = array() ) {

    $default_data['number'] = gamipress_purchases_get_payment_next_payment_number();
    $default_data['purchase_key'] = gamipress_purchases_generate_purchase_key();
    $default_data['status'] = 'processing';

    return $default_data;
}
add_filter( 'ct_gamipress_payments_default_data', 'gamipress_payments_default_data' );

/**
 * Register custom payments CMB2 meta boxes
 *
 * @since  1.0.0
 */
function gamipress_purchases_payments_meta_boxes( ) {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_purchases_';

    $countries_options = gamipress_purchases_get_countries();
    $countries_options = array_merge( array( '' => __( 'Choose a country', 'gamipress-purchases' ) ),  $countries_options );

    // Payment Data
    gamipress_add_meta_box(
        'gamipress-payment-data',
        __( 'Payment Data', 'gamipress-purchases' ),
        'gamipress_payments',
        array(
            'order_title' => array(
                'content_cb' => 'gamipress_purchases_payments_order_details',
                'type' 	=> 'html',
            ),

            // Order Details

            'order_details' => array(
                'name' 	=> __( 'Order Details', 'gamipress-purchases' ),
                'type' 	=> 'title',
                'before_row' => 'gamipress_purchases_payments_order_details_open_tag',
            ),
            'number' => array(
                'name' 	=> __( 'Order Number', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            'date' => array(
                'name' 	=> __( 'Order Date', 'gamipress-purchases' ),
                'type' 	=> 'text_datetime_timestamp',
            ),
            'status' => array(
                'name' 	=> __( 'Order Status', 'gamipress-purchases' ),
                'type' 	=> 'select',
                'options' => gamipress_purchases_get_payment_statuses()
            ),
            'gateway' => array(
                'name' 	=> __( 'Payment Method', 'gamipress-purchases' ),
                'type' 	=> 'select',
                'options' => gamipress_purchases_get_gateways()
            ),
            'purchase_key' => array(
                'name' 	=> __( 'Purchase Key', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            'transaction_id' => array(
                'name' 	=> __( 'Transaction ID', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            'user_ip' => array(
                'name' 	=> __( 'IP', 'gamipress-purchases' ),
                'type' 	=> 'text',
                'after_row' => 'gamipress_purchases_payments_close_tag',
            ),

            // User Details

            'user_details' => array(
                'name' 	=> __( 'User Details', 'gamipress-purchases' ),
                'type' 	=> 'title',
                'before_row' => 'gamipress_purchases_payments_user_details_open_tag',
            ),
            'user_id' => array(
                'name' 	=> __( 'User', 'gamipress-purchases' ),
                'type' 	=> 'advanced_select',
                'options_cb'  => 'gamipress_options_cb_users',
                'after_field' => 'gamipress_purchases_payments_after_user_id',
            ),
            'first_name' => array(
                'name' 	=> __( 'First Name', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            'last_name' => array(
                'name' 	=> __( 'Last Name', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            'email' => array(
                'name' 	=> __( 'Email', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            'address_1' => array(
                'name' 	=> __( 'Address Line 1', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            'address_2' => array(
                'name' 	=> __( 'Address Line 2', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            'city' => array(
                'name' 	=> __( 'City', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            'postcode' => array(
                'name' 	=> __( 'Postcode / ZIP', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            'country' => array(
                'name' 	=> __( 'Country', 'gamipress-purchases' ),
                'type' 	=> 'select',
                'options' => $countries_options,
            ),
            'state' => array(
                'name' 	=> __( 'State / County', 'gamipress-purchases' ),
                'type' 	=> 'text',
                'after_row' => 'gamipress_purchases_payments_close_tag',
            ),
        ),
        array(
            'priority' => 'core',
        )
    );

    // Payment items
    gamipress_add_meta_box(
        'gamipress-payment-items-data',
        __( 'Payment Items', 'gamipress-purchases' ),
        'gamipress_payments',
        array(
            'payment_items' => array(
                'type' 	=> 'group',
                'options'     => array(
                    'add_button'    => __( 'Add Item', 'gamipress-purchases' ),
                    'remove_button' => '<i class="dashicons dashicons-no-alt"></i>',
                ),
                'fields' => apply_filters( 'gamipress_payment_item_fields', array(
                    'description' => array(
                        'name' 	=> __( 'Description', 'gamipress-purchases' ),
                        'type' => 'text',
                        'after_field' => 'gamipress_purchases_payment_items_after_description',
                    ),
                    'quantity' => array(
                        'name' 	=> __( 'Quantity', 'gamipress-purchases' ),
                        'type' => 'text',
                        'attributes' => array(
                            'placeholder' => '0'
                        ),
                    ),
                    'price' => array(
                        'name' 	=> __( 'Price', 'gamipress-purchases' ),
                        'type' => 'text',
                        'attributes' => array(
                            'placeholder' => gamipress_purchases_format_amount( 0 )
                        ),
                    ),
                    'total' => array(
                        'name' 	=> __( 'Total', 'gamipress-purchases' ),
                        'type' => 'text',
                        'attributes' => array(
                            'placeholder' => gamipress_purchases_format_amount( 0 )
                        ),
                    ),

                    'payment_item_id' => array(
                        'type' => 'text',
                        'attributes' => array(
                            'type' => 'hidden'
                        ),
                    ),
                    'payment_id' => array(
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
                'before_group' => 'gamipress_purchases_payments_before_payment_items',
                'after_group' => 'gamipress_purchases_payments_after_payment_items'
            )
        ),
        array(
            'priority' => 'core',
        )
    );

    // Payment notes
    gamipress_add_meta_box(
        'gamipress-payment-notes-data',
        __( 'Payment Notes', 'gamipress-purchases' ),
        'gamipress_payments',
        array(
            'payment_notes' => array(
                'content_cb' => 'gamipress_purchases_payment_notes_table',
                'type' 	=> 'html',
            )
        )
    );

}
add_action( 'cmb2_admin_init', 'gamipress_purchases_payments_meta_boxes' );

function gamipress_purchases_payments_order_details( $field, $object_id, $object_type ) {

    $ct_object = ct_get_object( $object_id );

    ?>
    <h2><?php echo sprintf( __( 'Order #%s details', 'gamipress-purchases' ), $ct_object->number ); ?></h2>
    <span><?php echo sprintf( __( 'Order ID: %s', 'gamipress-purchases' ), $ct_object->payment_id ); ?></span>
    <?php
}

function gamipress_purchases_payments_order_details_open_tag() {
    echo '<div class="gamipress-purchases-order-details">';
}

function gamipress_purchases_payments_user_details_open_tag() {
    echo '<div class="gamipress-purchases-user-details">';
}

function gamipress_purchases_payments_close_tag() {
    echo '</div>';
}

function gamipress_purchases_payments_after_user_id( $field_args, $field ) {
    ?>
    <a href="#" id="gamipress-purchases-load-user-billing-details"><?php _e( 'Load billing address', 'gamipress-purchases' ); ?></a>
    <?php
}

function gamipress_purchases_payment_items_after_description( $field_args, $field ) {
    $points_types = gamipress_get_points_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();

    $index = $field->group->index;
    $group_value = $field->group->value[$index];
    $post_id = absint( $group_value['post_id'] );
    $post_type = $group_value['post_type']; ?>

    <div class="gamipress-purchases-payment-items-assignment">
        <div class="gamipress-purchases-payment-items-assignment-text"></div>
        <div class="gamipress-purchases-payment-items-assignment-fields" style="display: none;">

            <select class="gamipress-purchases-payment-items-assignment-post-type">
                <?php if( ! empty( $points_types ) ) : ?>
                    <optgroup label="<?php echo __( 'Points Types', 'gamipress-purchases' ); ?>">
                        <?php foreach( $points_types as $slug => $data ) : ?>
                            <option value="<?php echo $slug; ?>"><?php echo $data['plural_name']; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

                <?php if( ! empty( $achievement_types ) ) : ?>
                    <optgroup label="<?php echo __( 'Achievement Types', 'gamipress-purchases' ); ?>">
                        <?php foreach( $achievement_types as $slug => $data ) : ?>
                            <option value="<?php echo $slug; ?>"><?php echo $data['singular_name']; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

                <?php if( ! empty( $rank_types ) ) : ?>
                    <optgroup label="<?php echo __( 'Rank Types', 'gamipress-purchases' ); ?>">
                        <?php foreach( $rank_types as $slug => $data ) : ?>
                            <option value="<?php echo $slug; ?>"><?php echo $data['singular_name']; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>
            </select>

            <span class="spinner" style="float: none;"></span>

            <select class="gamipress-purchases-payment-items-assignment-post-id" <?php if( $post_id === 0 || in_array( $post_type, array_keys( $points_types ) ) ) : ?>style="display: none;"<?php endif; ?>>
                <?php if( $post_id !== 0 ) : ?>
                    <option value="<?php echo $post_id; ?>"><?php echo get_post_field( 'post_title', $post_id ); ?></option>
                <?php endif; ?>
            </select>

            <div class="gamipress-purchases-payment-items-assignment-actions">
                <a href="#" class="save-assignment button" class="button"><?php _e( 'Save', 'gamipress-purchases' ); ?></a>
                <a href="#" class="cancel-assignment"><?php _e( 'Cancel', 'gamipress-purchases' ); ?></a>
            </div>
        </div>
    </div>

    <?php

}

function gamipress_purchases_payments_before_payment_items( $field_args, $field ) {
    ?>
    <div class="gamipress-purchases-payment-item-columns">
    <?php

    foreach( $field_args['fields'] as $group_field ) {

        if( $group_field['type'] === 'hidden' || empty( $group_field['name'] ) ) {
            continue;
        } ?>

        <div class="gamipress-purchases-payment-item-col gamipress-purchases-payment-item-col-<?php echo $group_field['id']; ?>"><?php echo $group_field['name']; ?></div>

        <?php
    }

    ?>
    </div>
    <?php

}

function gamipress_purchases_payments_after_payment_items( $field_args, $field ) {

    ct_setup_table('gamipress_payments');

    $payment = ct_get_object( $field->object_id ); ?>

    <div class="gamipress-purchases-payment-total-wrapper">
        <div class="gamipress-purchases-payment-subtotal">
            <div class="gamipress-purchases-payment-subtotal-label"><?php _e( 'Subtotal:', 'gamipress-purchases' ); ?></div>
            <div class="gamipress-purchases-payment-subtotal-amount"><?php echo gamipress_purchases_format_price( $payment->subtotal ); ?></div>

            <input type="hidden" id="subtotal" name="subtotal" value="<?php echo $payment->subtotal; ?>">
        </div>

        <div class="gamipress-purchases-payment-tax">
            <div class="gamipress-purchases-payment-tax-label"><?php _e( 'Tax:', 'gamipress-purchases' ); ?></div>
            <div class="gamipress-purchases-payment-tax-percent"><input type="text" id="tax" name="tax" value="<?php echo $payment->tax; ?>" />%</div>
            <div class="gamipress-purchases-payment-tax-amount"><?php echo gamipress_purchases_format_price( $payment->tax_amount ); ?></div>

            <input type="hidden" id="tax_amount" name="tax_amount" value="<?php echo $payment->tax_amount; ?>">
        </div>

        <div class="gamipress-purchases-payment-total">
            <div class="gamipress-purchases-payment-total-label"><?php _e( 'Total:', 'gamipress-purchases' ); ?></div>
            <div class="gamipress-purchases-payment-total-amount"><?php echo gamipress_purchases_format_price( $payment->total ); ?></div>

            <input type="hidden" id="total" name="total" value="<?php echo $payment->total; ?>">
        </div>
    </div>

    <?php
}

function gamipress_purchases_payment_items_field_value( $value, $object_id, $args, $field ) {

    global $ct_registered_tables, $ct_table, $ct_cmb2_override;

    $original_ct_table = $ct_table;

    if( $ct_cmb2_override !== true ) {
        return $value;
    }

    $payment_items = gamipress_purchases_get_payment_items( $object_id, ARRAY_N );

    $ct_table = $original_ct_table;

    return $payment_items;

}
add_filter( 'cmb2_override_payment_items_meta_value', 'gamipress_purchases_payment_items_field_value', 10, 4 );

function gamipress_purchases_payment_items_field_save( $check, $args, $field_args, $field ) {

    global $ct_registered_tables, $ct_table, $ct_cmb2_override;

    if( $ct_cmb2_override !== true ) {
        return $check;
    }

    $original_ct_table = $ct_table;
    $ct_table = ct_setup_table( 'gamipress_payment_items' );

    $payment_items = gamipress_purchases_get_payment_items( $args['id'], ARRAY_N );
    $received_items = $args['value'];

    foreach( $received_items as $item_index => $item_data ) {

        if( empty( $item_data['payment_item_id'] ) ) {

            // New payment item
            unset( $item_data['payment_item_id'] );

            $item_data['payment_id'] = $args['id'];


            $ct_table->db->insert( $item_data );

        } else {

            // Already existent item, so update
            $ct_table->db->update( $item_data, array(
                'payment_item_id' => $item_data['payment_item_id']
            ) );

        }

    }

    // Next, lets to check the removed items
    $payment_items_ids = array_map( function( $payment_item ) {
        return $payment_item['payment_item_id'];
    }, $payment_items );

    foreach( $received_items as $item_index => $item_data ) {

        if( empty( $item_data['payment_item_id'] ) ) {
            continue;
        }

        if( ! in_array( $item_data['payment_item_id'], $payment_items_ids ) ) {

            // Delete the item that has not been received
            $ct_table->db->delete( $item_data['payment_item_id'] );

        }
    }

    $ct_table = $original_ct_table;

    return true;

}
add_filter( 'cmb2_override_payment_items_meta_save', 'gamipress_purchases_payment_items_field_save', 10, 4 );

function gamipress_purchases_payment_notes_table( $field, $object_id, $object_type ) {

    ct_setup_table( 'gamipress_payments' );

    $payment = ct_get_object( $object_id );

    $payment_notes = gamipress_purchases_get_payment_notes( $object_id ); ?>

    <table class="widefat fixed striped comments wp-list-table comments-box payment-notes-list">

        <tbody id="the-comment-list" data-wp-lists="list:comment">

            <?php foreach( $payment_notes as $payment_note ) :

                gamipress_purchases_admin_render_payment_note( $payment_note, $payment );

            endforeach; ?>

        </tbody>

    </table>

    <div id="new-payment-note-form">
        <p class="hide-if-no-js">
            <a id="add-new-payment-note" class="button" href="#"><?php _e( 'Add Payment Note', 'gamipress-purchases' ) ?></a>
        </p>

        <fieldset id="new-payment-note-fieldset" style="display: none;">

            <div id="new-payment-note-title-wrap">
                <input type="text" id="payment-note-title" size="50" placeholder="<?php _e( 'Title', 'gamipress-purchases' ); ?>">
            </div>

            <div id="new-payment-note-description-wrap">
                <textarea id="payment-note-description" placeholder="<?php _e( 'Note', 'gamipress-purchases' ); ?>"></textarea>
            </div>

            <div id="new-payment-note-submit" class="new-payment-note-submit">
                <p>
                    <a href="#" id="save-payment-note" class="save button button-primary alignright"><?php _e( 'Add Payment Note', 'gamipress-purchases' ) ?></a>
                    <a href="#" id="cancel-payment-note" class="cancel button alignleft"><?php _e( 'Cancel', 'gamipress-purchases' ) ?></a>
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
 * Render the given payment note
 *
 * @since 1.0.0
 *
 * @param stdClass $payment_note
 * @param stdClass $payment
 */
function gamipress_purchases_admin_render_payment_note( $payment_note, $payment ) {

    if( $payment_note->user_id === '-1' ) {
        // -1 is used for system notes
        $user_name = __( 'GamiPress Bot', 'gamipress-purchases' );

    } else if( $payment_note->user_id === '0' ) {
        // Get the user details from the payment
        $user_name = $payment->first_name . ' ' . $payment->last_name;
        $user_email =  $payment->email;
    } else {
        // Get the user details from the user profile
        $user = new WP_User( $payment_note->user_id );

        $user_name = $user->display_name . ' (' .  $user->user_login .')';
        $user_email =$user->user_email;
    }

    ?>

    <tr id="payment-note-<?php echo $payment_note->payment_note_id ?>" class="comment payment-note byuser comment-author-admin depth-1 approved">
        <td class="author column-author">
            <strong><?php echo $user_name; ?></strong>
            <?php if( isset( $user_email ) ) : ?>
                <br>
                <a href="mailto:<?php echo $user_email; ?>"><?php echo $user_email; ?></a>
            <?php endif; ?>
        </td>
        <td class="comment column-comment has-row-actions column-primary">
            <p>
                <strong class="payment-note-title"><?php echo $payment_note->title; ?></strong>
                <span class="payment-note-date"><?php echo date( 'Y/m/d H:i', strtotime( $payment_note->date ) ); ?></span>
                <br>
                <span class="payment-note-description"><?php echo $payment_note->description; ?></span>
            </p>

            <div class="row-actions">
                <span class="trash"><a href="#" class="delete vim-d vim-destructive" data-payment-note-id="<?php echo $payment_note->payment_note_id; ?>" aria-label="<?php _e( 'Delete this payment note', 'gamipress-purchases' ); ?>"><?php _e( 'Delete', 'gamipress-purchases' ); ?></a></span>
            </div>
        </td>
    </tr>

    <?php
}