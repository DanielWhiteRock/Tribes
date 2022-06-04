<?php
/**
 * Requirements
 *
 * @package GamiPress\Social_Share\Requirements
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the social network field to the requirement object
 *
 * @since 1.0.0
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_social_share_requirement_object( $requirement, $requirement_id ) {

    if(
        isset( $requirement['trigger_type'] )
        && (
            $requirement['trigger_type'] === 'gamipress_social_share_specific_url_on_any_network'
            || $requirement['trigger_type'] === 'gamipress_social_share_specific_url_on_specific_network'
        )
    ) {
        // URL field
        $requirement['social_share_url'] = get_post_meta( $requirement_id, '_gamipress_social_share_url', true );
    }

    if(
        isset( $requirement['trigger_type'] )
        && (
            // URL events
            $requirement['trigger_type'] === 'gamipress_social_share_url_on_specific_network'
            || $requirement['trigger_type'] === 'gamipress_social_share_specific_url_on_specific_network'
            // Post events
            || $requirement['trigger_type'] === 'gamipress_social_share_share_on_specific_network'
            || $requirement['trigger_type'] === 'gamipress_social_share_share_specific_on_specific_network'
            // Author events
            || $requirement['trigger_type'] === 'gamipress_social_share_get_share_on_specific_network'
            || $requirement['trigger_type'] === 'gamipress_social_share_get_share_specific_on_specific_network'
        )
    ) {
        // Social network field
        $requirement['social_network'] = get_post_meta( $requirement_id, '_gamipress_social_share_social_network', true );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_social_share_requirement_object', 10, 2 );

/**
 * Social network field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_social_share_requirement_ui_fields( $requirement_id, $post_id ) {

    $url = get_post_meta( $requirement_id, '_gamipress_social_share_url', true );
    $social_network = get_post_meta( $requirement_id, '_gamipress_social_share_social_network', true );

    ?>
    <input type="text" value="<?php echo $url; ?>" placeholder="http://..." class="social-share-url" style="display: none;"/>

    <select class="select-social-network select-social-network-<?php echo $requirement_id; ?>" style="display: none;">
        <option value=""><?php _e( 'Select a social network', 'gamipress-social-share' ); ?></option>
        <?php foreach( gamipress_social_share_get_social_networks() as $value => $label ) : ?>
            <option value="<?php echo $value; ?>" <?php selected( $social_network, $value ); ?>><?php echo $label; ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_social_share_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the social network on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_social_share_ajax_update_requirement( $requirement_id, $requirement ) {

    if(
        isset( $requirement['trigger_type'] )
        && (
            $requirement['trigger_type'] === 'gamipress_social_share_specific_url_on_any_network'
            || $requirement['trigger_type'] === 'gamipress_social_share_specific_url_on_specific_network'
        )
    ) {
        // Save the url field
        update_post_meta( $requirement_id, '_gamipress_social_share_url', $requirement['social_share_url'] );
    }

    if(
        isset( $requirement['trigger_type'] )
        && (
            // URL events
            $requirement['trigger_type'] === 'gamipress_social_share_url_on_specific_network'
            || $requirement['trigger_type'] === 'gamipress_social_share_specific_url_on_specific_network'
            // Post events
            || $requirement['trigger_type'] === 'gamipress_social_share_share_on_specific_network'
            || $requirement['trigger_type'] === 'gamipress_social_share_share_specific_on_specific_network'
            // Author events
            || $requirement['trigger_type'] === 'gamipress_social_share_get_share_on_specific_network'
            || $requirement['trigger_type'] === 'gamipress_social_share_get_share_specific_on_specific_network'
        )
    ) {
        // Save the social network field
        update_post_meta( $requirement_id, '_gamipress_social_share_social_network', $requirement['social_network'] );
    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_social_share_ajax_update_requirement', 10, 2 );