<?php
/**
 * Scripts
 *
 * @package     GamiPress\Transfers\Scripts
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
function gamipress_transfers_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-transfers-css', GAMIPRESS_TRANSFERS_URL . 'assets/css/gamipress-transfers' . $suffix . '.css', array( ), GAMIPRESS_TRANSFERS_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-transfers-js', GAMIPRESS_TRANSFERS_URL . 'assets/js/gamipress-transfers' . $suffix . '.js', array( 'jquery', 'jquery-ui-autocomplete' ), GAMIPRESS_TRANSFERS_VER, true );

}
add_action( 'init', 'gamipress_transfers_register_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_transfers_enqueue_scripts( $hook = null ) {

    // Enqueue stylesheets
    if( ! wp_script_is('gamipress-transfers-css') ) {
        wp_enqueue_style( 'gamipress-transfers-css' );
    }

    // Enqueue scripts
    if( ! wp_script_is('gamipress-transfers-js') ) {

        // Localize scripts
        wp_localize_script( 'gamipress-transfers-js', 'gamipress_transfers', array(
            'ajaxurl'                   => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
            'nonce'                     => wp_create_nonce( 'gamipress_transfers' ),
            'no_recipient_error'        => __( 'Please, choose someone to make the transfer', 'gamipress-transfers' ),
            'insufficient_amount_error' => __( 'You don\'t have the required amount to make the transfer', 'gamipress-transfers' ),
        ) );

        wp_enqueue_script( 'gamipress-transfers-js' );
    }

}
//add_action( 'wp_enqueue_scripts', 'gamipress_transfers_enqueue_scripts', 100 );

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_transfers_admin_register_scripts( $hook ) {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-transfers-admin-css', GAMIPRESS_TRANSFERS_URL . 'assets/css/gamipress-transfers-admin' . $suffix . '.css', array( ), GAMIPRESS_TRANSFERS_VER, 'all' );
    wp_register_style( 'gamipress-transfers-admin-transfers-css', GAMIPRESS_TRANSFERS_URL . 'assets/css/gamipress-transfers-admin-transfers' . $suffix . '.css', array( ), GAMIPRESS_TRANSFERS_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-transfers-admin-js', GAMIPRESS_TRANSFERS_URL . 'assets/js/gamipress-transfers-admin' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_TRANSFERS_VER, true );
    wp_register_script( 'gamipress-transfers-admin-transfers-js', GAMIPRESS_TRANSFERS_URL . 'assets/js/gamipress-transfers-admin-transfers' . $suffix . '.js', array( 'jquery', 'gamipress-admin-functions-js', 'gamipress-select2-js' ), GAMIPRESS_TRANSFERS_VER, true );
    wp_register_script( 'gamipress-transfers-shortcodes-editor-js', GAMIPRESS_TRANSFERS_URL . 'assets/js/gamipress-transfers-shortcodes-editor' . $suffix . '.js', array( 'jquery', 'gamipress-admin-functions-js', 'gamipress-select2-js' ), GAMIPRESS_TRANSFERS_VER, true );
    wp_register_script( 'gamipress-transfers-widgets-js', GAMIPRESS_TRANSFERS_URL . 'assets/js/gamipress-transfers-widgets' . $suffix . '.js', array( 'jquery', 'gamipress-admin-functions-js', 'gamipress-select2-js' ), GAMIPRESS_TRANSFERS_VER, true );
    wp_register_script( 'gamipress-transfers-requirements-ui-js', GAMIPRESS_TRANSFERS_URL . 'assets/js/gamipress-transfers-requirements-ui' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_TRANSFERS_VER, true );

}
add_action( 'admin_init', 'gamipress_transfers_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_transfers_admin_enqueue_scripts( $hook ) {

    global $post_type;

    //Stylesheets
    wp_enqueue_style( 'gamipress-transfers-admin-css' );

    //Scripts
    wp_enqueue_script( 'gamipress-transfers-admin-js' );

    // Transfer add/edit screen
    if( $hook === 'gamipress_page_gamipress_transfers' || $hook === 'admin_page_edit_gamipress_transfers' ) {

        $points_types = gamipress_get_points_types();
        $achievement_types = gamipress_get_achievement_types();
        $rank_types = gamipress_get_rank_types();

        // Localize scripts
        wp_localize_script( 'gamipress-transfers-admin-transfers-js', 'gamipress_transfers_transfers', array(
            'nonce' => gamipress_get_admin_nonce(),
            'points_types' => $points_types,
            'achievement_types' => $achievement_types,
            'rank_types' => $rank_types,
            'admin_url' => admin_url(),
            'strings' => array(
                'no_assignment' => sprintf(
                    __( 'Not assigned to anything, %s', 'gamipress-transfers' ),
                    '<a href="#" class="gamipress-transfers-assign-post-to-item">' . __( 'assign post', 'gamipress-transfers' ) . '</a>'
                ),
                'assignment' => sprintf(
                    __( 'Assigned to %s, %s or %s', 'gamipress-transfers' ),
                    '{item_link}',
                    '<a href="#" class="gamipress-transfers-assign-post-to-item">' . __( 'change assignment', 'gamipress-transfers' ) . '</a>',
                    '<a href="#" class="gamipress-transfers-unassign-post-to-item">' . __( 'remove assignment', 'gamipress-transfers' ) . '</a>'
                ),
            ),
        ) );

        //Stylesheets
        wp_enqueue_style( 'gamipress-transfers-admin-transfers-css' );
        wp_enqueue_style( 'gamipress-select2-css' );

        //Scripts
        wp_enqueue_script( 'gamipress-transfers-admin-transfers-js' );
    }

    // Just enqueue on add/edit views and on post types that supports editor feature
    if( ( $hook === 'post.php' || $hook === 'post-new.php' ) && post_type_supports( $post_type, 'editor' ) ) {
        wp_enqueue_script( 'gamipress-transfers-shortcodes-editor-js' );
    }

    // Widgets scripts
    if( $hook === 'widgets.php' ) {
        wp_enqueue_script( 'gamipress-transfers-widgets-js' );
    }

    // Requirements ui script
    if ( $post_type === 'points-type'
        || in_array( $post_type, gamipress_get_achievement_types_slugs() )
        || in_array( $post_type, gamipress_get_rank_types_slugs() ) ) {

        // Localize scripts
        wp_localize_script( 'gamipress-transfers-requirements-ui-js', 'gamipress_transfers_requirements_ui', array(
            'nonce' => gamipress_get_admin_nonce(),
        ) );

        wp_enqueue_script( 'gamipress-transfers-requirements-ui-js' );
    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_transfers_admin_enqueue_scripts', 100 );