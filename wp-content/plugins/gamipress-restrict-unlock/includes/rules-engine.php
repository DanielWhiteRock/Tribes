<?php
/**
 * Rules Engine
 *
 * @package     GamiPress\Restrict_Unlock\Rules_Engine
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Checks if an user is allowed to work on a given element
 *
 * @since  1.0.0
 *
 * @param  bool     $return   			The default return value
 * @param  int      $user_id  			The given user's ID
 * @param  int      $achievement_id  	The given post ID
 * @param  string   $trigger  	        Event triggered
 * @param  int      $site_id  	        Site where event has been triggered
 * @param  array    $args  	            Event parameters
 *
 * @return bool              			True if user has access, false otherwise
 */
function gamipress_restrict_unlock_user_has_access_to_achievement( $return, $user_id, $achievement_id, $trigger, $site_id, $args ) {

    if( ! $return )
        return $return;

    // Revoke access is achievement is restricted and user is not granted
    if( gamipress_restrict_unlock_is_restricted( $achievement_id )
        && ! gamipress_restrict_unlock_is_user_granted( $achievement_id, $user_id ) ) {
        return false;
    }

    return $return;

}
add_filter( 'user_has_access_to_achievement', 'gamipress_restrict_unlock_user_has_access_to_achievement', 10, 6 );