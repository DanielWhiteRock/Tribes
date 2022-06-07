<?php
/**
 * Compatibility
 *
 * @package     GamiPress\Purchases\Ajax Compatibility
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/* ------------------------------------
 * GamiPress installs less than 1.3.5
   ------------------------------------*/

// Hook added on 1.3.5
add_action( 'wp_ajax_gamipress_get_achievements_options_html', 'gamipress_achievement_post_ajax_handler' );

// Function added to CT on 1.3.5
if( ! function_exists('ct_reset_setup_table') ) {
    /**
     * Setup the global table
     *
     * @return CT_Table $ct_table
     */
    function ct_reset_setup_table() {

        global $ct_registered_tables, $ct_previous_table, $ct_table;

        if( is_object( $ct_previous_table ) ) {
            $ct_table = $ct_previous_table;
        }

        return $ct_table;

    }
}