<?php
/**
 * Transfer History template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/transfers/transfer-history.php
 */
if ( ! is_user_logged_in() )
    return;

global $gamipress_transfers_template_args;

// Shorthand
$a = $gamipress_transfers_template_args;

// Setup vars
$user_id = $a['user_id'];
$items_per_page = 20;

/**
 * Transfer history items per page
 *
 * @since 1.0.0
 *
 * @param integer   $items_per_page Items per page, by default 20
 * @param integer   $user_id        User ID
 */
$items_per_page = apply_filters( 'gamipress_transfers_transfer_history_items_per_page', $items_per_page, $user_id );

$query_args = array(
    'items_per_page' => $items_per_page,
    'paged' => max( 1, get_query_var( 'paged' ) )
);

if( $a['history'] === 'sent' || $a['history'] === 'both' ) {
    $query_args['user_id'] = $user_id;
}

if( $a['history'] === 'received' || $a['history'] === 'both' ) {
    $query_args['recipient_id'] = $user_id;
}

/**
 * Transfer history query args
 *
 * @since 1.1.4
 *
 * @param array     $query_args Query arguments
 * @param integer   $user_id    User ID
 */
$query_args = apply_filters( 'gamipress_transfers_transfer_history_query_args', $query_args, $user_id );

ct_setup_table( 'gamipress_transfers' );
$ct_query = new CT_Query( $query_args );
$transfers = $ct_query->get_results();
ct_reset_setup_table(); ?>

<?php if ( ! empty( $transfers ) ) : ?>

    <?php
    /**
     * Before render user transfer history
     *
     * @since 1.0.0
     *
     * @param integer     $user_id      User ID
     * @param array       $transfers    User transfers
     */
    do_action( 'gamipress_transfers_before_transfer_history', $user_id, $transfers ); ?>

    <?php
    $transfer_history_columns = array(
        'number'    => __( 'Number', 'gamipress-transfers' ),
        'from'      => __( 'From', 'gamipress-transfers' ),
        'to'        => __( 'To', 'gamipress-transfers' ),
        'date'      => __( 'Date', 'gamipress-transfers' ),
        'status'    => __( 'Status', 'gamipress-transfers' ),
        'actions'   => __( 'Actions', 'gamipress-transfers' ),
    );

    if( $a['history'] === 'sent' ) {
        unset( $transfer_history_columns['from'] );
    }

    if( $a['history'] === 'received' ) {
        unset( $transfer_history_columns['to'] );
    }

    /**
     * Transfer history columns
     *
     * @since 1.0.0
     *
     * @param array         $columns    Columns to be rendered
     * @param integer       $user_id    User ID
     * @param array         $transfers  User transfers
     */
    $transfer_history_columns = apply_filters( 'gamipress_transfers_transfer_history_columns', $transfer_history_columns, $user_id, $transfers )
    ?>

    <table id="gamipress-transfers-transfer-history" class="gamipress-transfers-transfer-history">

        <thead>

            <tr>

                <?php foreach( $transfer_history_columns as $column_name => $column_label ) : ?>
                    <th class="gamipress-transfers-col gamipress-transfers-col-<?php echo $column_name; ?>"><?php echo $column_label; ?></th>
                <?php endforeach ?>

            </tr>

        </thead>
        <tbody>

        <?php foreach ( $transfers as $transfer ) : ?>

            <tr>

                <?php foreach( $transfer_history_columns as $column_name => $column_label ) : ?>

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
                        case 'actions':

                            $actions = array();

                            $actions[] = sprintf(
                                '<a href="%s" class="%s">%s</a>',
                                add_query_arg( 'transfer_id', $transfer->transfer_id ),
                                'gamipress-transfers-view-transfer-details',
                                __( 'View Transfer Details', 'gamipress-transfers' )
                            );

                            $actions = apply_filters( 'gamipress_transfers_transfer_history_actions', $actions, $user_id, $transfer );

                            foreach( $actions as $action ) {
                                $column_output .= $action;
                            }
                            break;
                    }

                    /**
                     * Transfer history column render
                     *
                     * @since 1.0.0
                     *
                     * @param string        $output         Column output
                     * @param string        $column_name    Column name
                     * @param integer       $user_id        User ID
                     * @param array         $transfer       Transfer object
                     */
                    $column_output = apply_filters( 'gamipress_transfers_transfer_history_render_column', $column_output, $column_name, $user_id, $transfer )
                    ?>

                    <td class="gamipress-transfers-col gamipress-transfers-col-<?php echo $column_name; ?>"><?php echo $column_output; ?></td>
                <?php endforeach ?>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <div id="gamipress-transfers-transfer-history-pagination" class="gamipress-transfers-transfer-history-pagination navigation">
        <?php
        $big = 999999;
        echo paginate_links( array(
            'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'  => '?paged=%#%',
            'current' => max( 1, get_query_var( 'paged' ) ),
            'total'   => ceil( gamipress_transfers_get_user_transfers_count( $user_id ) / $items_per_page )
        ) );
        ?>
    </div>

    <?php
    /**
     * After render user transfer history
     *
     * @since 1.0.0
     *
     * @param integer     $user_id      User ID
     * @param array       $transfers    User transfers
     */
    do_action( 'gamipress_transfers_after_transfer_history', $user_id, $transfers ); ?>

<?php else : ?>
    <p class="gamipress-transfers-no-transfers"><?php _e('You have not made any transfers','gamipress-transfers' ); ?></p>
<?php endif;?>