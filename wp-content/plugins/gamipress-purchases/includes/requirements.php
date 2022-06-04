<?php
/**
 * Requirements
 *
 * @package GamiPress\Purchases\Requirements
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
function gamipress_purchases_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] ) ) {

        // The points type fields
        if ( $requirement['trigger_type'] === 'gamipress_purchases_new_points_purchase' ) {
            $requirement['purchases_points_type'] = gamipress_get_post_meta( $requirement_id, '_gamipress_purchases_points_type', true );
            $requirement['purchases_points_amount'] = gamipress_get_post_meta( $requirement_id, '_gamipress_purchases_points_amount', true );
        }

        // The achievement type fields
        if ( $requirement['trigger_type'] === 'gamipress_purchases_new_achievement_purchase' ) {
            $requirement['purchases_achievement_type'] = gamipress_get_post_meta( $requirement_id, '_gamipress_purchases_achievement_type', true );
        }

        // The rank type fields
        if ( $requirement['trigger_type'] === 'gamipress_purchases_new_rank_purchase' ) {
            $requirement['purchases_rank_type'] = gamipress_get_post_meta( $requirement_id, '_gamipress_purchases_rank_type', true );
        }

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_purchases_requirement_object', 10, 2 );

/**
 * Custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_purchases_requirement_ui_fields( $requirement_id, $post_id ) {

    // Get our types
    $points_types = gamipress_get_points_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();

    // Setup vars
    $requirement = gamipress_get_requirement_object( $requirement_id );
    $points_type_selected = isset( $requirement['purchases_points_type'] ) ? $requirement['purchases_points_type'] : '';
    $achievement_type_selected = isset( $requirement['purchases_achievement_type'] ) ? $requirement['purchases_achievement_type'] : '';
    $rank_type_selected = isset( $requirement['purchases_rank_type'] ) ? $requirement['purchases_rank_type'] : '';
    ?>

    <?php // Points type fields ?>

    <select id="select-purchases-points-type-<?php echo $requirement_id; ?>" class="select-purchases-points-type">
        <?php foreach( $points_types as $slug => $data ) : ?>
            <option value="<?php echo $slug; ?>" <?php selected( $points_type_selected, $slug ); ?>><?php echo $data['plural_name']; ?></option>
        <?php endforeach; ?>
    </select>

    <input type="number" id="input-<?php echo $requirement_id; ?>-purchases-points-amount" class="input-purchases-points-amount" value="<?php echo ( isset( $requirement['purchases_points_amount'] ) ? absint( $requirement['purchases_points_amount'] ) : 0 ); ?>" />
    <span class="purchases-points-amount-text"><?php _e( '(0 for no minimum)', 'gamipress-purchaes' ); ?></span>
    <?php // Achievement type fields ?>

    <select id="select-purchases-achievement-type-<?php echo $requirement_id; ?>" class="select-purchases-achievement-type">
        <?php foreach( $achievement_types as $slug => $data ) : ?>
            <option value="<?php echo $slug; ?>" <?php selected( $achievement_type_selected, $slug ); ?>><?php echo $data['singular_name']; ?></option>
        <?php endforeach; ?>
    </select>

    <?php // Rank type fields ?>

    <select id="select-purchases-rank-type-<?php echo $requirement_id; ?>" class="select-purchases-rank-type">
        <?php foreach( $rank_types as $slug => $data ) : ?>
            <option value="<?php echo $slug; ?>" <?php selected( $rank_type_selected, $slug ); ?>><?php echo $data['singular_name']; ?></option>
        <?php endforeach; ?>
    </select>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_purchases_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_purchases_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] ) ) {

        // The points type fields
        if ( $requirement['trigger_type'] === 'gamipress_purchases_new_points_purchase' ) {
            update_post_meta( $requirement_id, '_gamipress_purchases_points_type', $requirement['purchases_points_type'] );
            update_post_meta( $requirement_id, '_gamipress_purchases_points_amount', $requirement['purchases_points_amount'] );
        }

        // The achievement type fields
        if ( $requirement['trigger_type'] === 'gamipress_purchases_new_achievement_purchase' ) {
            update_post_meta( $requirement_id, '_gamipress_purchases_achievement_type', $requirement['purchases_achievement_type'] );
        }

        // The rank type fields
        if ( $requirement['trigger_type'] === 'gamipress_purchases_new_rank_purchase' ) {
            update_post_meta( $requirement_id, '_gamipress_purchases_rank_type', $requirement['purchases_rank_type'] );
        }

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_purchases_ajax_update_requirement', 10, 2 );