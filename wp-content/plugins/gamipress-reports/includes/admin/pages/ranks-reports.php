<?php
/**
 * Admin Ranks Reports Page
 *
 * @package     GamiPress\Reports\Admin\Ranks_Reports
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register ranks reports page.
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_reports_register_ranks_reports_page() {

    gamipress_register_reports_page( 'ranks', __( 'Ranks Reports', 'gamipress-reports' ) );

}
add_action( 'cmb2_admin_init', 'gamipress_reports_register_ranks_reports_page' );

/**
 * Register ranks reports sections
 *
 * @since  1.0.0
 *
 * @return array
 */
function gamipress_reports_register_ranks_reports_sections( $reports_sections ) {

    foreach( gamipress_get_rank_types() as $rank_type => $data ) {
        $reports_sections[$rank_type] = array(
            'title' => $data['plural_name'],
        );
    }

    return $reports_sections;

}
add_action( 'gamipress_reports_ranks_sections', 'gamipress_reports_register_ranks_reports_sections' );

/**
 * Dynamically load hooks by ranks type
 *
 * @since 1.0.0
 */
function gamipress_reports_load_ranks_reports() {

    foreach( gamipress_get_rank_types() as $rank_type => $data ) {
        // Dynamic meta boxes
        add_filter( "gamipress_reports_ranks_{$rank_type}_meta_boxes", 'gamipress_reports_ranks_meta_boxes' );

        // Dynamic user list table filters
        add_filter( "gamipress_reports_{$rank_type}_user_list_table_query", 'gamipress_reports_ranks_user_list_table_query', 10, 2 );
        add_filter( "gamipress_reports_manage_{$rank_type}_user_list_table_columns", 'gamipress_reports_ranks_user_list_table_columns' );
        add_action( "gamipress_reports_manage_{$rank_type}_user_list_table_custom_column", 'gamipress_reports_ranks_user_list_table_custom_column', 10, 3 );

        // Dynamic list table filters
        add_filter( "gamipress_reports_{$rank_type}_list_table_query", 'gamipress_reports_ranks_list_table_query', 10, 2 );
        add_filter( "gamipress_reports_manage_{$rank_type}_list_table_columns", 'gamipress_reports_ranks_list_table_columns' );
        add_action( "gamipress_reports_manage_{$rank_type}_list_table_custom_column", 'gamipress_reports_ranks_list_table_custom_column', 10, 3 );

        // Dynamic chart filters
        add_filter( "gamipress_reports_{$rank_type}_earned_chart_stats", 'gamipress_reports_ranks_earned_chart_stats', 10, 2 );
        add_filter( "gamipress_reports_{$rank_type}_earned_chart_counters", 'gamipress_reports_ranks_earned_chart_counters', 10, 2 );

        add_filter( "gamipress_reports_{$rank_type}_awarded_chart_stats", 'gamipress_reports_ranks_awarded_chart_stats', 10, 2 );
        add_filter( "gamipress_reports_{$rank_type}_awarded_chart_counters", 'gamipress_reports_ranks_awarded_chart_counters', 10, 2 );
    }

}
add_action( 'cmb2_admin_init', 'gamipress_reports_load_ranks_reports', 5 );

