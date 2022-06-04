<?php
/**
 * Settings
 *
 * @package     GamiPress\Purchases\Admin\Settings
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

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
function gamipress_purchases_get_option( $option_name, $default = false ) {

    $prefix = 'gamipress_purchases_';

    return gamipress_get_option( $prefix . $option_name, $default );
}

/**
 * GamiPress Purchases Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_purchases_settings_meta_boxes( $meta_boxes ) {

    $prefix = 'gamipress_purchases_';

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
    $currency_code_options = gamipress_purchases_get_currencies();

    foreach ( $currency_code_options as $code => $name ) {
        $currency_code_options[ $code ] = $name . ' (' . gamipress_purchases_get_currency_symbol( $code ) . ')';
    }

    // Setup countries options
    $countries_options = gamipress_purchases_get_countries();
    $countries_options = array_merge( array( '' => __( 'Choose a country', 'gamipress-purchases' ) ),  $countries_options );

    $meta_boxes['gamipress-purchases-settings'] = array(
        'title' => gamipress_dashicon( 'cart' ) . __( 'Purchases', 'gamipress-purchases' ),
        'fields' => apply_filters( 'gamipress_purchases_settings_fields', array(

            // General settings

            $prefix . 'purchase_history_page' => array(
                'name' => __( 'Purchase History Page', 'gamipress-purchases' ),
                'desc' => __( 'Page to show a complete purchase history for the current user, including each purchase details. The [gamipress_purchase_history] shortcode should be on this page.', 'gamipress-purchases' ),
                'type' => 'select',
                'options' => $pages_options,
            ),

            // TODO: Add remove plugin data on uninstall

            // Currency settings

            $prefix . 'currency' => array(
                'name' => __( 'Currency', 'gamipress-purchases' ),
                'desc' => __( 'Choose your currency. Note that some payment gateways have currency restrictions.', 'gamipress-purchases' ),
                'type' => 'select',
                'options' => $currency_code_options,
                'default' => 'USD'
            ),
            $prefix . 'currency_position' => array(
                'name' => __( 'Currency Position', 'gamipress-purchases' ),
                'desc' => __( 'Location of the currency sign.', 'gamipress-purchases' ),
                'type' => 'select',
                'options' => array(
                    'before' => __( 'Before ($10)', 'gamipress-purchases' ),
                    'after' => __( 'After (10$)', 'gamipress-purchases' )
                ),
                'default' => 'before'
            ),
            $prefix . 'thousands_separator' => array(
                'name' => __( 'Thousand separator', 'gamipress-purchases' ),
                'desc' => __( 'The symbol (usually , or .) to separate thousands.', 'gamipress-purchases' ),
                'type' => 'text',
                'default' => ','
            ),
            $prefix . 'decimal_separator' => array(
                'name' => __( 'Decimal separator', 'gamipress-purchases' ),
                'desc' => __( 'The symbol (usually , or .) to separate decimals.', 'gamipress-purchases' ),
                'type' => 'text',
                'default' => '.'
            ),
            $prefix . 'decimals' => array(
                'name' => __( 'Number of decimals', 'gamipress-purchases' ),
                'desc' => __( 'Number of decimals points.', 'gamipress-purchases' ),
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number'
                ),
                'default' => 2,
            ),

            // Taxes settings

            $prefix . 'enable_taxes' => array(
                'name' => __( 'Enable Taxes', 'gamipress-purchases' ),
                'desc' => __( 'Checking this option taxes will be applied on purchases.', 'gamipress-purchases' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            $prefix . 'taxes' => array(
                'name' => __( 'Taxes', 'gamipress-purchases' ),
                'desc' => __( 'Add tax rates for specific regions. Enter a percentage, such as 6.5 for 6.5%.', 'gamipress-purchases' ),
                'type' => 'group',
                'options'     => array(
                    'add_button'    => __( 'Add Tax', 'gamipress-purchases' ),
                    'remove_button' => '<i class="dashicons dashicons-no-alt"></i>',
                ),
                'fields' => array(
                    'country' => array(
                        'name' => __( 'Country', 'gamipress-purchases' ),
                        'type' => 'select',
                        'options' => $countries_options
                    ),
                    'state' => array(
                        'name' => __( 'State', 'gamipress-purchases' ),
                        'type' => 'text',
                    ),
                    'postcode' => array(
                        'name' => __( 'Postal Code / ZIP', 'gamipress-purchases' ),
                        'type' => 'text',
                    ),
                    'tax' => array(
                        'name' => __( 'Tax', 'gamipress-purchases' ),
                        'type' => 'text',
                        'attributes' => array(
                            'placeholder' => '0.00'
                        )
                    ),

                ),
            ),
            $prefix . 'default_tax' => array(
                'name' => __( 'Default Tax', 'gamipress-purchases' ),
                'desc' => __( 'Customers not in a specific tax will be charged this tax. Enter a percentage, such as 6.5 for 6.5%.', 'gamipress-purchases' ),
                'type' => 'text_small',
                'attributes' => array(
                    'placeholder' => '0.00'
                )
            ),

            // Gateways settings

            $prefix . 'gateways' => array(
                'name' => __( 'Payment Gateways', 'gamipress-purchases' ),
                'desc' => __( 'Choose the gateways.', 'gamipress-purchases' ),
                'type' => 'multicheck',
                'select_all_button' => false,
                'classes' => 'gamipress-switch',
                'options' => gamipress_purchases_get_gateways(),
            ),

        ) ),
        'tabs' => apply_filters( 'gamipress_purchases_settings_tabs', array(
            'general' => array(
                'icon' => 'dashicons-admin-generic',
                'title' => __( 'General', 'gamipress-purchases' ),
                'fields' => array(
                    $prefix . 'purchase_history_page',
                ),
            ),
            'currency' => array(
                'icon' => 'dashicons-dollar',
                'title' => __( 'Currency', 'gamipress-purchases' ),
                'fields' => array(
                    $prefix . 'currency',
                    $prefix . 'currency_position',
                    $prefix . 'thousands_separator',
                    $prefix . 'decimal_separator',
                    $prefix . 'decimals',
                ),
            ),
            'taxes' => array(
                'icon' => 'dashicons-admin-site',
                'title' => __( 'Taxes', 'gamipress-purchases' ),
                'fields' => array(
                    $prefix . 'enable_taxes',
                    $prefix . 'taxes',
                    $prefix . 'default_tax',
                ),
            ),
            'gateways' => array(
                'icon' => 'dashicons-credit-card',
                'title' => __( 'Gateways', 'gamipress-purchases' ),
                'fields' => array(
                    $prefix . 'gateways',
                ),
            ),
        ) ),
        'vertical_tabs' => true
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_addons_meta_boxes', 'gamipress_purchases_settings_meta_boxes' );

/**
 * Add the receipt email template fields
 *
 * @since 1.0.0
 *
 * @param array $fields
 *
 * @return array
 */
