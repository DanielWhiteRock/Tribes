<?php
/**
 * Logs
 *
 * @package     GamiPress\Points_Exchanges\Logs
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin log types
 *
 * @since 1.0.0
 *
 * @param array $gamipress_log_types
 *
 * @return array
 */
function gamipress_points_exchanges_logs_types( $gamipress_log_types ) {

    $gamipress_log_types['points_exchange'] = __( 'Points Exchange', 'gamipress-points-exchanges' );

    return $gamipress_log_types;
}
add_filter( 'gamipress_logs_types', 'gamipress_points_exchanges_logs_types' );

/**
 * Log a points exchange on logs
 *
 * @since 1.0.0
 *
 * @param int       $user_id            The user ID
 * @param int       $amount             Amount to exchange
 * @param string    $from               From points type
 * @param string    $to                 To points type
 * @param int       $exchanged_amount   Exchanged amount
 *
 * @return int|false
 */
function gamipress_points_exchanges_log_points_exchange( $user_id, $amount, $from, $to, $exchanged_amount ) {

    $from_points_type = gamipress_get_points_type( $from );
    $to_points_type = gamipress_get_points_type( $to );

    // Log meta data
    $log_meta = array(
        // User exchanged X points for X gems
        'pattern' => sprintf( __( '{user} exchanged %d %s for %d %s', 'gamipress-points-exchanges' ),
            $amount,
            _n( $from_points_type['singular_name'], $from_points_type['plural_name'], $amount ),
            $exchanged_amount,
            _n( $to_points_type['singular_name'], $to_points_type['plural_name'], $exchanged_amount )
        ),
        'from_points' => $amount,
        'from_points_type' => $from,
        'to_points' => $exchanged_amount,
        'to_points_type' => $to,
    );

    // Register the points exchange on logs
    return gamipress_insert_log( 'points_exchange', $user_id, 'public', 'gamipress_exchange_points', $log_meta );

}

/**
 * Adds custom fields on logs
 *
 * @since 1.0.0
 *
 * @param array     $fields            Already rgistered fields
 * @param int       $post_id           The log ID
 * @param string    $type              The log's type
 *
 * @return int|false
 */
function gamipress_points_exchanges_log_extra_data_fields( $fields, $post_id, $type ) {

    $prefix = '_gamipress_';

    if( $type === 'points_exchange' ) {

        // On GamiPress 1.5.1 and issue with CT and the points field has been fixed
        if( version_compare( GAMIPRESS_VER, '1.5.0', '>' ) ) {

            $fields[] = array(
                'name' 	=> __( 'From', 'gamipress-points-exchanges' ),
                'desc' 	=> __( 'Amount of points to exchange (deducted to the user).', 'gamipress-points-exchanges' ),
                'id'   	=> $prefix . 'from_points',
                'type' 	=> 'gamipress_points',
            );

            $fields[] = array(
                'name' 	=> __( 'To', 'gamipress-points-exchanges' ),
                'desc' 	=> __( 'Amount of points exchanged (awarded to the user).', 'gamipress-points-exchanges' ),
                'id'   	=> $prefix . 'to_points',
                'type' 	=> 'gamipress_points',
            );

        } else {

            $points_types = gamipress_get_points_types();
            $points_types_options = array();

            foreach( $points_types as $points_type => $data ) {
                $points_types_options[$points_type] = $data['plural_name'];
            }

            $fields[] = array(
                'name' 	    => __( 'From', 'gamipress-points-exchanges' ),
                'desc' 	    => __( 'Amount of points to exchange (deducted to the user).', 'gamipress-points-exchanges' ),
                'id'   	    => $prefix . 'from_points',
                'type' 	    => 'text_small',
            );

            $fields[] = array(
                'name' 	    => __( 'Amount\'s Points Type', 'gamipress-points-exchanges' ),
                'desc' 	    => __( 'Points type of the amount of points to exchange (deducted to the user).', 'gamipress-points-exchanges' ),
                'id'   	    => $prefix . 'from_points_type',
                'type' 	    => 'select',
                'options' 	=> $points_types_options,
            );

            $fields[] = array(
                'name' 	    => __( 'To', 'gamipress-points-exchanges' ),
                'desc' 	    => __( 'Amount of points exchanged (awarded to the user).', 'gamipress-points-exchanges' ),
                'id'   	    => $prefix . 'to_points',
                'type' 	    => 'text_small',
            );

            $fields[] = array(
                'name' 	    => __( 'Exchange Points Type', 'gamipress-points-exchanges' ),
                'desc' 	    => __( 'Points type of the amount of points exchanged (awarded to the user).', 'gamipress-points-exchanges' ),
                'id'   	    => $prefix . 'to_points_type',
                'type' 	    => 'text_small',
            );

        }

    }

    return $fields;

}
add_filter( 'gamipress_log_extra_data_fields', 'gamipress_points_exchanges_log_extra_data_fields', 10, 3 );