<?php
/**
 * Admin Points Reports Page
 *
 * @package     GamiPress\Reports\Admin\Points_Reports
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register points reports page.
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_reports_register_points_reports_page() {

    gamipress_register_reports_page( 'points', __( 'Points Reports', 'gamipress-reports' ) );

}
add_action( 'cmb2_admin_init', 'gamipress_reports_register_points_reports_page' );

/**
 * Register points reports sections
 *
 * @since  1.0.0
 *
 * @return array
 */
function gamipress_reports_register_points_reports_sections( $reports_sections ) {

    foreach( gamipress_get_points_types() as $points_type => $data ) {
        $reports_sections[$points_type] = array(
            'title' => $data['plural_name'],
        );
    }

    return $reports_sections;

}
add_action( 'gamipress_reports_points_sections', 'gamipress_reports_register_points_reports_sections' );

/**
 * Dynamically load hooks by points type
 *
 * @since 1.0.0
 */
function gamipress_reports_load_points_reports() {

    foreach( gamipress_get_points_types() as $points_type => $data ) {
        // Dynamic meta boxes
        add_filter( "gamipress_reports_points_{$points_type}_meta_boxes", 'gamipress_reports_points_meta_boxes' );

        // Dynamic list table filters
        add_filter( "gamipress_reports_{$points_type}_list_table_query", 'gamipress_reports_points_list_table_query', 10, 2 );
        add_filter( "gamipress_reports_manage_{$points_type}_list_table_columns", 'gamipress_reports_points_list_table_columns' );
        add_action( "gamipress_reports_manage_{$points_type}_list_table_custom_column", 'gamipress_reports_points_list_table_custom_column', 10, 3 );

        // Dynamic chart filters
        add_filter( "gamipress_reports_{$points_type}_awarded_chart_stats", 'gamipress_reports_points_awarded_chart_stats', 10, 2 );
        add_filter( "gamipress_reports_{$points_type}_awarded_chart_counters", 'gamipress_reports_points_awarded_chart_counters', 10, 2 );

        add_filter( "gamipress_reports_{$points_type}_deducted_chart_stats", 'gamipress_reports_points_deducted_chart_stats', 10, 2 );
        add_filter( "gamipress_reports_{$points_type}_deducted_chart_counters", 'gamipress_reports_points_deducted_chart_counters', 10, 2 );

        add_filter( "gamipress_reports_{$points_type}_expended_chart_stats", 'gamipress_reports_points_expended_chart_stats', 10, 2 );
        add_filter( "gamipress_reports_{$points_type}_expended_chart_counters", 'gamipress_reports_points_expended_chart_counters', 10, 2 );
    }

}
add_action( 'cmb2_admin_init', 'gamipress_reports_load_points_reports', 5 );

/**
 * Points type reports meta boxes
 *
 * @since 1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_reports_points_meta_boxes( $meta_boxes ) {

    $points_type = str_replace( 'gamipress_reports_points_', '', str_replace( '_meta_boxes', '', current_filter() ) );

    // Widgets
    $meta_boxes[$points_type . '-circulation'] = array(
        'classes' => 'gamipress-reports-col-3',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'In Circulation', 'gamipress-reports' ),
            'icon' => 'dashicons-image-rotate',
            'color' => 'blue'
        ) ),
    );

    $meta_boxes[$points_type . '-awarded'] = array(
        'classes' => 'gamipress-reports-col-3',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Awarded', 'gamipress-reports' ),
            'icon' => 'dashicons-star-filled',
            'color' => 'green'
        ) ),
    );

    $meta_boxes[$points_type . '-deducted'] = array(
        'classes' => 'gamipress-reports-col-3',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Deducted', 'gamipress-reports' ),
            'icon' => 'dashicons-star-empty',
            'color' => 'red'
        ) ),
    );

    $meta_boxes[$points_type . '-expended'] = array(
        'classes' => 'gamipress-reports-col-3',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Expended', 'gamipress-reports' ),
            'icon' => 'dashicons-update',
            'color' => 'purple'
        ) ),
    );

    // Awarded chart
    $awarded_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $points_type . '_awarded_chart',
        'type' => 'line',
        'points_type' => $points_type,
    ) );

    ob_start();
    $awarded_chart->display();
    $awarded_chart_html = ob_get_clean();

    $meta_boxes[$points_type . '-awarded-stats'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => $awarded_chart_html,
    );

    // Deducted chart
    $deducted_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $points_type . '_deducted_chart',
        'type' => 'line',
        'points_type' => $points_type,
    ) );

    ob_start();
    $deducted_chart->display();
    $deducted_chart_html = ob_get_clean();

    $meta_boxes[$points_type . '-deducted-stats'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => $deducted_chart_html,
    );

    // Expended chart
    $expended_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $points_type . '_expended_chart',
        'type' => 'line',
        'points_type' => $points_type,
    ) );

    ob_start();
    $expended_chart->display();
    $expended_chart_html = ob_get_clean();

    $meta_boxes[$points_type . '-expended-stats'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => $expended_chart_html,
    );

    // List table
    $meta_boxes[$points_type . '-list-table'] = array(
        'title' => __( 'Users Report', 'gamipress-reports' ),
        'classes' => 'gamipress-reports-col-12',
        'content' => '<div></div>',
    );

    return $meta_boxes;

}

/**
 * Initial report tab load through ajax
 *
 * @since 1.0.0
 */
