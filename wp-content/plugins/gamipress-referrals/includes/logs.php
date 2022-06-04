<?php
/**
 * Logs
 *
 * @package     GamiPress\Referrals\Logs
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin log types
 *
 * @since 1.0.0
 *
 * @param array $gamipress_log_types
 *
 * @return array
 */
function gamipress_referrals_logs_types( $gamipress_log_types ) {

    $gamipress_log_types['referral_visit']  = __( 'Referral Visit', 'gamipress-referrals' );
    $gamipress_log_types['referral_signup'] = __( 'Referral Sign Up', 'gamipress-referrals' );

    if( gamipress_referrals_enable_sales() ) {
        $gamipress_log_types['referral_sale'] = __( 'Referral Sale', 'gamipress-referrals' );
        $gamipress_log_types['referral_sale_refund'] = __( 'Referral Sale Refund', 'gamipress-referrals' );
    }

    return $gamipress_log_types;

}
add_filter( 'gamipress_logs_types', 'gamipress_referrals_logs_types' );

/**
 * Log referral visit on logs
 *
 * @since 1.0.0
 *
 * @param int       $affiliate_id   Affiliate ID
 * @param int       $referral_id    Referral ID
 * @param string    $referral_ip    Referral IP address
 * @param int       $post_id        Post ID
 * @param string    $post_url       Post URL
 * @param string    $referrer       Referrer
 *
 * @return int|false
 */
function gamipress_referrals_log_visit( $affiliate_id, $referral_id, $referral_ip, $post_id, $post_url, $referrer ) {

    // Log meta data
    $log_meta = array(
        'pattern' => __( 'Referral visit from {user}', 'gamipress-referrals' ),
        'referral_id'   => $referral_id,
        'referral_ip'   => $referral_ip,
        'post_id'       => $post_id,
        'post_url'      => $post_url,
        'referrer'      => $referrer,
    );

    // Register the referral visit on logs
    return gamipress_insert_log( 'referral_visit', $affiliate_id, 'private', $log_meta );

}

/**
 * Log referral signup on logs
 *
 * @since 1.0.0
 *
 * @param int       $affiliate_id   Affiliate ID
 * @param int       $referral_id    Referral ID
 * @param string    $referral_ip    Referral IP address
 *
 * @return int
 */
function gamipress_referrals_log_signup( $affiliate_id, $referral_id, $referral_ip ) {

    // Log meta data
    $log_meta = array(
        'pattern'       => __( 'Referral sign up from {user}', 'gamipress-referrals' ),
        'referral_id'   => $referral_id,
        'referral_ip'   => $referral_ip,
    );

    // Register the content unlock on logs
    return gamipress_insert_log( 'referral_signup', $affiliate_id, 'private', $log_meta );

}

/**
 * Log referral sale logs
 *
 * @since 1.0.0
 *
 * @param int       $affiliate_id   Affiliate ID
 * @param int       $referral_id    Referral ID
 * @param string    $referral_ip    Referral IP address
 *
 * @return int
 */
function gamipress_referrals_log_sale( $affiliate_id, $referral_id, $referral_ip, $post_id, $integration ) {

    // Log meta data
    $log_meta = array(
        'pattern'       => __( 'Referral sale from {user}', 'gamipress-referrals' ),
        'referral_id'   => $referral_id,
        'referral_ip'   => $referral_ip,
        'post_id'       => $post_id,
        'integration'   => $integration,
    );

    // Register the content unlock on logs
    return gamipress_insert_log( 'referral_sale', $affiliate_id, 'private', $log_meta );

}

/**
 * Log referral sale logs
 *
 * @since 1.0.0
 *
 * @param int       $affiliate_id   Affiliate ID
 * @param int       $referral_id    Referral ID
 * @param string    $referral_ip    Referral IP address
 *
 * @return int
 */
