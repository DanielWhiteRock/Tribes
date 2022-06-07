<?php
/**
 * Functions
 *
 * @package     GamiPress\Restrict_Unlock\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return an array of user roles as options to use on fields
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_restrict_unlock_get_roles_options() {

    $options = array();

    $editable_roles = array_reverse( get_editable_roles() );

    foreach ( $editable_roles as $role => $details ) {
        $options[$role] = translate_user_role( $details['name'] );
    }

    return apply_filters( 'gamipress_restrict_unlock_get_roles_options', $options );

}

/**
 * Helper function to easily get a plugin meta
 *
 * @since 1.0.0
 *
 * @param int       $post_id
 * @param string    $meta_key
 * @param bool      $single
 * @return mixed
 */
function gamipress_restrict_unlock_get_meta( $post_id = null, $meta_key, $single = true ) {

    if( $post_id === null ) {
        $post_id = get_the_ID();
    }

    $prefix = '_gamipress_restrict_unlock_';

    return get_post_meta( $post_id, $prefix . $meta_key, $single );

}

/**
 * Check if user has been granted to access to a given post
 *
 * Important! Administrator and authors are not restricted to access
 *
 * @since 1.0.0
 *
 * @param integer $post_id
 * @param integer $user_id
 *
 * @return bool
 */
function gamipress_restrict_unlock_is_user_granted( $post_id = null, $user_id = null ) {

    if( $post_id === null ) {
        $post_id = get_the_ID();
    }

    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    $post_type = gamipress_get_post_type( $post_id );

    if( in_array( $post_type, gamipress_get_requirement_types_slugs() ) ) {
        // Requirements
        $parent_id = absint( gamipress_get_post_field( 'post_parent', $post_id ) );

        if( $parent_id === 0 ) return false;

        // Update post ID with parent ID to retrieve the configured metas
        $post_id = $parent_id;
    }

    // Granted roles and users

    $granted_roles = gamipress_restrict_unlock_get_meta( $post_id, 'granted_roles', false );

    // Access granted if user role has been manually granted
    if( is_array( $granted_roles ) ) {
        foreach( $granted_roles as $granted_role ) {
            if( user_can( $user_id, $granted_role ) )
                return true;
        }
    }

    $granted_users = gamipress_restrict_unlock_get_meta( $post_id, 'granted_users', false );

    // Access granted if has been manually granted
    if( is_array( $granted_users ) ) {
        // Turn granted users IDs to int to ensure check
        $granted_users = array_map( 'intval', $granted_users );

        if( in_array( $user_id, $granted_users ) )
            return true;
    }

    /**
     * Filter to custom check is user has access to a post
     *
     * Note: To override if user meets requirements, check 'gamipress_restrict_unlock_user_meets_requirements' filter
     *
     * @since 1.0.0
     *
     * @param bool  $access_granted Whatever is granted to access to this post or not
     * @param int   $post_id        The post ID
     * @param int   $user_id        The user ID
     */
    if( apply_filters( 'gamipress_restrict_unlock_is_user_granted', false, $post_id, $user_id ) ) {
        return true;
    }

    // Restricted roles and users

    $restricted_roles = gamipress_restrict_unlock_get_meta( $post_id, 'restricted_roles', false );

    // Access restricted if user role has been manually restricted
    if( is_array( $restricted_roles ) ) {
        foreach( $restricted_roles as $restricted_role ) {
            if( user_can( $user_id, $restricted_role ) )
                return false;
        }
    }

    $restricted_users = gamipress_restrict_unlock_get_meta( $post_id, 'restricted_users', false );

    // Access restricted if has been manually restricted
    if( is_array( $restricted_users ) ) {
        // Turn restricted users IDs to int to ensure check
        $restricted_users = array_map( 'intval', $restricted_users );

        if( in_array( $user_id, $restricted_users ) )
            return false;
    }

    // Access granted if user has access to the post (by meeting all requirements or by expending points)
    if( gamipress_restrict_unlock_user_has_unlocked_post( $post_id, $user_id ) ) {
        return true;
    }

    $unlock_by = gamipress_restrict_unlock_get_meta( $post_id, 'unlock_by' );

    // Access granted if post is unlocked by completing requirements and user meets all of them
    if( $unlock_by === 'complete-requirements' && gamipress_restrict_unlock_user_meets_all_requirements( $post_id, $user_id ) ) {
        return true;
    }

    return false;

}

/**
 * Return true if post is restricted and has requirements
 *
 * @since 1.0.0
 *
 * @param integer $post_id
 *
 * @return bool
 */
