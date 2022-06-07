<?php
/**
 * Vikinger Child - Functions
 * 
 * @package Vikinger Child
 * @since 1.0.0
 * @author Odin Design Themes (https://odindesignthemes.com/)
 * 
 */

/**
 * Load child theme styles
 */
function vikingerchild_enqueue_styles() {
  wp_enqueue_style('vikingerchild-styles', get_stylesheet_uri(), ['vikinger-styles'], '1.0.0');
}

add_action('wp_enqueue_scripts', 'vikingerchild_enqueue_styles' );

/**
 * Load translations
 */
function vikingerchild_translations_load() {
  load_child_theme_textdomain('vikinger', get_stylesheet_directory() . '/languages');
}

add_action('after_setup_theme', 'vikingerchild_translations_load');

?>



