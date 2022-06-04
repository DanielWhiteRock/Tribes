<?php
/**
 * Triggers
 *
 * @package GamiPress\Social_Share\Triggers
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return plugin activity triggers
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_social_share_get_activity_triggers() {

    return array(

        // URL share events
        'gamipress_social_share_url_on_any_network'                   => __( 'Share any url on any social network', 'gamipress-social-share' ),
        'gamipress_social_share_specific_url_on_any_network'          => __( 'Share specific url on any social network', 'gamipress-social-share' ),
        'gamipress_social_share_url_on_specific_network'              => __( 'Share any url on specific social network', 'gamipress-social-share' ),
        'gamipress_social_share_specific_url_on_specific_network'     => __( 'Share specific url on specific social network', 'gamipress-social-share' ),

        // Post share events
        'gamipress_social_share_share_on_any_network'                   => __( 'Share any post on any social network', 'gamipress-social-share' ),
        'gamipress_social_share_share_specific_on_any_network'          => __( 'Share specific post on any social network', 'gamipress-social-share' ),
        'gamipress_social_share_share_on_specific_network'              => __( 'Share any post on specific social network', 'gamipress-social-share' ),
        'gamipress_social_share_share_specific_on_specific_network'     => __( 'Share specific post on specific social network', 'gamipress-social-share' ),

        // Post author events
        'gamipress_social_share_get_share_on_any_network'               => __( 'Get a share on any social network on any post', 'gamipress-social-share' ),
        'gamipress_social_share_get_share_specific_on_any_network'      => __( 'Get a share on any social network on a specific post', 'gamipress-social-share' ),
        'gamipress_social_share_get_share_on_specific_network'          => __( 'Get a share on a specific social network on any post', 'gamipress-social-share' ),
        'gamipress_social_share_get_share_specific_on_specific_network' => __( 'Get a share on a specific social network on a specific post', 'gamipress-social-share' ),

    );

}

/**
 * Register plugin activity triggers
 *
 * @since 1.0.0
 *
 * @param array $activity_triggers
 *
 * @return mixed
 */
