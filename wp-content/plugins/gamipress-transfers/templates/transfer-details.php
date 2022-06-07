<?php
/**
 * Transfer Details template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/transfers/transfer-details.php
 */
if ( ! is_user_logged_in() ) {
    return;
}

if( ! isset( $_GET['transfer_id'] ) ) {
    return;
}

// Setup vars
$user_id = get_current_user_id();
$transfer_id = $_GET['transfer_id'];

ct_setup_table( 'gamipress_transfers' );

$transfer = ct_get_object( $transfer_id );

if( ! in_array( $user_id, array( absint( $transfer->user_id ), absint( $transfer->recipient_id ) ) ) ) {
    return;
} ?>

<?php // Transfer Details ?>

<?php
/**
 * Before render user transfer details
 *
 * @since 1.0.0
 *
 * @param integer     $user_id      User ID
 * @param stdClass    $transfer     Transfer Object
 * @param integer     $transfer_id  Transfer ID
 */
do_action( 'gamipress_transfers_before_transfer_details', $user_id, $transfer, $transfer_id ); ?>

<?php
$transfer_details_columns = array(
    'number'    => __( 'Number', 'gamipress-transfers' ),
    'from'      => __( 'From', 'gamipress-transfers' ),
    'to'        => __( 'To', 'gamipress-transfers' ),
    'date'      => __( 'Date', 'gamipress-transfers' ),
    'status'    => __( 'Status', 'gamipress-transfers' ),
);

if( $user_id === absint( $transfer->user_id ) ) {
    unset( $transfer_details_columns['from'] );
}

if( $user_id === absint( $transfer->recipient_id ) ) {
    unset( $transfer_details_columns['to'] );
}

/**
 * Transfer details columns
 *
 * @since 1.0.0
 *
 * @param array         $columns        Columns to be rendered
 * @param integer       $user_id        User ID
 * @param stdClass      $transfer       Transfer object
 * @param integer       $transfer_id    Transfer ID
 */
$transfer_details_columns = apply_filters( 'gamipress_transfers_transfer_details_columns', $transfer_details_columns, $user_id, $transfer, $transfer_id )
?>

<table id="gamipress-transfers-transfer-details" class="gamipress-transfers-transfer-details">

    <tbody>

            <?php foreach( $transfer_details_columns as $column_name => $column_label ) : ?>

                <?php
                $column_output = '';

                switch( $column_name ) {
                    case 'number':
                        $column_output = '#' . $transfer->number;
                        break;
                    case 'from':
                        $user = get_userdata( $transfer->user_id );

                        if( $user ) {
                            $column_output = gamipress_transfers_display_recipient( $user );
                        }
                        break;
                    case 'to':
                        $recipient = get_userdata( $transfer->recipient_id );

                        if( $recipient ) {
                            $column_output = gamipress_transfers_display_recipient( $recipient );
                        }
                        break;
                    case 'date':
                        $column_output = date_i18n( get_option( 'date_format' ), strtotime( $transfer->date ) );
                        break;
                    case 'status':
                        $statuses = gamipress_transfers_get_transfer_statuses();
                        $column_output = '<span class="gamipress-transfers-status gamipress-transfers-status-' . $transfer->status . '">' . ( isset( $statuses[$transfer->status] ) ? $statuses[$transfer->status] : $transfer->status ) . '</span>';
                        break;
                }

                $column_output = apply_filters( 'gamipress_transfers_transfer_details_render_column', $column_output, $column_name, $transfer, $user_id )
                ?>
                <tr>
                    <th class="gamipress-transfers-col gamipress-transfers-col-<?php echo $column_name; ?>"><?php echo $column_label; ?></th>
                    <td class="gamipress-transfers-col gamipress-transfers-col-<?php echo $column_name; ?>"><?php echo $column_output; ?></td>
                </tr>
            <?php endforeach ?>

    </tbody>

</table>

<?php
/**
 * After render user transfer details
 *
 * @param integer     $user_id          User ID
 * @param stdClass    $transfer          Transfer Object
 * @param integer     $transfer_id       Transfer ID
 */
