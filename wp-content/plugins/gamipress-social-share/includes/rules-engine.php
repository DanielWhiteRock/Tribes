<?php
/**
 * Rules Engine
 *
 * @package GamiPress\Social_Share\Rules_Engine
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Checks if an user is allowed to work on a given requirement related to a specific social network
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
function gamipress_social_share_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // If is specific URL trigger, rules engine needs the attached URL
    if(
        $trigger === 'gamipress_social_share_specific_url_on_any_network'
        || $trigger === 'gamipress_social_share_specific_url_on_specific_network'
    ) {
        $url = $args[0];
        $required_url = get_post_meta( $requirement_id, '_gamipress_social_share_url', true );;

        // True if there is a specific URL, a attached URL and both are equal
        $return = (bool) (
            $url !== ''
            && $required_url !== ''
            && $url === $required_url
        );
    }

    // If is specific social network trigger, rules engine needs the attached social network
    if( $return &&
        // URL events
        ( $trigger === 'gamipress_social_share_url_on_specific_network'
        || $trigger === 'gamipress_social_share_specific_url_on_specific_network'
        // Post events
        || $trigger === 'gamipress_social_share_share_on_specific_network'
        || $trigger === 'gamipress_social_share_share_specific_on_specific_network'
        // Author events
        || $trigger === 'gamipress_social_share_get_share_on_specific_network'
        || $trigger === 'gamipress_social_share_get_share_specific_on_specific_network' )
    ) {
        $social_network = $args[2];
        $required_social_network = get_post_meta( $requirement_id, '_gamipress_social_share_social_network', true );;

        // True if there is a specific social network, a attached social network and both are equal
        $return = (bool) (
            $social_network !== ''
            && $required_social_network !== ''
            && $social_network === $required_social_network
        );
    }

    // Send back our eligibility
    return $return;
}
add_filter( 'user_has_access_to_achievement', 'gamipress_social_share_user_has_access_to_achievement', 10, 6 );