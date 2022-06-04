<?php
/**
 * Transfer Notes
 *
 * @package     GamiPress\Transfers\Custom_Tables\Transfer_Notes
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Parse query args for transfer notes
 *
 * @since  1.0.0
 *
 * @param string $where
 * @param CT_Query $ct_query
 *
 * @return string
 */
function gamipress_transfers_transfer_notes_query_where( $where, $ct_query ) {

    global $ct_table;

    if( $ct_table->name !== 'gamipress_transfer_notes' ) {
        return $where;
    }

    $table_name = $ct_table->db->table_name;

    // Transfer ID
    if( isset( $ct_query->query_vars['transfer_id'] ) && absint( $ct_query->query_vars['transfer_id'] ) !== 0 ) {

        $transfer_id = $ct_query->query_vars['transfer_id'];

        if( is_array( $transfer_id ) ) {
            $transfer_id = implode( ", ", $transfer_id );

            $where .= " AND {$table_name}.transfer_id IN ( {$transfer_id} )";
        } else {
            $where .= " AND {$table_name}.transfer_id = {$transfer_id}";
        }
    }

    return $where;
}
add_filter( 'ct_query_where', 'gamipress_transfers_transfer_notes_query_where', 10, 2 );