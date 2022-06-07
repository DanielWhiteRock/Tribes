<?php
/**
 * Widgets
 *
 * @package     GamiPress\Purchases\Widgets
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// GamiPress Purchases Widgets
require_once GAMIPRESS_PURCHASES_DIR . 'includes/widgets/points-purchase-form-widget.php';
require_once GAMIPRESS_PURCHASES_DIR . 'includes/widgets/achievement-purchase-form-widget.php';
require_once GAMIPRESS_PURCHASES_DIR . 'includes/widgets/rank-purchase-form-widget.php';
require_once GAMIPRESS_PURCHASES_DIR . 'includes/widgets/purchase-history-widget.php';

// Register plugin widgets
function gamipress_purchases_register_widgets() {
    register_widget( 'gamipress_points_purchase_form_widget' );
    register_widget( 'gamipress_achievement_purchase_form_widget' );
    register_widget( 'gamipress_rank_purchase_form_widget' );
    register_widget( 'gamipress_purchase_history_widget' );
}
add_action( 'widgets_init', 'gamipress_purchases_register_widgets' );