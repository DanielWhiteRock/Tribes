<?php
/**
 * Requirements
 *
 * @package GamiPress\Transfers\Requirements
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
function gamipress_transfers_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] ) ) {

        // The points type fields
        if ( $requirement['trigger_type'] === 'gamipress_transfers_new_points_transfer'
            || $requirement['trigger_type'] === 'gamipress_transfers_receive_points_transfer' ) {
            $requirement['transfers_points_type'] = gamipress_get_post_meta( $requirement_id, '_gamipress_transfers_points_type', true );
            $requirement['transfers_points_amount'] = gamipress_get_post_meta( $requirement_id, '_gamipress_transfers_points_amount', true );
        }

        // The achievement type fields
        if ( $requirement['trigger_type'] === 'gamipress_transfers_new_achievement_transfer'
            || $requirement['trigger_type'] === 'gamipress_transfers_receive_achievement_transfer' ) {
            $requirement['transfers_achievement_type'] = gamipress_get_post_meta( $requirement_id, '_gamipress_transfers_achievement_type', true );
        }

        // The rank type fields
        if ( $requirement['trigger_type'] === 'gamipress_transfers_new_rank_transfer'
            || $requirement['trigger_type'] === 'gamipress_transfers_receive_rank_transfer' ) {
            $requirement['transfers_rank_type'] = gamipress_get_post_meta( $requirement_id, '_gamipress_transfers_rank_type', true );
        }

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_transfers_requirement_object', 10, 2 );

/**
 * Custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_transfers_requirement_ui_fields( $requirement_id, $post_id ) {

    // Get our types
    $points_types = gamipress_get_points_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();

    // Setup vars
    $requirement = gamipress_get_requirement_object( $requirement_id );
    $points_type_selected = isset( $requirement['transfers_points_type'] ) ? $requirement['transfers_points_type'] : '';
    $achievement_type_selected = isset( $requirement['transfers_achievement_type'] ) ? $requirement['transfers_achievement_type'] : '';
    $rank_type_selected = isset( $requirement['transfers_rank_type'] ) ? $requirement['transfers_rank_type'] : '';
    ?>

    <?php // Points type fields ?>

    <select id="select-transfers-points-type-<?php echo $requirement_id; ?>" class="select-transfers-points-type">
        <?php foreach( $points_types as $slug => $data ) : ?>
            <option value="<?php echo $slug; ?>" <?php selected( $points_type_selected, $slug ); ?>><?php echo $data['plural_name']; ?></option>
        <?php endforeach; ?>
    </select>

    <input type="number" id="input-<?php echo $requirement_id; ?>-transfers-points-amount" class="input-transfers-points-amount" value="<?php echo ( isset( $requirement['transfers_points_amount'] ) ? absint( $requirement['transfers_points_amount'] ) : 0 ); ?>" />
    <span class="transfers-points-amount-text"><?php _e( '(0 for no minimum)', 'gamipress-purchaes' ); ?></span>
    <?php // Achievement type fields ?>

    <select id="select-transfers-achievement-type-<?php echo $requirement_id; ?>" class="select-transfers-achievement-type">
        <?php foreach( $achievement_types as $slug => $data ) : ?>
            <option value="<?php echo $slug; ?>" <?php selected( $achievement_type_selected, $slug ); ?>><?php echo $data['singular_name']; ?></option>
        <?php endforeach; ?>
    </select>

    <?php // Rank type fields ?>

    <select id="select-transfers-rank-type-<?php echo $requirement_id; ?>" class="select-transfers-rank-type">
        <?php foreach( $rank_types as $slug => $data ) : ?>
            <option value="<?php echo $slug; ?>" <?php selected( $rank_type_selected, $slug ); ?>><?php echo $data['singular_name']; ?></option>
        <?php endforeach; ?>
    </select>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_transfers_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_transfers_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] ) ) {

        // The points type fields
        if ( $requirement['trigger_type'] === 'gamipress_transfers_new_points_transfer'
            || $requirement['trigger_type'] === 'gamipress_transfers_receive_points_transfer' ) {
            update_post_meta( $requirement_id, '_gamipress_transfers_points_type', $requirement['transfers_points_type'] );
            update_post_meta( $requirement_id, '_gamipress_transfers_points_amount', $requirement['transfers_points_amount'] );
        }

        // The achievement type fields
        if ( $requirement['trigger_type'] === 'gamipress_transfers_new_achievement_transfer'
            || $requirement['trigger_type'] === 'gamipress_transfers_receive_achievement_transfer' ) {
            update_post_meta( $requirement_id, '_gamipress_transfers_achievement_type', $requirement['transfers_achievement_type'] );
        }

        // The rank type fields
        if ( $requirement['trigger_type'] === 'gamipress_transfers_new_rank_transfer'
            || $requirement['trigger_type'] === 'gamipress_transfers_receive_rank_transfer' ) {
            update_post_meta( $requirement_id, '_gamipress_transfers_rank_type', $requirement['transfers_rank_type'] );
        }

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_transfers_ajax_update_requirement', 10, 2 );