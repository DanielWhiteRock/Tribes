<?php
/**
 * Admin
 *
 * @package GamiPress\Points_Payouts\Admin
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add admin bar menu
 *
 * @since 1.0.0
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function gamipress_points_payouts_admin_bar_menu( $wp_admin_bar ) {

    // - Points Payout History
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-points-payouts',
        'title'  => __( 'Points Payout History', 'gamipress-points-payouts' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress_points_payouts' )
    ) );

}
add_action( 'admin_bar_menu', 'gamipress_points_payouts_admin_bar_menu', 150 );

/**
 * Shortcut function to get plugin options
 *
 * @since  1.0.0
 *
 * @param string    $option_name
 * @param bool      $default
 *
 * @return mixed
 */
function gamipress_points_payouts_get_option( $option_name, $default = false ) {

    $prefix = 'gamipress_points_payouts_';

    return gamipress_get_option( $prefix . $option_name, $default );
}

/**
 * GamiPress Points Payouts Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_points_payouts_settings_meta_boxes( $meta_boxes ) {

    $prefix = 'gamipress_points_payouts_';

    // Page Options
    $pages = get_posts( array(
        'post_type' => 'page',
        'numberposts' => -1
    ) );

    $pages_options = array();

    foreach( $pages as $page ) {
        $pages_options[$page->ID] = $page->post_title;
    }

    // Setup currency options
    $currency_code_options = gamipress_points_payouts_get_currencies();

    foreach ( $currency_code_options as $code => $name ) {
        $currency_code_options[ $code ] = $name . ' (' . gamipress_points_payouts_get_currency_symbol( $code ) . ')';
    }

    $meta_boxes['gamipress-points-payouts-settings'] = array(
        'title' => gamipress_dashicon( 'star-filled' ) . __( 'Points Payouts', 'gamipress-points-payouts' ),
        'fields' => apply_filters( 'gamipress_points_payouts_settings_fields', array(
            $prefix . 'points_payout_history_page' => array(
                'name' => __( 'Points Payout History Page', 'gamipress-points-payouts' ),
                'desc' => __( 'Page to show a complete points payout history for the current user, including each withdrawal details. The [gamipress_points_payout_history] shortcode should be on this page.', 'gamipress-points-payouts' ),
                'type' => 'select',
                'options' => $pages_options,
            ),
            $prefix . 'payment_method_text' => array(
                'name' => __( 'Payment method text', 'gamipress-points-payouts' ),
                'desc' => __( 'The payment method text displayed on points payout forms and history where users can enter the way to pay them.', 'gamipress-points-payouts' )
                . '<br>' . __( 'You can enter a text like "PayPal Email", "Payment Email" or "Bank account" to let user know the way you will process the payment.', 'gamipress-points-payouts' )
                . '<br>' . __( 'By default, "Payment Method".', 'gamipress-points-payouts' ),
                'type' => 'text',
                'default' => __( 'Payment Method', 'gamipress-points-payouts' ),
            ),
            $prefix . 'currency' => array(
                'name' => __( 'Currency', 'gamipress-points-payouts' ),
                'desc' => __( 'Choose your currency.', 'gamipress-points-payouts' ),
                'type' => 'select',
                'options' => $currency_code_options,
                'default' => 'USD'
            ),
            $prefix . 'currency_position' => array(
                'name' => __( 'Currency Position', 'gamipress-points-payouts' ),
                'desc' => __( 'Location of the currency sign.', 'gamipress-points-payouts' ),
                'type' => 'select',
                'options' => array(
                    'before' => __( 'Before ($10)', 'gamipress-points-payouts' ),
                    'after' => __( 'After (10$)', 'gamipress-points-payouts' )
                ),
                'default' => 'before'
            ),
            $prefix . 'thousands_separator' => array(
                'name' => __( 'Thousand separator', 'gamipress-points-payouts' ),
                'desc' => __( 'The symbol (usually , or .) to separate thousands.', 'gamipress-points-payouts' ),
                'type' => 'text',
                'default' => ','
            ),
            $prefix . 'decimal_separator' => array(
                'name' => __( 'Decimal separator', 'gamipress-points-payouts' ),
                'desc' => __( 'The symbol (usually , or .) to separate decimals.', 'gamipress-points-payouts' ),
                'type' => 'text',
                'default' => '.'
            ),
            $prefix . 'decimals' => array(
                'name' => __( 'Number of decimals', 'gamipress-points-payouts' ),
                'desc' => __( 'Number of decimals points.', 'gamipress-points-payouts' ),
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number'
                ),
                'default' => 2,
            ),
        ) ),
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_addons_meta_boxes', 'gamipress_points_payouts_settings_meta_boxes' );


/**
 * GamiPress Points Payouts Email Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_points_payouts_email_settings_meta_boxes( $meta_boxes ) {

    $prefix = 'gamipress_points_payouts_';

    $meta_boxes['gamipress-points-payouts-email-settings'] = array(
        'title' => gamipress_dashicon( 'star-filled' ) . __( 'Points Payouts: New payout request email', 'gamipress-points-payouts' ),
        'fields' => apply_filters( 'gamipress_points_payouts_email_settings_fields', array(
            $prefix . 'disable_payout_request_email' => array(
                'name' => __( 'Disable new payout request emails', 'gamipress-points-payouts' ),
                'desc' => __( 'Check this option to do not receive emails about new payout requests.', 'gamipress-points-payouts' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'payout_request_subject' => array(
                'name' => __( 'Email subject', 'gamipress-points-payouts' ),
                'desc' => __( 'The email subject.', 'gamipress-points-payouts' ),
                'type' => 'text',
                'default' => __( 'New points payout request #{id}', 'gamipress-points-payouts' ),
            ),
            $prefix . 'payout_request_content' => array(
                'name' => __( 'Email content', 'gamipress-points-payouts' ),
                'desc' => __( 'The email content. Available tags:', 'gamipress-points-payouts' )
                    . gamipress_points_payouts_get_pattern_tags_html(),
                'type' => 'wysiwyg',
                'default' => __( '{user} requested a new points payout.', 'gamipress-points-payouts' )
                    .  "\n" . __( 'Details:', 'gamipress-points-payouts' )
                    .  "\n" . __( 'Points: {points} {points_label} for {money}', 'gamipress-points-payouts' )
                    .  "\n" . __( 'Money: {money}', 'gamipress-points-payouts' )
                    .  "\n" . __( '{payment_method_label}: {payment_method}', 'gamipress-points-payouts' ),
            ),
        ) ),
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_email_meta_boxes', 'gamipress_points_payouts_email_settings_meta_boxes' );

/**
 * Plugin meta boxes
 *
 * @since  1.0.0
 */
