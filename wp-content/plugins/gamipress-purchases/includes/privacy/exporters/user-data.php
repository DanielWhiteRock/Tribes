<?php
/**
 * User Data Exporters
 *
 * @package     GamiPress\Purchases\Privacy\Exporters\User_Data
 * @since       1.0.2
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register exporter for user meta data.
 *
 * @since 1.0.2
 *
 * @param array $exporters
 *
 * @return array
 */
function gamipress_purchases_privacy_register_user_data_exporters( $exporters ) {

    $exporters[] = array(
        'exporter_friendly_name'    => __( 'Billing Details', 'gamipress' ),
        'callback'                  => 'gamipress_purchases_privacy_user_data_exporter',
    );

    return $exporters;

}
add_filter( 'wp_privacy_personal_data_exporters', 'gamipress_purchases_privacy_register_user_data_exporters' );

/**
 * Exporter for user meta data.
 *
 * @since 1.0.2
 *
 * @param string    $email_address
 * @param int       $page
 *
 * @return array
 */
function gamipress_purchases_privacy_user_data_exporter( $email_address, $page = 1 ) {

    // Setup vars
    $export_items   = array();

    $user = get_user_by( 'email', $email_address );

    if ( $user && $user->ID ) {

        $billing_details = gamipress_purchases_get_user_billing_details( $user->ID );

        $countries = gamipress_purchases_get_countries();

        $user_billing_details = array(
            'first_name' => array(
                'name' 	=> __( 'First Name', 'gamipress-purchases' ),
                'value' => $billing_details['first_name'],
            ),
            'last_name' => array(
                'name' 	=> __( 'Last Name', 'gamipress-purchases' ),
                'value' => $billing_details['last_name'],
            ),
            'email' => array(
                'name' 	=> __( 'Email', 'gamipress-purchases' ),
                'value' => $billing_details['email'],
            ),
            'address_1' => array(
                'name' 	=> __( 'Address Line 1', 'gamipress-purchases' ),
                'value' => $billing_details['address_1'],
            ),
            'address_2' => array(
                'name' 	=> __( 'Address Line 2', 'gamipress-purchases' ),
                'value' => $billing_details['address_2'],
            ),
            'city' => array(
                'name' 	=> __( 'City', 'gamipress-purchases' ),
                'value' => $billing_details['city'],
            ),
            'postcode' => array(
                'name' 	=> __( 'Postcode / ZIP', 'gamipress-purchases' ),
                'value' => $billing_details['postcode'],
            ),
            'country' => array(
                'name' 	=> __( 'Country', 'gamipress-purchases' ),
                'value' => isset( $countries[$billing_details['country']] ) ? $countries[$billing_details['country']] : $billing_details['country'],
            ),
            'state' => array(
                'name' 	=> __( 'State / County', 'gamipress-purchases' ),
                'value' => $billing_details['state'],
            ),
        );

        /**
         * User billing details to export
         *
         * @param array     $user_billing_details   The user billing details data to export
         * @param int       $user_id                The user ID
         * @param string    $billing_details        The user billing details
         */
        $user_billing_details = apply_filters( 'gamipress_purchases_privacy_get_user_billing_details', $user_billing_details, $user->ID, $billing_details );

        $export_items[] = array(
            'group_id'    => 'gamipress-purchases-billing-details',
            'group_label' => __( 'Billing Details', 'gamipress' ),
            'item_id'     => "gamipress-purchases-billing-details-{$user->ID}",
            'data'        => $user_billing_details
        );

    }

    // Return our exported items
    return array(
        'data' => $export_items,
        'done' => true
    );

}