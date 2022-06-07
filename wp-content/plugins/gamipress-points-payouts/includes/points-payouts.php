<?php
/**
 * Points Payouts
 *
 * @package GamiPress\Points_Payouts\Points_Payouts
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the registered points payout statuses
 *
 * @since  1.0.0
 *
 * @return array Array of points payout statuses
 */
function gamipress_points_payouts_get_points_payout_statuses() {

    return apply_filters( 'gamipress_points_payouts_get_points_payout_statuses', array(
        'pending'   => __( 'Pending', 'gamipress-points-payouts' ),
        'paid'      => __( 'Paid', 'gamipress-points-payouts' ),
        'rejected'  => __( 'Rejected', 'gamipress-points-payouts' ),
        'refunded'  => __( 'Refunded', 'gamipress-points-payouts' ),
    ) );

}

/**
 * Return the points payout history page link
 *
 * @since  1.0.0
 *
 * @return false|string
 */
function gamipress_points_payouts_get_points_payout_history_link() {

    $points_payout_history_page = gamipress_points_payouts_get_option( 'points_payout_history_page', '' );

    $permalink = get_permalink( $points_payout_history_page );

    return $permalink;

}

/**
 * Return the points payout details page link
 *
 * @since  1.0.0
 *
 * @param integer $points_payout_id
 *
 * @return false|string
 */
function gamipress_points_payouts_get_points_payout_details_link( $points_payout_id ) {

    $permalink = gamipress_points_payouts_get_points_payout_history_link();

    if( $permalink ) {
        $permalink = add_query_arg( 'points_payout_id', $points_payout_id, $permalink );
    }

    return $permalink;

}

/**
 * Return all user points payouts
 *
 * @since  1.0.0
 *
 * @param integer   $user_id
 * @param array     $query_args
 *
 * @return array
 */
function gamipress_points_payouts_get_user_points_payouts( $user_id = null, $query_args = array() ) {

    if( ! $user_id )
        $user_id = get_current_user_id();

    ct_setup_table( 'gamipress_points_payouts' );

    $query_args['user_id'] = $user_id;

    $ct_query = new CT_Query( $query_args );

    $results = $ct_query->get_results();

    ct_reset_setup_table();

    return $results;

}

/**
 * Return user points payouts count
 *
 * @since  1.0.0
 *
 * @param integer $user_id
 *
 * @return integer
 */
function gamipress_points_payouts_get_user_points_payouts_count( $user_id = null ) {

    global $wpdb;

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_points_payouts' );

    $user_points_payouts = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*)
         FROM {$ct_table->db->table_name}
         WHERE user_id = %d",
        absint( $user_id )
    ) );

    ct_reset_setup_table();

    return absint( $user_points_payouts );

}