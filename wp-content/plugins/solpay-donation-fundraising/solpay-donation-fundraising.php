<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              solpay.store
 * @since             1.0.0
 * @package           Solpay_Store_Donations
 *
 * @wordpress-plugin
 * Plugin Name:       Solpay Donation & Fundraising
 * Description:       Solpay Donation & Fundraising
 * Version:           1.0.0
 * Author:            solpay.store
 * Author URI:        https://solpay.store/
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       solpay-donation-fundraising
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SOLPAY_DONATION_FUNDRAISING_VERSION', '1.0.0' );

/**
 * Plugin constants
 */
define( 'SOLPAY_DONATION_FUNDRAISING_OPTION_NAME', 'solpay_donation_fundraising_settings' );
define( 'SOLPAY_DONATION_FUNDRAISING_FLASH_TRANSIENT', 'solpay_donation_fundraising_flash_transient' );
define( 'SOLPAY_DONATION_FUNDRAISING_VERIFICATION_SERVICE_URL', 'https://solana-payment-verifier.soma-labs.workers.dev' );
define( 'SOLPAY_DONATION_FUNDRAISING_VERIFICATION_INTERVAL', 3 * 1000 );
define( 'SOLPAY_DONATION_FUNDRAISING_VERIFICATION_TIMEOUT', 1 * 60 * 1000 );

/**
 * Array of SPL tokens available on the Solana Blockchain.
 * Keys represent SPL token address, values represent SPL token label shown on the frontend.
 */
define( 'SOLPAY_DONATION_FUNDRAISING_SPL_TOKENS', [
    'EPjFWdd5AufqSSqeM2qN1xzybapC8G4wEGGkZwyTDt1v' => 'USDC',
] );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-solpay-donation-fundraising-activator.php
 */
function activate_solpay_donation_fundraising() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-solpay-donation-fundraising-activator.php';
	Solpay_Donation_Fundraising_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-solpay-donation-fundraising-deactivator.php
 */
function deactivate_solpay_donation_fundraising() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-solpay-donation-fundraising-deactivator.php';
	Solpay_Donation_Fundraising_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_solpay_donation_fundraising' );
register_deactivation_hook( __FILE__, 'deactivate_solpay_donation_fundraising' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-solpay-donation-fundraising.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_solpay_donation_fundraising() {

	$plugin = new Solpay_Donation_Fundraising();
	$plugin->run();

}
run_solpay_donation_fundraising();
