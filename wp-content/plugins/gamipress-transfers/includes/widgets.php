<?php
/**
 * Widgets
 *
 * @package     GamiPress\Transfers\Widgets
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// GamiPress Transfers Widgets
require_once GAMIPRESS_TRANSFERS_DIR . 'includes/widgets/points-transfer-widget.php';
require_once GAMIPRESS_TRANSFERS_DIR . 'includes/widgets/achievement-transfer-widget.php';
require_once GAMIPRESS_TRANSFERS_DIR . 'includes/widgets/rank-transfer-widget.php';
require_once GAMIPRESS_TRANSFERS_DIR . 'includes/widgets/transfer-history-widget.php';

// Register plugin widgets
function gamipress_transfers_register_widgets() {
    register_widget( 'gamipress_points_transfer_widget' );
    register_widget( 'gamipress_achievement_transfer_widget' );
    register_widget( 'gamipress_rank_transfer_widget' );
    register_widget( 'gamipress_transfer_history_widget' );
}
add_action( 'widgets_init', 'gamipress_transfers_register_widgets' );