function gamipress_reports_points_load_tab() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Setup the points type and the points type object
    $points_type = $_REQUEST['points_type'];

    $points_types = gamipress_get_points_types();
    $points_type_object = $points_types[$points_type];

    // Setup widgets totals
    $circulation   = gamipress_get_user_meta_sum( "_gamipress_{$points_type}_points" );
    $awarded       = gamipress_get_user_meta_sum( "_gamipress_{$points_type}_points_awarded" );
    $deducted      = gamipress_get_user_meta_sum( "_gamipress_{$points_type}_points_deducted" );
    $expended      = gamipress_get_user_meta_sum( "_gamipress_{$points_type}_points_expended" );

    if( function_exists('gamipress_format_amount') ) {
        $circulation   = gamipress_format_amount( $circulation, $points_type );
        $awarded       = gamipress_format_amount( $awarded, $points_type );
        $deducted      = gamipress_format_amount( $deducted, $points_type );
        $expended      = gamipress_format_amount( $expended, $points_type );
    }

    // Setup the users list table
    $table = new GamiPress_Report_List_Table( array(
        'id' => $points_type . '_list_table',
        'singular' => $points_type_object['singular_name'],
        'plural' => $points_type_object['plural_name'],
        'points_type' => $points_type,
        'classes' => '',
    ) );

    $table->prepare_items();

    ob_start();
    $table->display();
    $list_table = ob_get_clean();

    // Awarded chart
    $awarded_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $points_type . '_awarded_chart',
        'type' => 'line',
        'points_type' => $points_type,
    ) );

    $awarded_chart->prepare_chart();

    // Deducted chart
    $deducted_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $points_type . '_deducted_chart',
        'type' => 'line',
        'points_type' => $points_type,
    ) );

    $deducted_chart->prepare_chart();

    // Expended chart
    $expended_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $points_type . '_expended_chart',
        'type' => 'line',
        'points_type' => $points_type,
    ) );

    $expended_chart->prepare_chart();

    wp_send_json_success( array(
        'circulation'   => $circulation,
        'awarded'       => $awarded,
        'deducted'      => $deducted,
        'expended'      => $expended,
        'list_table'    => $list_table,
        'awarded_chart' => array(
            'stats' => $awarded_chart->get_stats(),
            'counters' => $awarded_chart->counters,
        ),
        'deducted_chart' => array(
            'stats' => $deducted_chart->get_stats(),
            'counters' => $deducted_chart->counters,
        ),
        'expended_chart' => array(
            'stats' => $expended_chart->get_stats(),
            'counters' => $expended_chart->counters,
        ),
    ) );

}
add_action( 'wp_ajax_gamipress_reports_load_points_tab', 'gamipress_reports_points_load_tab' );

/**
 * Custom query for report list table
 *
 * @since 1.0.0
 *
 * @param string $query
 * @param array $args
 *
 * @return string
 */
