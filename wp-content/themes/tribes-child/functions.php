<?php 
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    $parenthandle = 'tribes-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(),  // if the parent theme code has a dependency, copy it to here
        $theme->parent()->get('Version')
    );
    wp_enqueue_style( 'tribes-child-style', get_stylesheet_uri(),
        array( $parenthandle ),
        $theme->get('Version') // this only works if you have Version in the style header
    );
}


function wpb_comment_count() { 
    $comments_count = wp_count_comments();
    $message =  ''.  $comments_count->approved . '';
     
    return $message; 
    } 
     
add_shortcode('wpb_total_comments','wpb_comment_count'); 
    
add_filter( 'show_admin_bar', '__return_false' );
    
function admin_default_page() {
    return get_site_url().'/';
}
  
add_filter('login_redirect', 'admin_default_page');

?>