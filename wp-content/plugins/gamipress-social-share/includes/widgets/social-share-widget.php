<?php
/**
 * Social Share Widget
 *
 * @package     GamiPress\Social_Share\Widgets\Widget\Social_Share
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Social_Share_Widget extends GamiPress_Widget {

    /**
     * Shortcode for this widget.
     *
     * @var string
     */
    protected $shortcode = 'gamipress_social_share';

    public function __construct() {
        parent::__construct(
            $this->shortcode . '_widget',
            __( 'GamiPress: Social Share Buttons', 'gamipress-social-share' ),
            __( 'Display buttons to let users share the current post.', 'gamipress-social-share' )
        );
    }

    public function get_tabs() {
        return GamiPress()->shortcodes[$this->shortcode]->tabs;
    }

    public function get_fields() {
        return GamiPress()->shortcodes[$this->shortcode]->fields;
    }

    public function get_widget( $args, $instance ) {

        // Title is rendered from widget
        $instance['title'] = '';

        // Build shortcode attributes from widget instance
        $atts = gamipress_build_shortcode_atts( $this->shortcode, $instance );

        echo gamipress_do_shortcode( $this->shortcode, $atts );
    }

}