function gamipress_referrals_log_sale_refund( $affiliate_id, $referral_id, $referral_ip, $post_id, $integration ) {

    // Log meta data
    $log_meta = array(
        'pattern'       => __( 'Referral sale refunded for {user}', 'gamipress-referrals' ),
        'referral_id'   => $referral_id,
        'referral_ip'   => $referral_ip,
        'post_id'       => $post_id,
        'integration'   => $integration,
    );

    // Register the content unlock on logs
    return gamipress_insert_log( 'referral_sale_refund', $affiliate_id, 'private', $log_meta );

}

/**
 * Extra data fields
 *
 * @since 1.0.0
 *
 * @param array     $fields
 * @param int       $log_id
 * @param string    $type
 *
 * @return array
 */
function gamipress_referrals_log_extra_data_fields( $fields, $log_id, $type ) {

    $prefix = '_gamipress_';

    $log = ct_get_object( $log_id );

    switch( $type ) {
        // Referral Visit
        case 'referral_visit':

            $referral_id = ct_get_object_meta( $log_id, $prefix . 'referral_id', true );
            $referral = get_userdata( $referral_id );

            $fields[] = array(
                'name' 	            => __( 'Referral', 'gamipress-referrals' ),
                'desc' 	            => __( 'User referred (just if user was logged in).', 'gamipress-referrals' ),
                'id'   	            => $prefix . 'referral_id',
                'type' 	            => 'select',
                'options'           => array(
                    $referral_id => ( $referral ? $referral->display_name : '' )
                )
            );

            $fields[] = array(
                'name' 	            => __( 'IP', 'gamipress-referrals' ),
                'desc' 	            => __( 'IP of referred user.', 'gamipress-referrals' ),
                'id'   	            => $prefix . 'referral_ip',
                'type' 	            => 'text',
            );

            $fields[] = array(
                'name' 	            => __( 'URL', 'gamipress-referrals' ),
                'desc' 	            => __( 'URL visited by the referred user.', 'gamipress-referrals' ),
                'id'   	            => $prefix . 'post_url',
                'type' 	            => 'text',
            );

            $fields[] = array(
                'name' 	            => __( 'Referring URL', 'gamipress-referrals' ),
                'desc' 	            => __( 'URL from referral comes.', 'gamipress-referrals' ),
                'id'   	            => $prefix . 'referrer',
                'type' 	            => 'text',
            );

            break;
        // Referral Signup
        case 'referral_signup':

            $referral_id = ct_get_object_meta( $log_id, $prefix . 'referral_id', true );
            $referral = get_userdata( $referral_id );

            $fields[] = array(
                'name' 	            => __( 'Referral', 'gamipress-referrals' ),
                'desc' 	            => __( 'User referred.', 'gamipress-referrals' ),
                'id'   	            => $prefix . 'referral_id',
                'type' 	            => 'select',
                'options'           => array(
                    $referral_id => ( $referral ? $referral->display_name : '' )
                )
            );

            $fields[] = array(
                'name' 	            => __( 'IP', 'gamipress-referrals' ),
                'desc' 	            => __( 'IP of referred user.', 'gamipress-referrals' ),
                'id'   	            => $prefix . 'referral_ip',
                'type' 	            => 'text',
            );


            break;
        // Referral Sale
        case 'referral_sale':
        case 'referral_sale_refund':

            $referral_id = ct_get_object_meta( $log_id, $prefix . 'referral_id', true );
            $referral = get_userdata( $referral_id );

            $fields[] = array(
                'name' 	            => __( 'Referral', 'gamipress-referrals' ),
                'desc' 	            => __( 'User referred.', 'gamipress-referrals' ),
                'id'   	            => $prefix . 'referral_id',
                'type' 	            => 'select',
                'options'           => array(
                    $referral_id => ( $referral ? $referral->display_name : '' )
                )
            );

            $fields[] = array(
                'name' 	            => __( 'IP', 'gamipress-referrals' ),
                'desc' 	            => __( 'IP of referred user.', 'gamipress-referrals' ),
                'id'   	            => $prefix . 'referral_ip',
                'type' 	            => 'text',
            );


            break;
    }

    return $fields;

}
add_filter( 'gamipress_log_extra_data_fields', 'gamipress_referrals_log_extra_data_fields', 10, 3 );