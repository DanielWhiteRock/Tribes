<?php
/**
 * Template Functions
 *
 * @package     GamiPress\Transfers\Template_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin templates directory on GamiPress template engine
 *
 * @since 1.0.0
 *
 * @param array $file_paths
 *
 * @return array
 */
function gamipress_transfers_template_paths( $file_paths ) {

    $file_paths[] = trailingslashit( get_stylesheet_directory() ) . 'gamipress/transfers/';
    $file_paths[] = trailingslashit( get_template_directory() ) . 'gamipress/transfers/';
    $file_paths[] = GAMIPRESS_TRANSFERS_DIR . 'templates/';

    return $file_paths;

}
add_filter( 'gamipress_template_paths', 'gamipress_transfers_template_paths' );

/**
 * Get an array of recipient pattern tags based on custom context
 *
 * @since 1.1.7

 * @return array                Recipient pattern tags
 */
function gamipress_transfers_get_recipient_pattern_tags() {

    return apply_filters( 'gamipress_transfers_recipient_pattern_tags', array(
        '{user_id}'         => __( 'Recipient ID.', 'gamipress-transfers' ),
        '{user}'            => __( 'Recipient display name.', 'gamipress-transfers' ),
        '{user_first}'      => __( 'Recipient first name.', 'gamipress-transfers' ),
        '{user_last}'       => __( 'Recipient last name.', 'gamipress-transfers' ),
        '{user_username}'   => __( 'Recipient username.', 'gamipress-transfers' ),
        '{user_email}'      => __( 'Recipient email.', 'gamipress-transfers' ),
    ) );

}

/**
 * Get a string with the recipient pattern tags html markup
 *
 * @since 1.1.7
 *
 * @return string Recipient pattern tags html markup
 */
function gamipress_transfers_get_recipient_pattern_tags_html() {

    $js = 'jQuery(this).parent().parent().find(\'.gamipress-pattern-tags-list\').slideToggle();'
        .'jQuery(this).text( ( jQuery(this).text() === \'Hide\' ? \'Show\' : \'Hide\') );';

    $output = '<a href="javascript:void(0);" onclick="' . $js . '">Show</a>';
    $output .= '<ul class="gamipress-pattern-tags-list gamipress-transfers-recipient-pattern-tags-list" style="display: none;">';

    foreach( gamipress_transfers_get_recipient_pattern_tags() as $tag => $description ) {

        $attr_id = 'tag-' . str_replace( array( '{', '}', '_' ), array( '', '', '-' ), $tag );

        $output .= "<li id='{$attr_id}'><code>{$tag}</code> - {$description}</li>";
    }

    $output .= '</ul>';

    return $output;

}

/**
 * Parses that tags in pattern based on recipient given
 *
 * @since 1.1.7
 *
 * @param string $pattern
 * @param WP_User $user
 *
 * @return string
 */
function gamipress_transfers_parse_recipient_tags( $pattern, $user ) {

    $replacements = array();

    $replacements['{user_id}']      =  ( $user ? $user->ID : '' );
    $replacements['{user}']         =  ( $user ? $user->display_name : '' );
    $replacements['{user_first}']   =  ( $user ? $user->first_name : '' );
    $replacements['{user_last}']    =  ( $user ? $user->last_name : '' );
    $replacements['{user_username}']    =  ( $user ? $user->user_login : '' );
    $replacements['{user_email}']    =  ( $user ? $user->user_email : '' );

    /**
     * Parse recipient tags
     *
     * @since 1.1.7
     *
     * @param array     $replacements
     * @param WP_User   $user
     * @param array     $template_args
     */
    $replacements = apply_filters( 'gamipress_transfers_parse_recipient_tags', $replacements, $user );

    return str_replace( array_keys( $replacements ), $replacements, $pattern );

}

/**
 * Function to report form error just if logged in user has permissions to manage GamiPress
 *
 * @param string $error_message
 * @return string
 */
function gamipress_transfers_notify_form_error( $error_message ) {

    if( current_user_can( gamipress_get_manager_capability() ) ) {
        // Notify to admins about the form error
        return $error_message;
    } else {
        // Do not output anything for non admins
        return '';
    }

}