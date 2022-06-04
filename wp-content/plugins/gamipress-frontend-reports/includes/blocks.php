<?php
/**
 * Blocks
 *
 * @package     GamiPress\Frontend_Reports\Blocks
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Override blocks fields parameters
 *
 * @since 1.0.0
 *
 * @param array                 $fields
 * @param GamiPress_Shortcode   $shortcode
 *
 * @return array
 */
function gamipress_frontend_reports_block_fields( $fields, $shortcode ) {
    switch ( $shortcode->slug ) {
        // Chart blocks
        case 'gamipress_frontend_reports_points_chart':
        case 'gamipress_frontend_reports_points_types_chart':
        case 'gamipress_frontend_reports_achievement_types_chart':
        case 'gamipress_frontend_reports_rank_types_chart':
            // Style conditional fields
            $inline_condition = array(
                array(
                    'field_id' => 'style',
                    'value' => 'inline',
                    'compare' => '!=',
                )
            );

            $inline_doughnut_pie_condition = array(
                array(
                    'field_id' => 'style',
                    'value' => array( 'inline', 'doughnut', 'pie' ),
                    'compare' => 'NOT IN',
                )
            );

            $fields['legend']['conditions'] = $inline_condition;
            $fields['background']['conditions'] = $inline_condition;
            $fields['border']['conditions'] = $inline_condition;
            $fields['grid']['conditions'] = $inline_doughnut_pie_condition;
            $fields['max_ticks']['conditions'] = $inline_doughnut_pie_condition;

            // Color fields ID
            $fields['background']['type'] = 'text';
            $fields['background']['description'] = $fields['background']['shortcode_desc'];
            $fields['border']['type'] = 'text';
            $fields['border']['description'] = $fields['border']['shortcode_desc'];
            $fields['grid']['type'] = 'text';
            break;
        // Graph blocks
        case 'gamipress_frontend_reports_points_graph':
        case 'gamipress_frontend_reports_points_types_graph':
        case 'gamipress_frontend_reports_achievement_types_graph':
        case 'gamipress_frontend_reports_rank_types_graph':
            // Period conditional fields
            $period_condition = array(
                'period_value' => 'custom'
            );

            $fields['period_start']['conditions'] = $period_condition;
            $fields['period_end']['conditions'] = $period_condition;

            // Color fields ID
            $fields['background']['type'] = 'text';
            $fields['background']['description'] = $fields['background']['shortcode_desc'];
            $fields['border']['type'] = 'text';
            $fields['border']['description'] = $fields['border']['shortcode_desc'];
            $fields['grid']['type'] = 'text';
            break;
    }

    return $fields;

}
add_filter( 'gamipress_get_block_fields', 'gamipress_frontend_reports_block_fields', 11, 2 );