<?php
/**
 * Meta Boxes
 *
 * @package     GamiPress\Points_Exchanges\Admin\Meta_Boxes
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register custom meta boxes used throughout GamiPress
 *
 * @since  1.0.0
 */
function gamipress_points_exchanges_meta_boxes() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_points_exchanges_';

    gamipress_add_meta_box(
        'points-type-exchanges-rates',
        __( 'Exchange Rates', 'gamipress-exchanges-rates' ),
        'points-type',
        array(
            $prefix . 'exchanges_enabled' => array(
                'name' 	        => __( 'Enable Exchanges', 'gamipress-exchanges-rates' ),
                'desc' 	        => __( 'Check this option to turn this points type available for exchanges between other points types.', 'gamipress-exchanges-rates' ),
                'type' 	        => 'checkbox',
                'classes'       => 'gamipress-switch'
            ),
            $prefix . 'rates' => array(
                'name' 	        => __( 'Exchange Rates', 'gamipress-exchanges-rates' ),
                'desc' 	        => __( 'Setup the exchanges rates used when user exchanges points. Set up any rate to 0 to disable exchanges between points types.', 'gamipress-exchanges-rates' )
                                . '<br>'
                                . '<a id="gamipress-points-exchanges-rates-table-link" href="#TB_inline?height=500&inlineId=gamipress_points_exchanges_rates_table" class="thickbox" data-width="800">' . __( 'See all points exchange rates', 'gamipress-exchanges-rates' ) . '</a>',
                'type' 	        => 'points_exchanges_rates',
                'show_names'    => false
            ),
        )
    );

}
add_action( 'cmb2_admin_init', 'gamipress_points_exchanges_meta_boxes' );

function gamipress_points_exchanges_admin_render_rates_table() {

    ?>

    <div id="gamipress_points_exchanges_rates_table" style="display:none;">
        <h3><?php _e( 'Exchange Rates', 'gamipress-exchanges-rates' ); ?></h3>
        <p><?php _e( '<strong>Note:</strong> This table just display points types that has exchange rates enabled.', 'gamipress-exchanges-rates' ) ?></p>
        <?php echo gamipress_do_shortcode( 'gamipress_points_exchange_rates', array( 'points_type' => 'all' ) ); ?>
    </div>

    <?php

}
add_action( 'admin_footer', 'gamipress_points_exchanges_admin_render_rates_table' );