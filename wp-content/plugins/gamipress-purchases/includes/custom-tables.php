<?php
/**
 * Custom Tables
 *
 * @package     GamiPress\Purchases\Custom_Tables
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_PURCHASES_DIR . 'includes/custom-tables/payments.php';
require_once GAMIPRESS_PURCHASES_DIR . 'includes/custom-tables/payment-items.php';
require_once GAMIPRESS_PURCHASES_DIR . 'includes/custom-tables/payment-notes.php';

/**
 * Register all GamiPress Custom DB Tables
 *
 * @since  1.2.8
 *
 * @return void
 */
function gamipress_purchases_register_custom_tables() {

    // Payments Table
    ct_register_table( 'gamipress_payments', array(
        'singular' => __( 'Payment', 'gamipress-purchases' ),
        'plural' => __( 'Payments', 'gamipress-purchases' ),
        'show_ui' => true,
        'version' => 2,
        'global' => gamipress_is_network_wide_active(),
        'supports' => array( 'meta' ),
        'views' => array(
            'list' => array(
                'menu_title' => __( 'Payment History', 'gamipress-purchases' ),
                'parent_slug' => 'gamipress'
            ),
            'add' => array(
                'show_in_menu' => false,
            ),
            'edit' => array(
                'show_in_menu' => false,
            ),
        ),
        'schema' => array(

            // Payment details

            'payment_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'number' => array(
                'type' => 'bigint',
                'length' => '20',
            ),
            'date' => array(
                'type' => 'datetime',
                'default' => '0000-00-00 00:00:00'
            ),
            'status' => array(
                'type' => 'text',
            ),
            'gateway' => array(
                'type' => 'text',
            ),
            'purchase_key' => array(
                'type' => 'text',
            ),
            'transaction_id' => array(
                'type' => 'text',
            ),
            'subtotal' => array(
                'type' => 'text',
            ),
            'tax' => array(
                'type' => 'text',
            ),
            'tax_amount' => array(
                'type' => 'text',
            ),
            'total' => array(
                'type' => 'text',
            ),

            // User details

            'user_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'user_ip' => array(
                'type' => 'text',
            ),
            'first_name' => array(
                'type' => 'text',
            ),
            'last_name' => array(
                'type' => 'text',
            ),
            'email' => array(
                'type' => 'text',
            ),
            'address_1' => array(
                'type' => 'text',
            ),
            'address_2' => array(
                'type' => 'text',
            ),
            'city' => array(
                'type' => 'text',
            ),
            'postcode' => array(
                'type' => 'text',
            ),
            'country' => array(
                'type' => 'text',
            ),
            'state' => array(
                'type' => 'text',
            ),
        ),
    ) );

    // Payment Items Table
    ct_register_table( 'gamipress_payment_items', array(
        'singular' => __( 'Payment Item', 'gamipress-purchases' ),
        'plural' => __( 'Payment Items', 'gamipress-purchases' ),
        'show_ui' => false,
        'version' => 1,
        'global' => gamipress_is_network_wide_active(),
        'supports' => array( 'meta' ),
        'schema' => array(
            'payment_item_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),

            // Relationships

            'payment_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'post_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'post_type' => array(
                'type' => 'varchar',
                'length' => '50',
            ),

            // Fields

            'description' => array(
                'type' => 'text',
            ),
            'quantity' => array(
                'type' => 'bigint',
            ),
            'price' => array(
                'type' => 'text',
            ),
            'total' => array(
                'type' => 'text',
            ),
        ),
    ) );

    // Payment Notes Table
    ct_register_table( 'gamipress_payment_notes', array(
        'singular' => __( 'Payment Note', 'gamipress-purchases' ),
        'plural' => __( 'Payment Notes', 'gamipress-purchases' ),
        'show_ui' => false,
        'version' => 1,
        'global' => gamipress_is_network_wide_active(),
        'supports' => array( 'meta' ),
        'schema' => array(
            'payment_note_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'payment_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),

            // Fields

            'title' => array(
                'type' => 'text',
            ),
            'description' => array(
                'type' => 'text',
            ),
            'user_id' => array(
                'type' => 'bigint',
                'length' => '20',
            ),
            'date' => array(
                'type' => 'datetime',
                'default' => '0000-00-00 00:00:00'
            ),
        ),
    ) );

}
add_action( 'ct_init', 'gamipress_purchases_register_custom_tables' );