<?php
/**
 * Widgets
 *
 * @package     GamiPress\Points_Exchanges\Widgets
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// GamiPress Points Exchanges Widgets
require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/widgets/points-exchange-widget.php';
require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/widgets/points-exchange-rates-widget.php';

// Register plugin widgets
function gamipress_points_exchanges_register_widgets() {

    register_widget( 'gamipress_points_exchange_widget' );
    register_widget( 'gamipress_points_exchange_rates_widget' );

}
add_action( 'widgets_init', 'gamipress_points_exchanges_register_widgets' );