function gamipress_points_payouts_meta_boxes() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_points_payouts_';

    // Points Type Payment Gateway
    gamipress_add_meta_box(
        'gamipress-points-payouts',
        __( 'Points Payouts', 'gamipress-points-payouts' ),
        'points-type',
        array(
            $prefix . 'enable' => array(
                'name' 	    => __( 'Allow Points Payouts', 'gamipress-points-payouts' ),
                'desc' 	    => __( 'Check this option to allow users to request points payouts of this points type.', 'gamipress-points-payouts' ),
                'type' 	    => 'checkbox',
                'classes' 	=> 'gamipress-switch',
            ),
            $prefix . 'conversion' => array(
                'name' 	=> __( 'Exchange Conversion', 'gamipress-points-payouts' ),
                'desc' 	=> __( 'Points to money conversion rate.', 'gamipress-points-payouts' ),
                'currency_symbol' => '$',
                'type' 	=> 'points_rate',
            ),
            $prefix . 'min_amount' => array(
                'name' 	    => __( 'Minimum Amount', 'gamipress-points-payouts' ),
                'desc' 	    => __( 'Set the minimum amount for the points amount input. Leave it to 0 for no minimum.', 'gamipress-points-payouts' ),
                'type' 	    => 'text',
                'attributes' => array(
                    'type' => 'number',
                    'placeholder' => '0',
                    'min' => '0',
                    'step' => '1',
                ),
                'default' => '0'
            ),
            $prefix . 'max_amount' => array(
                'name' 	    => __( 'Maximum Amount', 'gamipress-points-payouts' ),
                'desc' 	    => __( 'Set the maximum amount allowed for the points amount input. Leave it to 0 for no maximum.', 'gamipress-points-payouts' ),
                'type' 	    => 'text',
                'attributes' => array(
                    'type' => 'number',
                    'placeholder' => '0',
                    'min' => '0',
                    'step' => '1',
                ),
                'default' => '0'
            ),
        )
    );

}
add_action( 'cmb2_admin_init', 'gamipress_points_payouts_meta_boxes' );

/**
 * Plugin Licensing meta box
 *
 * @since  1.0.0
 *
 * @param $meta_boxes
 *
 * @return mixed
 */
function gamipress_points_payouts_licenses_meta_boxes( $meta_boxes ) {

    $meta_boxes['gamipress-points-payouts-license'] = array(
        'title' => __( 'Points Payouts', 'gamipress-points-payouts' ),
        'fields' => array(
            'gamipress_points_payouts_license' => array(
                'name' => __( 'License', 'gamipress-points-payouts' ),
                'type' => 'edd_license',
                'file' => GAMIPRESS_POINTS_PAYOUTS_FILE,
                'item_name' => 'Points Payouts',
            ),
        )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_points_payouts_licenses_meta_boxes' );

/**
 * Plugin automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_points_payouts_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress-points-payouts'] = __( 'Points Payouts', 'gamipress-points-payouts' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_points_payouts_automatic_updates' );