<?php
/**
 * Widgets
 *
 * @package     GamiPress\Points_Payouts\Widgets
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// GamiPress Points Payouts Shortcodes
require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'includes/widgets/points-payout-widget.php';
require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'includes/widgets/points-payout-history-widget.php';

// Register plugin widgets
function gamipress_points_payouts_register_widgets() {

    register_widget( 'gamipress_points_payout_widget' );
    register_widget( 'gamipress_points_payout_history_widget' );

}
add_action( 'widgets_init', 'gamipress_points_payouts_register_widgets' );