/**
 * Rank type reports meta boxes
 *
 * @since 1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_reports_ranks_meta_boxes( $meta_boxes ) {

    $rank_type = str_replace( 'gamipress_reports_ranks_', '', str_replace( '_meta_boxes', '', current_filter() ) );

    $meta_boxes[$rank_type . '-active'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Active', 'gamipress-reports' ),
            'icon' => 'dashicons-rank',
            'color' => 'blue'
        ) ),
    );

    $meta_boxes[$rank_type . '-highest'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Users on highest rank', 'gamipress-reports' ),
            'icon' => 'dashicons-arrow-up-alt',
            'color' => 'green'
        ) ),
    );

    $meta_boxes[$rank_type . '-lowest'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Users on lowest rank', 'gamipress-reports' ),
            'icon' => 'dashicons-arrow-down-alt',
            'color' => 'red'
        ) ),
    );

    // Earned chart
    $earned_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $rank_type . '_earned_chart',
        'type' => 'line',
        'rank_type' => $rank_type,
    ) );

    ob_start();
    $earned_chart->display();
    $earned_chart_html = ob_get_clean();

    $meta_boxes[$rank_type . '-earned-stats'] = array(
        'classes' => 'gamipress-reports-col-6',
        'content' => $earned_chart_html,
    );

    // Awarded chart
    $awarded_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $rank_type . '_awarded_chart',
        'type' => 'line',
        'rank_type' => $rank_type,
    ) );

    ob_start();
    $awarded_chart->display();
    $awarded_chart_html = ob_get_clean();

    $meta_boxes[$rank_type . '-awarded-stats'] = array(
        'classes' => 'gamipress-reports-col-6',
        'content' => $awarded_chart_html,
    );

    // USer list table
    $meta_boxes[$rank_type . '-user-list-table'] = array(
        'title' => __( 'Users Report', 'gamipress-reports' ),
        'classes' => 'gamipress-reports-col-6',
        'content' => '<div></div>',
    );

    // List table
    $meta_boxes[$rank_type . '-list-table'] = array(
        'title' => __( 'Ranks Report', 'gamipress-reports' ),
        'classes' => 'gamipress-reports-col-6',
        'content' => '<div></div>',
    );

    return $meta_boxes;

}

/**
 * Initial report tab load through ajax
 *
 * @since 1.0.0
 */
