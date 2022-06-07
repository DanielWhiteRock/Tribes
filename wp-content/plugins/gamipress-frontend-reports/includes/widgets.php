<?php
/**
 * Widgets
 *
 * @package     GamiPress\Frontend_Reports\Widgets
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Plain widgets
require_once GAMIPRESS_FRONTEND_REPORTS_DIR .'includes/widgets/points-widget.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR .'includes/widgets/achievements-widget.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR .'includes/widgets/ranks-widget.php';

// Chart widgets
require_once GAMIPRESS_FRONTEND_REPORTS_DIR .'includes/widgets/points-chart-widget.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR .'includes/widgets/points-types-chart-widget.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR .'includes/widgets/achievement-types-chart-widget.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR .'includes/widgets/rank-types-chart-widget.php';

// Graph widgets
require_once GAMIPRESS_FRONTEND_REPORTS_DIR .'includes/widgets/points-graph-widget.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR .'includes/widgets/points-types-graph-widget.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR .'includes/widgets/achievement-types-graph-widget.php';
require_once GAMIPRESS_FRONTEND_REPORTS_DIR .'includes/widgets/rank-types-graph-widget.php';

// Register plugin widgets
function gamipress_frontend_reports_register_widgets() {

    // Plain widgets
    register_widget( 'gamipress_frontend_reports_points_widget' );
    register_widget( 'gamipress_frontend_reports_achievements_widget' );
    register_widget( 'gamipress_frontend_reports_ranks_widget' );

    // Chart widgets
    register_widget( 'gamipress_frontend_reports_points_chart_widget' );
    register_widget( 'gamipress_frontend_reports_points_types_chart_widget' );
    register_widget( 'gamipress_frontend_reports_achievement_types_chart_widget' );
    register_widget( 'gamipress_frontend_reports_rank_types_chart_widget' );

    // Graph widgets
    register_widget( 'gamipress_frontend_reports_points_graph_widget' );
    register_widget( 'gamipress_frontend_reports_points_types_graph_widget' );
    register_widget( 'gamipress_frontend_reports_achievement_types_graph_widget' );
    register_widget( 'gamipress_frontend_reports_rank_types_graph_widget' );

}
add_action( 'widgets_init', 'gamipress_frontend_reports_register_widgets' );