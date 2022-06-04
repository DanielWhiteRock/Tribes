<?php
/**
 * Plugin Name:     GamiPress - Frontend Reports
 * Plugin URI:      https://gamipress.com/add-ons/gamipress-frontend-reports
 * Description:     Advanced and attractive reports with live controls to your users at frontend.
 * Version:         1.0.3
 * Author:          GamiPress
 * Author URI:      https://gamipress.com/
 * Text Domain:     gamipress-frontend-reports
 * License:         GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package         GamiPress\Frontend_Reports
 * @author          GamiPress
 * @copyright       Copyright (c) GamiPress
 */

final class GamiPress_Frontend_Reports {

    /**
     * @var         GamiPress_Frontend_Reports $instance The one true GamiPress_Frontend_Reports
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Frontend_Reports self::$instance The one true GamiPress_Frontend_Reports
     */
    public static function instance() {

        if( ! self::$instance ) {

            self::$instance = new GamiPress_Frontend_Reports();
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
        define( 'GAMIPRESS_FRONTEND_REPORTS_VER', '1.0.3' );

        // GamiPress minimum required version
        define( 'GAMIPRESS_FRONTEND_REPORTS_GAMIPRESS_MIN_VER', '2.0.0' );

        // Plugin file
        define( 'GAMIPRESS_FRONTEND_REPORTS_FILE', __FILE__ );

        // Plugin path
        define( 'GAMIPRESS_FRONTEND_REPORTS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_FRONTEND_REPORTS_URL', plugin_dir_url( __FILE__ ) );

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

            require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/admin.php';
            require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/ajax-functions.php';
            require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/chart-functions.php';
            require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/functions.php';
            require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/graph-functions.php';
            require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/template-functions.php';
            require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/scripts.php';
            require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/shortcodes.php';
            require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/blocks.php';
            require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/widgets.php';

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

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );

    }

    /**
     * Activation hook for the plugin.
     *
     * @since  1.0.0
     */
    public static function activate() {

        GamiPress_Frontend_Reports::instance();

    }

    /**
     * Deactivation hook for the plugin.
     *
     * @since  1.0.0
     */
    public static function deactivate() {

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
                        __( 'GamiPress - Frontend Reports requires %s in order to work. Please install and activate it.', 'gamipress-frontend-reports' ),
                        '<a href="https://wordpress.org/plugins/gamipress/" target="_blank">GamiPress</a>'
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

        if ( class_exists( 'GamiPress' ) && version_compare( GAMIPRESS_VER, GAMIPRESS_FRONTEND_REPORTS_GAMIPRESS_MIN_VER, '>=' ) ) {
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
        $lang_dir = GAMIPRESS_FRONTEND_REPORTS_DIR . '/languages/';
        $lang_dir = apply_filters( 'gamipress_frontend_reports_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'gamipress-frontend-reports' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'gamipress-frontend-reports', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/gamipress-frontend-reports/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/gamipress/ folder
            load_textdomain( 'gamipress-frontend-reports', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/gamipress/languages/ folder
            load_textdomain( 'gamipress-frontend-reports', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'gamipress-frontend-reports', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Frontend_Reports instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Frontend_Reports The one true GamiPress_Frontend_Reports
 */
function GamiPress_Frontend_Reports() {
    return GamiPress_Frontend_Reports::instance();
}
add_action( 'plugins_loaded', 'GamiPress_Frontend_Reports' );

// Setup our activation and deactivation hooks
register_activation_hook( __FILE__, array( 'GamiPress_Frontend_Reports', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'GamiPress_Frontend_Reports', 'deactivate' ) );