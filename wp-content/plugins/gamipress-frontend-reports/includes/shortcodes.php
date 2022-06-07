<?php
/**
 * Shortcodes
 *
 * @package     GamiPress\Frontend_Reports\Shortcodes
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// GamiPress Frontend Reports Shortcodes

// Plain shortcodes
require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/shortcodes/gamipress_frontend_reports_points.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/shortcodes/gamipress_frontend_reports_achievements.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/shortcodes/gamipress_frontend_reports_ranks.php';

// Chart shortcodes
require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/shortcodes/gamipress_frontend_reports_points_chart.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/shortcodes/gamipress_frontend_reports_points_types_chart.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/shortcodes/gamipress_frontend_reports_achievement_types_chart.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/shortcodes/gamipress_frontend_reports_rank_types_chart.php';

// Graph shortcodes
require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/shortcodes/gamipress_frontend_reports_points_graph.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/shortcodes/gamipress_frontend_reports_points_types_graph.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/shortcodes/gamipress_frontend_reports_achievement_types_graph.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR . 'includes/shortcodes/gamipress_frontend_reports_rank_types_graph.php';

/**
 * Register plugin shortcode groups
 *
 * @since 1.0.0
 *
 * @param array $shortcode_groups
 *
 * @return array
 */
function gamipress_frontend_reports_shortcodes_groups( $shortcode_groups ) {

    $shortcode_groups['frontend_reports'] = __( 'Frontend Reports', 'gamipress-frontend-reports' );

    return $shortcode_groups;

}
add_filter( 'gamipress_shortcodes_groups', 'gamipress_frontend_reports_shortcodes_groups' );
