<?php
/**
 * Payment Items
 *
 * @package     GamiPress\Points_Purchases\Custom_Tables\Payment_Items
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Parse query args for payment items
 *
 * @since  1.0.0
 *
 * @param string $where
 * @param CT_Query $ct_query
 *
 * @return string
 */
function gamipress_purchases_payment_items_query_where( $where, $ct_query ) {

    global $ct_table;

    if( $ct_table->name !== 'gamipress_payment_items' ) {
        return $where;
    }

    $table_name = $ct_table->db->table_name;

    // Payment ID
    if( isset( $ct_query->query_vars['payment_id'] ) && absint( $ct_query->query_vars['payment_id'] ) !== 0 ) {

        $payment_id = $ct_query->query_vars['payment_id'];

        if( is_array( $payment_id ) ) {
            $payment_id = implode( ", ", $payment_id );

            $where .= " AND {$table_name}.payment_id IN ( {$payment_id} )";
        } else {
            $where .= " AND {$table_name}.payment_id = {$payment_id}";
        }
    }

    return $where;
}
add_filter( 'ct_query_where', 'gamipress_purchases_payment_items_query_where', 10, 2 );