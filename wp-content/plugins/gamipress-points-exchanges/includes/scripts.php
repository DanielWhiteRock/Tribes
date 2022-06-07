<?php
/**
 * Scripts
 *
 * @package     GamiPress\Points_Exchanges\Scripts
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_points_exchanges_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Libraries
    wp_register_script( 'gamipress-points-exchanges-functions-js', GAMIPRESS_POINTS_EXCHANGES_URL . 'assets/js/gamipress-points-exchanges-functions' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_POINTS_EXCHANGES_VER, true );

    // Stylesheets
    wp_register_style( 'gamipress-points-exchanges-css', GAMIPRESS_POINTS_EXCHANGES_URL . 'assets/css/gamipress-points-exchanges' . $suffix . '.css', array( ), GAMIPRESS_POINTS_EXCHANGES_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-points-exchanges-js', GAMIPRESS_POINTS_EXCHANGES_URL . 'assets/js/gamipress-points-exchanges' . $suffix . '.js', array( 'jquery', 'gamipress-points-exchanges-functions-js' ), GAMIPRESS_POINTS_EXCHANGES_VER, true );

}
add_action( 'init', 'gamipress_points_exchanges_register_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_points_exchanges_enqueue_scripts( $hook = null ) {

    // Enqueue stylesheets
    if( ! wp_script_is('gamipress-points-exchanges-css') ) {
        wp_enqueue_style( 'gamipress-points-exchanges-css' );
    }

    // Enqueue scripts
    if( ! wp_script_is('gamipress-points-exchanges-js') ) {

        // Setup vars
        $user_id = get_current_user_id();
        $points_types = gamipress_get_points_types();
        $rates = gamipress_points_exchanges_get_all_exchange_rates();
        $user_points = array();

        if( $user_id !== 0 ) {

            // Loop all points types to get the current user points
            foreach( $points_types as $points_type => $data ) {
                $user_points[$points_type] = gamipress_get_user_points( $user_id, $points_type );
            }

        }

        // Localize scripts
        wp_localize_script( 'gamipress-points-exchanges-functions-js', 'gamipress_points_exchanges_functions', array(
            'points_types'  => $points_types,
            'rates'         => $rates,
            'user_points'   => $user_points,
        ) );

        wp_localize_script( 'gamipress-points-exchanges-js', 'gamipress_points_exchanges', array(
            'ajaxurl'           => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
            'acceptance_error'  => __( 'You need to check the acceptance checkbox.', 'gamipress-points-exchanges' ),
        ) );

        wp_enqueue_script( 'gamipress-points-exchanges-functions-js' );
        wp_enqueue_script( 'gamipress-points-exchanges-js' );
    }

}
//add_action( 'wp_enqueue_scripts', 'gamipress_points_exchanges_enqueue_scripts', 100 );

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_points_exchanges_admin_register_scripts( $hook ) {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Libraries
    wp_register_script( 'gamipress-points-exchanges-functions-js', GAMIPRESS_POINTS_EXCHANGES_URL . 'assets/js/gamipress-points-exchanges-functions' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_POINTS_EXCHANGES_VER, true );

    // Stylesheets
    wp_register_style( 'gamipress-points-exchanges-admin-css', GAMIPRESS_POINTS_EXCHANGES_URL . 'assets/css/gamipress-points-exchanges-admin' . $suffix . '.css', array( ), GAMIPRESS_POINTS_EXCHANGES_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-points-exchanges-admin-js', GAMIPRESS_POINTS_EXCHANGES_URL . 'assets/js/gamipress-points-exchanges-admin' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_POINTS_EXCHANGES_VER, true );
    wp_register_script( 'gamipress-points-exchanges-requirements-ui-js', GAMIPRESS_POINTS_EXCHANGES_URL . 'assets/js/gamipress-points-exchanges-requirements-ui' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_POINTS_EXCHANGES_VER, true );

}
add_action( 'admin_init', 'gamipress_points_exchanges_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_points_exchanges_admin_enqueue_scripts( $hook ) {

    global $post_type;

    //Stylesheets
    wp_enqueue_style( 'gamipress-points-exchanges-admin-css' );

    //Scripts
    wp_enqueue_script( 'gamipress-points-exchanges-admin-js' );

    // Enqueue thickbox on points type edit screen
    if ( ( $hook === 'post.php' || $hook === 'post-new.php' ) && $post_type === 'points-type' ) {
        add_thickbox();
    }

    // Requirements ui script
    if ( $post_type === 'points-type'
        || in_array( $post_type, gamipress_get_achievement_types_slugs() )
        || in_array( $post_type, gamipress_get_rank_types_slugs() ) ) {
        wp_enqueue_script( 'gamipress-points-exchanges-requirements-ui-js' );
    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_points_exchanges_admin_enqueue_scripts', 100 );