<?php
/**
 * Template Functions
 *
 * @package     GamiPress\Frontend_Reports\Template_Functions
 * @since       1.0.0
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
function gamipress_frontend_reports_template_paths($file_paths) {

    $file_paths[] = trailingslashit(get_stylesheet_directory()) . 'gamipress/frontend-reports/';
    $file_paths[] = trailingslashit(get_template_directory()) . 'gamipress/frontend-reports/';
    $file_paths[] = GAMIPRESS_FRONTEND_REPORTS_DIR . 'templates/';

    return $file_paths;

}

add_filter('gamipress_template_paths', 'gamipress_frontend_reports_template_paths');