do_action( 'gamipress_transfers_after_transfer_details', $user_id, $transfer, $transfer_id ); ?>

<?php // Transfer Items Details ?>

<h3><?php echo apply_filters( 'gamipress_transfers_transfer_items_details_title', __( 'Transfer Items', 'gamipress-transfers' ), $user_id, $transfer, $transfer_id ); ?></h3>

<?php $transfer_items = gamipress_transfers_get_transfer_items( $transfer_id ); ?>

<?php if ( ! empty( $transfer_items ) ) : ?>

    <?php
    /**
     * Before render user transfer details items
     *
     * @since 1.0.0
     *
     * @param integer     $user_id          User ID
     * @param stdClass    $transfer          Transfer Object
     * @param integer     $transfer_id       Transfer ID
     * @param array       $transfer_items    Transfer Items
     */
    do_action( 'gamipress_transfers_before_transfer_details_items', $user_id, $transfer, $transfer_id, $transfer_items ); ?>

    <?php
    $transfer_details_items_columns = array(
        'description' => __( 'Description', 'gamipress-transfers' ),
        'quantity' => __( 'Quantity', 'gamipress-transfers' ),
    );

    /**
     * Transfer item details columns
     *
     * @since 1.0.0
     *
     * @param array         $columns        Columns to be rendered
     * @param integer       $user_id        User ID
     * @param stdClass      $transfer       Transfer object
     * @param integer       $transfer_id    Transfer ID
     */
    $transfer_details_items_columns = apply_filters( 'gamipress_transfers_transfer_details_items_columns', $transfer_details_items_columns, $user_id, $transfer, $transfer_id, $transfer_items )
    ?>

    <table id="gamipress-transfers-transfer-details-items" class="gamipress-transfers-transfer-details-items">

        <thead>

        <tr>

            <?php foreach( $transfer_details_items_columns as $column_name => $column_label ) : ?>
                <th class="gamipress-transfers-col gamipress-transfers-col-<?php echo $column_name; ?>"><?php echo $column_label; ?></th>
            <?php endforeach ?>

        </tr>

        </thead>
        <tbody>

        <?php foreach ( $transfer_items as $transfer_item ) : ?>

            <tr>

                <?php foreach( $transfer_details_items_columns as $column_name => $column_label ) : ?>

                    <?php
                    $column_output = '';

                    switch( $column_name ) {
                        case 'description':
                            $column_output = $transfer_item->description;
                            break;
                        case 'quantity':
                            $column_output = $transfer_item->quantity;
                            break;
                    }

                    /**
                     * Transfer item details column render
                     *
                     * @since 1.0.0
                     *
                     * @param string        $output         Column output
                     * @param string        $column_name    Column name
                     * @param stdClass      $transfer_item  Transfer Item object
                     * @param integer       $user_id        User ID
                     * @param stdClass      $transfer       Transfer object
                     * @param integer       $transfer_id    Transfer ID
                     */
                    $column_output = apply_filters( 'gamipress_transfers_transfer_details_items_render_column', $column_output, $column_name, $transfer_item, $user_id, $transfer, $transfer_id )
                    ?>

                    <td class="gamipress-transfers-col gamipress-transfers-col-<?php echo $column_name; ?>"><?php echo $column_output; ?></td>
                <?php endforeach ?>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <?php
    /**
     * After render user transfer details items
     *
     * @since 1.0.0
     *
     * @param integer     $user_id          User ID
     * @param stdClass    $transfer         Transfer Object
     * @param integer     $transfer_id      Transfer ID
     * @param array       $transfer_items   Transfer Items
     */
    do_action( 'gamipress_transfers_after_transfer_details_items', $user_id, $transfer, $transfer_id, $transfer_items ); ?>

<?php else : ?>
    <p class="gamipress-transfers-no-transfer-items"><?php _e('This transfer have not items','gamipress-transfers' ); ?></p>
<?php endif;?>