function gamipress_purchases_email_templates_settings_fields( $fields ) {

    $prefix = 'gamipress_purchases_';

    return array_merge( $fields, array(

        // Purchase receipt

        $prefix . 'purchase_receipt_email_actions' => array(
            'type' => 'multi_buttons',
            'buttons' => array(
                'purchase-receipt-email-preview' => array(
                    'label' => __( 'Preview Email', 'gamipress-purchases' ),
                    'type' => 'link',
                    'link' => admin_url( 'admin.php?gamipress-action=preview_purchase_receipt_email' ),
                    'target' => '_blank',
                ),
                'purchase-receipt-email-send' => array(
                    'label' => __( 'Send Test Email', 'gamipress-purchases' ),
                    'type' => 'link',
                    'link' => admin_url( 'admin.php?gamipress-action=send_test_purchase_receipt_email' ),
                    'target' => '_blank',
                )
            ),
        ),
        $prefix . 'disable_purchase_receipt_email' => array(
            'name' => __( 'Disable purchase receipt email sending', 'gamipress-purchases' ),
            'desc' => __( 'Check this option to stop sending emails to users for new purchases.', 'gamipress-purchases' ),
            'type' => 'checkbox',
            'classes' => 'gamipress-switch',
        ),
        $prefix . 'purchase_receipt_email_subject' => array(
            'name' => __( 'Subject', 'gamipress-purchases' ),
            'desc' => __( 'Enter the subject line for the purchase receipt email.', 'gamipress-purchases' ),
            'type' => 'text',
            'default' => __( '[{site_title}] {user_first}, your purchase receipt #{purchase_number}', 'gamipress-purchases' ),
        ),
        $prefix . 'purchase_receipt_email_content' => array(
            'name' => __( 'Content', 'gamipress-purchases' ),
            'desc' => __( 'Available tags:', 'gamipress-purchases' )
                . gamipress_purchases_get_purchase_receipt_email_pattern_tags_html(),
            'type' => 'wysiwyg',
            'default' =>
                '<h2>' . __( '{user_first}, your purchase receipt here!', 'gamipress-purchases' ) . '</h2>' . "\n"
                . __( 'You have purchased the following items:', 'gamipress-purchases' ) . "\n"
                . '{purchase_items}' . "\n\n"
                . '<strong>Subtotal:</strong> {purchase_subtotal}' . "\n"
                . '<strong>Tax:</strong> {purchase_tax}' . "\n"
                . '<strong>Total:</strong> {purchase_total}' . "\n\n"
                . __( 'Best regards', 'gamipress-purchases' ),
        ),

        // New sale

        $prefix . 'new_sale_email_actions' => array(
            'type' => 'multi_buttons',
            'buttons' => array(
                'new-sale-email-preview' => array(
                    'label' => __( 'Preview Email', 'gamipress-purchases' ),
                    'type' => 'link',
                    'link' => admin_url( 'admin.php?gamipress-action=preview_new_sale_email' ),
                    'target' => '_blank',
                ),
                'new-sale-email-send' => array(
                    'label' => __( 'Send Test Email', 'gamipress-purchases' ),
                    'type' => 'link',
                    'link' => admin_url( 'admin.php?gamipress-action=send_test_new_sale_email' ),
                    'target' => '_blank',
                )
            ),
        ),
        $prefix . 'disable_new_sale_email' => array(
            'name' => __( 'Disable new sale email sending', 'gamipress-purchases' ),
            'desc' => __( 'Check this option to stop sending emails to administrators for new sales.', 'gamipress-purchases' ),
            'type' => 'checkbox',
            'classes' => 'gamipress-switch',
        ),
        $prefix . 'new_sale_email_subject' => array(
            'name' => __( 'Subject', 'gamipress-purchases' ),
            'desc' => __( 'Enter the subject line for the new sale email.', 'gamipress-purchases' ),
            'type' => 'text',
            'default' => __( '[{site_title}] New sale! #{purchase_number}', 'gamipress-purchases' ),
        ),
        $prefix . 'new_sale_email_content' => array(
            'name' => __( 'Content', 'gamipress-purchases' ),
            'desc' => __( 'Available tags:', 'gamipress-purchases' )
                . gamipress_purchases_get_purchase_receipt_email_pattern_tags_html(),
            'type' => 'wysiwyg',
            'default' =>
                '<h2>' . __( 'New Sale!', 'gamipress-purchases' ) . '</h2>' . "\n"
                . __( '{user_first} ({user_email}) has purchased the following items:', 'gamipress-purchases' ) . "\n"
                . '{purchase_items}' . "\n\n"
                . '<strong>Subtotal:</strong> {purchase_subtotal}' . "\n"
                . '<strong>Tax:</strong> {purchase_tax}' . "\n"
                . '<strong>Total:</strong> {purchase_total}' . "\n\n"
                . __( 'Best regards', 'gamipress-purchases' ),
        ),

    ) );

}
add_filter( 'gamipress_email_templates_fields', 'gamipress_purchases_email_templates_settings_fields' );

/**
 * Add the receipt email template tabs
 *
 * @since 1.0.0
 *
 * @param array $tabs
 *
 * @return array
 */
function gamipress_purchases_email_templates_settings_tabs( $tabs ) {

    $prefix = 'gamipress_purchases_';

    return array_merge( $tabs, array(

        $prefix . 'purchase_receipt' => array(
            'title' => __( 'Purchase Receipt', 'gamipress' ),
            'icon' => 'dashicons-media-text',
            'fields' => array(
                $prefix . 'purchase_receipt_email_actions',
                $prefix . 'disable_purchase_receipt_email',
                $prefix . 'purchase_receipt_email_subject',
                $prefix . 'purchase_receipt_email_content'
            )
        ),
        $prefix . 'new_sale' => array(
            'title' => __( 'New Sale', 'gamipress' ),
            'icon' => 'dashicons-megaphone',
            'fields' => array(
                $prefix . 'new_sale_email_actions',
                $prefix . 'disable_new_sale_email',
                $prefix . 'new_sale_email_subject',
                $prefix . 'new_sale_email_content'
            )
        ),

    ) );

}
add_filter( 'gamipress_email_templates_tabs', 'gamipress_purchases_email_templates_settings_tabs' );