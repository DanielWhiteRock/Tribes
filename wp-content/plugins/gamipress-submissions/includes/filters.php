<?php
/**
 * Filters
 *
 * @package GamiPress\Submissions\Filters
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Filter to render achievement submission
 *
 * @since   1.0.0
 *
 * @param int   $achievement_id The achievement ID
 * @param array $template_args  Template received arguments
 */
function gamipress_submissions_achievement_submission_button( $achievement_id, $template_args ) {

    // Set default value
    if( ! isset( $template_args['submissions'] ) ) {
        $template_args['submissions'] = 'yes';
    }

    // Return if submission is set to no
    if( $template_args['submissions'] !== 'yes' ) {
        return;
    }

    $logged_in_user_id = get_current_user_id();

    // Guests not allowed
    if( $logged_in_user_id === 0 ) {
        return;
    }

    // Determine the user to check
    if( isset( $template_args['user_id'] ) ) {
        $user_id = absint( $template_args['user_id'] );
    } else {
        $user_id = $logged_in_user_id;
    }

    // Only show submission button if is displayed for the current logged in user
    if( $user_id !== $logged_in_user_id ) {
        return;
    }

    $earned = gamipress_achievement_user_exceeded_max_earnings( $user_id, $achievement_id );

    // Return if user has completely earned this achievement
    if( $earned ) {
        return;
    }

    echo gamipress_submissions_form_markup( $achievement_id );

}
add_action( 'gamipress_achievement_description_bottom', 'gamipress_submissions_achievement_submission_button', 10, 2 );
add_action( 'gamipress_single_achievement_description_bottom', 'gamipress_submissions_achievement_submission_button', 10, 2 );

/**
 * Filter to render rank submission
 *
 * @since   1.0.0
 *
 * @param int   $rank_id        The rank ID
 * @param array $template_args  Template received arguments
 */
function gamipress_submissions_rank_submission_button( $rank_id, $template_args ) {

    // Set default value
    if( ! isset( $template_args['submissions'] ) ) {
        $template_args['submissions'] = 'yes';
    }

    // Return if submission is set to no
    if( $template_args['submissions'] !== 'yes' ) {
        return;
    }

    $logged_in_user_id = get_current_user_id();

    // Guests not allowed
    if( $logged_in_user_id === 0 ) {
        return;
    }

    // Determine the user to check
    if( isset( $template_args['user_id'] ) ) {
        $user_id = absint( $template_args['user_id'] );
    } else {
        $user_id = $logged_in_user_id;
    }

    // Only show submission button if is displayed for the current logged in user
    if( $user_id !== $logged_in_user_id ) {
        return;
    }

    $rank_type = gamipress_get_post_type( $rank_id );

    $next_rank_id = gamipress_get_next_user_rank_id( $user_id, $rank_type );

    // Return if not is the next rank to unlock
    if( $next_rank_id !== $rank_id ) {
        return;
    }

    echo gamipress_submissions_form_markup( $rank_id );

}
add_action( 'gamipress_rank_description_bottom', 'gamipress_submissions_rank_submission_button', 10, 2 );
add_action( 'gamipress_single_rank_description_bottom', 'gamipress_submissions_rank_submission_button', 10, 2 );
