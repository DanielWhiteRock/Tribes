<?php
/**
 * Blocks
 *
 * @package     GamiPress\Transfers\Blocks
 * @since       1.0.6
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin block icons
 *
 * @since 1.0.6
 *
 * @param array $icons
 *
 * @return array
 */
function gamipress_transfers_block_icons( $icons ) {

    $icons['transfer'] =
        '<svg width="24" height="24" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" >
            <g id="g835" transform="matrix(0.05681664,0,0,0.05681664,7.2641452,7.8340398)" style="fill:#000000;fill-opacity:1;stroke:none" />
            <g transform="translate(-118.53559,-219.68474)" id="layer1">
                <path d="m 130.13559,235.66392 2.76667,-2.97184 h -7.03334 v -1.98122 h 11.66667 l -5.93333,6.27388 z" />
                <path d="m 131.20226,228.72963 h -11.66667 l 5.93333,-6.27389 1.46667,1.32082 -2.76667,2.97184 h 7.03334 z" />
                <g transform="translate(281.51504,207.3192)" />
            </g>            
        </svg>';

    return $icons;
}
add_filter( 'gamipress_block_icons', 'gamipress_transfers_block_icons' );

/**
 * Turn select2 fields into 'post' or 'user' field types
 *
 * @since 1.0.6
 *
 * @param array                 $fields
 * @param GamiPress_Shortcode   $shortcode
 *
 * @return array
 */
function gamipress_transfers_block_fields( $fields, $shortcode ) {

    switch ( $shortcode->slug ) {
        case 'gamipress_achievement_transfer':
            // Achievement ID
            $fields['id']['type'] = 'post';
            $fields['id']['post_type'] = gamipress_get_achievement_types_slugs();
            break;
        case 'gamipress_rank_transfer':
            // Rank ID
            $fields['id']['type'] = 'post';
            $fields['id']['post_type'] = gamipress_get_rank_types_slugs();
            break;
        case 'gamipress_points_transfer':
            // Fixed
            $fields['amount']['conditions'] = array(
                'transfer_type' => 'fixed',
            );

            // Options
            $fields['options']['conditions'] = array(
                'transfer_type' => 'options',
            );
            $fields['allow_user_input']['conditions'] = array(
                'transfer_type' => 'options',
            );

            // Used for custom and allow user input options
            $fields['initial_amount']['conditions'] = array(
                'relation' => 'OR',
                'transfer_type' => 'custom',
                'allow_user_input' => true,
            );
            break;
    }

    if( in_array( $shortcode->slug, array( 'gamipress_achievement_transfer', 'gamipress_rank_transfer', 'gamipress_points_transfer' ) ) ) {
        // Recipient ID
        $fields['recipient_id']['type'] = 'user';

        // For placeholder and autocomplete, set as display condition that select_recipient needs to be true (checked)
        $fields['recipient_placeholder']['conditions'] = array(
            'select_recipient' => true,
        );

        $fields['recipient_autocomplete']['conditions'] = array(
            'select_recipient' => true,
        );
    }

    return $fields;

}
add_filter( 'gamipress_get_block_fields', 'gamipress_transfers_block_fields', 11, 2 );
