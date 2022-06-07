<?php
/**
 * WooCommerce
 *
 * @package     GamiPress\Referrals\Integrations\WooCommerce
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Requirements on multisite install
if( is_multisite() && function_exists( 'gamipress_is_network_wide_active' ) && gamipress_is_network_wide_active() && is_main_site() ) {
    // On main site, need to check if integrated plugin is installed on any sub site to load all configuration files
    if( ! gamipress_is_plugin_active_on_network( 'woocommerce/woocommerce.php' ) ) {
        return;
    }
} else if ( ! class_exists( 'WooCommerce' ) ) {
    return;
}

// Exit if integration not installed
if ( ! class_exists( 'WooCommerce' ) ) return;

class GamiPress_Referrals_WooCommerce_Integration {

    private $name = 'WooCommerce';
    private $integration = 'woocommerce';

    public function __construct() {

        // Enable referral sales
        add_filter( 'gamipress_referrals_enable_sales', '__return_true', 10 );

        // Listen for order status changes
        add_action( 'woocommerce_order_status_changed', array( $this, 'status_listener' ), 10, 4 );

        // Listen for new purchases
        add_action( 'woocommerce_order_status_completed', array( $this, 'sale' ), 10 );

        // Sale commission box
        add_action( 'cmb2_admin_init', array( $this, 'sale_commission_box' ) );

        // Sale total
        add_action( "gamipress_referrals_{$this->integration}_sale_total", array( $this, 'sale_total' ), 10, 4 );
        add_action( "gamipress_referrals_{$this->integration}_sale_total_formatted", array( $this, 'sale_total_formatted' ), 10, 5 );
    }

    // Order status listener
    public function status_listener( $order_id, $from, $to, $order ) {

        if( $from !== 'completed' && $to === 'completed' )
            $this->sale( $order_id );

        if( $from !== 'refunded' && $to === 'refunded' )
            $this->sale_refund( $order_id );

    }

    // Sale referral
    public function sale( $order_id ) {

        $order = wc_get_order( $order_id );

        if( ! $order ) return;

        $user_id = $order->get_user_id();

        gamipress_referrals_sale_listener( $order_id, $user_id, $this->integration );

    }

    // Sale refund referral
    public function sale_refund( $order_id ) {

        $order = wc_get_order( $order_id );

        if( ! $order ) return;

        $user_id = $order->get_user_id();

        gamipress_referrals_sale_refund_listener( $order_id, $user_id, $this->integration );

    }

    // Sale commission box
    public function sale_commission_box() {
        gamipress_referrals_sale_commission_box( $this->name, $this->integration );
    }

    // Sale total
    public function sale_total( $sale_total, $affiliate_id, $referral_id, $sale_id ) {

        $order = wc_get_order( $sale_id );

        if( ! $order ) return $sale_total;

        return $order->get_total();

    }

    // Sale total formatted
    public function sale_total_formatted( $sale_total_formatted, $sale_total, $affiliate_id, $referral_id, $sale_id ) {

        return strip_tags( wc_price( $sale_total, array( 'currency' => get_woocommerce_currency() ) ) );

    }

}

new GamiPress_Referrals_WooCommerce_Integration();