<?php
/**
 * Functions
 *
 * @package GamiPress\Social_Share\Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get available social networks
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_social_share_get_social_networks() {

    return apply_filters( 'gamipress_social_share_get_social_networks', array(
        'twitter'           => __( 'Twitter', 'gamipress-social-share' ),
        'facebook'          => __( 'Facebook (Like/Recommend)', 'gamipress-social-share' ),
        'facebook_share'    => __( 'Facebook (Share)', 'gamipress-social-share' ),
        'linkedin'          => __( 'LinkedIn', 'gamipress-social-share' ),
        'pinterest'         => __( 'Pinterest', 'gamipress-social-share' ),
    ) );

}