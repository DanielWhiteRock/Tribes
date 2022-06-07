<?php
/**
 * Users
 *
 * @package     GamiPress\Purchases\Users
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function gamipress_purchases_get_user_billing_details( $user_id = 0 ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    $user = new WP_User( $user_id );

    $prefix = '_gamipress_purchases_';

    $user_details = array(
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'address_1' => '',
        'address_2' => '',
        'city' => '',
        'postcode' => '',
        'country' => '',
        'state' => '',
    );

    foreach( $user_details as $meta_key => $meta_value ) {
        $user_details[$meta_key] = get_user_meta( $user_id, $prefix . $meta_key, true );
    }

    // Fallback empty values to user values
    if( empty( $user_details['first_name'] ) ) {
        $user_details['first_name'] =  $user->first_name;
    }

    if( empty( $user_details['last_name'] ) ) {
        $user_details['last_name'] =  $user->last_name;
    }

    if( empty( $user_details['email'] ) ) {
        $user_details['email'] =  $user->user_email;
    }

    return apply_filters( 'gamipress_purchases_get_user_billing_details', $user_details, $user_id, $user );

}

function gamipress_purchases_update_user_billing_details( $user_id = 0, $new_user_details = array() ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    $prefix = '_gamipress_purchases_';

    $user_details = gamipress_purchases_get_user_billing_details();

    foreach( $new_user_details as $meta_key => $meta_value ) {
        update_user_meta( $user_id, $prefix . $meta_key, $meta_value, $user_details[$meta_key] );
    }

}

function gamipress_purchases_user_meta_boxes() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_purchases_';

    $countries_options = gamipress_purchases_get_countries();
    $countries_options = array_merge( array( '' => __( 'Choose a country', 'gamipress-purchases' ) ),  $countries_options );

    gamipress_add_meta_box(
        'user-billing-details',
        __( 'Billing Details', 'gamipress-purchases' ),
        'user',
        array(
            $prefix . 'billing-details-title' => array(
                'content' 	=> '<h2>' . __( 'Billing Details', 'gamipress-purchases' ) . '</h2>',
                'type' 	=> 'html',
            ),
            $prefix . 'first_name' => array(
                'name' 	=> __( 'First Name', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            $prefix . 'last_name' => array(
                'name' 	=> __( 'Last Name', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            $prefix . 'email' => array(
                'name' 	=> __( 'Email', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            $prefix . 'address_1' => array(
                'name' 	=> __( 'Address Line 1', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            $prefix . 'address_2' => array(
                'name' 	=> __( 'Address Line 2', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            $prefix . 'city' => array(
                'name' 	=> __( 'City', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            $prefix . 'postcode' => array(
                'name' 	=> __( 'Postcode / ZIP', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
            $prefix . 'country' => array(
                'name' 	=> __( 'Country', 'gamipress-purchases' ),
                'type' 	=> 'select',
                'options' => $countries_options,
            ),
            $prefix . 'state' => array(
                'name' 	=> __( 'State / County', 'gamipress-purchases' ),
                'type' 	=> 'text',
            ),
        )
    );
}
add_action( 'cmb2_admin_init', 'gamipress_purchases_user_meta_boxes' );