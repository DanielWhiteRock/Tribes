<?php
/**
 * Ajax Functions
 *
 * @package     GamiPress\Frontend_Reports\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax Helper for returning graph stats
 *
 * @since 1.0.0
 *
 * @return void
 */
function gamipress_frontend_reports_ajax_graph() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_frontend_reports', 'nonce' );

    // ---------------------------
    // Errors
    // ---------------------------

    $graph = $_POST['graph'];

    $user_id = absint( $_POST['user_id'] );

    if( $_POST['current_user'] === 'yes' )
        $user_id = get_current_user_id();

    // ---------------------------
    // Processing
    // ---------------------------

    // Switch values
    $_POST['period_value'] = $_POST['period'];

    // Process period attributes
    $_POST = gamipress_frontend_reports_process_periods( $_POST );

    // Setup the multiples backgrounds and border colors
    $backgrounds = gamipress_frontend_reports_split_colors( $_POST['background'] );
    $borders = gamipress_frontend_reports_split_colors( $_POST['border'] );

    /**
     * Retrieve the stats from this filter, filtered functions should return an array like:
     * array(
     *     'label' => '',
     *     'data' => array(
     *          array( 'date' => 'Y-m-d', 'count' => 0 )
     *     )
     * ),
     *
     * @since 1.0.0
     *
     * @param array $stats
     *
     * @return array
     */
    $stats = apply_filters( "gamipress_frontend_reports_{$graph}_graph_stats", array() );

    $labels = array();

    switch( $_POST['range'] ) {
        case 'week':
            $labels = gamipress_frontend_reports_week_period( $_POST['period_start'] );
            break;
        case 'month':
            $labels = gamipress_frontend_reports_month_period( $_POST['period_start'] );
            break;
        case 'year':
            $labels = gamipress_frontend_reports_year_period( $_POST['period_start'] );
            break;
    }

    // Setup a data pattern as array of elements like: array( 'Y-m-d' => 0 )
    $data_pattern = array();

    foreach ($labels as $label) {
        $data_pattern[$label] = 0;
    }

    $data_sets = array();

    // Setup stats data set
    foreach( $stats as $stat_key => $stat ) {

        $data = $data_pattern;
        $total_count = 0;
        $i = count( $data_sets );

        // Setup background color
        $background = ( isset( $backgrounds[$i] ) ? $backgrounds[$i] : $backgrounds[0] );
        $stat['backgroundColor'] = $background;

        // Setup border color
        $border = ( isset( $borders[$i] ) ? $borders[$i] : $borders[0] );
        $stat['borderColor'] = $border;

        // First parse each set of data, data should be an array like array( 'date' => 'Y-m-d', 'count' => 0 )
        foreach( $stat['data'] as $key => $value ) {

            // For year range, data is stored on different days so we need to force to first day of month
            if( $_POST['range'] === 'year' ) {
                $date = date( 'Y-m-01', strtotime( $value['date'] ) );
            } else {
                $date = $value['date'];
            }

            if( isset( $data[$date] ) ) {
                $data[$date] = absint( $value['count'] );

                $total_count += absint( $value['count'] );
            }

        }

        $stat['data'] = array_values( $data );

        $data_sets[] = $stat;

    }

    // Turn labels date format (Y-m-d) to a more friendly format
    $labels = gamipress_frontend_reports_format_graph_labels( $labels, $_POST['range'] );

    // Send back our successful response
    wp_send_json_success( array(
        'labels' => $labels,
        'datasets' => $data_sets
    ) );

}
add_action( 'wp_ajax_gamipress_frontend_reports_graph', 'gamipress_frontend_reports_ajax_graph' );
add_action( 'wp_ajax_nopriv_gamipress_frontend_reports_graph', 'gamipress_frontend_reports_ajax_graph' );

/**
 * Points graph stats
 *
 * @since 1.0.0
 *
 * @param array $stats
 *
 * @return array
 */
