<?php
/**
 * Points Transfer Widget
 *
 * @package     GamiPress\Transfers\Widgets\Widget\Points_Transfer
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Points_Transfer_Widget extends GamiPress_Widget {

    /**
     * Shortcode for this widget.
     *
     * @var string
     */
    protected $shortcode = 'gamipress_points_transfer';

    public function __construct() {
        parent::__construct(
            $this->shortcode . '_widget',
            __( 'GamiPress: Points Transfer', 'gamipress-transfers' ),
            __( 'Display a points transfer form.', 'gamipress-transfers' )
        );
    }

    public function get_tabs() {
        $tabs = GamiPress()->shortcodes[$this->shortcode]->tabs;

        $tabs['form']['fields'][] = 'title';

        return $tabs;
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