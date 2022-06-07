<?php
/**
 * Purchase Details template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/purchases/purchase-details.php
 */
if ( ! is_user_logged_in() ) {
    return;
}

if( ! isset( $_GET['payment_id'] ) ) {
    return;
}

// Setup vars
$user_id = get_current_user_id();
$payment_id = $_GET['payment_id'];

ct_setup_table( 'gamipress_payments' );

$payment = ct_get_object( $payment_id );

if( absint( $payment->user_id ) !== absint( $user_id ) ) {
    return;
} ?>

<?php // Payment Details ?>

<?php
/**
 * Before render user purchase details
 *
 * @param $user_id          integer     User ID
 * @param $payment          stdClass    Payment Object
 * @param $payment_id       integer     Payment ID
 */
do_action( 'gamipress_purchases_before_purchase_details', $user_id, $payment, $payment_id ); ?>

<?php
$purchase_details_columns = array(
    'number' => __( 'Order Number', 'gamipress-purchases' ),
    'date' => __( 'Date', 'gamipress-purchases' ),
    'status' => __( 'Status', 'gamipress-purchases' ),
    'gateway' => __( 'Payment Method', 'gamipress-purchases' ),
);

if( (bool) gamipress_purchases_get_option( 'enable_taxes', false ) ) {
    $purchase_details_columns['subtotal'] = __( 'Subtotal', 'gamipress-purchases' );
    $purchase_details_columns['tax'] = __( 'Tax', 'gamipress-purchases' );
}

$purchase_details_columns['total'] = __( 'Total', 'gamipress-purchases' );

$purchase_details_columns = apply_filters( 'gamipress_purchases_purchase_details_columns', $purchase_details_columns, $user_id, $payment, $payment_id )
?>

<table id="gamipress-purchases-purchase-details" class="gamipress-purchases-purchase-details">

    <tbody>

            <?php foreach( $purchase_details_columns as $column_name => $column_label ) : ?>

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
                    case 'gateway':
                        $gateways = gamipress_purchases_get_gateways();
                        $column_output = '<span class="gamipress-purchases-gateway gamipress-purchases-gateway-' . $payment->gateway . '">' . ( isset( $gateways[$payment->gateway] ) ? $gateways[$payment->gateway] : $payment->gateway ) . '</span>';
                        break;
                    case 'subtotal':
                        $column_output = gamipress_purchases_format_price( $payment->subtotal );
                        break;
                    case 'tax':
                        $column_output = gamipress_purchases_format_price( $payment->tax_amount ) . ' (' . $payment->tax . '%)';
                        break;
                    case 'total':
                        $column_output = gamipress_purchases_format_price( $payment->total );
                        break;
                }

                $column_output = apply_filters( 'gamipress_purchases_purchase_details_render_column', $column_output, $column_name, $payment, $user_id )
                ?>
                <tr>
                    <th class="gamipress-purchases-col gamipress-purchases-col-<?php echo $column_name; ?>"><?php echo $column_label; ?></th>
                    <td class="gamipress-purchases-col gamipress-purchases-col-<?php echo $column_name; ?>"><?php echo $column_output; ?></td>
                </tr>
            <?php endforeach ?>

    </tbody>

</table>

<?php
/**
 * After render user purchase details
 *
 * @param $user_id          integer     User ID
 * @param $payment          stdClass    Payment Object
 * @param $payment_id       integer     Payment ID
 */
do_action( 'gamipress_purchases_after_purchase_details', $user_id, $payment, $payment_id ); ?>

<?php // Payment Items Details ?>

<h3><?php echo apply_filters( 'gamipress_purchases_purchase_items_details_title', __( 'Order Items', 'gamipress-purchases' ), $user_id, $payment, $payment_id ); ?></h3>

<?php $payment_items = gamipress_purchases_get_payment_items( $payment_id ); ?>

<?php if ( ! empty( $payment_items ) ) : ?>

    <?php
    /**
     * Before render user purchase details items
     *
     * @param $user_id          integer     User ID
     * @param $payment          stdClass    Payment Object
     * @param $payment_id       integer     Payment ID
     * @param $payment_items    array       Payment Items
     */
    do_action( 'gamipress_purchases_before_purchase_details_items', $user_id, $payment, $payment_id, $payment_items ); ?>

    <?php
    $purchase_details_items_columns = array(
        'description' => __( 'Description', 'gamipress-purchases' ),
        'quantity' => __( 'Quantity', 'gamipress-purchases' ),
        'price' => __( 'Price', 'gamipress-purchases' ),
        'total' => __( 'Total', 'gamipress-purchases' ),
    );

    $purchase_details_items_columns = apply_filters( 'gamipress_purchases_purchase_details_items_columns', $purchase_details_items_columns, $user_id, $payment, $payment_id, $payment_items )
    ?>

    <table id="gamipress-purchases-purchase-details-items" class="gamipress-purchases-purchase-details-items">

        <thead>

        <tr>

            <?php foreach( $purchase_details_items_columns as $column_name => $column_label ) : ?>
                <th class="gamipress-purchases-col gamipress-purchases-col-<?php echo $column_name; ?>"><?php echo $column_label; ?></th>
            <?php endforeach ?>

        </tr>

        </thead>
        <tbody>

        <?php foreach ( $payment_items as $payment_item ) : ?>

            <tr>

                <?php foreach( $purchase_details_items_columns as $column_name => $column_label ) : ?>

                    <?php
                    $column_output = '';

                    switch( $column_name ) {
                        case 'description':
                            $column_output = $payment_item->description;
                            break;
                        case 'quantity':
                            $column_output = $payment_item->quantity;
                            break;
                        case 'price':
                            $column_output = gamipress_purchases_format_price( $payment_item->price );
                            break;
                        case 'total':
                            $column_output = gamipress_purchases_format_price( $payment_item->total );
                            break;
                    }

                    $column_output = apply_filters( 'gamipress_purchases_purchase_details_items_render_column', $column_output, $column_name, $payment_item, $user_id, $payment, $payment_id )
                    ?>

                    <td class="gamipress-purchases-col gamipress-purchases-col-<?php echo $column_name; ?>"><?php echo $column_output; ?></td>
                <?php endforeach ?>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <?php
    /**
     * After render user purchase details items
     *
     * @param $user_id          integer     User ID
     * @param $payment          stdClass    Payment Object
     * @param $payment_items    array       Payment Items
     */
    do_action( 'gamipress_purchases_after_purchase_details_items', $user_id, $payment, $payment_id, $payment_items ); ?>

<?php else : ?>
    <p class="gamipress-purchases-no-purchase-items"><?php _e('This purchase have not items','gamipress-purchases' ); ?></p>
<?php endif;?>
