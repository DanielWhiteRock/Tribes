<?php
/**
 * Scripts
 *
 * @package     GamiPress\Purchase\Scripts
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
function gamipress_purchases_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Libraries
    wp_register_script( 'gamipress-purchases-functions-js', GAMIPRESS_PURCHASES_URL . 'assets/js/gamipress-purchases-functions' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_PURCHASES_VER, true );

    // Stylesheets
    wp_register_style( 'gamipress-purchases-css', GAMIPRESS_PURCHASES_URL . 'assets/css/gamipress-purchases' . $suffix . '.css', array( ), GAMIPRESS_PURCHASES_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-purchases-js', GAMIPRESS_PURCHASES_URL . 'assets/js/gamipress-purchases' . $suffix . '.js', array( 'jquery', 'gamipress-purchases-functions-js' ), GAMIPRESS_PURCHASES_VER, true );

}
add_action( 'init', 'gamipress_purchases_register_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_purchases_enqueue_scripts( $hook = null ) {

    // Enqueue stylesheets
    if( ! wp_script_is('gamipress-purchases-css') ) {
        wp_enqueue_style( 'gamipress-purchases-css' );
    }

    // Enqueue scripts
    if( ! wp_script_is('gamipress-purchases-js') ) {

        $points_types = gamipress_get_points_types();
        $conversions = array();

        foreach( $points_types as $slug => $data ) {

            $conversions[$slug] = gamipress_purchases_get_conversion( $slug );

        }

        // Localize scripts
        wp_localize_script( 'gamipress-purchases-functions-js', 'gamipress_purchases_functions', array(
            'currency_symbol'       => gamipress_purchases_get_currency_symbol(),
            'currency_position'     => gamipress_purchases_get_option( 'currency_position', 'before' ),
            'thousands_separator'   => gamipress_purchases_get_option( 'thousands_separator', ',' ),
            'decimal_separator'     => gamipress_purchases_get_option( 'decimal_separator', '.' ),
            'decimals'              => absint( gamipress_purchases_get_option( 'decimals', 2 ) ),
            'points_types'          => $points_types,
            'conversions'           => $conversions,
            'enable_taxes'          => (bool) gamipress_purchases_get_option( 'enable_taxes', false ),
            'taxes'                 => gamipress_purchases_get_option( 'taxes', array() ),
            'default_tax'           => gamipress_purchases_get_option( 'default_tax', '' ),
        ) );

        wp_localize_script( 'gamipress-purchases-js', 'gamipress_purchases', array(
            'ajaxurl'           => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
            'acceptance_error'  => __( 'You need to check the acceptance checkbox.', 'gamipress-purchases' ),
        ) );

        wp_enqueue_script( 'gamipress-purchases-functions-js' );
        wp_enqueue_script( 'gamipress-purchases-js' );
    }

}
//add_action( 'wp_enqueue_scripts', 'gamipress_purchases_enqueue_scripts', 100 );

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_purchases_admin_register_scripts( $hook ) {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Libraries
    wp_register_script( 'gamipress-purchases-functions-js', GAMIPRESS_PURCHASES_URL . 'assets/js/gamipress-purchases-functions' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_PURCHASES_VER, true );

    // Stylesheets
    wp_register_style( 'gamipress-purchases-admin-css', GAMIPRESS_PURCHASES_URL . 'assets/css/gamipress-purchases-admin' . $suffix . '.css', array( ), GAMIPRESS_PURCHASES_VER, 'all' );
    wp_register_style( 'gamipress-purchases-admin-payments-css', GAMIPRESS_PURCHASES_URL . 'assets/css/gamipress-purchases-admin-payments' . $suffix . '.css', array( ), GAMIPRESS_PURCHASES_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-purchases-admin-js', GAMIPRESS_PURCHASES_URL . 'assets/js/gamipress-purchases-admin' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_PURCHASES_VER, true );
    wp_register_script( 'gamipress-purchases-admin-user-js', GAMIPRESS_PURCHASES_URL . 'assets/js/gamipress-purchases-admin-user' . $suffix . '.js', array( 'jquery', 'gamipress-select2-js' ), GAMIPRESS_PURCHASES_VER, true );
    wp_register_script( 'gamipress-purchases-admin-settings-js', GAMIPRESS_PURCHASES_URL . 'assets/js/gamipress-purchases-admin-settings' . $suffix . '.js', array( 'jquery', 'gamipress-select2-js' ), GAMIPRESS_PURCHASES_VER, true );
    wp_register_script( 'gamipress-purchases-admin-payments-js', GAMIPRESS_PURCHASES_URL . 'assets/js/gamipress-purchases-admin-payments' . $suffix . '.js', array( 'jquery', 'gamipress-purchases-functions-js', 'gamipress-select2-js' ), GAMIPRESS_PURCHASES_VER, true );
    wp_register_script( 'gamipress-purchases-shortcodes-editor-js', GAMIPRESS_PURCHASES_URL . 'assets/js/gamipress-purchases-shortcodes-editor' . $suffix . '.js', array( 'jquery', 'gamipress-select2-js' ), GAMIPRESS_PURCHASES_VER, true );
    wp_register_script( 'gamipress-purchases-widgets-js', GAMIPRESS_PURCHASES_URL . 'assets/js/gamipress-purchases-widgets' . $suffix . '.js', array( 'jquery', 'gamipress-select2-js' ), GAMIPRESS_PURCHASES_VER, true );
    wp_register_script( 'gamipress-purchases-requirements-ui-js', GAMIPRESS_PURCHASES_URL . 'assets/js/gamipress-purchases-requirements-ui' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_PURCHASES_VER, true );

}
add_action( 'admin_init', 'gamipress_purchases_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_purchases_admin_enqueue_scripts( $hook ) {

    global $post_type;

    //Stylesheets
    wp_enqueue_style( 'gamipress-purchases-admin-css' );

    //Scripts
    wp_enqueue_script( 'gamipress-purchases-admin-js' );

    // User
    if( $hook === 'profile.php' || $hook === 'user-edit.php' ) {
        wp_enqueue_style( 'gamipress-select2-css' );
        wp_enqueue_script( 'gamipress-select2-js' );

        wp_enqueue_script( 'gamipress-purchases-admin-user-js' );
    }

    // Settings
    if( $hook === 'gamipress_page_gamipress_settings' ) {
        wp_enqueue_script( 'gamipress-purchases-admin-settings-js' );
    }

    // Payment add/edit screen
    if( $hook === 'gamipress_page_gamipress_payments' || $hook === 'admin_page_edit_gamipress_payments' ) {

        $points_types = gamipress_get_points_types();
        $achievement_types = gamipress_get_achievement_types();
        $rank_types = gamipress_get_rank_types();
        $conversions = array();

        foreach( $points_types as $slug => $data ) {

            $conversions[$slug] = gamipress_purchases_get_conversion( $slug );

        }

        // Localize scripts
        wp_localize_script( 'gamipress-purchases-functions-js', 'gamipress_purchases_functions', array(
            'currency_symbol'       => gamipress_purchases_get_currency_symbol(),
            'currency_position'     => gamipress_purchases_get_option( 'currency_position', 'before' ),
            'thousands_separator'   => gamipress_purchases_get_option( 'thousands_separator', ',' ),
            'decimal_separator'     => gamipress_purchases_get_option( 'decimal_separator', '.' ),
            'decimals'              => absint( gamipress_purchases_get_option( 'decimals', 2 ) ),
            'points_types'          => $points_types,
            'conversions'           => $conversions,
        ) );

        wp_localize_script( 'gamipress-purchases-admin-payments-js', 'gamipress_purchases_payments', array(
            'nonce' => gamipress_get_admin_nonce(),
            'points_types' => $points_types,
            'achievement_types' => $achievement_types,
            'rank_types' => $rank_types,
            'admin_url' => admin_url(),
            'strings' => array(
                'no_assignment' => sprintf(
                    __( 'Not assigned to anything, %s', 'gamipress-purchases' ),
                    '<a href="#" class="gamipress-purchases-assign-post-to-item">' . __( 'assign post', 'gamipress-purchases' ) . '</a>'
                ),
                'assignment' => sprintf(
                    __( 'Assigned to %s, %s or %s', 'gamipress-purchases' ),
                    '{item_link}',
                    '<a href="#" class="gamipress-purchases-assign-post-to-item">' . __( 'change assignment', 'gamipress-purchases' ) . '</a>',
                    '<a href="#" class="gamipress-purchases-unassign-post-to-item">' . __( 'remove assignment', 'gamipress-purchases' ) . '</a>'
                ),
            ),
        ) );

        //Stylesheets
        wp_enqueue_style( 'gamipress-purchases-admin-payments-css' );
        wp_enqueue_style( 'gamipress-select2-css' );

        //Scripts
        wp_enqueue_script( 'gamipress-purchases-functions-js' );
        wp_enqueue_script( 'gamipress-purchases-admin-payments-js' );
    }

    // Just enqueue on add/edit views and on post types that supports editor feature
    if( ( $hook === 'post.php' || $hook === 'post-new.php' ) && post_type_supports( $post_type, 'editor' ) ) {
        wp_enqueue_script( 'gamipress-purchases-shortcodes-editor-js' );
    }

    // Widgets scripts
    if( $hook === 'widgets.php' ) {
        wp_enqueue_style( 'gamipress-select2-css' );
        wp_enqueue_script( 'gamipress-select2-js' );

        wp_enqueue_script( 'gamipress-purchases-widgets-js' );
    }

    // Requirements ui script
    if ( $post_type === 'points-type'
        || in_array( $post_type, gamipress_get_achievement_types_slugs() )
        || in_array( $post_type, gamipress_get_rank_types_slugs() ) ) {

        wp_localize_script( 'gamipress-purchases-requirements-ui-js', 'gamipress_purchases_requirements_ui', array(
            'nonce' => gamipress_get_admin_nonce(),
        ) );

        wp_enqueue_script( 'gamipress-purchases-requirements-ui-js' );
    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_purchases_admin_enqueue_scripts', 100 );