<?php
/**
 * Privacy Functions
 *
 * @package     GamiPress\Purchases\Privacy
 * @since       1.0.2
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_PURCHASES_DIR . 'includes/privacy/exporters.php';
require_once GAMIPRESS_PURCHASES_DIR . 'includes/privacy/erasers.php';

/**
 * Return an anonymized email address.
 *
 * While WP Core supports anonymizing email addresses with the wp_privacy_anonymize_data function,
 * it turns every email address into deleted@site.invalid, which does not work when some purchase/customer records
 * are still needed for legal and regulatory reasons.
 *
 * This function will anonymize the email with an MD5 that is salted
 * and given a randomized uniqid prefixed with the store URL in order to prevent connecting a single customer across
 * multiple stores, as well as the timestamp at the time of anonymization (so it trying the same email again will not be
 * repeatable and therefore connected), and return the email address as <hash>@site.invalid.
 *
 * Taken from Easy Digital Downloads.
 *
 * @since 1.0.2
 *
 * @param string $email_address
 *
 * @return string
 */
function gamipress_purchases_anonymize_email( $email_address ) {

    if ( empty( $email_address ) ) {
        return $email_address;
    }

    $email_address    = strtolower( $email_address );
    $email_parts      = explode( '@', $email_address );
    $anonymized_email = wp_hash( uniqid( get_option( 'site_url' ), true ) . $email_parts[0] . current_time( 'timestamp' ), 'nonce' );

    return $anonymized_email . '@site.invalid';
}