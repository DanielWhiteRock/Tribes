<?php
/**
 * Shortcodes
 *
 * @package     GamiPress\Transfers\Shortcodes
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// GamiPress Transfers Shortcodes
require_once GAMIPRESS_TRANSFERS_DIR . 'includes/shortcodes/gamipress_points_transfer.php';
require_once GAMIPRESS_TRANSFERS_DIR . 'includes/shortcodes/gamipress_achievement_transfer.php';
require_once GAMIPRESS_TRANSFERS_DIR . 'includes/shortcodes/gamipress_rank_transfer.php';
require_once GAMIPRESS_TRANSFERS_DIR . 'includes/shortcodes/gamipress_transfer_history.php';

/**
 * Register plugin shortcode groups
 *
 * @since 1.0.0
 *
 * @param array $shortcode_groups
 *
 * @return array
 */
function gamipress_transfers_shortcodes_groups( $shortcode_groups ) {

    $shortcode_groups['transfers'] = __( 'Transfers', 'gamipress-transfers' );

    return $shortcode_groups;

}
add_filter( 'gamipress_shortcodes_groups', 'gamipress_transfers_shortcodes_groups' );