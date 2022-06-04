<?php
/**
 * Custom Tables
 *
 * @package     GamiPress\Submissions\Custom_Tables
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_SUBMISSIONS_DIR . 'includes/custom-tables/submissions.php';

/**
 * Register all plugin Custom DB Tables
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_submissions_register_custom_tables() {

    // Submissions Table
    ct_register_table( 'gamipress_submissions', array(
        'singular' => __( 'Submission', 'gamipress-submissions' ),
        'plural' => __( 'Submissions', 'gamipress-submissions' ),
        'show_ui' => true,
        'version' => 1,
        'global' => gamipress_is_network_wide_active(),
        'capability' => gamipress_get_manager_capability(),
        'supports' => array( 'meta' ),
        'views' => array(
            'list' => array(
                'menu_title' => __( 'Submissions', 'gamipress-submissions' ),
                'parent_slug' => 'gamipress',
            ),
            'add' => false,
            'edit' => array(
                'show_in_menu' => false,
            ),
        ),
        'schema' => array(
            'submission_id' => array(
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
            'post_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'notes' => array(
                'type' => 'text',
            ),
            'date' => array(
                'type' => 'datetime',
                'default' => '0000-00-00 00:00:00'
            ),
            'status' => array(
                'type' => 'text',
            ),
        ),
    ) );

}
add_action( 'ct_init', 'gamipress_submissions_register_custom_tables' );