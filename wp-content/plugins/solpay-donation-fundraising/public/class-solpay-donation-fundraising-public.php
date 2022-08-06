<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://solpay.store
 * @since      1.0.0
 *
 * @package    Solpay_Donation_Fundraising
 * @subpackage Solpay_Donation_Fundraising/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Solpay_Donation_Fundraising
 * @subpackage Solpay_Donation_Fundraising/public
 * @author     solpay.store <hello@solpay.store>
 */
class Solpay_Donation_Fundraising_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Solpay_Donation_Fundraising_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Solpay_Donation_Fundraising_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        $version = $this->version;

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
            $version = time();
        }

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'dist/css/solpay-donation-fundraising.css', array(), $version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Solpay_Donation_Fundraising_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Solpay_Donation_Fundraising_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        $version = $this->version;

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
            $version = time();
        }

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'dist/js/solpay-donation-fundraising.js', [], $version, true );

	}

    public function register_shortcode() {

        add_shortcode( 'solpay_donation_form', [ $this, 'render_donation_form' ] );

    }

    public function render_donation_form() {

        $settings = get_option( SOLPAY_DONATION_FUNDRAISING_OPTION_NAME );

        $transactionLabel = apply_filters(
            'solpay_donations_fundraising_label',
            sprintf(
                __( 'Donation for %s', 'solpay-donation-fundraising' ),
                get_bloginfo( 'name' )
            )
        );

        if ( $settings === false ) {
            return __( 'Please save your Solpay Donations & Fundraising settings first.', 'solpay-donation-fundraising' );
        }

        ob_start();

        require_once plugin_dir_path( __FILE__ ) . '/partials/solpay-donation-fundraising-form.php';

        return ob_get_clean();

    }
}