function gamipress_reports_points_list_table_query( $query, $args ) {

    global $wpdb;

    // Initialize query vars
    $select = array( 'u.ID AS user_id' );
    $from = array( "{$wpdb->users} AS u" );
    $join = array();
    $where = array( '1=1' );
    $order_by = array();
    $query_args = array();

    // Multisite check
    if( is_multisite() ) {
        $join[] = "LEFT JOIN {$wpdb->usermeta} AS umcap ON ( umcap.user_id = u.ID ) ";
        $where[] = "umcap.meta_key = '" . $wpdb->get_blog_prefix() . "capabilities' ";
    }

    $points_type = $args['points_type'];

    // Define metrics as fields => query arg
    $metrics = array(
        'points' => "_gamipress_{$points_type}_points",
        'awarded' => "_gamipress_{$points_type}_points_awarded",
        'deducted' => "_gamipress_{$points_type}_points_deducted",
        'expended' => "_gamipress_{$points_type}_points_expended",
    );

    // Loop all metrics to generate the query
    foreach( $metrics as $metric => $query_arg ) {

        $index = count($join);

        $select[] = "IFNULL( um{$index}.meta_value, 0 ) AS `{$metric}`";
        $join[] = "LEFT JOIN $wpdb->usermeta AS um{$index} ON ( um{$index}.user_id = u.ID AND um{$index}.meta_key = %s )";
        $query_args[] = $query_arg;

    }

    $order_by[] = "CAST( `points` AS UNSIGNED )";

    // Process query vars
    $select = ( ! empty( $select ) ? implode( ', ', $select ) : '' );
    $from = ( ! empty( $from ) ? implode( ', ', $from ) : '' );
    $join = ( ! empty( $join ) ? implode( ' ', $join ) : '' );
    $where = ( ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '' );
    $order_by = ( ! empty( $order_by ) ? 'ORDER BY ' . implode( ', ', $order_by ) . ' DESC' : '' );

    // Execute our query
    return $wpdb->prepare(
        "SELECT DISTINCT {$select}
         FROM {$from}
         {$join}
         {$where}
         {$order_by}",
        $query_args
    );

}

/**
 * Custom report list table columns
 *
 * @since 1.0.0
 *
 * @param array $columns
 *
 * @return array
 */
function gamipress_reports_points_list_table_columns( $columns ) {

    $columns['user_id'] = __( 'User', 'gamipress-reports' );
    $columns['points'] = __( 'Current Balance', 'gamipress-reports' );
    $columns['awarded'] = __( 'Awarded', 'gamipress-reports' );
    $columns['deducted'] = __( 'Deducted', 'gamipress-reports' );
    $columns['expended'] = __( 'Expended', 'gamipress-reports' );

    return $columns;
}

/**
 * Custom report list table columns output
 *
 * @since 1.0.0
 *
 * @param string    $column_name
 * @param stdClass  $item
 * @param array     $args
 */
function gamipress_reports_points_list_table_custom_column( $column_name, $item, $args ) {

    switch( $column_name ) {
        case 'user_id':

            $user = get_userdata( $item->user_id );

            $can_edit_users = current_user_can( 'edit_users' );

            if( $can_edit_users ) {
                printf(
                    '<a href="%s" aria-label="%s">%s (%s)</a>',
                    get_edit_user_link( $user->ID ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $user->user_login ) ),
                    $user->display_name,
                    $user->user_login
                );
            } else {
                echo $user->display_name;
            }
            break;
        case 'points':
        case 'awarded':
        case 'deducted':
        case 'expended':

            if( function_exists('gamipress_format_amount') ) {
                echo gamipress_format_amount( $item->$column_name, $args['points_type'] );
            }

            break;
    }

}

/**
 * Awarded report chart stats
 *
 * @since 1.0.0
 *
 * @param array $stats
 * @param array $args
 *
 * @return array
 */
