<?php
/**
 * Purchase History template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/purchases/purchase-history.php
 */
if ( ! is_user_logged_in() ) {
    return;
}

// Setup vars
$user_id = get_current_user_id();
$items_per_page = 20;

$items_per_page = apply_filters( 'gamipress_purchases_purchase_history_items_per_page', $items_per_page, $user_id, $payments );

$query_args = array(
    'items_per_page' => $items_per_page,
    'paged' => max( 1, get_query_var( 'paged' ) )
);

$payments = gamipress_purchases_get_user_payments( $user_id, $query_args ); ?>

<?php if ( ! empty( $payments ) ) : ?>

    <?php
    /**
     * Before render user purchase history
     *
     * @param $user_id          integer     User ID
     * @param $payments         array       User payments
     */
    do_action( 'gamipress_purchases_before_purchase_history', $user_id, $payments ); ?>

    <?php
    $purchase_history_columns = array(
        'number' => __( 'Order Number', 'gamipress-purchases' ),
        'date' => __( 'Date', 'gamipress-purchases' ),
        'status' => __( 'Status', 'gamipress-purchases' ),
        'total' => __( 'Total', 'gamipress-purchases' ),
        'actions' => __( 'Actions', 'gamipress-purchases' ),
    );

    $purchase_history_columns = apply_filters( 'gamipress_purchases_purchase_history_columns', $purchase_history_columns, $user_id, $payments )
    ?>

    <table id="gamipress-purchases-purchase-history" class="gamipress-purchases-purchase-history">

        <thead>

            <tr>

                <?php foreach( $purchase_history_columns as $column_name => $column_label ) : ?>
                    <th class="gamipress-purchases-col gamipress-purchases-col-<?php echo $column_name; ?>"><?php echo $column_label; ?></th>
                <?php endforeach ?>

            </tr>

        </thead>
        <tbody>

        <?php foreach ( $payments as $payment ) : ?>

            <tr>

                <?php foreach( $purchase_history_columns as $column_name => $column_label ) : ?>

                    <?php
                    $column_output = '';

                    switch( $column_name ) {
                        case 'number':
                            $column_output = '#' . $payment->number;
                            break;
                        case 'date':
                            $column_output = date_i18n( get_option( 'date_format' ), strtotime( $payment->date ) );
                            break;
                        case 'status':
                            $statuses = gamipress_purchases_get_payment_statuses();
                            $column_output = '<span class="gamipress-purchases-status gamipress-purchases-status-' . $payment->status . '">' . ( isset( $statuses[$payment->status] ) ? $statuses[$payment->status] : $payment->status ) . '</span>';
                            break;
                        case 'total':
                            $column_output = gamipress_purchases_format_price( $payment->total );
                            break;
                        case 'actions':

                            $actions = array();

                            $actions[] = sprintf(
                                '<a href="%s" class="%s">%s</a>',
                                gamipress_purchases_get_purchase_details_link( $payment->payment_id ),
                                'gamipress-purchases-view-purchase-details',
                                __( 'View Order Details', 'gamipress-purchases' )
                            );

                            $actions = apply_filters( 'gamipress_purchases_purchase_history_actions', $actions, $user_id, $payment );

                            foreach( $actions as $action ) {
                                $column_output .= $action;
                            }
                            break;
                    }

                    $column_output = apply_filters( 'gamipress_purchases_purchase_history_render_column', $column_output, $column_name, $user_id, $payment )
                    ?>

                    <td class="gamipress-purchases-col gamipress-purchases-col-<?php echo $column_name; ?>"><?php echo $column_output; ?></td>
                <?php endforeach ?>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <div id="gamipress-purchases-purchase-history-pagination" class="gamipress-purchases-purchase-history-pagination navigation">
        <?php
        $big = 999999;
        echo paginate_links( array(
            'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'  => '?paged=%#%',
            'current' => max( 1, get_query_var( 'paged' ) ),
            'total'   => ceil( gamipress_purchases_get_user_payments_count( $user_id ) / $items_per_page )
        ) );
        ?>
    </div>

    <?php
    /**
     * After render user purchase history
     *
     * @param $user_id          integer     User ID
     * @param $payments         array       User payments
     */
    do_action( 'gamipress_purchases_after_purchase_history', $user_id, $payments ); ?>

<?php else : ?>
    <p class="gamipress-purchases-no-purchases"><?php _e('You have not made any purchases','gamipress-purchases' ); ?></p>
<?php endif;?>