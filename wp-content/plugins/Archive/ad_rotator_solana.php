<?php

/*
  Plugin Name: Ad Rotator
  Plugin URI: thetribes.io
  Version: 0.1

*/


add_shortcode('ad_rotator','ad_rotator');

function ad_rotator($atts = array(), $content = null, $tag = 'mint_candy')
{
  ob_start();
  ?>
  <div id="root">Loading </div>
  <?php wp_enqueue_script('ad_rotator', plugins_url('build/static/js/main.c0bbb864.js', __FILE__),array('wp-element'),time(),true); ?>
  <?php wp_enqueue_style('ad_rotator', plugins_url('build/static/js/main.15c86e83.css', __FILE__)); ?>
  <?php return ob_get_clean();
}
