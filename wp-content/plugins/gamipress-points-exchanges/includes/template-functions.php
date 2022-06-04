<?php
/**
 * Template Functions
 *
 * @package     GamiPress\Points_Exchanges\Template_Functions
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
function gamipress_points_exchanges_template_paths( $file_paths ) {

    $file_paths[] = trailingslashit( get_stylesheet_directory() ) . 'gamipress/points-exchanges/';
    $file_paths[] = trailingslashit( get_template_directory() ) . 'gamipress/points-exchanges/';
    $file_paths[] = GAMIPRESS_POINTS_EXCHANGES_DIR . 'templates/';

    return $file_paths;

}
add_filter( 'gamipress_template_paths', 'gamipress_points_exchanges_template_paths' );

/**
 * Function to report form error just if logged in user has permissions to manage GamiPress
 *
 * @since 1.0.0
 *
 * @param string $error_message
 *
 * @return string
 */
function gamipress_points_exchanges_notify_form_error( $error_message ) {

    if( current_user_can( gamipress_get_manager_capability() ) ) {
        // Notify to admins about the form error
        return $error_message;
    } else {
        // Do not output anything for non admins
        return '';
    }

}