function gamipress_frontend_reports_points_graph_stats( $stats ) {

    $user_id = absint( $_POST['user_id'] );
    $points_type = $_POST['type'];
    $amounts = explode( ',', $_POST['amount'] );
    $query_args = array(
        'select' => array(
            'date' => array( 'cast' => 'DATE' ),
            'count' => array(
                'field' => '_gamipress_points',
                'function' => 'SUM'
            )
        ),
        'where' => array(
            'user_id' => array( 'value' => $user_id ),
            'points_type' => array( 'value' => $points_type ),
            'type' => array() // Type changes per amount type
        ),
        'date_query' => array(
            'after' => $_POST['period_start'],
            'before' => $_POST['period_end'],
        ),
        'group_by' => 'YEAR(l.date), MONTH(l.date)' . ( $_POST['range'] !== 'year' ? ', DAY(l.date)' : '' ),
        'output' => ARRAY_A
    );

    foreach( $amounts as $amount ) {

        if( $amount === 'earned' ) {

            // Earned points

            $query_args['where']['type'] = array(
                'value' => array('points_deduct', 'points_expend', 'points_revoke'),
                'compare' => 'NOT IN'
            );

            $stats['earned'] = array(
                'label' => __('Earned', 'gamipress-frontend-reports'),
                'data' => gamipress_query_logs( $query_args )
            );

        } else if( $amount === 'deducted' ) {

            // Deducted points

            $query_args['where']['type'] = array(
                'value' => array( 'points_deduct', 'points_revoke' ),
                'compare' => 'IN'
            );

            $stats['deducted'] = array(
                'label' => __( 'Deducted', 'gamipress-reports' ),
                'data' => gamipress_query_logs( $query_args )
            );

        } else if( $amount === 'expended' ) {

            // Expended points

            $query_args['where']['type'] = array(
                'value' => 'points_expend',
            );

            $stats['expended'] = array(
                'label' => __( 'Expended', 'gamipress-reports' ),
                'data' => gamipress_query_logs( $query_args )
            );

        }

    }

    return $stats;

}
add_filter( 'gamipress_frontend_reports_points_graph_stats', 'gamipress_frontend_reports_points_graph_stats' );

/**
 * Points types graph stats
 *
 * @since 1.0.0
 *
 * @param array $stats
 *
 * @return array
 */
function gamipress_frontend_reports_points_types_graph_stats( $stats ) {

    $user_id = absint( $_POST['user_id'] );
    $query_args = array(
        'select' => array(
            'date' => array( 'cast' => 'DATE' ),
            'count' => array(
                'field' => '_gamipress_points',
                'function' => 'SUM'
            )
        ),
        'where' => array(
            'user_id' => array( 'value' => $user_id ),
            'points_type' => array(), // Points type changes per points type
            'type' => array() // Type changes per amount type
        ),
        'date_query' => array(
            'after' => $_POST['period_start'],
            'before' => $_POST['period_end'],
        ),
        'group_by' => 'YEAR(l.date), MONTH(l.date)' . ( $_POST['range'] !== 'year' ? ', DAY(l.date)' : '' ),
        'output' => ARRAY_A
    );

    $types = explode( ',', $_POST['type'] );

    if( $_POST['type'] === 'all') {
        $types = gamipress_get_points_types_slugs();
    }

    foreach( $types as $type ) {

        $points_type = gamipress_get_points_type( $type );

        // Bail invalid points types
        if( ! $points_type ) continue;

        // Setup the points type where
        $query_args['where']['points_type'] = array(
            'value' => $type,
        );

        // Setup the type where based on amount
        switch( $_POST['amount'] ) {
            case 'deducted':
                $query_args['where']['type'] = array(
                    'value' => array( 'points_deduct', 'points_revoke' ),
                    'compare' => 'IN'
                );
                break;
            case 'expended':
                $query_args['where']['type'] = array(
                    'value' => 'points_expend',
                );
                break;
            case 'earned':
            default:
                $query_args['where']['type'] = array(
                    'value' => array('points_deduct', 'points_expend', 'points_revoke'),
                    'compare' => 'NOT IN'
                );
                break;
        }

        $stats[$type] = array(
            'label' => $points_type['plural_name'],
            'data' => gamipress_query_logs( $query_args )
        );

    }

    return $stats;

}
add_filter( 'gamipress_frontend_reports_points_types_graph_stats', 'gamipress_frontend_reports_points_types_graph_stats' );

