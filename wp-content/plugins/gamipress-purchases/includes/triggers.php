<?php
/**
 * Triggers
 *
 * @package     GamiPress\Purchases\Triggers
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
function gamipress_purchases_activity_triggers( $activity_triggers ) {

    $activity_triggers[__( 'Purchases', 'gamipress-purchases' )] = array(
        'gamipress_purchases_new_purchase' 		                => __( 'Make a new purchase', 'gamipress-purchases' ),
        'gamipress_purchases_new_points_purchase'  		        => __( 'Purchase a minimum amount of points', 'gamipress-purchases' ),
        'gamipress_purchases_new_achievement_purchase'	        => __( 'Purchase access to an achievement', 'gamipress-purchases' ),
        'gamipress_purchases_new_specific_achievement_purchase'	=> __( 'Purchase access to a specific achievement', 'gamipress-purchases' ),
        'gamipress_purchases_new_rank_purchase'	                => __( 'Purchase access to a rank', 'gamipress-purchases' ),
        'gamipress_purchases_new_specific_rank_purchase'	    => __( 'Purchase access to a specific rank', 'gamipress-purchases' ),
    );

    return $activity_triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_purchases_activity_triggers' );

/**
 * Register specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_purchases_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_purchases_new_specific_achievement_purchase'] = gamipress_get_achievement_types_slugs();
    $specific_activity_triggers['gamipress_purchases_new_specific_rank_purchase'] = gamipress_get_rank_types_slugs();

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_purchases_specific_activity_triggers' );

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
function gamipress_purchases_activity_trigger_label( $title, $requirement_id, $requirement ) {

    // Get our types
    $points_types = gamipress_get_points_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();

    switch( $requirement['trigger_type'] ) {

        // Points type label
        case 'gamipress_purchases_new_points_purchase':

            // Bail if points type not well configured
            if( ! isset( $points_types[$requirement['purchases_points_type']] ) ) {
                return $title;
            }

            $points_type = $points_types[$requirement['purchases_points_type']];
            $points_amount = absint( $requirement['purchases_points_amount'] );

            if( $points_amount > 0 ) {
                return sprintf( __( 'Purchase a minimum of %d %s', 'gamipress-purchases' ), $points_amount, $points_type['plural_name'] );
            } else {
                return sprintf( __( 'Purchase any amount of %s', 'gamipress-purchases' ), $points_type['plural_name'] );
            }

            break;

        // Achievement type label
        case 'gamipress_purchases_new_achievement_purchase':

            // Bail if achievement type not well configured
            if( ! isset( $achievement_types[$requirement['purchases_achievement_type']] ) ) {
                return $title;
            }

            $achievement_type = $achievement_types[$requirement['purchases_achievement_type']];

            return sprintf( __( 'Purchase access to any %s', 'gamipress-purchases' ), $achievement_type['singular_name'] );

            break;
        case 'gamipress_purchases_new_specific_achievement_purchase':

            $achievement = gamipress_get_post( $requirement['achievement_post'] );

            // Bail if achievement not exists
            if( ! $achievement ) {
                return $title;
            }

            // Bail if achievement type not well configured
            if( ! isset( $achievement_types[$achievement->post_type] ) ) {
                return $title;
            }

            $achievement_type = $achievement_types[$achievement->post_type];

            return sprintf( __( 'Purchase access to the %s %s', 'gamipress-purchases' ), $achievement_type['singular_name'], $achievement->post_title );

            break;

        // Rank type label
        case 'gamipress_purchases_new_rank_purchase':

            // Bail if rank type not well configured
            if( ! isset( $rank_types[$requirement['purchases_rank_type']] ) ) {
                return $title;
            }

            $rank_type = $rank_types[$requirement['purchases_rank_type']];

            return sprintf( __( 'Purchase access to any %s', 'gamipress-purchases' ), $rank_type['singular_name'] );

            break;
        case 'gamipress_purchases_new_specific_rank_purchase':

            $rank = gamipress_get_post( $requirement['achievement_post'] );

            // Bail if rank not exists
            if( ! $rank ) {
                return $title;
            }

            // Bail if rank type not well configured
            if( ! isset( $rank_types[$rank->post_type] ) ) {
                return $title;
            }

            $rank_type = $rank_types[$rank->post_type];

            return sprintf( __( 'Purchase access to the %s %s', 'gamipress-purchases' ), $rank_type['singular_name'], $rank->post_title );

            break;

    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_purchases_activity_trigger_label', 10, 3 );

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
function gamipress_purchases_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_purchases_new_purchase':
        case 'gamipress_purchases_new_points_purchase':
        case 'gamipress_purchases_new_achievement_purchase':
        case 'gamipress_purchases_new_specific_achievement_purchase':
        case 'gamipress_purchases_new_rank_purchase':
        case 'gamipress_purchases_new_specific_rank_purchase':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_purchases_trigger_get_user_id', 10, 3 );

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
function gamipress_purchases_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_purchases_new_specific_achievement_purchase':
        case 'gamipress_purchases_new_specific_rank_purchase':
            $specific_id = $args[3];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_purchases_specific_trigger_get_id', 10, 3 );