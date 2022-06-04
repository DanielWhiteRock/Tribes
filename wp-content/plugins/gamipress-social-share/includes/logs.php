<?php
/**
 * Logs
 *
 * @package GamiPress\Social_Share\Logs
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.0
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_social_share_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    // Store the URL
    if( in_array( $trigger, array(
        'gamipress_social_share_url_on_any_network',
        'gamipress_social_share_specific_url_on_any_network',
        'gamipress_social_share_url_on_specific_network',
        'gamipress_social_share_specific_url_on_specific_network'
    ) ) ) {
        $log_meta['url'] = $args[0];
    }

    // Store the social network
    if( in_array( $trigger, array_keys( gamipress_social_share_get_activity_triggers() ) ) ) {
        $log_meta['social_network'] = $args[2];
    }

    return $log_meta;

}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_social_share_log_event_trigger_meta_data', 10, 5 );

/**
 * Custom fields for logs
 *
 * @since 1.0.0
 *
 * @param array 	$fields
 * @param integer 	$post_id
 * @param string 	$type
 * @param stdClass 	$object
 *
 * @return array
 */
function gamipress_social_share_log_extra_data_fields( $fields, $post_id, $type, $object ) {

    $prefix = '_gamipress_';

    if( $type === 'event_trigger' ) {

        // URL field
        if( in_array( $object->trigger_type, array(
            'gamipress_social_share_url_on_any_network',
            'gamipress_social_share_specific_url_on_any_network',
            'gamipress_social_share_url_on_specific_network',
            'gamipress_social_share_specific_url_on_specific_network'
        ) ) ) {
            $fields[] = array(
                'name' 	=> __( 'URL', 'gamipress-social-share' ),
                'desc' 	=> __( 'URL assigned to this log.', 'gamipress-social-share' ),
                'id'   	=> $prefix . 'url',
                'type' 	=> 'text',
            );
        }

        // Social network field
        if( in_array( $object->trigger_type, array_keys( gamipress_social_share_get_activity_triggers() ) ) ) {
            $fields[] = array(
                'name' 	=> __( 'Social Network', 'gamipress-social-share' ),
                'desc' 	=> __( 'Social network assigned to this log.', 'gamipress-social-share' ),
                'id'   	=> $prefix . 'social_network',
                'type' 	=> 'advanced_select',
                'options' 	=> gamipress_social_share_get_social_networks(),
            );
        }
    }

    return $fields;

}
add_filter( 'gamipress_log_extra_data_fields', 'gamipress_social_share_log_extra_data_fields', 10, 4 );