function gamipress_restrict_unlock_is_restricted( $post_id = null ) {

    $post_type = gamipress_get_post_type( $post_id );

    if( in_array( $post_type, gamipress_get_requirement_types_slugs() ) ) {
        // Requirements
        $parent_id = absint( gamipress_get_post_field( 'post_parent', $post_id ) );

        if( $parent_id === 0 ) {
            return false;
        }

        // Check if restrict is enabled
        $restricted = ( gamipress_restrict_unlock_get_meta( $parent_id, 'restrict' ) === 'on' );

        // If restrict is enabled, check if the specific requirement restriction is enabled too
        if( $restricted ) {
            $restricted = ( gamipress_restrict_unlock_get_meta( $parent_id, 'restrict_' . str_replace( '-', '_', $post_type ) . 's' ) === 'on' );
        }
        // Update post ID with parent ID to retrieve the unlock_by and requirements metas
        $post_id = $parent_id;
    } else if( $post_type === 'points-type' ) {
        // Points types
        $restricted = ( gamipress_restrict_unlock_get_meta( $post_id, 'restrict_points_awards' ) === 'on'
            || gamipress_restrict_unlock_get_meta( $post_id, 'restrict_points_deducts' ) === 'on' );
    } else {
        // Achievements and ranks
        $restricted = ( gamipress_restrict_unlock_get_meta( $post_id, 'restrict' ) === 'on' );
    }

    $unlock_by = gamipress_restrict_unlock_get_meta( $post_id, 'unlock_by' );

    // If unlock by is setup to complete requirements, then check requirements to set if post is correctly restricted
    if( $unlock_by === 'complete-requirements' ) {

        $requirements = gamipress_restrict_unlock_get_meta( $post_id, 'requirements' );

        // Post restricted if enabled and has requirements
        return $restricted && count( $requirements ) > 0;

    }

    // Post restricted if enabled
    return $restricted;
}

/**
 * Check if user meets all requirements of a given post
 *
 * @since 1.0.0
 *
 * @param int $post_id
 * @param int $user_id
 *
 * @return bool
 */
function gamipress_restrict_unlock_user_meets_all_requirements( $post_id = null, $user_id = null ) {

    if( $post_id === null ) {
        $post_id = get_the_ID();
    }

    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    $requirements = gamipress_restrict_unlock_get_meta( $post_id, 'requirements' );

    if( ! is_array( $requirements ) ) {
        // Prevent to block access to everyone if requirements are not configured yet
        return true;
    }

    $passed_requirements = array();

    foreach( $requirements as $requirement ) {

        if( gamipress_restrict_unlock_user_meets_requirement( $requirement, $user_id ) ) {
            $passed_requirements[] = true;
        }

    }

    $meet_all_requirements = count( $passed_requirements ) >= count( $requirements );

    /**
     * Filter to custom check is user meets all requirements to access a post
     *
     * @since 1.0.0
     *
     * @param bool      $meet_all_requirements
     * @param int       $post_id                Post ID
     * @param int       $user_id                User ID
     * @param array     $requirements           Post configured requirements
     * @param array     $passed_requirements    User passed requirements
     */
    $meet_all_requirements = apply_filters( 'gamipress_restrict_unlock_user_meets_requirements', $meet_all_requirements, $post_id, $user_id, $requirements, $passed_requirements );

    // If user meets all requirements but this is not registered in logs, then register and fire the action
    if( $meet_all_requirements && ! gamipress_restrict_unlock_user_has_unlocked_post( $post_id, $user_id ) ) {
        // Register the unlock unlock on logs
        gamipress_restrict_unlock_log_unlock( $post_id, $user_id );

        /**
         * Post unlocked meeting all requirements
         *
         * @since 1.0.0
         *
         * @param int       $post_id 	    The post unlocked ID
         * @param int       $user_id 	    The user ID
         * @param array     $requirements 	Post configured requirements
         */
        do_action( 'gamipress_restrict_unlock_post_unlocked_meeting_all_requirements', $post_id, $user_id, $requirements );
    }

    return $meet_all_requirements;

}

/**
 * Check if user meets a given requirement
 *
 * @since 1.0.0
 *
 * @param array     $requirement
 * @param int       $user_id
 *
 * @return bool
 */
