<?php
/**
 * Points payout Details template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/points-payouts/points-payout-details.php
 */
if ( ! is_user_logged_in() ) {
    return;
}

if( ! isset( $_GET['points_payout_id'] ) ) {
    return;
}

global $gamipress_points_payouts_template_args;

// Shorthand
$a = $gamipress_points_payouts_template_args;

// Setup vars
$user_id = $a['user_id'];
$points_payout_id = $_GET['points_payout_id'];

if( $user_id !== get_current_user_id() ) {
    return;
}

ct_setup_table( 'gamipress_points_payouts' );

$points_payout = ct_get_object( $points_payout_id );

if( absint( $points_payout->user_id ) !== absint( $user_id ) ) {
    return;
} ?>

<?php // Points payout Details ?>

<?php
/**
 * Before render user points payout details
 *
 * @since 1.0.0
 *
 * @param integer     $user_id      User ID
 * @param stdClass    $points_payout     Points payout Object
 * @param integer     $points_payout_id  Points payout ID
 */
do_action( 'gamipress_points_payouts_before_points_payout_details', $user_id, $points_payout, $points_payout_id ); ?>

<?php
$points_payout_details_columns = array(
    'id'        => __( 'Number', 'gamipress-points-payouts' ),
    'points'    => __( 'Amount', 'gamipress-points-payouts' ),
    'money'     => __( 'Money', 'gamipress-points-payouts' ),
    'payment_method' => gamipress_points_payouts_get_option( 'payment_method_text', __( 'Payment Method', 'gamipress-points-payouts' ) ),
    'date'      => __( 'Date', 'gamipress-points-payouts' ),
    'status'    => __( 'Status', 'gamipress-points-payouts' ),
);

/**
 * Points payout details columns
 *
 * @since 1.0.0
 *
 * @param array         $columns            Columns to be rendered
 * @param integer       $user_id            User ID
 * @param stdClass      $points_payout      Points payout object
 * @param integer       $points_payout_id   Points payout ID
 */
$points_payout_details_columns = apply_filters( 'gamipress_points_payouts_points_payout_details_columns', $points_payout_details_columns, $user_id, $points_payout, $points_payout_id )
?>

<table id="gamipress-points-payouts-points-payout-details" class="gamipress-points-payouts-points-payout-details">

    <tbody>

            <?php foreach( $points_payout_details_columns as $column_name => $column_label ) : ?>

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
                    case 'payment_method':
                        $column_output = gamipress_get_user_meta( $user_id, '_gamipress_points_payouts_payment_method', true );
                        break;
                    case 'date':
                        $column_output = date_i18n( get_option( 'date_format' ), strtotime( $points_payout->date ) );
                        break;
                    case 'status':
                        $statuses = gamipress_points_payouts_get_points_payout_statuses();
                        $column_output = '<span class="gamipress-points-payouts-status gamipress-points-payouts-status-' . $points_payout->status . '">' . ( isset( $statuses[$points_payout->status] ) ? $statuses[$points_payout->status] : $points_payout->status ) . '</span>';
                        break;
                }

                $column_output = apply_filters( 'gamipress_points_payouts_points_payout_details_render_column', $column_output, $column_name, $points_payout, $user_id )
                ?>
                <tr>
                    <th class="gamipress-points-payouts-col gamipress-points-payouts-col-<?php echo $column_name; ?>"><?php echo $column_label; ?></th>
                    <td class="gamipress-points-payouts-col gamipress-points-payouts-col-<?php echo $column_name; ?>"><?php echo $column_output; ?></td>
                </tr>
            <?php endforeach ?>

    </tbody>

</table>

<?php
/**
 * After render user points payout details
 *
 * @since 1.0.0
 *
 * @param integer     $user_id              User ID
 * @param stdClass    $points_payout        Points payout Object
 * @param integer     $points_payout_id     Points payout ID
 */
do_action( 'gamipress_points_payouts_after_points_payout_details', $user_id, $points_payout, $points_payout_id ); ?>