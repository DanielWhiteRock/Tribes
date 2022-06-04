<?php
/**
 * User Data Erasers
 *
 * @package     GamiPress\Purchases\Privacy\Erasers\User_Data
 * @since       1.0.2
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register eraser for user meta data.
 *
 * @since 1.0.2
 *
 * @param array $erasers
 *
 * @return array
 */
function gamipress_purchases_privacy_register_user_data_erasers( $erasers ) {

    $erasers[] = array(
        'eraser_friendly_name'    => __( 'Billing Details', 'gamipress-purchases' ),
        'callback'                => 'gamipress_purchases_privacy_user_data_eraser',
    );

    return $erasers;

}
add_filter( 'wp_privacy_personal_data_erasers', 'gamipress_purchases_privacy_register_user_data_erasers' );

/**
 * Eraser for user meta data.
 *
 * @since 1.0.2
 *
 * @param string    $email_address
 * @param int       $page
 *
 * @return array
 */
function gamipress_purchases_privacy_user_data_eraser( $email_address, $page = 1 ) {

    // Setup vars
    $prefix = '_gamipress_purchases_';

    $user = get_user_by( 'email', $email_address );
    $response = array(
        'items_removed'  => true,
        'items_retained' => false,
        'messages'       => array(),
        'done'           => true
    );

    if ( $user && $user->ID ) {

        // Array of user meta key and label
        $user_details = array(
            'first_name' => __( 'First Name', 'gamipress-purchases' ),
            'last_name' => __( 'Last Name', 'gamipress-purchases' ),
            'email' => __( 'Email', 'gamipress-purchases' ),
            'address_1' => __( 'Address Line 1', 'gamipress-purchases' ),
            'address_2' => __( 'Address Line 2', 'gamipress-purchases' ),
            'city' => __( 'City', 'gamipress-purchases' ),
            'postcode' => __( 'Postcode / ZIP', 'gamipress-purchases' ),
            'country' => __( 'Country', 'gamipress-purchases' ),
            'state' => __( 'State', 'gamipress-purchases' ),
        );

        // Loop all user details to remove them
        foreach( $user_details as $meta_key => $label ) {

            if ( ! gamipress_delete_user_meta( $user->ID, $prefix . $meta_key ) ) {
                $response['messages'][] = sprintf( __( 'Your billing information "%s" was unable to be removed at this time.', 'gamipress-purchases' ), $label );
                $response['items_retained'] = true;
            }

        }

    }

    // Return our removed items
    return $response;

}