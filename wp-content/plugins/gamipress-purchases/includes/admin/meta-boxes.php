<?php
/**
 * Meta Boxes
 *
 * @package     GamiPress\Purchases\Admin\Meta_Boxes
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register custom meta boxes used throughout GamiPress
 *
 * @since  1.0.0
 */
function gamipress_purchases_meta_boxes() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_purchases_';

    gamipress_add_meta_box(
        'points-type-conversions',
        __( 'Points Type Conversions', 'gamipress-purchases' ),
        'points-type',
        array(
            $prefix . 'conversion' => array(
                'name' 	=> __( 'Conversion Rate', 'gamipress-purchases' ),
                'desc' 	=> __( 'Money to points conversion rate. Used when user purchases points.', 'gamipress-purchases' ),
                'type' 	=> 'points_rate',
                'currency_symbol' => gamipress_purchases_get_currency_symbol(),
                'inverse' 	=> true,
            ),
        )
    );

    // Grab our achievement types as an array
    $achievement_types = gamipress_get_achievement_types_slugs();

    // Grab our rank types as an array
    $rank_types = gamipress_get_rank_types_slugs();

    gamipress_add_meta_box(
        'achievement-purchase-options',
        __( 'Purchase Options', 'gamipress-purchases' ),
        $achievement_types,
        array(
            $prefix . 'allow_purchase' => array(
                'name' 	=> __( 'Allow users purchase completion?', 'gamipress-purchases' ),
                'desc' 	=> __( 'Check this option to allow users purchase the achievement completion without complete the achievement requirements.', 'gamipress-purchases' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'price' => array(
                'name' 	=> __( 'Price', 'gamipress-purchases' ),
                'type' 	=> 'text',
                'attributes' => array(
                    'placeholder' => gamipress_purchases_format_amount( 0 )
                ),
            ),
        ),
        array(
            'context' => 'side'
        )
    );

    gamipress_add_meta_box(
        'rank-purchase-options',
        __( 'Purchase Options', 'gamipress-purchases' ),
        $rank_types,
        array(
            $prefix . 'allow_purchase' => array(
                'name' 	=> __( 'Allow users purchase completion?', 'gamipress-purchases' ),
                'desc' 	=> __( 'Check this option to allow users purchase the rank completion without complete the rank requirements.', 'gamipress-purchases' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'price' => array(
                'name' 	=> __( 'Price', 'gamipress-purchases' ),
                'type' 	=> 'text',
                'attributes' => array(
                    'placeholder' => gamipress_purchases_format_amount( 0 )
                ),
            ),
        ),
        array(
            'context' => 'side'
        )
    );

}
add_action( 'cmb2_admin_init', 'gamipress_purchases_meta_boxes' );