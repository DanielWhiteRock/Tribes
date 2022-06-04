<?php
/**
 * Plugin Name:     GamiPress - Points Exchanges
 * Plugin URI:      https://gamipress.com/add-ons/gamipress-points-exchanges
 * Description:     Let your users exchange points between different points types.
 * Version:         1.0.9
 * Author:          GamiPress
 * Author URI:      https://gamipress.com/
 * Text Domain:     gamipress-points-exchanges
 * License:         GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package         GamiPress\Points_Exchanges
 * @author          GamiPress
 * @copyright       Copyright (c) GamiPress
 */

final class GamiPress_Points_Exchanges {

    /**
     * @var         GamiPress_Points_Exchanges $instance The one true GamiPress_Points_Exchanges
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      object self::$instance The one true GamiPress_Points_Exchanges
     */
    public static function instance() {

        if( ! self::$instance ) {

            self::$instance = new GamiPress_Points_Exchanges();
            self::$instance->constants();
            self::$instance->libraries();
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
        define( 'GAMIPRESS_POINTS_EXCHANGES_VER', '1.0.9' );

        // GamiPress minimum required version
        define( 'GAMIPRESS_POINTS_EXCHANGES_GAMIPRESS_MIN_VER', '2.0.0' );

        // Plugin file
        define( 'GAMIPRESS_POINTS_EXCHANGES_FILE', __FILE__ );

        // Plugin path
        define( 'GAMIPRESS_POINTS_EXCHANGES_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_POINTS_EXCHANGES_URL', plugin_dir_url( __FILE__ ) );
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

            require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'libraries/points-exchanges-rates-field-type.php';

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

            require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/admin.php';
            require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/ajax-functions.php';
            require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/functions.php';
            require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/logs.php';
            require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/requirements.php';
            require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/rules-engine.php';
            require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/scripts.php';
            require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/shortcodes.php';
            require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/template-functions.php';
            require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/triggers.php';
            require_once GAMIPRESS_POINTS_EXCHANGES_DIR . 'includes/widgets.php';

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
     * Plugin admin notices.
     *
     * @since  1.0.0
     */
    public function admin_notices() {

        if ( ! $this->meets_requirements() && ! defined( 'GAMIPRESS_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php printf(
                        __( 'GamiPress - Points Exchanges requires %s (%s or higher) in order to work. Please install and activate them.', 'gamipress-points-exchanges' ),
                        '<a href="https://wordpress.org/plugins/gamipress/" target="_blank">GamiPress</a>',
                        GAMIPRESS_POINTS_EXCHANGES_GAMIPRESS_MIN_VER
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

        if ( class_exists( 'GamiPress' ) && version_compare( GAMIPRESS_VER, GAMIPRESS_POINTS_EXCHANGES_GAMIPRESS_MIN_VER, '>=' ) ) {
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
        $lang_dir = GAMIPRESS_POINTS_EXCHANGES_DIR . '/languages/';
        $lang_dir = apply_filters( 'gamipress_points_exchanges_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'gamipress-points-exchanges' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'gamipress-points-exchanges', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/gamipress-points-exchanges/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/gamipress/ folder
            load_textdomain( 'gamipress-points-exchanges', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/gamipress/languages/ folder
            load_textdomain( 'gamipress-points-exchanges', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'gamipress-points-exchanges', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Points_Exchanges instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Points_Exchanges The one true GamiPress_Points_Exchanges
 */
function GamiPress_Points_Exchanges() {
    return GamiPress_Points_Exchanges::instance();
}
add_action( 'plugins_loaded', 'GamiPress_Points_Exchanges' );
