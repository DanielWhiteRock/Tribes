<?php
/**
 * Points Purchase Form Widget
 *
 * @package     GamiPress\Purchases\Widgets\Widget\Points_Purchase_Form
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Points_Purchase_Form_Widget extends GamiPress_Widget {

    /**
     * Shortcode for this widget.
     *
     * @var string
     */
    protected $shortcode = 'gamipress_points_purchase';

    public function __construct() {
        parent::__construct(
            $this->shortcode . '_widget',
            __( 'GamiPress: Points Purchase Form', 'gamipress-purchases' ),
            __( 'Display a points purchase form.', 'gamipress-purchases' )
        );
    }

    public function get_fields() {
        return GamiPress()->shortcodes[$this->shortcode]->fields;
    }

    public function get_widget( $args, $instance ) {

        if( is_array( $instance['options'] ) )
            $instance['options'] = implode( ',', $instance['options'] );

        // Build shortcode attributes from widget instance
        $atts = gamipress_build_shortcode_atts( $this->shortcode, $instance );

        echo gamipress_do_shortcode( $this->shortcode, $atts );

    }

}