function gamipress_reports_points_awarded_chart_stats( $stats, $args ) {

    $points_type = $args['points_type'];
    $date_range = isset( $args['date_range'] ) ? $args['date_range'] : 'week';
    $date = isset( $args['date'] ) ? $args['date'] : 0;

    // Awarded points
    $awarded = gamipress_reports_get_logs_report( array(
        'select' => array(
            'date' => array(
                'cast' => 'DATE'
            ),
            'count' => array(
                'field' => '_gamipress_points',
                'function' => 'SUM'
            )
        ),
        'where' => array(
            'type' => array(
                'value' => array( 'points_deduct', 'points_expend', 'points_revoke' ),
                'compare' => 'NOT IN'
            ),
            '_gamipress_points_type' => array(
                'value' => $points_type
            )
        ),
        'date_range' => $date_range,
        'since' => $date,
        'group_by' => $date_range === 'year' ? 'month' : 'day'
    ) );

    $stats['awarded'] = array(
        'label' => __( 'Awarded', 'gamipress-reports' ),
        'backgroundColor' => 'transparent',
        'borderColor' => '#34b75f',
        'data' => $awarded
    );

    return $stats;

}

/**
 * Awarded report chart counters
 *
 * @since 1.0.0
 *
 * @param array $counters
 * @param array $args
 *
 * @return array
 */
function gamipress_reports_points_awarded_chart_counters( $counters, $args ) {

    $counters['awarded'] = __( 'Awarded', 'gamipress-reports' );

    return $counters;

}

/**
 * Deducted report chart stats
 *
 * @since 1.0.0
 *
 * @param array $stats
 * @param array $args
 *
 * @return array
 */
function gamipress_reports_points_deducted_chart_stats( $stats, $args ) {

    $points_type = $args['points_type'];
    $date_range = isset( $args['date_range'] ) ? $args['date_range'] : 'week';
    $date = isset( $args['date'] ) ? $args['date'] : 0;

    // Deducted points
    $deducted = gamipress_reports_get_logs_report( array(
        'select' => array(
            'date' => array(
                'cast' => 'DATE'
            ),
            'count' => array(
                'field' => '_gamipress_points',
                'function' => 'SUM'
            )
        ),
        'where' => array(
            'type' => array(
                'value' => array( 'points_deduct', 'points_revoke' ),
                'compare' => 'IN'
            ),
            '_gamipress_points_type' => array(
                'value' => $points_type
            )
        ),
        'date_range' => $date_range,
        'since' => $date,
        'group_by' => $date_range === 'year' ? 'month' : 'day'
    ) );

    $stats['deducted'] = array(
        'label' => __( 'Deducted', 'gamipress-reports' ),
        'backgroundColor' => 'transparent',
        'borderColor' => '#e14d43',
        'data' => $deducted
    );

    return $stats;

}

/**
 * Deducted report chart counters
 *
 * @since 1.0.0
 *
 * @param array $counters
 * @param array $args
 *
 * @return array
 */
function gamipress_reports_points_deducted_chart_counters( $counters, $args ) {

    $counters['deducted'] = __( 'Deducted', 'gamipress-reports' );

    return $counters;

}

/**
 * Expended report chart stats
 *
 * @since 1.0.0
 *
 * @param array $stats
 * @param array $args
 *
 * @return array
 */
function gamipress_reports_points_expended_chart_stats( $stats, $args ) {

    $points_type = $args['points_type'];
    $date_range = isset( $args['date_range'] ) ? $args['date_range'] : 'week';
    $date = isset( $args['date'] ) ? $args['date'] : 0;

    // Expended points
    $expended = gamipress_reports_get_logs_report( array(
        'select' => array(
            'date' => array(
                'cast' => 'DATE'
            ),
            'count' => array(
                'field' => '_gamipress_points',
                'function' => 'SUM'
            )
        ),
        'where' => array(
            'type' => array(
                'value' => 'points_expend',
            ),
            '_gamipress_points_type' => array(
                'value' => $points_type
            )
        ),
        'date_range' => $date_range,
        'since' => $date,
        'group_by' => $date_range === 'year' ? 'month' : 'day'
    ) );

    $stats['expended'] = array(
        'label' => __( 'Expended', 'gamipress-reports' ),
        'backgroundColor' => 'transparent',
        'borderColor' => '#8461b5',
        'data' => $expended
    );

    return $stats;

}

/**
 * Expended report chart counters
 *
 * @since 1.0.0
 *
 * @param array $counters
 * @param array $args
 *
 * @return array
 */
function gamipress_reports_points_expended_chart_counters( $counters, $args ) {

    $counters['expended'] = __( 'Expended', 'gamipress-reports' );

    return $counters;

}