<?php
/**
 * Template Functions
 *
 * @package     GamiPress\Restrict_Unlock\Template_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get an array of pattern tags to being used on post restrictions
 *
 * @since  1.0.0

 * @return array The registered pattern tags
 */
function gamipress_restrict_unlock_get_pattern_tags() {

    return apply_filters( 'gamipress_restrict_unlock_pattern_tags', array(
        '{user}'            => __( 'User display name.', 'gamipress-restrict-unlock' ),
        '{user_first}'      => __( 'User first name.', 'gamipress-restrict-unlock' ),
        '{user_last}'       => __( 'User last name.', 'gamipress-restrict-unlock' ),
        '{site_title}'      => __( 'Site name.', 'gamipress-restrict-unlock' ),
        '{site_link}'       => __( 'Link to the site with site name as text.', 'gamipress-restrict-unlock' ),
        '{requirements}'    => __( 'A list with the requirements (already completed by the user will look as completed).', 'gamipress-restrict-unlock' ),
        '{points}'          => __( 'The amount of points to get access.', 'gamipress-restrict-unlock' ),
        '{points_balance}'  => __( 'The full amount of points user has earned until this date.', 'gamipress-restrict-unlock' ),
        '{points_type}'     => __( 'The points type label of points to get access. Singular or plural is based on the amount of points to get access.', 'gamipress-restrict-unlock' ),
    ) );

}

/**
 * Get a string with the desired achievement pattern tags html markup
 *
 * @since   1.0.0
 *
 * @return string               Pattern tags html markup
 */
function gamipress_restrict_unlock_get_pattern_tags_html() {

    $tags = gamipress_restrict_unlock_get_pattern_tags();

    $output = '<ul class="gamipress-pattern-tags-list gamipress-restrict-unlock-pattern-tags-list">';

    foreach( $tags as $tag => $description ) {

        $attr_id = 'tag-' . str_replace( array( '{', '}', '_' ), array( '', '', '-' ), $tag );

        $output .= "<li id='{$attr_id}'><code>{$tag}</code> - {$description}</li>";
    }

    $output .= '</ul>';

    return $output;

}

/**
 * Parse pattern tags to a given post pattern
 *
 * @since  1.0.0
 *
 * @param string    $pattern
 * @param int       $post_id
 * @param int       $user_id
 *
 * @return string Parsed pattern
 */
function gamipress_restrict_unlock_parse_pattern( $pattern, $post_id = null, $user_id = null ) {

    if( $post_id === null ) {
        $post_id = get_the_ID();
    }

    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    $user = get_userdata( $user_id );

    $requirements_html = '';

    $prefix = '_gamipress_restrict_unlock_';

    if( gamipress_restrict_unlock_get_meta( $post_id, 'unlock_by' ) === 'complete-requirements' ) {

        $requirements = gamipress_restrict_unlock_get_meta( $post_id, 'requirements' );

        if( is_array( $requirements ) && count( $requirements ) ) {

            $requirements_html .= "<ul>";

            foreach( $requirements as $requirement ) {
                // Check if user has earned this requirement, and add an 'earned' class
                $earned = gamipress_restrict_unlock_user_meets_requirement( $requirement, $user_id );

                $requirements_html .= '<li style="' . ( $earned ? 'text-decoration: line-through;' : '' ) . '">'
                    .  apply_filters( 'gamipress_restrict_unlock_requirement_label', $requirement[$prefix . 'label'], $post_id, $user_id )
                    . '</li>';
            }

            $requirements_html .= "</ul>";
        }

    }

    // Setup points vars
    $points = absint( gamipress_restrict_unlock_get_meta( $post_id, 'points_to_access' ) );
    $points_types = gamipress_get_points_types();
    $points_type = gamipress_restrict_unlock_get_meta( $post_id, 'points_type_to_access' );

    // Default points label
    $points_singular_label = __( 'Point', 'gamipress' );
    $points_plural_label = __( 'Points', 'gamipress' );

    if( isset( $points_types[$points_type] ) ) {
        // Points type label
        $points_singular_label = $points_types[$points_type]['singular_name'];
        $points_plural_label = $points_types[$points_type]['plural_name'];
    }

    $pattern_replacements = array(
        '{user}'                => $user ? $user->display_name : '',
        '{user_first}'          => $user ? $user->first_name : '',
        '{user_last}'           => $user ? $user->last_name : '',
        '{site_title}'          => get_bloginfo( 'name' ),
        '{site_link}'           => '<a href="' . esc_url( home_url() ) . '">' . get_bloginfo( 'name' ) . '</a>',
        '{requirements}'        => $requirements_html,
        '{points}'              => gamipress_format_amount( $points, $points_type ),
        '{points_balance}'      => gamipress_get_user_points( $user_id, $points_type ),
        '{points_type}'         => _n( $points_singular_label, $points_plural_label, $points ),
    );

    $pattern_replacements = apply_filters( 'gamipress_restrict_unlock_parse_pattern_replacements', $pattern_replacements, $pattern, $post_id, $user_id );

    return apply_filters( 'gamipress_restrict_unlock_parse_pattern', str_replace( array_keys( $pattern_replacements ), $pattern_replacements, $pattern ), $pattern_replacements, $pattern, $post_id, $user_id );

}

