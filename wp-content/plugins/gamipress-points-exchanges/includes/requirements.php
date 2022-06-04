<?php
/**
 * Requirements
 *
 * @package GamiPress\Points_Exchanges\Requirements
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add custom fields to the requirement object
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_points_exchanges_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] ) ) {

        // The points type fields
        if ( $requirement['trigger_type'] === 'gamipress_points_exchanges_new_points_exchange' ) {
            $requirement['points_exchanges_points_type'] = gamipress_get_post_meta( $requirement_id, '_gamipress_points_exchanges_points_type', true );
            $requirement['points_exchanges_points_amount'] = gamipress_get_post_meta( $requirement_id, '_gamipress_points_exchanges_points_amount', true );
        }

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_points_exchanges_requirement_object', 10, 2 );

/**
 * Custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_points_exchanges_requirement_ui_fields( $requirement_id, $post_id ) {

    // Get our types
    $points_types = gamipress_get_points_types();

    // Setup vars
    $requirement = gamipress_get_requirement_object( $requirement_id );
    $points_type_selected = isset( $requirement['points_exchanges_points_type'] ) ? $requirement['points_exchanges_points_type'] : '';
    ?>

    <?php // Points type fields ?>

    <select id="select-points-exchanges-points-type-<?php echo $requirement_id; ?>" class="select-points-exchanges-points-type">
        <?php foreach( $points_types as $slug => $data ) : ?>
            <option value="<?php echo $slug; ?>" <?php selected( $points_type_selected, $slug ); ?>><?php echo $data['plural_name']; ?></option>
        <?php endforeach; ?>
    </select>

    <input type="number" id="input-<?php echo $requirement_id; ?>-points-exchanges-points-amount" class="input-points-exchanges-points-amount" value="<?php echo ( isset( $requirement['points_exchanges_points_amount'] ) ? absint( $requirement['points_exchanges_points_amount'] ) : 0 ); ?>" />
    <span class="points-exchanges-points-amount-text"><?php _e( '(0 for no minimum)', 'gamipress-points-exchanges' ); ?></span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_points_exchanges_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_points_exchanges_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] ) ) {

        // The points type fields
        if ( $requirement['trigger_type'] === 'gamipress_points_exchanges_new_points_exchange' ) {
            gamipress_update_post_meta( $requirement_id, '_gamipress_points_exchanges_points_type', $requirement['points_exchanges_points_type'] );
            gamipress_update_post_meta( $requirement_id, '_gamipress_points_exchanges_points_amount', $requirement['points_exchanges_points_amount'] );
        }

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_points_exchanges_ajax_update_requirement', 10, 2 );