/**
 * Achievement types graph stats
 *
 * @since 1.0.0
 *
 * @param array $stats
 *
 * @return array
 */
function gamipress_frontend_reports_achievement_types_graph_stats( $stats ) {

    $user_id = absint( $_POST['user_id'] );
    $query_args = array(
        'select' => array(
            'date' => array( 'cast' => 'DATE' ),
            'count' => array(
                'field' => 'log_id',
                'function' => 'COUNT'
            )
        ),
        'where' => array(
            'user_id' => array( 'value' => $user_id ),
            'achievement_id' => array(), // Achievements IDs changes per achievement type
            'type' => array( 'value' => 'achievement_earn' )
        ),
        'date_query' => array(
            'after' => $_POST['period_start'],
            'before' => $_POST['period_end'],
        ),
        'group_by' => 'YEAR(l.date), MONTH(l.date)' . ( $_POST['range'] !== 'year' ? ', DAY(l.date)' : '' ),
        'output' => ARRAY_A
    );

    $types = explode( ',', $_POST['type'] );

    if( $_POST['type'] === 'all') {
        $types = gamipress_get_achievement_types_slugs();
    }

    foreach( $types as $type ) {

        $achievement_type = gamipress_get_achievement_type( $type );

        // Bail invalid achievement types
        if( ! $achievement_type ) continue;

        // Get all achievements IDs of this type
        $achievement_ids = get_posts( array(
            'post_type'       => $achievement_type,
            'post_status'     => 'any',
            'fields'          => 'ids',
            'posts_per_page'  => -1
        ) );

        // Setup the achievements IDs where
        $query_args['where']['achievement_id'] = array(
            'value' => $achievement_ids,
            'compare' => 'IN'
        );

        $stats[$type] = array(
            'label' => $achievement_type['plural_name'],
            'data' => gamipress_query_logs( $query_args )
        );

    }

    return $stats;

}
add_filter( 'gamipress_frontend_reports_achievement_types_graph_stats', 'gamipress_frontend_reports_achievement_types_graph_stats' );

/**
 * Rank types graph stats
 *
 * @since 1.0.0
 *
 * @param array $stats
 *
 * @return array
 */
function gamipress_frontend_reports_rank_types_graph_stats( $stats ) {

    $user_id = absint( $_POST['user_id'] );
    $query_args = array(
        'select' => array(
            'date' => array( 'cast' => 'DATE' ),
            'count' => array(
                'field' => 'log_id',
                'function' => 'COUNT'
            )
        ),
        'where' => array(
            'user_id' => array( 'value' => $user_id ),
            'rank_id' => array(), // Ranks IDs changes per rank type
            'type' => array( 'value' => 'rank_earn' )
        ),
        'date_query' => array(
            'after' => $_POST['period_start'],
            'before' => $_POST['period_end'],
        ),
        'group_by' => 'YEAR(l.date), MONTH(l.date)' . ( $_POST['range'] !== 'year' ? ', DAY(l.date)' : '' ),
        'output' => ARRAY_A
    );

    $types = explode( ',', $_POST['type'] );

    if( $_POST['type'] === 'all') {
        $types = gamipress_get_rank_types_slugs();
    }

    foreach( $types as $type ) {

        $rank_type = gamipress_get_rank_type( $type );

        // Bail invalid rank types
        if( ! $rank_type ) continue;

        // Get all ranks IDs of this type
        $rank_ids = get_posts( array(
            'post_type'       => $rank_type,
            'post_status'     => 'any',
            'fields'          => 'ids',
            'posts_per_page'  => -1
        ) );

        // Setup the ranks IDs where
        $query_args['where']['rank_id'] = array(
            'value' => $rank_ids,
            'compare' => 'IN'
        );

        $stats[$type] = array(
            'label' => $rank_type['plural_name'],
            'data' => gamipress_query_logs( $query_args )
        );

    }

    return $stats;

}
add_filter( 'gamipress_frontend_reports_rank_types_graph_stats', 'gamipress_frontend_reports_rank_types_graph_stats' );