<?php
/**
 * Blocks
 *
 * @package     GamiPress\Submissions\Blocks
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Turn select2 fields into 'post' or 'user' field types
 *
 * @since 1.0.0
 *
 * @param array                 $fields
 * @param GamiPress_Shortcode   $shortcode
 *
 * @return array
 */
function gamipress_submissions_block_fields( $fields, $shortcode ) {

    switch ( $shortcode->slug ) {
        case 'gamipress_submission':
            // ID
            $fields['id']['type'] = 'post';
            $fields['id']['post_type'] = array_merge( gamipress_get_achievement_types_slugs(), gamipress_get_rank_types_slugs() );
            break;
    }

    return $fields;

}
add_filter( 'gamipress_get_block_fields', 'gamipress_submissions_block_fields', 11, 2 );