function gamipress_social_share_activity_triggers( $activity_triggers ) {

    $activity_triggers[__( 'Social Share', 'gamipress-social-share' )] = gamipress_social_share_get_activity_triggers();

    return $activity_triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_social_share_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_social_share_specific_activity_triggers( $specific_activity_triggers ) {

    $post_types = gamipress_social_share_post_types_options_cb();

    // Turn array( 'post-type' => 'Label' ) to array( 'post-type )
    $post_types = array_keys( $post_types );

    $specific_activity_triggers['gamipress_social_share_share_specific_on_any_network'] = $post_types;
    $specific_activity_triggers['gamipress_social_share_share_specific_on_specific_network'] = $post_types;

    $specific_activity_triggers['gamipress_social_share_get_share_specific_on_any_network'] = $post_types;
    $specific_activity_triggers['gamipress_social_share_get_share_specific_on_specific_network'] = $post_types;

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_social_share_specific_activity_triggers' );

/**
 * Register plugins specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_social_share_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_social_share_share_specific_on_any_network'] = __( 'Share %s on any social network', 'gamipress-social-share' );
    $specific_activity_trigger_labels['gamipress_social_share_share_specific_on_specific_network'] = __( 'Share %s on a specific social network', 'gamipress-social-share' );

    $specific_activity_trigger_labels['gamipress_social_share_get_share_specific_on_any_network'] = __( ' Get a share on any social network on %s', 'gamipress-social-share' );
    $specific_activity_trigger_labels['gamipress_social_share_get_share_specific_on_specific_network'] = __( 'Get a share on a specific social network on %s', 'gamipress-social-share' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_social_share_specific_activity_trigger_label' );

/**
 * Build custom activity trigger label
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_social_share_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $url = ( isset( $requirement['social_share_url'] ) ? $requirement['social_share_url'] : '' );

    // Check if requirement has assigned an URL
    if( isset( $requirement['social_share_url'] ) ) {
        switch( $requirement['trigger_type'] ) {
            // Post share events
            case 'gamipress_social_share_specific_url_on_any_network':
                // Share {url} on any social network
                return sprintf( __( 'Share %s on any social network', 'gamipress-social-share' ), $url );
                break;
        }
    }

    // Check if requirement has assigned a social network
    if( isset( $requirement['social_network'] ) ) {

        $social_networks = gamipress_social_share_get_social_networks();
        $label = ( isset( $social_networks[$requirement['social_network']] ) ) ? $social_networks[$requirement['social_network']] : '';

        if( $requirement['social_network'] === 'facebook_share' ) $label = __( 'Facebook', 'gamipress-social-share' );

        switch( $requirement['trigger_type'] ) {
            // URL share events
            case 'gamipress_social_share_url_on_specific_network':
                if( $requirement['social_network'] === 'facebook' ) {
                    // Like or recommend any url on Facebook
                    return __( 'Like or recommend any url on Facebook', 'gamipress-social-share' );
                } else {
                    // Share any url on {social_network}
                    return sprintf( __( 'Share any url on %s', 'gamipress-social-share' ), $label );
                }
                break;
            case 'gamipress_social_share_specific_url_on_specific_network':
                if( $requirement['social_network'] === 'facebook' ) {
                    // Like or recommend {url} on Facebook
                    return sprintf( __( 'Like or recommend %s on Facebook', 'gamipress-social-share' ), $url );
                } else {
                    // Share {url} on {social_network}
                    return sprintf( __( 'Share %s on %s', 'gamipress-social-share' ), $url, $label );
                }
                break;
            // Post share events
            case 'gamipress_social_share_share_on_specific_network':
                if( $requirement['social_network'] === 'facebook' ) {
                    // Like or recommend any post on Facebook
                    return __( 'Like or recommend any post on Facebook', 'gamipress-social-share' );
                } else {
                    // Share any post on {social_network}
                    return sprintf( __( 'Share any post on %s', 'gamipress-social-share' ), $label );
                }
                break;
            case 'gamipress_social_share_share_specific_on_specific_network':
                if( $requirement['social_network'] === 'facebook' ) {
                    // Like or recommend {page} on Facebook
                    return sprintf( __( 'Like or recommend %s on Facebook', 'gamipress-social-share' ), get_the_title( absint( $requirement['achievement_post'] ) ) );
                } else {
                    // Share {page} on {social_network}
                    return sprintf( __( 'Share %s on %s', 'gamipress-social-share' ), get_the_title( absint( $requirement['achievement_post'] ) ), $label );
                }
                break;
            // Post author events
            case 'gamipress_social_share_get_share_on_specific_network':
                if( $requirement['social_network'] === 'facebook' ) {
                    // Get a like or recommendation on Facebook on any post
                    return __( 'Get a like or recommendation on Facebook on any post', 'gamipress-social-share' );
                } else {
                    // Get a share on {social_network} on any post
                    return sprintf( __( 'Get a share on %s on any post', 'gamipress-social-share' ), $label );
                }
                break;
            case 'gamipress_social_share_get_share_specific_on_specific_network':
                if( $requirement['social_network'] === 'facebook' ) {
                    // Get a like or recommendation on Facebook on {page}
                    return sprintf( __( 'Get a like or recommendation on Facebook on %s', 'gamipress-social-share' ), get_the_title( absint( $requirement['achievement_post'] ) ) );
                } else {
                    // Get a share on {social_network} on {page}
                    return sprintf( __( 'Get a share on %s on %s', 'gamipress-social-share' ), $label, get_the_title( absint( $requirement['achievement_post'] ) ) );
                }
                break;
        }
    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_social_share_activity_trigger_label', 10, 3 );

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
function gamipress_social_share_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // URL share events
        case 'gamipress_social_share_url_on_any_network':
        case 'gamipress_social_share_specific_url_on_any_network':
        case 'gamipress_social_share_url_on_specific_network':
        case 'gamipress_social_share_specific_url_on_specific_network':
        // Post share events
        case 'gamipress_social_share_share_on_any_network':
        case 'gamipress_social_share_share_specific_on_any_network':
        case 'gamipress_social_share_share_on_specific_network':
        case 'gamipress_social_share_share_specific_on_specific_network':
        // Post author events
        case 'gamipress_social_share_get_share_on_any_network':
        case 'gamipress_social_share_get_share_specific_on_any_network':
        case 'gamipress_social_share_get_share_on_specific_network':
        case 'gamipress_social_share_get_share_specific_on_specific_network':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_social_share_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $specific_id Specific ID to override.
 * @param  string  $trigger     Trigger name.
 * @param  array   $args        Passed trigger args.
 *
 * @return integer              Specific ID.
 */
function gamipress_social_share_specific_trigger_get_id( $specific_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Post share events
        case 'gamipress_social_share_share_specific_on_any_network':
        case 'gamipress_social_share_share_specific_on_specific_network':
        // Post author events
        case 'gamipress_social_share_get_share_specific_on_any_network':
        case 'gamipress_social_share_get_share_specific_on_specific_network':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;

}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_social_share_specific_trigger_get_id', 10, 3 );