function gamipress_reports_ranks_load_tab() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    global $wpdb;

    // Setup the rank type and the rank type object
    $rank_type = $_REQUEST['rank_type'];

    $rank_types = gamipress_get_rank_types();
    $rank_type_object = $rank_types[$rank_type];

    $active = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s",
        $rank_type,
        'publish'
    ) );

    // Get rank type highest priority rank ID
    $highest_rank_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s ORDER BY menu_order DESC LIMIT 1",
        $rank_type,
        'publish'
    ) );

    // Get rank type lowest priority rank ID
    $lowest_rank_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s ORDER BY menu_order ASC LIMIT 1",
        $rank_type,
        'publish'
    ) );

    // Select users with lowest rank id or without this meta key
    $lowest =  $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT( DISTINCT user_id )
         FROM {$wpdb->usermeta}
         WHERE meta_key = %s
          AND meta_value = %s
          OR user_id NOT IN (
            SELECT DISTINCT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s
          )",
        "_gamipress_{$rank_type}_rank",
        $lowest_rank_id,
        "_gamipress_{$rank_type}_rank"
    ) );

    // Setup the users list table
    $user_table = new GamiPress_Report_List_Table( array(
        'id' => $rank_type . '_user_list_table',
        'singular' => $rank_type_object['singular_name'],
        'plural' => $rank_type_object['plural_name'],
        'rank_type' => $rank_type,
        'classes' => '',
    ) );

    $user_table->prepare_items();

    ob_start();
    $user_table->display();
    $user_list_table = ob_get_clean();

    // Setup the ranks list table
    $table = new GamiPress_Report_List_Table( array(
        'id' => $rank_type . '_list_table',
        'singular' => $rank_type_object['singular_name'],
        'plural' => $rank_type_object['plural_name'],
        'rank_type' => $rank_type,
        'classes' => '',
    ) );

    $table->prepare_items();

    ob_start();
    $table->display();
    $list_table = ob_get_clean();

    // Earned chart
    $earned_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $rank_type . '_earned_chart',
        'type' => 'line',
        'rank_type' => $rank_type,
    ) );

    $earned_chart->prepare_chart();

    // Awarded chart
    $awarded_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $rank_type . '_awarded_chart',
        'type' => 'line',
        'rank_type' => $rank_type,
    ) );

    $awarded_chart->prepare_chart();

    wp_send_json_success( array(
        'active'            => $active,
        'highest'           => gamipress_reports_get_user_meta_count( "_gamipress_{$rank_type}_rank", $highest_rank_id ),
        'lowest'            => $lowest,
        'expended'          => 0,
        'user_list_table'   => $user_list_table,
        'list_table'        => $list_table,
        'earned_chart' => array(
            'stats' => $earned_chart->get_stats(),
            'counters' => $earned_chart->counters,
        ),
        'awarded_chart' => array(
            'stats' => $awarded_chart->get_stats(),
            'counters' => $awarded_chart->counters,
        ),
    ) );

}
add_action( 'wp_ajax_gamipress_reports_load_ranks_tab', 'gamipress_reports_ranks_load_tab' );

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
function gamipress_reports_ranks_user_list_table_query( $query, $args ) {

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

    $rank_type = $args['rank_type'];

    $ct_table = ct_setup_table( 'gamipress_user_earnings' );

    $select[] = "IFNULL( ( SELECT r.menu_order FROM {$wpdb->posts} AS r WHERE r.ID = rm.meta_value ), 1 ) AS `rank`";
    $join[] = "LEFT JOIN {$wpdb->usermeta} AS rm ON ( rm.user_id = u.ID AND rm.meta_key = %s )";
    $query_args[] = "_gamipress_{$rank_type}_rank";

    $select[] = "re.meta_value AS `earned`";
    $join[] = "LEFT JOIN {$wpdb->usermeta} AS re ON ( re.user_id = u.ID AND re.meta_key = %s )";
    $query_args[] = "_gamipress_{$rank_type}_rank_earned_time";

    $order_by[] = "CAST( `rank` AS UNSIGNED )";

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
function gamipress_reports_ranks_user_list_table_columns( $columns ) {

    $columns['user_id'] = __( 'User', 'gamipress-reports' );
    $columns['rank'] = __( 'Current', 'gamipress-reports' );
    $columns['earned'] = __( 'Earned', 'gamipress-reports' );

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
function gamipress_reports_ranks_user_list_table_custom_column( $column_name, $item, $args ) {

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
        case 'rank':
            $rank = gamipress_get_user_rank( $item->user_id, $args['rank_type'] );

            if( $rank ) {
                echo $rank->post_title;
            }
            break;
        case 'earned':

            $time = absint( $item->earned );

            if( $time !== 0 ) {
                echo '<abbr title="' . date( 'Y/m/d H:i', $time ) . '">' . date( 'Y/m/d', $time ) . '</abbr>';
            }

            break;
    }

}

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
function gamipress_reports_ranks_list_table_query( $query, $args ) {

    global $wpdb;

    // Initialize query vars
    $select = array( 'p.ID AS rank_id' );
    $from = array( "{$wpdb->posts} AS p" );
    $join = array();
    $where = array( '1=1' );
    $order_by = array();
    $query_args = array();

    $rank_type = $args['rank_type'];

    $where[] = 'p.post_type = %s';
    $query_args[] = $rank_type;

    $where[] = 'p.post_status = %s';
    $query_args[] = 'publish';

    $ct_table = ct_setup_table( 'gamipress_user_earnings' );

    $select[] = "( SELECT COUNT(*) FROM {$ct_table->db->table_name} AS e WHERE e.post_id = p.ID ) AS `earners`";
    $select[] = "( SELECT e.user_id FROM {$ct_table->db->table_name} AS e WHERE e.post_id = p.ID ORDER BY e.date DESC LIMIT 1 ) AS `last_earner`";

    $order_by[] = "p.menu_order";

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
function gamipress_reports_ranks_list_table_columns( $columns ) {

    $columns['rank_id'] = __( 'Rank', 'gamipress-reports' );
    $columns['earners'] = __( 'Earners', 'gamipress-reports' );
    $columns['last_earner'] = __( 'Last Earner', 'gamipress-reports' );

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
function gamipress_reports_ranks_list_table_custom_column( $column_name, $item, $args ) {

    switch( $column_name ) {
        case 'rank_id':

            $post_title = get_post_field( 'post_title', $item->rank_id );

            $can_edit_posts = current_user_can( 'edit_posts' );

            if( $can_edit_posts ) {
                printf(
                    '<a href="%s" aria-label="%s">%s</a>',
                    get_edit_post_link( $item->rank_id ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $post_title ) ),
                    $post_title
                );
            } else {
                echo $post_title;
            }

            break;
        case 'last_earner':

            if( $item->last_earner ) {

                $user = get_userdata( $item->last_earner );

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

            }

            break;
    }

}

/**
 * Earned report chart stats
 *
 * @since 1.0.0
 *
 * @param array $stats
 * @param array $args
 *
 * @return array
 */
function gamipress_reports_ranks_earned_chart_stats( $stats, $args ) {

    $rank_type = $args['rank_type'];
    $date_range = isset( $args['date_range'] ) ? $args['date_range'] : 'week';
    $date = isset( $args['date'] ) ? $args['date'] : 0;

    $rank_ids = get_posts( array(
        'post_type'       => $rank_type,
        'post_status'     => 'any',
        'fields'          => 'ids',
        'posts_per_page'  => -1
    ) );

    // Earned points
    $earned = gamipress_reports_get_logs_report( array(
        'select' => array(
            'date' => array(
                'cast' => 'DATE'
            ),
            'count' => array(
                'field' => 'log_id',
                'function' => 'COUNT'
            )
        ),
        'where' => array(
            'type' => array(
                'value' => 'rank_earn',
            ),
            '_gamipress_rank_id' => array(
                'value' => $rank_ids,
                'compare' => 'IN'
            )
        ),
        'date_range' => $date_range,
        'since' => $date,
        'group_by' => $date_range === 'year' ? 'month' : 'day'
    ) );

    $stats['earned'] = array(
        'label' => __( 'Earned', 'gamipress-reports' ),
        'backgroundColor' => 'transparent',
        'borderColor' => '#34b75f',
        'data' => $earned
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
function gamipress_reports_ranks_earned_chart_counters( $counters, $args ) {

    $counters['earned'] = __( 'Earned', 'gamipress-reports' );

    return $counters;

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
function gamipress_reports_ranks_awarded_chart_stats( $stats, $args ) {

    $rank_type = $args['rank_type'];
    $date_range = isset( $args['date_range'] ) ? $args['date_range'] : 'week';
    $date = isset( $args['date'] ) ? $args['date'] : 0;

    $rank_ids = get_posts( array(
        'post_type'       => $rank_type,
        'post_status'     => 'any',
        'fields'          => 'ids',
        'posts_per_page'  => -1
    ) );

    // Awarded points
    $awarded = gamipress_reports_get_logs_report( array(
        'select' => array(
            'date' => array(
                'cast' => 'DATE'
            ),
            'count' => array(
                'field' => 'log_id',
                'function' => 'COUNT',
            )
        ),
        'where' => array(
            'type' => array(
                'value' => 'rank_award',
            ),
            '_gamipress_rank_id' => array(
                'value' => $rank_ids,
                'compare' => 'IN'
            )
        ),
        'date_range' => $date_range,
        'since' => $date,
        'group_by' => $date_range === 'year' ? 'month' : 'day'
    ) );

    $stats['awarded'] = array(
        'label' => __( 'Awarded', 'gamipress-reports' ),
        'backgroundColor' => 'transparent',
        'borderColor' => '#8461b5',
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
function gamipress_reports_ranks_awarded_chart_counters( $counters, $args ) {

    $counters['awarded'] = __( 'Awarded', 'gamipress-reports' );

    return $counters;

}