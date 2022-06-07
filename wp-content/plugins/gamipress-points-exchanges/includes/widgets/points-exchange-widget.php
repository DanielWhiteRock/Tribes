<?php
/**
 * Points Exchange Form Widget
 *
 * @package     GamiPress\Points_Exchanges\Widgets\Widget\Points_Exchange
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Points_Exchange_Widget extends GamiPress_Widget {

    /**
     * Shortcode for this widget.
     *
     * @var string
     */
    protected $shortcode = 'gamipress_points_exchange';

    public function __construct() {

        parent::__construct(
            $this->shortcode . '_widget',
            __( 'GamiPress: Points Exchange Form', 'gamipress-points-exchanges' ),
            __( 'Display a points exchange form.', 'gamipress-points-exchanges' )
        );

    }

    public function get_fields() {
        return GamiPress()->shortcodes[$this->shortcode]->fields;
    }

    public function get_widget( $args, $instance ) {

        // Build shortcode attributes from widget instance
        $atts = gamipress_build_shortcode_atts( $this->shortcode, $instance );

        echo gamipress_do_shortcode( $this->shortcode, $atts );

    }

}