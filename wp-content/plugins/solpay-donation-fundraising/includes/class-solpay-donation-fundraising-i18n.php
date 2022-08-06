<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://solpay.store
 * @since      1.0.0
 *
 * @package    Solpay_Donation_Fundraising
 * @subpackage Solpay_Donation_Fundraising/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Solpay_Donation_Fundraising
 * @subpackage Solpay_Donation_Fundraising/includes
 * @author     solpay.store <hello@solpay.store>
 */
class Solpay_Donation_Fundraising_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'solpay-donation-fundraising',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
