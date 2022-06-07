<?php
/**
 * Plugin Name:     GamiPress - WooCommerce Points Gateway
 * Plugin URI:      https://gamipress.com/add-ons/gamipress-wc-points-gateway
 * Description:     Use GamiPress points types as a payment gateway for WooCommerce.
 * Version:         1.1.4
 * Author:          GamiPress
 * Author URI:      https://gamipress.com/
 * Text Domain:     gamipress-wc-points-gateway
 * WC requires at least:  3.0
 * WC tested up to:       6.4
 * License:         GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package         GamiPress\WooCommerce\Points_Gateway
 * @author          GamiPress <contact@gamipress.com>
 * @copyright       Copyright (c) GamiPress
 */

final class GamiPress_WooCommerce_Points_Gateway {

    /**
     * @var         GamiPress_WooCommerce_Points_Gateway $instance The one true GamiPress_WooCommerce_Points_Gateway
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_WooCommerce_Points_Gateway self::$instance The one true GamiPress_WooCommerce_Points_Gateway
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new GamiPress_WooCommerce_Points_Gateway();
            self::$instance->constants();
            self::$instance->classes();
            self::$instance->includes();
            self::$instance->hooks();
            self::$instance->load_textdomain();
        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'GAMIPRESS_WC_POINTS_GATEWAY_VER', '1.1.4' );

        // GamiPress minimum required version
        define( 'GAMIPRESS_WC_POINTS_GATEWAY_GAMIPRESS_MIN_VER', '2.0.0' );

        // Plugin file
        define( 'GAMIPRESS_WC_POINTS_GATEWAY_FILE', __FILE__ );

        // Plugin path
        define( 'GAMIPRESS_WC_POINTS_GATEWAY_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_WC_POINTS_GATEWAY_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin classes
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function classes() {

        if( $this->meets_requirements() ) {

            require_once GAMIPRESS_WC_POINTS_GATEWAY_DIR . 'classes/class-gamipress-wc-points-gateway.php';

        }
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            require_once GAMIPRESS_WC_POINTS_GATEWAY_DIR . 'includes/admin.php';
            require_once GAMIPRESS_WC_POINTS_GATEWAY_DIR . 'includes/functions.php';
            require_once GAMIPRESS_WC_POINTS_GATEWAY_DIR . 'includes/gateway.php';
            require_once GAMIPRESS_WC_POINTS_GATEWAY_DIR . 'includes/scripts.php';
            require_once GAMIPRESS_WC_POINTS_GATEWAY_DIR . 'includes/template-functions.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {
        // Setup our activation and deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Activation hook for the plugin.
     *
     * @since  1.0.0
     */
    function activate() {

        if( $this->meets_requirements() ) {

        }

    }

    /**
     * Deactivation hook for the plugin.
     *
     * @since  1.0.0
     */
    function deactivate() {

    }

    /**
     * Plugin admin notices.
     *
     * @since  1.0.0
     */
    public function admin_notices() {

        if ( ! $this->meets_requirements() && ! defined( 'GAMIPRESS_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php printf(
                        __( 'GamiPress - WooCommerce Points Gateway requires %s (%s or higher) and %s in order to work. Please install and activate them.', 'gamipress-wc-points-gateway' ),
                        '<a href="https://wordpress.org/plugins/gamipress/" target="_blank">GamiPress</a>',
                        GAMIPRESS_WC_POINTS_GATEWAY_GAMIPRESS_MIN_VER,
                        '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>'
                    ); ?>
                </p>
            </div>

            <?php define( 'GAMIPRESS_ADMIN_NOTICES', true ); ?>

        <?php endif;

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( class_exists( 'GamiPress' ) && version_compare( GAMIPRESS_VER, GAMIPRESS_WC_POINTS_GATEWAY_GAMIPRESS_MIN_VER, '>=' )
            && class_exists( 'WooCommerce' ) ) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Internationalization
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function load_textdomain() {
        // Set filter for language directory
        $lang_dir = GAMIPRESS_WC_POINTS_GATEWAY_DIR . '/languages/';
        $lang_dir = apply_filters( 'gamipress_wc_points_gateway_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'gamipress-wc-points-gateway' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'gamipress-wc-points-gateway', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/gamipress-wc-points-gateway/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/gamipress/ folder
            load_textdomain( 'gamipress-wc-points-gateway', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/gamipress/languages/ folder
            load_textdomain( 'gamipress-wc-points-gateway', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'gamipress-wc-points-gateway', false, $lang_dir );
        }
    }

}

/**
 * The main function responsible for returning the one true GamiPress_WooCommerce_Points_Gateway instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_WooCommerce_Points_Gateway The one true GamiPress_WooCommerce_Points_Gateway
 */
function GamiPress_WC_Points_Gateway() {
    return GamiPress_WooCommerce_Points_Gateway::instance();
}
add_action( 'plugins_loaded', 'GamiPress_WC_Points_Gateway' );
