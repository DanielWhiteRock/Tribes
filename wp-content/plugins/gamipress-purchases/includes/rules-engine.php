<?php
/**
 * Rules Engine
 *
 * @package GamiPress\Purchases\Rules_Engine
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Checks if an user is allowed to work on a given requirement related to a specific minimum amount of points to be purchased
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
function gamipress_purchases_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // If is specific points purchase trigger, rules engine needs to see
    if( $trigger === 'gamipress_purchases_new_points_purchase' ) {

        $points_types = gamipress_get_points_types();
        $purchased_type_id = absint( $args[3] );
        $required_type = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_purchases_points_type', true ) );

        // Bail if required type does not exists
        if( ! isset( $points_types[$required_type] ) ) {
            return $return;
        }

        // Bail if not is the required points type
        if( $purchased_type_id !== $points_types[$required_type]['ID'] ) {
            return $return;
        }

        $purchased_amount = absint( $args[4] );
        $required_amount = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_purchases_points_amount', true ) );

        // True if purchased amount is bigger than required amount
        $return = (bool) ( $purchased_amount >= $required_amount );
    }

    // Send back our eligibility
    return $return;
}
add_filter( 'user_has_access_to_achievement', 'gamipress_purchases_user_has_access_to_achievement', 10, 6 );