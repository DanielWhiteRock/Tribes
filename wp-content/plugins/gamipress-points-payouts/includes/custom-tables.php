<?php
/**
 * Custom Tables
 *
 * @package     GamiPress\Points_Payouts\Custom_Tables
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'includes/custom-tables/points-payouts.php';

/**
 * Register all plugin Custom DB Tables
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_points_payouts_register_custom_tables() {

    // Points Payouts Table
    ct_register_table( 'gamipress_points_payouts', array(
        'singular' => __( 'Points Payout', 'gamipress-points-payouts' ),
        'plural' => __( 'Points Payouts', 'gamipress-points-payouts' ),
        'show_ui' => true,
        'version' => 1,
        'global' => gamipress_is_network_wide_active(),
        'capability' => gamipress_get_manager_capability(),
        'supports' => array( 'meta' ),
        'views' => array(
            'list' => array(
                'menu_title' => __( 'Points Payout History', 'gamipress-points-payouts' ),
                'parent_slug' => 'gamipress',
            ),
            'add' => false,
            'edit' => array(
                'show_in_menu' => false,
            ),
        ),
        'schema' => array(
            'points_payout_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'user_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'points' => array(
                'type' => 'bigint',
                'length' => '20',
            ),
            'points_type' => array(
                'type' => 'text',
            ),
            'money' => array(
                'type' => 'double',
                'decimals' => 2,
                'length' => '20',
            ),
            'status' => array(
                'type' => 'text',
            ),
            'date' => array(
                'type' => 'datetime',
                'default' => '0000-00-00 00:00:00'
            ),

        ),
    ) );

}
add_action( 'ct_init', 'gamipress_points_payouts_register_custom_tables' );