function gamipress_restrict_unlock_user_meets_requirement( $requirement, $user_id = null ) {

    global $wpdb;

    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    // Setup vars
    $prefix = '_gamipress_restrict_unlock_';
    $ct_table = ct_setup_table( 'gamipress_user_earnings' );

    if( $requirement[$prefix . 'type'] === 'points-balance' ) {
        // Restriction based on current user points

        $required_points = absint( $requirement[$prefix . 'points'] );
        $required_points_type = $requirement[$prefix . 'points_type'];

        $user_points = gamipress_get_user_points( $user_id, $required_points_type );

        if( $user_points >= $required_points ) {
            return true;
        }

    } else if( $requirement[$prefix . 'type'] === 'earn-rank' ) {
        // Restriction based if user has earned a specific rank

        $rank_id = $requirement[$prefix . 'rank'];

        $earned = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*)
            FROM   {$ct_table->db->table_name} AS p
            WHERE p.user_id = %d
             AND p.post_id = %d
            LIMIT 1",
            $user_id,
            absint( $rank_id )
        ) );

        if( absint( $earned ) > 0 ) {
            return true;
        }

    } else if( $requirement[$prefix . 'type'] === 'specific-achievement' ) {
        // Restriction based if user has earned a specific achievement

        $achievement_id = $requirement[$prefix . 'achievement'];
        $required_times = $requirement[$prefix . 'count'];

        $earned_times = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*)
            FROM   {$ct_table->db->table_name} AS p
            WHERE p.user_id = %d
             AND p.post_id = %d",
            $user_id,
            absint( $achievement_id )
        ) );

        if( absint( $earned_times ) >= absint( $required_times ) ) {
            return true;
        }

    } else if( $requirement[$prefix . 'type'] === 'any-achievement' ) {

        $achievement_type = $requirement[$prefix . 'achievement_type'];
        $required_times = $requirement[$prefix . 'count'];

        $earned_times = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*)
            FROM   {$ct_table->db->table_name} AS p
            WHERE p.user_id = %d
             AND p.post_type = %s",
            $user_id,
            $achievement_type
        ) );

        if( absint( $earned_times ) >= absint( $required_times ) ) {
            return true;
        }

    } else if( $requirement[$prefix . 'type'] === 'all-achievements' ) {

        $achievement_type = $requirement[$prefix . 'achievement_type'];

        // Get all earned achievements of type
        $earned_achievements = $wpdb->get_results( $wpdb->prepare(
            "SELECT p.post_id
            FROM   {$ct_table->db->table_name} AS p
            WHERE p.user_id = %d
             AND p.post_type = %s
            LIMIT 1",
            $user_id,
            $achievement_type
        ) );

        // Bail if user has not earned no achievements of this type
        if( count( $earned_achievements ) === 0 ) {
            return false;
        }

        // Get all achievements of type
        $all_achievements_of_type = gamipress_get_achievements( array( 'post_type' => $achievement_type ) );

        // Bail if there are no achievements of this type
        if( ! is_array( $all_achievements_of_type ) ) {
            return true;
        }

        $all_per_type = true;

        foreach ( $all_achievements_of_type as $achievement ) {

            // Assume the user hasn't earned this achievement
            $found_achievement = false;

            // Loop through each earned achievement and see if we've earned it
            foreach ( $earned_achievements as $earned_achievement ) {
                if ( $earned_achievement->post_id == $achievement->ID ) {
                    $found_achievement = true;
                    break;
                }
            }

            // If we haven't earned this single achievement, we haven't earned them all
            if ( ! $found_achievement ) {
                $all_per_type = false;
                break;
            }

        }

        if( $all_per_type ) {
            return true;
        }

    }

    /**
     * Filter to custom check is user meet a specific requirement (commonly used for custom requirements)
     *
     * @since 1.0.0
     *
     * @param bool      $meet_requirement
     * @param int       $user_id                User ID
     * @param array     $requirement            The requirement
     */
    return apply_filters( 'gamipress_restrict_unlock_user_meets_requirement', false, $user_id, $requirement );

}

/**
 * Check if user already has unlocked the post
 *
 * @since 1.0.0
 *
 * @param int $post_id
 * @param int $user_id
 *
 * @return bool
 */
function gamipress_restrict_unlock_user_has_unlocked_post( $post_id = null, $user_id = null ) {

    // Guest has access restricted
    if( $user_id === 0 ) {
        return false;
    }

    $has_unlocked = gamipress_get_user_last_log( $user_id, array(
        'type' => 'restrict_unlock',
        'post_id' => $post_id,
    ) );

    // Check if user has unlocked the post
    return ( $has_unlocked !== false );

}
