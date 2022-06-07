<?php
/**
 * Triggers
 *
 * @package     GamiPress\Restrict_Unlock\Triggers
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin specific triggers
 *
 * @since 1.0.2
 *
 * @param array $triggers
 *
 * @return array
 */
function gamipress_restrict_unlock_activity_triggers( $triggers ) {

    $triggers[__( 'Restrict Unlock', 'gamipress-restrict-unlock' )] = array(

        // Unlock post by any way
        'gamipress_restrict_unlock_unlock_post' => __( 'Get access to unlock an element', 'gamipress-restrict-unlock' ),
        'gamipress_restrict_unlock_unlock_specific_post' => __( 'Get access to unlock a specific element', 'gamipress-restrict-unlock' ),

        // Unlock post by meet the requirements
        'gamipress_restrict_unlock_unlock_post_by_requirements' => __( 'Get access to unlock an element by meeting all requirements', 'gamipress-restrict-unlock' ),
        'gamipress_restrict_unlock_unlock_specific_post_by_requirements' => __( 'Get access to unlock a specific element by meeting all requirements', 'gamipress-restrict-unlock' ),

        // Unlock post with points
        'gamipress_restrict_unlock_unlock_post_with_points' => __( 'Get access to unlock an element using points', 'gamipress-restrict-unlock' ),
        'gamipress_restrict_unlock_unlock_specific_post_with_points' => __( 'Get access to unlock a specific element using points', 'gamipress-restrict-unlock' ),

    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_restrict_unlock_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since 1.0.2
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_restrict_unlock_specific_activity_triggers( $specific_activity_triggers ) {

    $post_types = array_merge( array( 'points-type' ), gamipress_get_achievement_types_slugs(), gamipress_get_rank_types_slugs() );

    // Unlock post
    $specific_activity_triggers['gamipress_restrict_unlock_unlock_specific_post'] = $post_types;
    $specific_activity_triggers['gamipress_restrict_unlock_unlock_specific_post_by_requirements'] = $post_types;
    $specific_activity_triggers['gamipress_restrict_unlock_unlock_specific_post_with_points'] = $post_types;

    return $specific_activity_triggers;

}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_restrict_unlock_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since 1.0.2
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_restrict_unlock_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Unlock post
    $specific_activity_trigger_labels['gamipress_restrict_unlock_unlock_specific_post'] = __( 'Get access to unlock %s', 'gamipress-restrict-unlock' );
    $specific_activity_trigger_labels['gamipress_restrict_unlock_unlock_specific_post_by_requirements'] = __( 'Get access to unlock %s by completing all requirements', 'gamipress-restrict-unlock' );
    $specific_activity_trigger_labels['gamipress_restrict_unlock_unlock_specific_post_with_points'] = __( 'Get access to unlock %s using points', 'gamipress-restrict-unlock' );

    return $specific_activity_trigger_labels;

}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_restrict_unlock_specific_activity_trigger_label' );

/**
 * Get user for a given trigger action.
 *
 * @since 1.0.2
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          User ID.
 */
function gamipress_restrict_unlock_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_restrict_unlock_unlock_post':
        case 'gamipress_restrict_unlock_unlock_specific_post':

        case 'gamipress_restrict_unlock_unlock_post_by_requirements':
        case 'gamipress_restrict_unlock_unlock_specific_post_by_requirements':

        case 'gamipress_restrict_unlock_unlock_post_with_points':
        case 'gamipress_restrict_unlock_unlock_specific_post_with_points':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_restrict_unlock_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since 1.0.2
 *
 * @param  integer  $specific_id Specific ID.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_restrict_unlock_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_restrict_unlock_unlock_specific_post':
        case 'gamipress_restrict_unlock_unlock_specific_post_by_requirements':
        case 'gamipress_restrict_unlock_unlock_specific_post_with_points':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;

}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_restrict_unlock_specific_trigger_get_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.2
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_restrict_unlock_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_restrict_unlock_unlock_post':
        case 'gamipress_restrict_unlock_unlock_specific_post':

        case 'gamipress_restrict_unlock_unlock_post_by_requirements':
        case 'gamipress_restrict_unlock_unlock_specific_post_by_requirements':

        case 'gamipress_restrict_unlock_unlock_post_with_points':
        case 'gamipress_restrict_unlock_unlock_specific_post_with_points':
            // Add the post ID
            $log_meta['post_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_restrict_unlock_log_event_trigger_meta_data', 10, 5 );