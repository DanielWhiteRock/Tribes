<?php
/**
 * Plugin Name:     GamiPress - Transfers
 * Plugin URI:      https://gamipress.com/add-ons/gamipress-transfers
 * Description:     Allow your users to transfer points, achievements or ranks between them.
 * Version:         1.2.5
 * Author:          GamiPress
 * Author URI:      https://gamipress.com/
 * Text Domain:     gamipress-transfers
 * License:         GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package         GamiPress\Transfers
 * @author          GamiPress
 * @copyright       Copyright (c) GamiPress
 */

final class GamiPress_Transfers {

    /**
     * @var         GamiPress_Transfers $instance The one true GamiPress_Transfers
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Transfers self::$instance The one true GamiPress_Transfers
     */
    public static function instance() {

        if( ! self::$instance ) {

            self::$instance = new GamiPress_Transfers();
            self::$instance->constants();
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
        define( 'GAMIPRESS_TRANSFERS_VER', '1.2.5' );

        // GamiPress minimum required version
        define( 'GAMIPRESS_TRANSFERS_GAMIPRESS_MIN_VER', '2.0.0' );

        // Plugin file
        define( 'GAMIPRESS_TRANSFERS_FILE', __FILE__ );

        // Plugin path
        define( 'GAMIPRESS_TRANSFERS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_TRANSFERS_URL', plugin_dir_url( __FILE__ ) );
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

            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/admin.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/ajax-functions.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/custom-tables.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/functions.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/privacy.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/requirements.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/rules-engine.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/scripts.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/blocks.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/shortcodes.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/template-functions.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/transfers.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/triggers.php';
            require_once GAMIPRESS_TRANSFERS_DIR . 'includes/widgets.php';

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
     * @since       1.0.0
     * @return      void
     */
    function init() {

        global $wpdb;

        GamiPress()->db->transfers 		    = $wpdb->gamipress_transfers;
        GamiPress()->db->transfer_items 	= $wpdb->gamipress_transfer_items;
        GamiPress()->db->transfer_notes 	= $wpdb->gamipress_transfer_notes;

        // Multi site support
        if( is_multisite() && gamipress_is_network_wide_active() ) {

            GamiPress()->db->transfers 		    = $wpdb->base_prefix . 'gamipress_transfers';
            GamiPress()->db->transfer_items 	= $wpdb->base_prefix . 'gamipress_transfer_items';
            GamiPress()->db->transfer_notes 	= $wpdb->base_prefix . 'gamipress_transfer_notes';

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
                        __( 'GamiPress - Transfers requires %s (%s or higher) in order to work. Please install and activate them.', 'gamipress-transfers' ),
                        '<a href="https://wordpress.org/plugins/gamipress/" target="_blank">GamiPress</a>',
                        GAMIPRESS_TRANSFERS_GAMIPRESS_MIN_VER
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

        if ( class_exists( 'GamiPress' ) && version_compare( GAMIPRESS_VER, GAMIPRESS_TRANSFERS_GAMIPRESS_MIN_VER, '>=' ) ) {
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
        $lang_dir = GAMIPRESS_TRANSFERS_DIR . '/languages/';
        $lang_dir = apply_filters( 'gamipress_transfers_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'gamipress-transfers' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'gamipress-transfers', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/gamipress-transfers/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/gamipress/ folder
            load_textdomain( 'gamipress-transfers', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/gamipress/languages/ folder
            load_textdomain( 'gamipress-transfers', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'gamipress-transfers', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Transfers instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Transfers The one true GamiPress_Transfers
 */
function GamiPress_Transfers() {
    return GamiPress_Transfers::instance();
}
add_action( 'plugins_loaded', 'GamiPress_Transfers' );

// Setup our activation and deactivation hooks
/**
 * Activation hook for the plugin.
 *
 * @since  1.0.0
 */
function gamipress_transfers_activate() {

    $prefix = 'gamipress_transfers_';

    // Setup default GamiPress options
    $gamipress_settings = ( $exists = get_option( 'gamipress_settings' ) ) ? $exists : array();

    // Check if transfer history has been setup
    $history_page = array_key_exists( $prefix . 'transfer_history_page', $gamipress_settings ) ? get_post( $gamipress_settings[$prefix . 'transfer_history_page'] ) : false;

    if ( empty( $history_page ) ) {

        // Create a page with the [gamipress_transfer_history] shortcode as content
        $history = wp_insert_post(
            array(
                'post_title'     => __( 'Transfer History', 'gamipress-transfers' ),
                'post_content'   => '[gamipress_transfer_history]',
                'post_status'    => 'publish',
                'post_author'    => 1,
                'post_type'      => 'page',
                'comment_status' => 'closed'
            )
        );

        $gamipress_settings[$prefix . 'transfer_history_page'] = $history;

    }

    update_option( 'gamipress_settings', $gamipress_settings );

}
register_activation_hook( __FILE__, 'gamipress_transfers_activate' );

/**
 * Deactivation hook for the plugin.
 *
 * @since  1.0.0
 */
function gamipress_transfers_deactivate() {

    // TODO: Remove data on uninstall

}
register_deactivation_hook( __FILE__, 'gamipress_transfers_deactivate' );
