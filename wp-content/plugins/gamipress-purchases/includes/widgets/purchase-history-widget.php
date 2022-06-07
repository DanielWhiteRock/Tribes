<?php
/**
 * Purchase History Widget
 *
 * @package     GamiPress\Purchases\Widgets\Widget\Purchase_History
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Purchase_History_Widget extends GamiPress_Widget {

    /**
     * Shortcode for this widget.
     *
     * @var string
     */
    protected $shortcode = 'gamipress_purchase_history';

    public function __construct() {
        parent::__construct(
            $this->shortcode . '_widget',
            __( 'GamiPress: Purchase History', 'gamipress-purchases' ),
            __( 'Display the purchase history of the current logged in user or the provided user.', 'gamipress-purchases' )
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