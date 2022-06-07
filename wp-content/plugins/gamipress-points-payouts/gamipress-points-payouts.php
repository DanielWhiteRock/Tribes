<?php
/**
 * Plugin Name:     GamiPress - Points Payouts
 * Plugin URI:      https://gamipress.com/add-ons/gamipress-points-payouts
 * Description:     Let users withdrawal points for money based on a conversion rate.
 * Version:         1.0.6
 * Author:          GamiPress
 * Author URI:      https://gamipress.com/
 * Text Domain:     gamipress-points-payouts
 * License:         GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package         GamiPress\Points_Payouts
 * @author          GamiPress <contact@gamipress.com>
 * @copyright       Copyright (c) GamiPress
 */

final class GamiPress_Points_Payouts {

    /**
     * @var         GamiPress_Points_Payouts $instance The one true GamiPress_Points_Payouts
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Points_Payouts self::$instance The one true GamiPress_Points_Payouts
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new GamiPress_Points_Payouts();
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
        define( 'GAMIPRESS_POINTS_PAYOUTS_VER', '1.0.6' );

        // GamiPress minimum required version
        define( 'GAMIPRESS_POINTS_PAYOUTS_GAMIPRESS_MIN_VER', '2.0.0' );

        // Plugin file
        define( 'GAMIPRESS_POINTS_PAYOUTS_FILE', __FILE__ );

        // Plugin path
        define( 'GAMIPRESS_POINTS_PAYOUTS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_POINTS_PAYOUTS_URL', plugin_dir_url( __FILE__ ) );
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

            require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'libraries/points-rate-field-type.php';

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

            require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'includes/admin.php';
            require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'includes/ajax-functions.php';
            require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'includes/custom-tables.php';
            require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'includes/emails.php';
            require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'includes/functions.php';
            require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'includes/points-payouts.php';
            require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'includes/scripts.php';
            require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'includes/shortcodes.php';
            require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'includes/template-functions.php';
            require_once GAMIPRESS_POINTS_PAYOUTS_DIR . 'includes/widgets.php';

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
                        __( 'GamiPress - Points Payouts requires %s (%s or higher) in order to work. Please install and activate them.', 'gamipress-points-payouts' ),
                        '<a href="https://wordpress.org/plugins/gamipress/" target="_blank">GamiPress</a>',
                        GAMIPRESS_POINTS_PAYOUTS_GAMIPRESS_MIN_VER
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

        if ( class_exists( 'GamiPress' ) && version_compare( GAMIPRESS_VER, GAMIPRESS_POINTS_PAYOUTS_GAMIPRESS_MIN_VER, '>=' ) ) {
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
        $lang_dir = GAMIPRESS_POINTS_PAYOUTS_DIR . '/languages/';
        $lang_dir = apply_filters( 'gamipress_points_payouts_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'gamipress-points-payouts' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'gamipress-points-payouts', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/gamipress-points-payouts/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/gamipress/ folder
            load_textdomain( 'gamipress-points-payouts', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/gamipress/languages/ folder
            load_textdomain( 'gamipress-points-payouts', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'gamipress-points-payouts', false, $lang_dir );
        }
    }

}

/**
 * The main function responsible for returning the one true GamiPress_Points_Payouts instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Points_Payouts The one true GamiPress_Points_Payouts
 */
function GamiPress_Points_Payouts() {
    return GamiPress_Points_Payouts::instance();
}
add_action( 'plugins_loaded', 'GamiPress_Points_Payouts' );

// Setup our activation and deactivation hooks
/**
 * Activation hook for the plugin.
 *
 * @since  1.0.0
 */
function gamipress_points_payouts_activate() {

    $prefix = 'gamipress_points_payouts_';

    // Setup default GamiPress options
    $gamipress_settings = ( $exists = get_option( 'gamipress_settings' ) ) ? $exists : array();

    // Check if points payout history has been setup
    $history_page = array_key_exists( $prefix . 'points_payout_history_page', $gamipress_settings ) ? get_post( $gamipress_settings[$prefix . 'points_payout_history_page'] ) : false;

    if ( empty( $history_page ) ) {

        // Create a page with the [gamipress_points_payout_history] shortcode as content
        $history = wp_insert_post(
            array(
                'post_title'     => __( 'Points Payout History', 'gamipress-points-payouts' ),
                'post_content'   => '[gamipress_points_payout_history]',
                'post_status'    => 'publish',
                'post_author'    => 1,
                'post_type'      => 'page',
                'comment_status' => 'closed'
            )
        );

        $gamipress_settings[$prefix . 'points_payout_history_page'] = $history;

    }

    update_option( 'gamipress_settings', $gamipress_settings );

}
register_activation_hook( __FILE__, 'gamipress_points_payouts_activate' );

/**
 * Deactivation hook for the plugin.
 *
 * @since  1.0.0
 */
function gamipress_points_payouts_deactivate() {

    // TODO: Remove data on uninstall

}
register_deactivation_hook( __FILE__, 'gamipress_points_payouts_deactivate' );
