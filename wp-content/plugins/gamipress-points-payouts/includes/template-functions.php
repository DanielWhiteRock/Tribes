<?php
/**
 * Template Functions
 *
 * @package GamiPress\Points_Payouts\Template_Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin templates directory on GamiPress template engine
 *
 * @param array $file_paths
 *
 * @return array
 * @since 1.0.0
 *
 */
function gamipress_points_payouts_template_paths($file_paths) {

    $file_paths[] = trailingslashit(get_stylesheet_directory()) . 'gamipress/points-payouts/';
    $file_paths[] = trailingslashit(get_template_directory()) . 'gamipress/points-payouts/';
    $file_paths[] = GAMIPRESS_POINTS_PAYOUTS_DIR . 'templates/';

    return $file_paths;

}

add_filter('gamipress_template_paths', 'gamipress_points_payouts_template_paths');

/**
 * Common user pattern tags
 *
 * @since  1.0.0

 * @return array The registered pattern tags
 */
function gamipress_points_payouts_get_user_pattern_tags() {

    return apply_filters( 'gamipress_points_payouts_user_pattern_tags', array(
        '{user}'                => __( 'User display name.', 'gamipress-points-payouts' ),
        '{user_email}'          => __( 'User email.', 'gamipress-points-payouts' ),
        '{user_first}'          => __( 'User first name.', 'gamipress-points-payouts' ),
        '{user_last}'           => __( 'User last name.', 'gamipress-points-payouts' ),
        '{user_id}'             => __( 'User ID (useful for shortcodes that user ID can be passed as attribute).', 'gamipress-points-payouts' ),
    ) );

}

/**
 * Parse user pattern tags to a given pattern
 *
 * @since  1.0.0
 *
 * @param string    $pattern
 * @param int       $user_id
 *
 * @return string Parsed pattern
 */
function gamipress_points_payouts_parse_user_pattern( $pattern, $user_id ) {

    if( absint( $user_id ) === 0 ) {
        $user_id = get_current_user_id();
    }

    $user = get_userdata( $user_id );

    $pattern_replacements = array(
        '{user}'                =>  ( $user ? $user->display_name : '' ),
        '{user_email}'          =>  ( $user ? $user->user_email : '' ),
        '{user_first}'          =>  ( $user ? $user->first_name : '' ),
        '{user_last}'           =>  ( $user ? $user->last_name : '' ),
        '{user_id}'             =>  ( $user ? $user->ID : '' ),
    );

    $pattern_replacements = apply_filters( 'gamipress_points_payouts_parse_user_pattern_replacements', $pattern_replacements, $pattern );

    return apply_filters( 'gamipress_points_payouts_parse_user_pattern', str_replace( array_keys( $pattern_replacements ), $pattern_replacements, $pattern ), $pattern );

}


/**
 * Get an array of pattern tags
 *
 * @since  1.0.0

 * @return array The registered pattern tags
 */
function gamipress_points_payouts_get_pattern_tags() {

    return apply_filters( 'gamipress_points_payouts_pattern_tags', array_merge(
        gamipress_points_payouts_get_user_pattern_tags(),
        array(
            '{id}'                      => __( 'The points payout number.', 'gamipress-expirations' ),
            '{points}'                  => __( 'The amount of points requested.', 'gamipress-expirations' ),
            '{points_label}'            => __( 'The points type label. Singular or plural is based on the amount of points requested.', 'gamipress-expirations' ),
            '{money}'                   => __( 'The amount of money to pay.', 'gamipress-points-payouts' ),
            '{payment_method_label}'    => __( 'The payment method label (configurable on Points Payout settings).', 'gamipress-points-payouts' ),
            '{payment_method}'          => __( 'The payment method that user entered.', 'gamipress-points-payouts' ),
        )
    ) );

}

/**
 * Get a string with the desired pattern tags html markup
 *
 * @since  1.0.0
 *
 * @return string Pattern tags html markup
 */
function gamipress_points_payouts_get_pattern_tags_html() {

    $output = ' <a href="" class="gamipress-pattern-tags-list-toggle" data-show-text="' . __( 'Show tags', 'gamipress-points-payouts' ) . '" data-hide-text="' . __( 'Show tags', 'gamipress-points-payouts' ) . '">' . __( 'Show tags', 'gamipress-points-payouts' ) . '</a>';
    $output .= '<ul class="gamipress-pattern-tags-list gamipress-points-payouts-pattern-tags-list" style="display: none;">';

    foreach( gamipress_points_payouts_get_pattern_tags() as $tag => $description ) {

        $attr_id = 'tag-' . str_replace( array( '{', '}', '_' ), array( '', '', '-' ), $tag );

        $output .= "<li id='{$attr_id}'><code>{$tag}</code> - {$description}</li>";
    }

    $output .= '</ul>';

    return $output;

}

/**
 * Parse pattern tags to a given pattern
 *
 * @since  1.0.0
 *
 * @param string $pattern
 * @param int $user_id
 * @param stdClass $points_payout
 *
 * @return string Parsed pattern
 */
function gamipress_points_payouts_parse_pattern( $pattern, $user_id, $points_payout ) {

    // Parse user replacements
    $pattern = gamipress_points_payouts_parse_user_pattern( $pattern, $user_id );

    // Parse replacements
    $pattern_replacements = array(
        '{id}'                      => $points_payout->points_payout_id,
        '{points}'                  => $points_payout->points,
        '{points_label}'            => gamipress_get_points_amount_label( $points_payout->points, $points_payout->points_type, true ),
        '{money}'                   => gamipress_points_payouts_format_price( $points_payout->money ),
        '{payment_method_label}'    => gamipress_points_payouts_get_option( 'payment_method_text', __( 'Payment Method', 'gamipress-points-payouts' ) ),
        '{payment_method}'          =>  gamipress_get_user_meta( $user_id, '_gamipress_points_payouts_payment_method', true ),
    );

    $pattern_replacements = apply_filters( 'gamipress_points_payouts_parse_pattern_replacements', $pattern_replacements, $pattern );

    return apply_filters( 'gamipress_points_payouts_parse_pattern', str_replace( array_keys( $pattern_replacements ), $pattern_replacements, $pattern ), $pattern );

}