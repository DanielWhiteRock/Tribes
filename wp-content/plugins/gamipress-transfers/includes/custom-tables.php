<?php
/**
 * Custom Tables
 *
 * @package     GamiPress\Transfers\Custom_Tables
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_TRANSFERS_DIR . 'includes/custom-tables/transfers.php';
require_once GAMIPRESS_TRANSFERS_DIR . 'includes/custom-tables/transfer-items.php';
require_once GAMIPRESS_TRANSFERS_DIR . 'includes/custom-tables/transfer-notes.php';

/**
 * Register all plugin Custom DB Tables
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_transfers_register_custom_tables() {

    // Transfers Table
    ct_register_table( 'gamipress_transfers', array(
        'singular' => __( 'Transfer', 'gamipress-transfers' ),
        'plural' => __( 'Transfers', 'gamipress-transfers' ),
        'show_ui' => true,
        'version' => 1,
        'global' => gamipress_is_network_wide_active(),
        'supports' => array( 'meta' ),
        'views' => array(
            'list' => array(
                'menu_title' => __( 'Transfer History', 'gamipress-transfers' ),
                'parent_slug' => 'gamipress',
                'capability' => gamipress_get_manager_capability(),
            ),
            'add' => array(
                'show_in_menu' => false,
            ),
            'edit' => array(
                'show_in_menu' => false,
            ),
        ),
        'schema' => array(

            // Transfer details

            'transfer_id' => array(
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
            'transfer_key' => array(
                'type' => 'bigint',
                'length' => '20',
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

            // Recipient ID

            'recipient_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),

        ),
    ) );

    // Transfer Items Table
    ct_register_table( 'gamipress_transfer_items', array(
        'singular' => __( 'Transfer Item', 'gamipress-transfers' ),
        'plural' => __( 'Transfer Items', 'gamipress-transfers' ),
        'show_ui' => false,
        'version' => 1,
        'global' => gamipress_is_network_wide_active(),
        'supports' => array( 'meta' ),
        'schema' => array(
            'transfer_item_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),

            // Relationships

            'transfer_id' => array(
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
        ),
    ) );

    // Transfer Notes Table
    ct_register_table( 'gamipress_transfer_notes', array(
        'singular' => __( 'Transfer Note', 'gamipress-transfers' ),
        'plural' => __( 'Transfer Notes', 'gamipress-transfers' ),
        'show_ui' => false,
        'version' => 1,
        'global' => gamipress_is_network_wide_active(),
        'supports' => array( 'meta' ),
        'schema' => array(
            'transfer_note_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'transfer_id' => array(
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
add_action( 'ct_init', 'gamipress_transfers_register_custom_tables' );