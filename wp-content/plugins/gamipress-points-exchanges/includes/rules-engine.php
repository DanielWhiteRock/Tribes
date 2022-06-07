<?php
/**
 * Rules Engine
 *
 * @package GamiPress\Points_Exchanges\Rules_Engine
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Checks if a user is allowed to work on a given requirement related to a specific minimum amount of points to be exchanged
 *
 * @since  1.0.0
 *
 * @param int 	    $requirement_id
 * @param string 	$trigger
 * @param array 	$args
 *
 * @return bool True if user has access to the requirement, false otherwise
 */
function gamipress_points_exchanges_check_if_meets_requirements( $requirement_id, $trigger, $args ) {

    // Initialize the return value
    $return = true;

    // If is specific points exchange trigger, rules engine needs to see
    if( $trigger === 'gamipress_points_exchanges_new_points_exchange' ) {

        $points_types = gamipress_get_points_types();
        $exchanged_type_id = gamipress_get_points_type_id( $args[2] );
        $required_type = gamipress_get_post_meta( $requirement_id, '_gamipress_points_exchanges_points_type', true );

        // Bail if required type does not exists
        if( ! isset( $points_types[$required_type] ) ) {
            return false;
        }

        // Bail if not is the required points type
        if( $exchanged_type_id !== $points_types[$required_type]['ID'] ) {
            return false;
        }

        $exchanged_amount = absint( $args[1] );
        $required_amount = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_points_exchanges_points_amount', true ) );

        // True if exchanged amount is bigger than required amount
        $return = (bool) ( $exchanged_amount >= $required_amount );
    }

    // Send back our eligibility
    return $return;

}

/**
 * Filter triggered requirements to reduce the number of requirements to check by the awards engine
 *
 * @since 1.0.0
 *
 * @param array 	$triggered_requirements
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_points_exchanges_filter_triggered_requirements( $triggered_requirements, $user_id, $trigger, $site_id, $args ) {

    $new_requirements = array();

    foreach( $triggered_requirements as $i => $requirement ) {

        // Skip item
        if( ! gamipress_points_exchanges_check_if_meets_requirements( $requirement->ID, $trigger, $args ) ) {
            continue;
        }

        // Keep the requirement on the list of requirements to check by the awards engine
        $new_requirements[] = $requirement;

    }

    return $new_requirements;

}
add_filter( 'gamipress_get_triggered_requirements', 'gamipress_points_exchanges_filter_triggered_requirements', 20, 5 );

/**
 * Checks if an user is allowed to work on a given requirement related to a minimum of score
 *
 * @since  1.0.0
 *
 * @param bool $return          The default return value
 * @param int $user_id          The given user's ID
 * @param int $requirement_id   The given requirement's post ID
 * @param string $trigger       The trigger triggered
 * @param int $site_id          The site id
 * @param array $args           Arguments of this trigger
 *
 * @return bool True if user has access to the requirement, false otherwise
 */
function gamipress_points_exchanges_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // Send back our eligibility
    return gamipress_points_exchanges_check_if_meets_requirements( $requirement_id, $trigger, $args );

}
add_filter( 'user_has_access_to_achievement', 'gamipress_points_exchanges_user_has_access_to_achievement', 10, 6 );