/**
 * Helper function to determine if there is any markup to render
 *
 * @since  1.0.0
 *
 * @param int $post_id
 * @param int $user_id
 *
 * @return void
 */
function gamipress_restrict_unlock_maybe_render_markup( $post_id = null, $user_id = null ) {

    // Grab the current post ID if not given
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    // Grab the current logged in user ID if not given
    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    // Guest not supported yet (basically because they has not points)
    if( $user_id === 0 ) {
        return;
    }

    if( gamipress_restrict_unlock_is_restricted( $post_id )
        && ! gamipress_restrict_unlock_is_user_granted( $post_id, $user_id ) ) {

        // Adds the informational text markup
        echo gamipress_restrict_unlock_informational_text_markup( $post_id, $user_id );

        // Adds the access with points markup
        echo gamipress_restrict_unlock_access_with_points_markup( $post_id, $user_id );

    }
}

/**
 * HTML markup for the informational text
 *
 * @since  1.0.0
 *
 * @param int $post_id
 * @param int $user_id
 *
 * @return string
 */
function gamipress_restrict_unlock_informational_text_markup( $post_id = null, $user_id = null ) {

    // Grab the current post ID if not given
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    // Grab the current logged in user ID if not given
    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    // Guest not supported yet (basically because they has not points)
    if( $user_id === 0 ) {
        return '';
    }

    $informational_text = gamipress_restrict_unlock_get_meta( $post_id, 'informational_text' );

    $informational_text = gamipress_restrict_unlock_parse_pattern( $informational_text );

    /**
     * Available filter to override button text when unlock a restricted post with points
     *
     * @since 1.0.0
     *
     * @param string    $informational_text     The informational text setup
     * @param int       $post_id                The restricted post ID
     * @param int       $user_id                The current logged in user ID
     */
    $informational_text = apply_filters( 'gamipress_restrict_unlock_informational_text', $informational_text, $post_id, $user_id );

    // Text formatting and shortcode execution
    $informational_text = wpautop( $informational_text );
    $informational_text = do_shortcode( $informational_text );

    ob_start(); ?>
    <div class="gamipress-restrict-unlock-informational-text"><?php echo $informational_text; ?></div>
    <?php $output = ob_get_clean();

    // Return our markup
    return apply_filters( 'gamipress_restrict_unlock_informational_text_markup', $output, $post_id, $user_id );

}

/**
 * HTML markup for the "Get access by using points" button to unlock a post
 *
 * @since  1.0.0
 *
 * @param int $post_id
 * @param int $user_id
 *
 * @return string
 */
function gamipress_restrict_unlock_access_with_points_markup( $post_id = null, $user_id = null ) {

    // Grab the current post ID if not given
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    // Grab the current logged in user ID if not given
    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    // Guest not supported yet (basically because they has not points)
    if( $user_id === 0 ) {
        return '';
    }

    // Return if user already has got access
    if( gamipress_restrict_unlock_user_has_unlocked_post( $post_id, $user_id ) ) {
        return '';
    }

    // Check if is unlocked by expending points or is allowed access by expending points
    if( gamipress_restrict_unlock_get_meta( $post_id, 'unlock_by' ) !== 'expend-points'
        && gamipress_restrict_unlock_get_meta( $post_id, 'access_with_points' ) !== 'on' ) {
        return '';
    }

    $points = absint( gamipress_restrict_unlock_get_meta( $post_id, 'points_to_access' ) );

    // Return if no points configured
    if( $points === 0 ) {
        return '';
    }

    // Setup vars
    $points_type = gamipress_restrict_unlock_get_meta( $post_id, 'points_type_to_access' );

    $button_label = sprintf( __( 'Get access using %s', 'gamipress-restrict-unlock' ), gamipress_format_points( $points, $points_type ) );

    /**
     * Available filter to override button text when unlock a restricted post with points
     *
     * @since 1.0.0
     *
     * @param string    $button_label   The button label
     * @param int       $post_id        The restricted post ID
     * @param int       $user_id        The current logged in user ID
     * @param int       $points         The required amount of points
     * @param string    $points_type    The required amount points type
     */
    $button_label = apply_filters( 'gamipress_restrict_unlock_access_with_points_button_text', $button_label, $post_id, $user_id, $points, $points_type );

    ob_start(); ?>
    <div class="gamipress-restrict-unlock-access-with-points">
        <div class="gamipress-spinner" style="display: none;"></div>
        <button type="button" class="gamipress-restrict-unlock-access-with-points-button" data-id="<?php echo $post_id; ?>"><?php echo $button_label; ?></button>
    </div>
    <?php $output = ob_get_clean();

    // Return our markup
    return apply_filters( 'gamipress_restrict_unlock_access_with_points_markup', $output, $post_id, $user_id, $points, $points_type );

}