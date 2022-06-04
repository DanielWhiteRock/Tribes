<?php
/**
 * Achievement Purchase Form Widget
 *
 * @package     GamiPress\Purchases\Widgets\Widget\Achievement_Purchase_Form
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Achievement_Purchase_Form_Widget extends GamiPress_Widget {

    /**
     * Shortcode for this widget.
     *
     * @var string
     */
    protected $shortcode = 'gamipress_achievement_purchase';

    public function __construct() {
        parent::__construct(
            $this->shortcode . '_widget',
            __( 'GamiPress: Achievement Purchase Form', 'gamipress-purchases' ),
            __( 'Display an achievement purchase form.', 'gamipress-purchases' )
        );
    }

    public function get_tabs() {

        $tabs = GamiPress()->shortcodes[$this->shortcode]->tabs;

        $tabs['form']['fields'][] = 'title';
        $tabs['form']['fields'][] = 'achievement';

        // Add the renamed achievement title field to the achievement tab
        $tabs['achievement']['fields'][] = 'show_title';

        // Get the numeric index of the achievement field 'title'
        $index = array_search( 'title', $tabs['achievement']['fields'] );

        // Remove the title from this tab
        unset( $tabs['achievement']['fields'][$index] );

        return $tabs;

    }

    public function get_fields() {

        // Need to change field id to achievement to avoid problems with GamiPress javascript selectors
        $fields = GamiPress()->shortcodes[$this->shortcode]->fields;

        // Get the fields keys
        $keys = array_keys( $fields );

        // Get the numeric index of the field 'id'
        $index = array_search( 'id', $keys );

        // Replace the 'id' key by 'progress_map_id'
        $keys[$index] = 'achievement';

        // Get the numeric index of the field 'title'
        $index = array_search( 'title', $keys );

        // Replace the 'title' key by 'show_title'
        $keys[$index] = 'show_title';

        // Combine new array with new keys with an array of values
        $fields = array_combine( $keys, array_values( $fields ) );

        return $fields;

    }

    public function get_widget( $args, $instance ) {

        // Get back replaced fields
        $instance['id'] = $instance['achievement'];
        $instance['title'] = $instance['show_title'];

        // Build shortcode attributes from widget instance
        $atts = gamipress_build_shortcode_atts( $this->shortcode, $instance );

        echo gamipress_do_shortcode( $this->shortcode, $atts );

    }

}