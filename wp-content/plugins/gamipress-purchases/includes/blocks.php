<?php
/**
 * Blocks
 *
 * @package     GamiPress\Purchases\Blocks
 * @since       1.0.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


/**
 * Turn select2 fields into 'post' or 'user' field types
 *
 * @since 1.0.7
 *
 * @param array                 $fields
 * @param GamiPress_Shortcode   $shortcode
 *
 * @return array
 */
function gamipress_purchases_block_fields( $fields, $shortcode ) {

    switch ( $shortcode->slug ) {
        case 'gamipress_achievement_purchase':
            // Achievement ID
            $fields['id']['type'] = 'post';
            $fields['id']['post_type'] = gamipress_get_achievement_types_slugs();
            break;
        case 'gamipress_rank_purchase':
            // Rank ID
            $fields['id']['type'] = 'post';
            $fields['id']['post_type'] = gamipress_get_rank_types_slugs();
            break;
        case 'gamipress_points_purchase':
            // Fixed
            $fields['amount']['conditions'] = array(
                'form_type' => 'fixed',
            );

            // Options
            $fields['options']['conditions'] = array(
                'form_type' => 'options',
            );
            $fields['allow_user_input']['conditions'] = array(
                'form_type' => 'options',
            );

            // Used for custom and allow user input options
            $fields['initial_amount']['conditions'] = array(
                'relation' => 'OR',
                'form_type' => 'custom',
                'allow_user_input' => true,
            );

            break;
    }

    if( in_array( $shortcode->slug, array( 'gamipress_achievement_purchase', 'gamipress_rank_purchase', 'gamipress_points_purchase' ) ) ) {
        // For acceptance_text, set as display condition that acceptance needs to be true (checked)
        $fields['acceptance_text']['conditions'] = array(
            'acceptance' => true,
        );
    }

    return $fields;

}
add_filter( 'gamipress_get_block_fields', 'gamipress_purchases_block_fields', 11, 2 );
