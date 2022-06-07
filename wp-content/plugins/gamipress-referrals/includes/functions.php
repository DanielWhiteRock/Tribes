<?php
/**
 * Functions
 *
 * @package GamiPress\Referrals\Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return the WP_User object of the given affiliate ID
 *
 * @since 1.0.0
 *
 * @param WP_User|string|int    $affiliate_id
 *
 * @return WP_User|false
 */
function gamipress_referrals_get_affiliate( $affiliate_id ) {

    $affiliate = false;

    if( $affiliate_id instanceof WP_User )
        return $affiliate_id;

    if( ! empty( $affiliate_id ) ) {

        if( is_numeric( $affiliate_id ) ) {
            // Get user by ID
            $affiliate = get_userdata( absint( $affiliate_id ) );
        } else {
            // Get user by login field
            $affiliate = get_user_by( 'login', sanitize_text_field( urldecode( $affiliate_id ) ) );
        }

    }


    /**
     * Available filter to override this
     *
     * @param WP_User|false         $affiliate
     * @param WP_User|string|int    $affiliate_id
     *
     * @return WP_User|false
     */
    return apply_filters( 'gamipress_referrals_get_affiliate', $affiliate, $affiliate_id );

}

/**
 * Assign the affiliate to the user
 *
 * @since 1.0.6
 *
 * @param int    $user_id
 * @param int    $affiliate_id
 */
function gamipress_referrals_set_user_affiliate( $user_id, $affiliate_id ) {

    if( absint( $user_id ) === 0 ) {
        return;
    }

    if( absint( $affiliate_id ) === 0 ) {
        return;
    }

    // Bail if user already has an affiliate
    if( gamipress_referrals_get_user_affiliate( $user_id ) ) {
        return;
    }

    // Update the user meta with the new affiliate
    gamipress_update_user_meta( $user_id, '_gamipress_referrals_affiliate_id', $affiliate_id );

}

/**
 * Return the WP_User of the affiliate who referred the given user ID
 *
 * @since 1.0.6
 *
 * @param WP_User|string|int    $user_id
 *
 * @return WP_User|false
 */
function gamipress_referrals_get_user_affiliate( $user_id ) {

    if( $user_id instanceof WP_User ) {
        $user_id = $user_id->ID;
    }

    $affiliate_id = gamipress_get_user_meta( $user_id, '_gamipress_referrals_affiliate_id', true );

    if( $affiliate_id === 'none' ) {
        // If user was marked without any affiliate, return false
        return false;
    } else if( absint( $affiliate_id ) !== 0 ) {
        // Return the affiliate WP_User
        return get_userdata( absint( $affiliate_id ) );
    } else {
        // Try to find the affiliate from the log
        $affiliate_id = gamipress_query_logs( array(
            'select' => 'l.user_id',
            'where' => array(
                'type' 	        => 'referral_signup',
                'referral_id'   => $user_id,
            ),
            'limit' => 1,
            'get_var' => true,
        ) );

        if( absint( $affiliate_id ) !== 0 ) {
            // Update the user affiliate ID to prevent execute the previous query on every check
            gamipress_update_user_meta( $user_id, '_gamipress_referrals_affiliate_id', $affiliate_id );

            return get_userdata( absint( $affiliate_id ) );
        } else {
            // Update the user affiliate ID to none to prevent execute the previous query on every check
            gamipress_update_user_meta( $user_id, '_gamipress_referrals_affiliate_id', 'none' );
        }
    }

    return false;

}

/**
 * Return the referral ID of the given affiliate ID
 *
 * @since 1.0.0
 *
 * @param WP_User|string|int    $affiliate_id
 *
 * @return mixed
 */
function gamipress_referrals_get_affiliate_referral_id( $affiliate_id ) {

    $referral_id = '';
    $affiliate = gamipress_referrals_get_affiliate( $affiliate_id );

    if( $affiliate ) {

        // If affiliate id given is not the affiliate ID, set it now for next filters
        $affiliate_id = $affiliate->ID;

        // Get the affiliate links setting
        $affiliate_links = gamipress_referrals_get_option( 'affiliate_links', 'user_id' );

        if( $affiliate_links === 'user_id' )
            $referral_id = $affiliate->ID;
        else if( $affiliate_links === 'user_login' )
            $referral_id = $affiliate->user_login;

    }

    return apply_filters( 'gamipress_referrals_get_affiliate_referral_id', $referral_id, $affiliate_id, $affiliate );

}

/**
 * Get the current affiliate from the cookie set
 *
 * @since  1.0.6
 *
 * @global integer $user_id
 *
 * @return WP_User|false $page_url Current page URL
 */
function gamipress_referrals_get_affiliate_from_cookie( $user_id ) {

    // Check if user has affiliate
    $affiliate = gamipress_referrals_get_user_affiliate( $user_id );

    if( ! $affiliate ) {
        // Check referral cookies
        $referral = false;

        if ( isset( $_COOKIE['gamipress_referrals_ref'] ) ) {
            $referral = $_COOKIE['gamipress_referrals_ref'];
        }

        // Return if user hasn't been referred
        if ( $referral === false ) return false;

        $affiliate = gamipress_referrals_get_affiliate( $referral );
    }

    // Return if affiliate not found
    if( ! $affiliate ) {
        return false;
    }

    // Return if affiliate is trying to refer himself
    if( $affiliate->ID === $user_id ) return false;

    return $affiliate;

}

/**
 * Return the IP address of the current visitor
 *
 * @since 1.0.0
 *
 * @return string $ip User's IP address
 */
function gamipress_referrals_get_ip() {

    $ip = '127.0.0.1';

    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        //Check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        // Check ip is pass from proxy, can include more than 1 ip, first is the public one
        $ip = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ip[0]);
    } elseif( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Fix potential CSV returned from $_SERVER variables
    $ip_array = explode( ',', $ip );
    $ip_array = array_map( 'trim', $ip_array );

    return apply_filters( 'gamipress_referrals_get_ip', $ip_array[0] );

}

/**
 * Get the current page URL
 *
 * @since  1.0.0
 *
 * @global $post
 *
 * @return string $page_url Current page URL
 */
function gamipress_referrals_get_current_page_url() {

    if ( is_front_page() ) {
        $page_url = home_url();
    } else {
        $protocol =  ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' );

        $page_url = set_url_scheme( $protocol . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] );
    }

    return apply_filters( 'gamipress_referrals_get_current_page_url', $page_url );

}

/**
 * Check if sales should get enabled
 *
 * @since  1.0.6
 *
 * @return bool
 */
function gamipress_referrals_enable_sales() {

    $enable = false;

    return apply_filters( 'gamipress_referrals_enable_sales', $enable );

}