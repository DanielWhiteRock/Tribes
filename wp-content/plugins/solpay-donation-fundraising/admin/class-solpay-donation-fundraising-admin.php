<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://solpay.store
 * @since      1.0.0
 *
 * @package    Solpay_Donation_Fundraising
 * @subpackage Solpay_Donation_Fundraising/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Solpay_Donation_Fundraising
 * @subpackage Solpay_Donation_Fundraising/admin
 * @author     solpay.store <hello@solpay.store>
 */
class Solpay_Donation_Fundraising_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/solpay-donation-fundraising-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/solpay-donation-fundraising-admin.js', array( 'jquery' ), $this->version, false );

	}

    public function register_admin_page() {

        add_submenu_page(
            'options-general.php',
            __( 'Solpay Donation & Fundraising', 'solpay-donation-fundraising' ),
            __( 'Solpay Donation & Fundraising', 'solpay-donation-fundraising' ),
            'manage_options',
            $this->plugin_name,
            function () {
                require_once plugin_dir_path( __FILE__ ) . '/partials/solpay-donation-fundraising-admin-settings.php';
            }
        );

    }

    public function save_settings() {

        $formFieldPrefix = $this->plugin_name . '_settings';

        $nonceName = $formFieldPrefix . '_nonce';

        if ( !isset( $_POST[ $nonceName ] )
             || !wp_verify_nonce( $_POST[ $nonceName ], $this->plugin_name . '_nonce')
        ) {
            wp_die( __( 'Invalid nonce', 'solpay-donation-fundraising' ) );
        }

        $walletAddress = sanitize_text_field( $_POST[ $formFieldPrefix . '_wallet_address' ] );
        $splToken = sanitize_text_field( $_POST[ $formFieldPrefix . '_spl_token' ] );
        $predefinedAmounts = sanitize_textarea_field( $_POST[ $formFieldPrefix . '_predefined_amounts' ] );
        $customAmountInput = sanitize_text_field( $_POST[ $formFieldPrefix . '_custom_amount_input' ] );
        $thankYouMessage = wp_kses_post( wpautop( $_POST[ $formFieldPrefix . '_thank_you_message' ] ) );

        $filteredPredefinedAmounts = [];

        foreach ( explode("\n", $predefinedAmounts) as $amount ) {
            $value = trim( $amount );

            if ( !is_numeric( $value )) {
                continue;
            }

            $filteredPredefinedAmounts[] = (float) $amount;
        }

        $splTokenAddresses = array_keys( SOLPAY_DONATION_FUNDRAISING_SPL_TOKENS );

        if ( !in_array( $splToken, $splTokenAddresses ) ) {
            $splToken = '';
        }

        $settings = [
            'wallet_address' => $walletAddress,
            'spl_token' => $splToken,
            'predefined_amounts' => $filteredPredefinedAmounts,
            'custom_amount_input' => $customAmountInput === 'on',
            'thank_you_message' => $thankYouMessage,
        ];

        update_option( SOLPAY_DONATION_FUNDRAISING_OPTION_NAME, $settings );

        set_transient(
            SOLPAY_DONATION_FUNDRAISING_FLASH_TRANSIENT,
            [
                'type' => 'success',
                'text' => __( 'Settings saved!', 'solpay-donation-fundraising' )
            ],
            30
        );

        wp_redirect( admin_url( '/options-general.php?page=' . $this->plugin_name ) );

        exit;

    }

}
