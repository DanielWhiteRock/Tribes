<?php
/**
 * Shortcodes
 *
 * @package     GamiPress\Purchases\Shortcodes
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// GamiPress Purchases Shortcodes
require_once GAMIPRESS_PURCHASES_DIR . 'includes/shortcodes/gamipress_points_purchase.php';
require_once GAMIPRESS_PURCHASES_DIR . 'includes/shortcodes/gamipress_achievement_purchase.php';
require_once GAMIPRESS_PURCHASES_DIR . 'includes/shortcodes/gamipress_rank_purchase.php';
require_once GAMIPRESS_PURCHASES_DIR . 'includes/shortcodes/gamipress_purchase_history.php';

/**
 * Register plugin shortcode groups
 *
 * @since 1.0.0
 *
 * @param array $shortcode_groups
 *
 * @return array
 */
function gamipress_purchases_shortcodes_groups( $shortcode_groups ) {

    $shortcode_groups['purchases'] = __( 'Purchases', 'gamipress-purchases' );

    return $shortcode_groups;

}
add_filter( 'gamipress_shortcodes_groups', 'gamipress_purchases_shortcodes_groups' );