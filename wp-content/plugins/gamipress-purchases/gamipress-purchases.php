<?php
/**
 * Plugin Name:     GamiPress - Purchases
 * Plugin URI:      https://gamipress.com/add-ons/gamipress-purchases
 * Description:     Allow your users purchase points, achievements or ranks access.
 * Version:         1.1.8
 * Author:          GamiPress
 * Author URI:      https://gamipress.com/
 * Text Domain:     gamipress-purchases
 * License:         GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package         GamiPress\Purchases
 * @author          GamiPress
 * @copyright       Copyright (c) GamiPress
 */

final class GamiPress_Purchases {

    /**
     * @var         GamiPress_Purchases $instance The one true GamiPress_Purchases
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Purchases self::$instance The one true GamiPress_Purchases
     */
    public static function instance() {

        if( ! self::$instance ) {

            self::$instance = new GamiPress_Purchases();
            self::$instance->constants();
            self::$instance->libraries();
            self::$instance->compatibility();
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
        define( 'GAMIPRESS_PURCHASES_VER', '1.1.8' );

        // GamiPress minimum required version
        define( 'GAMIPRESS_PURCHASES_GAMIPRESS_MIN_VER', '2.0.0' );

        // Plugin file
        define( 'GAMIPRESS_PURCHASES_FILE', __FILE__ );

        // Plugin path
        define( 'GAMIPRESS_PURCHASES_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_PURCHASES_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin libraries
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function libraries() {

        if( $this->meets_requirements() ) {

            require_once GAMIPRESS_PURCHASES_DIR . 'libraries/points-rate-field-type.php';

        }
    }

    /**
     * Include plugin compatibility files
     *
     * @access      private
     * @since       1.0.3
     * @return      void
     */
    private function compatibility() {

        if( $this->meets_requirements() ) {

            require_once GAMIPRESS_PURCHASES_DIR . 'includes/compatibility/1.0.3.php';

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

            require_once GAMIPRESS_PURCHASES_DIR . 'includes/admin.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/ajax-functions.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/compatibility.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/custom-tables.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/emails.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/functions.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/gateways.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/logs.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/payments.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/privacy.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/requirements.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/rules-engine.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/scripts.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/blocks.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/shortcodes.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/template-functions.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/triggers.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/users.php';
            require_once GAMIPRESS_PURCHASES_DIR . 'includes/widgets.php';

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

        add_action( 'gamipress_init', array( $this, 'init' ) );
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );

    }

    /**
     * Init function
     *
     * @access      private
     * @since       1.0.2
     * @return      void
     */
    function init() {

        global $wpdb;

        GamiPress()->db->payments 		= $wpdb->gamipress_payments;
        GamiPress()->db->payment_items 	= $wpdb->gamipress_payment_items;
        GamiPress()->db->payment_notes 	= $wpdb->gamipress_payment_notes;

        // Multi site support
        if( is_multisite() && gamipress_is_network_wide_active() ) {

            GamiPress()->db->payments 		= $wpdb->base_prefix . 'gamipress_payments';
            GamiPress()->db->payment_items 	= $wpdb->base_prefix . 'gamipress_payment_items';
            GamiPress()->db->payment_notes 	= $wpdb->base_prefix . 'gamipress_payment_notes';

        }

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
                        __( 'GamiPress - Purchases requires %s (%s or higher) in order to work. Please install and activate them.', 'gamipress-purchases' ),
                        '<a href="https://wordpress.org/plugins/gamipress/" target="_blank">GamiPress</a>',
                        GAMIPRESS_PURCHASES_GAMIPRESS_MIN_VER
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

        if ( class_exists( 'GamiPress' ) && version_compare( GAMIPRESS_VER, GAMIPRESS_PURCHASES_GAMIPRESS_MIN_VER, '>=' ) ) {
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
        $lang_dir = GAMIPRESS_PURCHASES_DIR . '/languages/';
        $lang_dir = apply_filters( 'gamipress_purchases_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'gamipress-purchases' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'gamipress-purchases', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/gamipress-purchases/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/gamipress/ folder
            load_textdomain( 'gamipress-purchases', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/gamipress/languages/ folder
            load_textdomain( 'gamipress-purchases', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'gamipress-purchases', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Purchases instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Purchases The one true GamiPress_Purchases
 */
function GamiPress_Purchases() {
    return GamiPress_Purchases::instance();
}
add_action( 'plugins_loaded', 'GamiPress_Purchases' );

// Setup our activation and deactivation hooks
/**
 * Activation hook for the plugin.
 *
 * @since  1.0.0
 */
function gamipress_purchases_activate() {

    $prefix = 'gamipress_purchases_';

    // Setup default GamiPress options
    $gamipress_settings = ( $exists = get_option( 'gamipress_settings' ) ) ? $exists : array();

    // Check if purchase history has been setup
    $history_page = array_key_exists( $prefix . 'purchase_history_page', $gamipress_settings ) ? gamipress_get_post( $gamipress_settings[$prefix . 'purchase_history_page'] ) : false;

    if ( empty( $history_page ) ) {

        // Create a page with the [gamipress_purchase_history] shortcode as content
        $history = wp_insert_post(
            array(
                'post_title'     => __( 'Purchase History', 'gamipress-purchases' ),
                'post_content'   => '[gamipress_purchase_history]',
                'post_status'    => 'publish',
                'post_author'    => 1,
                'post_type'      => 'page',
                'comment_status' => 'closed'
            )
        );

        $gamipress_settings[$prefix . 'purchase_history_page'] = $history;

    }

    update_option( 'gamipress_settings', $gamipress_settings );

}
register_activation_hook( __FILE__, 'gamipress_purchases_activate' );

/**
 * Deactivation hook for the plugin.
 *
 * @since  1.0.0
 */
function gamipress_purchases_deactivate() {

    // TODO: Remove data on uninstall

}
register_deactivation_hook( __FILE__, 'gamipress_purchases_deactivate' );
