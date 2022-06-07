<?php
/**
 * Triggers
 *
 * @package     GamiPress\Transfers\Triggers
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin activity triggers
 *
 * @since 1.0.0
 *
 * @param array $activity_triggers
 *
 * @return mixed
 */
function gamipress_transfers_activity_triggers( $activity_triggers ) {

    $activity_triggers[__( 'Transfers', 'gamipress-transfers' )] = array(
        'gamipress_transfers_new_transfer' 		                    => __( 'Make a new transfer', 'gamipress-transfers' ),
        'gamipress_transfers_new_points_transfer'  		            => __( 'Transfer a minimum amount of points', 'gamipress-transfers' ),
        'gamipress_transfers_new_achievement_transfer'	            => __( 'Transfer an achievement', 'gamipress-transfers' ),
        'gamipress_transfers_new_specific_achievement_transfer'	    => __( 'Transfer a specific achievement', 'gamipress-transfers' ),
        'gamipress_transfers_new_rank_transfer'	                    => __( 'Transfer a rank', 'gamipress-transfers' ),
        'gamipress_transfers_new_specific_rank_transfer'	        => __( 'Transfer a specific rank', 'gamipress-transfers' ),
        // Recipient events
        'gamipress_transfers_receive_transfer' 		                => __( 'Receive a new transfer', 'gamipress-transfers' ),
        'gamipress_transfers_receive_points_transfer'  		        => __( 'Receive a transfer with a minimum amount of points', 'gamipress-transfers' ),
        'gamipress_transfers_receive_achievement_transfer'	        => __( 'Receive a transfer with an achievement', 'gamipress-transfers' ),
        'gamipress_transfers_receive_specific_achievement_transfer'	=> __( 'Receive a transfer with a specific achievement', 'gamipress-transfers' ),
        'gamipress_transfers_receive_rank_transfer'	                => __( 'Receive a transfer with a rank', 'gamipress-transfers' ),
        'gamipress_transfers_receive_specific_rank_transfer'	    => __( 'Receive a transfer with a specific rank', 'gamipress-transfers' ),
    );

    return $activity_triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_transfers_activity_triggers' );

/**
 * Register specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_transfers_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_transfers_new_specific_achievement_transfer'] = gamipress_get_achievement_types_slugs();
    $specific_activity_triggers['gamipress_transfers_new_specific_rank_transfer'] = gamipress_get_rank_types_slugs();
    // Recipient events
    $specific_activity_triggers['gamipress_transfers_receive_specific_achievement_transfer'] = gamipress_get_achievement_types_slugs();
    $specific_activity_triggers['gamipress_transfers_receive_specific_rank_transfer'] = gamipress_get_rank_types_slugs();

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_transfers_specific_activity_triggers' );

/**
 * Build custom activity trigger label
 *
 * @since 1.0.0
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_transfers_activity_trigger_label( $title, $requirement_id, $requirement ) {

    // Get our types
    $points_types = gamipress_get_points_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();

    switch( $requirement['trigger_type'] ) {
        // Points type label
        case 'gamipress_transfers_new_points_transfer':
        case 'gamipress_transfers_receive_points_transfer':

            // Bail if points type not well configured
            if( ! isset( $points_types[$requirement['transfers_points_type']] ) )
                return $title;

            $points_type = $points_types[$requirement['transfers_points_type']];
            $points_amount = absint( $requirement['transfers_points_amount'] );

            if( $requirement['trigger_type'] === 'gamipress_transfers_new_points_transfer' ) {
                if( $points_amount > 0 )
                    $pattern = __( 'Transfer a minimum of %d %s', 'gamipress-transfers' );
                else
                    $pattern = __( 'Transfer any amount of %s', 'gamipress-transfers' );
            } else {
                if( $points_amount > 0 )
                    $pattern = __( 'Receive a transfer with a minimum of %d %s', 'gamipress-transfers' );
                else
                    $pattern = __( 'Receive a transfer with any amount of %s', 'gamipress-transfers' );
            }

            if( $points_amount > 0 )
                return sprintf( $pattern, $points_amount, $points_type['plural_name'] );
            else
                return sprintf( $pattern, $points_type['plural_name'] );
            break;
        // Achievement type label
        case 'gamipress_transfers_new_achievement_transfer':
        case 'gamipress_transfers_receive_achievement_transfer':

            // Bail if achievement type not well configured
            if( ! isset( $achievement_types[$requirement['transfers_achievement_type']] ) )
                return $title;

            $achievement_type = $achievement_types[$requirement['transfers_achievement_type']];

            if( $requirement['trigger_type'] === 'gamipress_transfers_new_achievement_transfer' )
                $pattern = __( 'Transfer any %s', 'gamipress-transfers' );
            else
                $pattern = __( 'Receive a transfer with any %s', 'gamipress-transfers' );

            return sprintf( $pattern, $achievement_type['singular_name'] );
            break;
        case 'gamipress_transfers_new_specific_achievement_transfer':
        case 'gamipress_transfers_receive_specific_achievement_transfer':

            $achievement = get_post( $requirement['achievement_post'] );

            // Bail if achievement not exists
            if( ! $achievement )
                return $title;

            // Bail if achievement type not well configured
            if( ! isset( $achievement_types[$achievement->post_type] ) )
                return $title;

            $achievement_type = $achievement_types[$achievement->post_type];

            if( $requirement['trigger_type'] === 'gamipress_transfers_new_specific_achievement_transfer' )
                $pattern = __( 'Transfer the %s %s', 'gamipress-transfers' );
            else
                $pattern = __( 'Receive a transfer with the %s %s', 'gamipress-transfers' );

            return sprintf( $pattern, $achievement_type['singular_name'], $achievement->post_title );
            break;

        // Rank type label
        case 'gamipress_transfers_new_rank_transfer':
        case 'gamipress_transfers_receive_rank_transfer':

            // Bail if rank type not well configured
            if( ! isset( $rank_types[$requirement['transfers_rank_type']] ) )
                return $title;

            $rank_type = $rank_types[$requirement['transfers_rank_type']];

            if( $requirement['trigger_type'] === 'gamipress_transfers_new_rank_transfer' )
                $pattern = __( 'Transfer any %s', 'gamipress-transfers' );
            else
                $pattern = __( 'Receive a transfer with any %s', 'gamipress-transfers' );

            return sprintf( $pattern, $rank_type['singular_name'] );
            break;
        case 'gamipress_transfers_new_specific_rank_transfer':
        case 'gamipress_transfers_receive_specific_rank_transfer':

            $rank = get_post( $requirement['achievement_post'] );

            // Bail if rank not exists
            if( ! $rank )
                return $title;

            // Bail if rank type not well configured
            if( ! isset( $rank_types[$rank->post_type] ) )
                return $title;

            $rank_type = $rank_types[$rank->post_type];

            if( $requirement['trigger_type'] === 'gamipress_transfers_new_specific_rank_transfer' )
                $pattern = __( 'Transfer the %s %s', 'gamipress-transfers' );
            else
                $pattern = __( 'Receive a transfer with the %s %s', 'gamipress-transfers' );

            return sprintf( $pattern, $rank_type['singular_name'], $rank->post_title );
            break;

    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_transfers_activity_trigger_label', 10, 3 );

/**
 * Get user for a given trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          User ID.
 */
function gamipress_transfers_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_transfers_new_transfer':
        case 'gamipress_transfers_new_points_transfer':
        case 'gamipress_transfers_new_achievement_transfer':
        case 'gamipress_transfers_new_specific_achievement_transfer':
        case 'gamipress_transfers_new_rank_transfer':
        case 'gamipress_transfers_new_specific_rank_transfer':
        // Recipient events
        case 'gamipress_transfers_receive_transfer':
        case 'gamipress_transfers_receive_points_transfer':
        case 'gamipress_transfers_receive_achievement_transfer':
        case 'gamipress_transfers_receive_specific_achievement_transfer':
        case 'gamipress_transfers_receive_rank_transfer':
        case 'gamipress_transfers_receive_specific_rank_transfer':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_transfers_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.0
 *
 * @param integer $specific_id  Specific ID.
 * @param string  $trigger      Trigger name.
 * @param array   $args         Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_transfers_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_transfers_new_specific_achievement_transfer':
        case 'gamipress_transfers_new_specific_rank_transfer':
        // Recipient events
        case 'gamipress_transfers_receive_specific_achievement_transfer':
        case 'gamipress_transfers_receive_specific_rank_transfer':
            $specific_id = $args[3];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_transfers_specific_trigger_get_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.0
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_transfers_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_transfers_new_transfer':
        case 'gamipress_transfers_receive_transfer':
            // Add the transfer ID
            $log_meta['transfer_id'] = $args[0];
            break;
        case 'gamipress_transfers_new_points_transfer':
        case 'gamipress_transfers_receive_points_transfer':
            // Add the transfer ID, points type ID and points transferred amount
            $log_meta['transfer_id'] = $args[0];
            $log_meta['points_type_id'] = $args[3];
            $log_meta['points_amount'] = $args[4];
            break;
        case 'gamipress_transfers_new_achievement_transfer':
        case 'gamipress_transfers_new_specific_achievement_transfer':
        case 'gamipress_transfers_receive_achievement_transfer':
        case 'gamipress_transfers_receive_specific_achievement_transfer':
            // Add the transfer ID and the achievement ID
            $log_meta['transfer_id'] = $args[0];
            $log_meta['achievement_id'] = $args[3];
            break;
        case 'gamipress_transfers_new_rank_transfer':
        case 'gamipress_transfers_new_specific_rank_transfer':
        case 'gamipress_transfers_receive_rank_transfer':
        case 'gamipress_transfers_receive_specific_rank_transfer':
            // Add the transfer ID and the rank ID
            $log_meta['transfer_id'] = $args[0];
            $log_meta['rank_id'] = $args[3];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_transfers_log_event_trigger_meta_data', 10, 5 );