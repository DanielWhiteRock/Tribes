<?php
/**
 * Points payout History template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/points-payouts/points-payout-history.php
 */

global $gamipress_points_payouts_template_args;

// Shorthand
$a = $gamipress_points_payouts_template_args;

// Setup vars
$user_id = $a['user_id'];
$items_per_page = 20;

/**
 * Points payout history items per page
 *
 * @since 1.0.0
 *
 * @param integer   $items_per_page Items per page, by default 20
 * @param integer   $user_id        User ID
 */
$items_per_page = apply_filters( 'gamipress_points_payouts_points_payout_history_items_per_page', $items_per_page, $user_id );

$query_args = array(
    'items_per_page' => $items_per_page,
    'paged' => max( 1, get_query_var( 'paged' ) )
);

/**
 * Points payout history query args
 *
 * @since 1.0.0
 *
 * @param array     $query_args Query arguments
 * @param integer   $user_id    User ID
 */
$query_args = apply_filters( 'gamipress_points_payouts_points_payout_history_query_args', $query_args, $user_id );

$points_payouts = gamipress_points_payouts_get_user_points_payouts( $user_id, $query_args ); ?>

<?php if ( ! empty( $points_payouts ) ) : ?>

    <?php
    /**
     * Before render user points payout history
     *
     * @since 1.0.0
     *
     * @param integer     $user_id      User ID
     * @param array       $points_payouts    User points payouts
     */
    do_action( 'gamipress_points_payouts_before_points_payout_history', $user_id, $points_payouts ); ?>

    <?php
    $points_payout_history_columns = array(
        'id'        => __( 'Number', 'gamipress-points-payouts' ),
        'points'    => __( 'Amount', 'gamipress-points-payouts' ),
        'money'     => __( 'Money', 'gamipress-points-payouts' ),
        'date'      => __( 'Date', 'gamipress-points-payouts' ),
        'status'    => __( 'Status', 'gamipress-points-payouts' ),
        'actions'   => __( 'Actions', 'gamipress-points-payouts' ),
    );

    /**
     * Points payout history columns
     *
     * @since 1.0.0
     *
     * @param array         $columns    Columns to be rendered
     * @param integer       $user_id    User ID
     * @param array         $points_payouts  User points payouts
     */
    $points_payout_history_columns = apply_filters( 'gamipress_points_payouts_points_payout_history_columns', $points_payout_history_columns, $user_id, $points_payouts )
    ?>

    <table id="gamipress-points-payouts-points-payout-history" class="gamipress-points-payouts-points-payout-history">

        <thead>

            <tr>

                <?php foreach( $points_payout_history_columns as $column_name => $column_label ) : ?>
                    <th class="gamipress-points-payouts-col gamipress-points-payouts-col-<?php echo $column_name; ?>"><?php echo $column_label; ?></th>
                <?php endforeach ?>

            </tr>

        </thead>
        <tbody>

        <?php foreach ( $points_payouts as $points_payout ) : ?>

            <tr>

                <?php foreach( $points_payout_history_columns as $column_name => $column_label ) : ?>

                    <?php
                    $column_output = '';

                    switch( $column_name ) {
                        case 'id':
                            $column_output = '#' . $points_payout->points_payout_id;
                            break;
                        case 'points':
                            $column_output = gamipress_format_points( $points_payout->points, $points_payout->points_type );
                            break;
                        case 'money':
                            $column_output = gamipress_points_payouts_format_price( $points_payout->money );
                            break;
                        case 'date':
                            $column_output = date_i18n( get_option( 'date_format' ), strtotime( $points_payout->date ) );
                            break;
                        case 'status':
                            $statuses = gamipress_points_payouts_get_points_payout_statuses();
                            $column_output = '<span class="gamipress-points-payouts-status gamipress-points-payouts-status-' . $points_payout->status . '">' . ( isset( $statuses[$points_payout->status] ) ? $statuses[$points_payout->status] : $points_payout->status ) . '</span>';
                            break;
                        case 'actions':

                            $actions = array();

                            $actions[] = sprintf(
                                '<a href="%s" class="%s">%s</a>',
                                gamipress_points_payouts_get_points_payout_details_link( $points_payout->points_payout_id ),
                                'gamipress-points-payouts-view-points-payout-details',
                                __( 'View Details', 'gamipress-points-payouts' )
                            );

                            $actions = apply_filters( 'gamipress_points_payouts_points_payout_history_actions', $actions, $user_id, $points_payout );

                            foreach( $actions as $action ) {
                                $column_output .= $action;
                            }
                            break;
                    }

                    /**
                     * Points payout history column render
                     *
                     * @since 1.0.0
                     *
                     * @param string        $output         Column output
                     * @param string        $column_name    Column name
                     * @param integer       $user_id        User ID
                     * @param array         $points_payout  Points payout object
                     */
                    $column_output = apply_filters( 'gamipress_points_payouts_points_payout_history_render_column', $column_output, $column_name, $user_id, $points_payout )
                    ?>

                    <td class="gamipress-points-payouts-col gamipress-points-payouts-col-<?php echo $column_name; ?>"><?php echo $column_output; ?></td>
                <?php endforeach ?>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <div id="gamipress-points-payouts-points-payout-history-pagination" class="gamipress-points-payouts-points-payout-history-pagination navigation">
        <?php
        $big = 999999;
        echo paginate_links( array(
            'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'  => '?paged=%#%',
            'current' => max( 1, get_query_var( 'paged' ) ),
            'total'   => ceil( gamipress_points_payouts_get_user_points_payouts_count( $user_id ) / $items_per_page )
        ) );
        ?>
    </div>

    <?php
    /**
     * After render user points payout history
     *
     * @since 1.0.0
     *
     * @param integer     $user_id      User ID
     * @param array       $points_payouts    User points payouts
     */
    do_action( 'gamipress_points_payouts_after_points_payout_history', $user_id, $points_payouts ); ?>

<?php else : ?>
    <p class="gamipress-points-payouts-no-points-payouts"><?php _e('You have not made any withdrawals','gamipress-points-payouts' ); ?></p>
<?php endif;?>